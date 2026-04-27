<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use App\Models\Barang;
use App\Models\KelBarang;
use App\Models\DetailPermintaan;
use App\Models\LogBarang;
use App\Models\Kapal;
use App\Models\Perusahaan;
use App\Models\ChecklistData;
use App\Models\KodeForm;
use App\Models\User;
use App\Models\StatusBarang;
use App\Models\Cabang;
use App\Models\Currency;
use App\Models\PoBarang;
use App\Models\PurchasingBarang;
use App\Models\KirimBarang;
use App\Models\DetailKirim;
use App\Models\Gudang;
use App\Models\FormISM;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;

class PermintaanController extends Controller
{
    private function statusPermintaanId(): int
    {
        $status = StatusBarang::where('is_delete', 0)
            ->where('flag_permintaan', 1)
            ->orderBy('id')
            ->first();

        return (int) ($status->id ?? 1);
    }

    private function statusProsesId(): int
    {
        $status = StatusBarang::where('is_delete', 0)
            ->where(function ($q) {
                $q->where('flag_proses', 1)->orWhere('flag_berlangsung', 1);
            })
            ->orderBy('id')
            ->first();

        return (int) ($status->id ?? 2);
    }

    private function statusSelesaiId(): int
    {
        $status = StatusBarang::where('is_delete', 0)
            ->whereRaw('COALESCE(flag_permintaan, 0) = 0')
            ->whereRaw('COALESCE(flag_proses, 0) = 0')
            ->whereRaw('COALESCE(flag_berlangsung, 0) = 0')
            ->orderBy('id')
            ->first();

        return (int) ($status->id ?? $this->statusProsesId());
    }

    private function normalizedStatusName($statusId): string
    {
        $status = StatusBarang::find($statusId);
        if (!$status) {
            return '-';
        }

        if ((int) ($status->flag_permintaan ?? 0) === 1) {
            return 'Permintaan';
        }
        if ((int) ($status->flag_proses ?? 0) === 1 || (int) ($status->flag_berlangsung ?? 0) === 1) {
            return 'Proses (Berlangsung)';
        }

        return 'Selesai';
    }

    private function createFlowLog(int $idDetail, string $tanggal, int $statusId, string $eventCode, string $keterangan, string $img = ''): void
    {
        LogBarang::create([
            'uid' => Str::uuid()->toString(),
            'id_detail_permintaan' => $idDetail,
            'tanggal' => $tanggal,
            'status' => $statusId,
            'event_code' => $eventCode,
            'keterangan' => $keterangan,
            'img' => $img,
            'is_delete' => 0,
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s')
        ]);
    }

    private function flowStageLabel(?string $flowStage): string
    {
        return match ($flowStage) {
            'logistik' => 'Logistik',
            'purchasing' => 'Purchasing',
            'po' => 'PO',
            'gudang' => 'Gudang',
            'naik_kapal' => 'Naik Kapal',
            'selesai' => 'Selesai',
            default => 'Proses',
        };
    }

    private function normalizeAmount($value): float
    {
        if ($value === null) {
            return 0;
        }
        $clean = preg_replace('/[^\d\-]/', '', (string) $value);
        return $clean === '' ? 0 : (float) $clean;
    }

    private function currentRoleJenis(): int
    {
        return (int) Session::get('previllage');
    }

    private function applyPermintaanVisibility($query, string $permintaanAlias = 'a', ?string $kapalAlias = null)
    {
        $roleJenis = $this->currentRoleJenis();

        if ($roleJenis === 2) {
            $kapalAlias = $kapalAlias ?: '__kapal_scope';
            if ($kapalAlias === '__kapal_scope') {
                $query->leftJoin('kapal as ' . $kapalAlias, $kapalAlias . '.id', '=', $permintaanAlias . '.id_kapal');
            }
            $query->where($kapalAlias . '.pemilik', Session::get('id_perusahaan'));
        } elseif ($roleJenis === 3) {
            $query->where($permintaanAlias . '.id_kapal', Session::get('id_kapal'));
        }

        return $query;
    }

    private function canAccessPermintaan(Permintaan $permintaan): bool
    {
        $roleJenis = $this->currentRoleJenis();

        if ($roleJenis === 1 || $roleJenis === 4) {
            return true;
        }

        if ($roleJenis === 3) {
            return (int) $permintaan->id_kapal === (int) Session::get('id_kapal');
        }

        if ($roleJenis === 2) {
            $kapal = Kapal::find($permintaan->id_kapal);
            return $kapal && (int) $kapal->pemilik === (int) Session::get('id_perusahaan');
        }

        return true;
    }

    private function visiblePermintaanById(int $id): ?Permintaan
    {
        $permintaan = Permintaan::where('id', $id)->where('is_delete', 0)->first();
        if (!$permintaan || !$this->canAccessPermintaan($permintaan)) {
            return null;
        }

        return $permintaan;
    }

    private function visiblePermintaanByUid(string $uid): ?Permintaan
    {
        $permintaan = Permintaan::where('uid', $uid)->where('is_delete', 0)->first();
        if (!$permintaan || !$this->canAccessPermintaan($permintaan)) {
            return null;
        }

        return $permintaan;
    }

    private function visibleDetailById(int $id): ?DetailPermintaan
    {
        $detail = DetailPermintaan::where('id', $id)->where('is_delete', 0)->first();
        if (!$detail) {
            return null;
        }

        $permintaan = $this->visiblePermintaanById((int) $detail->id_permintaan);
        if (!$permintaan) {
            return null;
        }

        return $detail;
    }

    private function normalizeIncomingStage(?string $stage): ?string
    {
        return match ((string) $stage) {
            '1', 'logistik' => 'logistik',
            '2', 'purchasing' => 'purchasing',
            '3', 'po' => 'po',
            default => $stage ?: null,
        };
    }

    private function effectiveProcessingStage(?string $flowStage): string
    {
        return match ($flowStage) {
            'purchasing' => 'purchasing',
            'po' => 'po',
            default => 'logistik',
        };
    }

    public function index()
    {
        $data['active'] = "permintaan";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        $data['statusbarang'] = StatusBarang::where('is_delete',0)->get();
        $data['currencies'] = Currency::where('is_delete', 0)->orderBy('is_base', 'DESC')->orderBy('code')->get();
        return view('permintaan.index', $data);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = Permintaan::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                });

        if ((int) $roleJenis === 2) {
            $query->whereIn('id_kapal', Kapal::where('pemilik', Session::get('id_perusahaan'))->pluck('id'));
        }

        $query->orderByDesc('tanggal')->orderByDesc('id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('created', function ($row) {
                $created = User::find($row->created_by);
                return $created ? $created->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('permintaan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function store(Request $request)
    {   
        $request->validate([
            'id_kapal' => ['required', 'integer'],
            'bagian' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'item' => ['required', 'array'],
            'jumlah' => ['required', 'array'],
        ]);

        $kapal = Kapal::where(['id' => $request->id_kapal, 'status' => 'A'])->firstOrFail();
        $id_cabang = (int) $kapal->id_cabang;
        if ((int) $this->currentRoleJenis() === 2 && (int) $kapal->pemilik !== (int) Session::get('id_perusahaan')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }
        if ((int) $this->currentRoleJenis() === 3 && (int) $kapal->id !== (int) Session::get('id_kapal')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }

        $barangs = (array) $request->input('item', []);
        $jumlah = (array) $request->input('jumlah', []);
        $validItems = [];
        foreach ($barangs as $item => $value) {
            $jum = $jumlah[$item] ?? null;
            if ($value && $jum !== null && $jum !== '') {
                $validItems[] = [
                    'barang' => $value,
                    'jumlah' => $jum,
                ];
            }
        }

        if (empty($validItems)) {
            return response()->json(['message' => 'Minimal satu barang dan jumlah harus diisi'], 422);
        }

        $bagian = $request->input('bagian');
        $tanggal = Carbon::parse($request->input('tanggal'))->format('dmY');
        $nomor = $kapal->call_sign.'/'.$bagian.'/'.$tanggal;

        DB::transaction(function () use ($request, $bagian, $nomor, $validItems, $id_cabang) {
            $save = Permintaan::create([
              'uid' => Str::uuid()->toString(),
              'id_kapal' => $request->input('id_kapal'),
              'nomor' => $nomor,
              'bagian' => $bagian,
              'tanggal' => $request->input('tanggal'),
              'is_delete' => 0,
              'created_by' => Session::get('userid'),
              'created_date' => date('Y-m-d H:i:s')
            ]);

            foreach ($validItems as $payload) {
                $statusId = $this->statusPermintaanId();
                $savedetail = DetailPermintaan::create([
                    'uid' => Str::uuid()->toString(),
                    'id_permintaan' => $save->id,
                    'id_barang' => $payload['barang'],
                    'jumlah' => $payload['jumlah'],
                    'status' => $statusId,
                    'id_cabang' => $id_cabang,
                    'flow_stage' => 'logistik',
                    'is_delete' => 0,
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d H:i:s')
                ]);

                $this->createFlowLog(
                    $savedetail->id,
                    $save->tanggal,
                    $statusId,
                    'permintaan_created',
                    'Permintaan berhasil dibuat'
                );
            }
        });

        return response()->json(['success' => true, 'message' => 'Permintaan berhasil disimpan']);
    }

    public function destroy($id)
    {
        $permintaan = $this->visiblePermintaanById((int) $id);
        if (!$permintaan) {
            return response()->json(['message' => 'Data tidak ditemukan atau tidak dapat diakses'], 404);
        }

        $permintaanStatusId = $this->statusPermintaanId();
        $cek = DetailPermintaan::where('id_permintaan', $permintaan->id)
            ->where('status', '!=', $permintaanStatusId)
            ->where('is_delete', 0)
            ->exists();

        if ($cek) {
            return response()->json(['status' => 'error', 'message' => 'Maaf, permintaan tidak dapat dibatalkan karena sudah diproses'],422); 
        }

        DB::transaction(function () use ($permintaan) {
            $detailIds = DetailPermintaan::where('id_permintaan', $permintaan->id)
                ->where('is_delete', 0)
                ->pluck('id')
                ->all();

            $permintaan->update([
                'is_delete' => 1,
                'changed_by' => Session::get('userid'),
                'changed_date' => date('Y-m-d H:i:s')
            ]);

            DetailPermintaan::where('id_permintaan', $permintaan->id)->update([
                'is_delete' => 1,
                'changed_by' => Session::get('userid'),
                'changed_date' => date('Y-m-d H:i:s')
            ]);

            if (!empty($detailIds)) {
                LogBarang::whereIn('id_detail_permintaan', $detailIds)->update([
                    'is_delete' => 1,
                ]);
                PurchasingBarang::whereIn('id_detail_permintaan', $detailIds)->update([
                    'is_delete' => 1,
                    'changed_by' => Session::get('userid'),
                    'changed_date' => date('Y-m-d H:i:s')
                ]);
                PoBarang::whereIn('id_detail_permintaan', $detailIds)->update([
                    'is_delete' => 1,
                    'changed_by' => Session::get('userid'),
                    'changed_date' => date('Y-m-d H:i:s')
                ]);
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Permintaan berhasil dibatalkan'],200);
    }

    public function form(Request $request, $uid=null)
    {
        $data['active'] = "permintaan";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        $data['barang'] = Barang::where('is_delete', 0)->orderBy('id_kel_barang', 'ASC')->get();
        $data['kelompok'] = KelBarang::where('is_delete', 0)->get();
        $data['permintaanStatusId'] = $this->statusPermintaanId();
        if ($uid) {
            $get = $this->visiblePermintaanByUid($uid);
            abort_unless($get, 404);
            $data['data'] = $get;
            $data['detail'] = DetailPermintaan::where('id_permintaan', $get->id)->where('is_delete',0)->get();
        }
        return view('permintaan.form', $data);
    }

    public function deldetail($id)
    {
        $up = $this->visibleDetailById((int) $id);
        if (!$up) {
            return response()->json(['message' => 'Detail tidak ditemukan atau tidak dapat diakses'], 404);
        }

        if ((int) $up->status !== (int) $this->statusPermintaanId()) {
            return response()->json(['message' => 'Detail tidak dapat dihapus karena sudah diproses'], 422);
        }

        DB::transaction(function () use ($up) {
            $up->update([
                'is_delete' => 1,
                'changed_by' => Session::get('userid'),
                'changed_date' => date('Y-m-d H:i:s')
            ]);

            LogBarang::where('id_detail_permintaan', $up->id)->update([
                'is_delete' => 1,
            ]);
            PurchasingBarang::where('id_detail_permintaan', $up->id)->update([
                'is_delete' => 1,
                'changed_by' => Session::get('userid'),
                'changed_date' => date('Y-m-d H:i:s')
            ]);
            PoBarang::where('id_detail_permintaan', $up->id)->update([
                'is_delete' => 1,
                'changed_by' => Session::get('userid'),
                'changed_date' => date('Y-m-d H:i:s')
            ]);
        });
        return response()->json(['status' => 'success', 'message' => 'Permintaan berhasil dibatalkan'],200);
    }

    public function update(Request $request, $id)
    {   
        $permintaan = $this->visiblePermintaanById((int) $id);
        if (!$permintaan) {
            return response()->json(['message' => 'Data tidak ditemukan atau tidak dapat diakses'], 404);
        }

        $barangs = (array) $request->input('item', []);
        $jumlah = (array) $request->input('jumlah', []);
        $createdCount = 0;

        DB::transaction(function () use ($permintaan, $barangs, $jumlah, &$createdCount) {
            foreach ($barangs as $item => $value) {
                if (!$value) {
                    continue;
                }

                $jum = $jumlah[$item] ?? null;
                if ($jum === null || $jum === '') {
                    continue;
                }

                $statusId = $this->statusPermintaanId();
                $savedetail = DetailPermintaan::create([
                    'uid' => Str::uuid()->toString(),
                    'id_permintaan' => $permintaan->id,
                    'id_barang' => $value,
                    'jumlah' => $jum,
                    'status' => $statusId,
                    'flow_stage' => 'logistik',
                    'is_delete' => 0,
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d H:i:s')
                ]);

                $createdCount++;
                $this->createFlowLog(
                    $savedetail->id,
                    $permintaan->tanggal,
                    $statusId,
                    'permintaan_created',
                    'Permintaan berhasil dibuat'
                );
            }
        });

        if ($createdCount === 0) {
            return response()->json(['success' => true, 'message' => 'Tidak ada item baru yang ditambahkan']);
        }

        return response()->json(['success' => true, 'message' => 'Permintaan berhasil diperbarui']);
    }

    public function get($id) 
    {
        $permintaan = $this->visiblePermintaanById((int) $id);
        if (!$permintaan) {
            return response()->json(['message' => 'Data tidak ditemukan atau tidak dapat diakses'], 404);
        }

        $result = DB::table('t_detail_permintaan as a')
                ->leftjoin('m_barang as b', 'b.id', '=', 'a.id_barang')
                ->leftjoin('m_status_barang as c', 'c.id', '=', 'a.status')
                ->select('a.*', 'a.status as status_id', 'b.nama as barang', 'b.deskripsi as satuan', 'c.nama as status', 'c.flag_permintaan', 'c.flag_proses', 'c.flag_berlangsung')
                ->where('id_permintaan', $permintaan->id)->where('a.is_delete', 0)->get();

        $result = $result->map(function ($item) {
            $item->status = $this->normalizedStatusName($item->status_id);
            return $item;
        });

        return response()->json($result);
    }

    public function datalog(Request $request)
    {
        $status = $request->input('status');
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');

        $query = DB::table('t_detail_permintaan as a')
                ->leftjoin('t_permintaan_barang as b', 'b.id', '=', 'a.id_permintaan')
                ->leftjoin('m_status_barang as c', 'c.id', '=', 'a.status')
                ->leftJoin(DB::raw("
                    (
                        SELECT l.id_detail_permintaan, l.keterangan
                        FROM t_log_barang l
                        INNER JOIN (
                            SELECT id_detail_permintaan, MAX(id) AS max_id
                            FROM t_log_barang
                            WHERE is_delete = 0
                            GROUP BY id_detail_permintaan
                        ) lm ON lm.max_id = l.id
                    ) lg
                "), 'lg.id_detail_permintaan', '=', 'a.id')
                ->select('a.*', 'b.tanggal', 'b.nomor', 'b.id_kapal', 'c.nama as status_nama', 'c.flag_permintaan', 'c.flag_proses', 'c.flag_berlangsung', 'lg.keterangan as log_keterangan')
                ->where('a.is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('b.id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('b.tanggal', $tanggal);
                });

        $this->applyPermintaanVisibility($query, 'b');

        // status tab:
        // 1 => logistik, 2 => purchasing, 3 => po
        if ((string) $status === '1' || (string) $status === 'logistik') {
            $query->whereIn('a.flow_stage', ['logistik', 'gudang']);
        } elseif ((string) $status === '2' || (string) $status === 'purchasing') {
            $query->where('a.flow_stage', 'purchasing');
        } elseif ((string) $status === '3' || (string) $status === 'po') {
            $query->where('a.flow_stage', 'po');
        } elseif ((string) $status === '4' || (string) $status === 'workshop') {
            $query->where('a.flow_stage', 'workshop');
        }


        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('barang', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->nama : '-';
            })
            ->addColumn('satuan', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->deskripsi : '-';
            })
            ->addColumn('cabang', function ($row) {
                $cabang = Cabang::find($row->id_cabang);
                return $cabang ? $cabang->cabang : '-';
            })
            ->addColumn('status_view', function ($row) {
                return $this->normalizedStatusName($row->status);
            })
            ->addColumn('flow_view', function ($row) {
                return $this->flowStageLabel($row->flow_stage);
            })
            ->addColumn('flow_note', function ($row) {
                return $row->log_keterangan ?: '-';
            })
            ->make(true);
    }

    public function getcabang($idkapal)
    {
        $kapal = Kapal::where('status', 'A')->find($idkapal);
        if (!$kapal) {
            return response()->json([], 404);
        }

        if ((int) $this->currentRoleJenis() === 2 && (int) $kapal->pemilik !== (int) Session::get('id_perusahaan')) {
            return response()->json([], 403);
        }
        if ((int) $this->currentRoleJenis() === 3 && (int) $kapal->id !== (int) Session::get('id_kapal')) {
            return response()->json([], 403);
        }

        $data = Cabang::where('id', $kapal->id_cabang)->orWhere('id', 5)->get();
        return response()->json($data);
    }

    public function proses(Request $request)
    {   
        $id  = (int) $request->input('id');
        $up = $this->visibleDetailById($id);
        if (!$up) {
            return response()->json(['message' => 'Detail tidak ditemukan atau tidak dapat diakses'], 404);
        }

        if ($up->flow_stage === 'selesai') {
            return response()->json(['message' => 'Barang sudah selesai diproses'], 422);
        }

        $currentTab = $this->normalizeIncomingStage($request->input('current_status'));
        $actualStage = $up->flow_stage ?: 'logistik';
        $effectiveStage = $this->effectiveProcessingStage($actualStage);
        if ($currentTab && $currentTab !== $effectiveStage) {
            return response()->json(['message' => 'Tahap barang sudah berubah, muat ulang data terlebih dahulu'], 409);
        }

        $tanggalLog = $request->input('tanggal') ?: date('Y-m-d');
        $statusId = $this->statusProsesId();
        $flowStage = $actualStage;
        $procurementChannel = $up->procurement_channel;
        $keterangan = 'Barang diproses oleh logistik';
        $amount = $this->normalizeAmount($request->input('amount'));
        $currencyId = $request->input('id_currency');
        $shippingMode = $request->input('shipping_mode');
        $shippingPoint = $request->input('shipping_point');
        $vendor = $request->input('vendor');
        $jumlah = $request->input('jumlah');
        $kodePo = $request->input('kode_po');
        $id_cabang = $up->id_cabang;
        $id_barang = $up->id_barang;
        $id_kapal = $up->get_permintaan()->id_kapal;

        if($request->input('sedia')==0){
            $get = explode("|", $request->input('status'));
            $target = $get[0] ?? '';
            $id_cabang = $get[1] ?? null;
        } else {
            $target = $request->input('sedia');
        }

        if ($effectiveStage === 'logistik' && $actualStage === 'gudang' && (string) $target !== '6') {
            return response()->json(['message' => 'Barang di gudang hanya dapat dilanjutkan ke naik kapal'], 422);
        }

        if ($effectiveStage === 'logistik') {
            if ((string) $target === '4') {
                $flowStage = 'workshop';
                $procurementChannel = 'workshop';
                $id_cabang = null;
                $keterangan = 'Barang tersedia di workshop';
            } elseif ((string) $target === '5') {
                $flowStage = 'gudang';
                $keterangan = 'Barang masuk gudang logistik';
            } elseif ((string) $target === '6') {
                $flowStage = 'selesai';
                $keterangan = 'Barang dinaikkan ke kapal';
            } elseif ((string) $target === '2') {
                if (!$id_cabang) {
                    return response()->json(['message' => 'Cabang pembelian wajib dipilih'], 422);
                }
                $flowStage = 'purchasing';
                $procurementChannel = 'purchasing';
                $namaCabang = $id_cabang ? optional(Cabang::find($id_cabang))->cabang : null;
                $keterangan = 'Barang sedang di PO/Purchasing' . ($namaCabang ? ' (Pembelian di ' . $namaCabang . ')' : '');
            } elseif ((string) $target === '3') {
                $flowStage = 'po';
                $procurementChannel = 'po';
                $id_cabang = null;
                $keterangan = 'Barang sedang di PO';
            } else {
                return response()->json(['message' => 'Transisi logistik tidak valid'], 422);
            }
        } elseif ($effectiveStage === 'purchasing') {
            if (!in_array((string) $target, ['1', '4', '7'], true)) {
                return response()->json(['message' => 'Transisi purchasing tidak valid'], 422);
            }
            if ($amount <= 0 || !$currencyId || !$vendor || !$jumlah) {
                return response()->json(['message' => 'Vendor, jumlah, nominal, dan mata uang wajib diisi untuk purchasing'], 422);
            }
            $flowStage = 'purchasing';
            $procurementChannel = 'purchasing';
            if ((string) $target === '4') {               
                if ($shippingMode === 'transit' && !empty($shippingPoint)) {
                    $keterangan = 'Transit pada ' . $shippingPoint;
                    $flowStage = 'purchasing';
                } elseif ($shippingMode === 'direct_workshop') {
                    $keterangan = 'Direct langsung ke workshop';
                    $flowStage = 'workshop';
                    $this->createGudang($id_cabang, $id_barang, $jumlah);
                } else {
                    $keterangan = 'Barang sudah dibeli oleh purchasing, diteruskan ke workshop';
                    $flowStage = 'workshop';
                    $this->createGudang($id_cabang, $id_barang, $jumlah);
                }
                if ($shippingMode === 'transit' && empty($shippingPoint)) {
                    return response()->json(['message' => 'Lokasi transit wajib diisi'], 422);
                }
                if (!in_array($shippingMode, ['transit', 'direct_workshop'], true)) {
                    return response()->json(['message' => 'Mode kirim wajib dipilih saat purchasing selesai'], 422);
                }
            } elseif ((string) $target === '7') {   
                    $cek_kapal = Kapal::findorFail($id_kapal);
                    $id_cabang  = $cek_kapal->id_cabang;            
                    $shippingMode = null;
                    $keterangan = 'Barang dikirim ke Cabang';
            } else {
                $shippingMode = null;
                $shippingPoint = null;
                $keterangan = 'Barang sedang dibeli';
            }
        } elseif ($effectiveStage === 'po') {
            if (!in_array((string) $target, ['1', '4', '7'], true)) {
                return response()->json(['message' => 'Transisi PO tidak valid'], 422);
            }
            if ($amount <= 0 || !$currencyId || !$kodePo) {
                return response()->json(['message' => 'Nomor PO, nominal, dan mata uang wajib diisi untuk proses PO'], 422);
            }
            $flowStage = 'po';
            $procurementChannel = 'po';
            if ((string) $target === '4') {
                $flowStage = 'workshop';
                $keterangan = 'PO sudah selesai, barang akan masuk ke workshop';
                $this->createGudang($id_cabang, $id_barang, $jumlah);
            } elseif ((string) $target === '7') {   
                    $cek_kapal = Kapal::findorFail($id_kapal);
                    $id_cabang  = $cek_kapal->id_cabang;            
                    $shippingMode = null;
                    $keterangan = 'Barang dikirim ke Cabang';
            } else {
                $keterangan = 'Barang sedang di PO';
            }
        }

        // Jika sudah final naik kapal / selesai bisa diarahkan ke status selesai di endpoint berikutnya.
        if ($flowStage === 'selesai') {
            $statusId = $this->statusSelesaiId();
        }

        $imgName = '';
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $destination = public_path('file_permintaan_log');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $imgName = 'permintaan-log-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $imgName);
        }

        DB::transaction(function () use (
            $up,
            $statusId,
            $id_cabang,
            $kodePo,
            $procurementChannel,
            $flowStage,
            $effectiveStage,
            $id,
            $target,
            $vendor,
            $jumlah,
            $amount,
            $currencyId,
            $tanggalLog,
            $shippingMode,
            $shippingPoint,
            $keterangan,
            $imgName
        ) {
            $up->update([
                'status' => $statusId,
                'id_cabang' => $id_cabang,
                'kode_po' => $kodePo ?: $up->kode_po,
                'procurement_channel' => $procurementChannel,
                'flow_stage' => $flowStage,
                'changed_by' => Session::get('userid'),
                'changed_date' => date('Y-m-d H:i:s')
            ]);

            if ($effectiveStage === 'purchasing') {
                $purchase = PurchasingBarang::where('id_detail_permintaan', $id)
                    ->where('is_delete', 0)
                    ->orderByDesc('id')
                    ->first();

                $purchasePayload = [
                    'vendor' => $vendor,
                    'jumlah' => $jumlah,
                    'status_purchasing' => ((string) $target === '4') ? 'bought' : 'on_buy',
                    'amount' => $amount,
                    'jumlah' => $jumlah,
                    'id_currency' => $currencyId ?: null,
                    'tanggal_beli' => $tanggalLog,
                    'shipping_mode' => $shippingMode,
                    'shipping_point' => $shippingPoint,
                    'keterangan' => $keterangan,
                    'changed_by' => Session::get('userid'),
                    'changed_date' => date('Y-m-d H:i:s')
                ];

                if ($purchase) {
                    $purchase->update($purchasePayload);
                } else {
                    PurchasingBarang::create(array_merge($purchasePayload, [
                        'uid' => Str::uuid()->toString(),
                        'id_detail_permintaan' => $id,
                        'is_delete' => 0,
                        'created_by' => Session::get('userid'),
                        'created_date' => date('Y-m-d H:i:s')
                    ]));
                }
            }

            if ($effectiveStage === 'po') {
                $po = PoBarang::where('id_detail_permintaan', $id)
                    ->where('is_delete', 0)
                    ->orderByDesc('id')
                    ->first();

                $poPayload = [
                    'nomor_po' => $kodePo,
                    'status_po' => ((string) $target === '4') ? 'done' : 'on_process',
                    'amount' => $amount,
                    'jumlah' => $jumlah,
                    'id_currency' => $currencyId ?: null,
                    'tanggal_po' => $tanggalLog,
                    'keterangan' => $keterangan,
                    'changed_by' => Session::get('userid'),
                    'changed_date' => date('Y-m-d H:i:s')
                ];

                if ($po) {
                    $po->update($poPayload);
                } else {
                    PoBarang::create(array_merge($poPayload, [
                        'uid' => Str::uuid()->toString(),
                        'id_detail_permintaan' => $id,
                        'is_delete' => 0,
                        'created_by' => Session::get('userid'),
                        'created_date' => date('Y-m-d H:i:s')
                    ]));
                }
            }

            $eventCode = 'flow_' . $flowStage;
            $this->createFlowLog($id, $tanggalLog, $statusId, $eventCode, $keterangan, $imgName);
        });

        return response()->json(['success' => true, 'message' => 'Proses permintaan berhasil diperbarui']);
    }

     public function laporan()
    {
        $data['active'] = "lappermintaan";
        return view('laporan.permintaan.index', $data);
    }

    public function datalaporan(Request $request)
    {
        $status = $request->input('status');
        $query = DB::table('t_detail_permintaan as a')
                ->leftjoin('t_permintaan_barang as b', 'b.id', '=', 'a.id_permintaan')
                ->leftjoin('user as u', 'u.id', '=', 'b.created_by')
                ->select('a.*', 'b.tanggal', 'b.nomor', 'b.id_kapal', 'b.bagian', 'u.nama as peminta')
                ->where('a.is_delete', 0)
                ->orderBy('b.tanggal', 'DESC');

        $this->applyPermintaanVisibility($query, 'b');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('barang', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->nama : '-';
            })
            ->addColumn('satuan', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->deskripsi : '-';
            })
            ->addColumn('status', function ($row) {
                return $this->normalizedStatusName($row->status);
            })
            ->make(true);
    }

    public function getlog($id) 
    {
        $detail = $this->visibleDetailById((int) $id);
        if (!$detail) {
            return response()->json(['message' => 'Data log tidak ditemukan atau tidak dapat diakses'], 404);
        }

        $result = DB::table('t_log_barang as a')
                ->leftjoin('m_status_barang as c', 'c.id', '=', 'a.status')
                ->leftjoin('user as d', 'd.id', '=', 'a.created_by')
                ->select('a.*', 'a.status as status_id', 'c.nama as status', 'c.flag_permintaan', 'c.flag_proses', 'c.flag_berlangsung', 'd.nama as created')
                ->where('a.id_detail_permintaan', $detail->id)
                ->where('a.is_delete', 0)
                ->orderByDesc('a.created_date')
                ->orderByDesc('a.id')
                ->get();

        $result = $result->map(function ($item) {
            $item->status = $this->normalizedStatusName($item->status_id);
            return $item;
        });

        return response()->json($result);
    }

    public function pdf($uid) {
        $show = $this->visiblePermintaanByUid($uid);
        abort_unless($show, 404);
        $nama = $show->get_kapal()->call_sign;
        $id_perusahaan = $show->get_kapal()->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 47)->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['perusahaan'] = Perusahaan::find($id_perusahaan);
        $data['item'] = DetailPermintaan::where('id_permintaan', $show->id)->where('is_delete', 0)->get(); 
        $data['created'] = User::find($show->created_by);
        $pdf = Pdf::loadView('permintaan.pdf', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream($form->ket.' '.$nama.'.pdf');
    }

    public function kirim(Request $request, $uid=null)
    {
        $data['active'] = "permintaan";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        $data['barang'] = DetailPermintaan::where('is_delete', 0)->where('flow_stage', 'workshop')->orderBy('changed_date', 'ASC')->get();
        $data['gudang'] = Gudang::where('is_delete', 0)->get();
        $data['permintaanStatusId'] = $this->statusPermintaanId();
        if ($uid) {
            $get = $this->visiblePermintaanByUid($uid);
            abort_unless($get, 404);
            $data['data'] = $get;
            $data['detail'] = DetailPermintaan::where('id_permintaan', $get->id)->where('is_delete',0)->get();
        }
        return view('permintaan.kirim', $data);
    }

    public function storekirim(Request $request)
    {   
        $request->validate([
            'id_kapal' => ['required', 'integer'],
            'bagian' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'check' => 'required|array|min:1',
            'jumlah.*' => 'nullable|numeric|min:0'
        ]);

        $kapal = Kapal::where('status', 'A')->findOrFail($request->input('id_kapal'));
        $id_cabang = $kapal->id_cabang;
        if ((int) $this->currentRoleJenis() === 2 && (int) $kapal->pemilik !== (int) Session::get('id_perusahaan')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }
        if ((int) $this->currentRoleJenis() === 3 && (int) $kapal->id !== (int) Session::get('id_kapal')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }

        $bagian = $request->input('bagian');
        if($bagian==1) { $kat= 'Deck'; } else { $kat= 'Mesin'; }
        $tanggal = Carbon::parse($request->input('tanggal'))->format('dmY');
        $nomor = $kapal->call_sign.'/'.$kat.'/'.$tanggal;

            $save = KirimBarang::create([
              'uid' => Str::uuid()->toString(),
              'id_kapal' => $request->input('id_kapal'),
              'nomor' => $nomor,
              'bagian' => $kat,
              'tanggal' => $request->input('tanggal'),
              'is_delete' => 0,
              'created_by' => Session::get('userid'),
              'created_date' => date('Y-m-d H:i:s')
            ]);

            $checked = $request->check ?? [];
            $jml_kirim = $request->jumlah ?? [];
            $tot_barang = $request->total ?? [];
            $barang = $request->barang ?? [];
            $gudang = $request->gudang ?? [];
            
            DB::beginTransaction();
            try {
                foreach ($checked as $id) {
                    $jumlah = $jml_kirim[$id] ?? 0;
                    $tot = $tot_barang[$id] ?? 0;
                    $id_barang = $barang[$id] ?? 0;
                    $id_gudang = $gudang[$id] ?? 0;
                    $savedetail = DetailKirim::create([
                        'uid' => Str::uuid()->toString(),
                        'id_kirim' => $save->id,
                        'id_detail_permintaan' => $id,
                        'jumlah' => $jumlah,
                        'is_delete' => 0,
                        'created_by' => Session::get('userid'),
                        'created_date' => date('Y-m-d H:i:s')
                    ]);

                    if($jumlah<=$tot) {
                        $statusId=2;
                        $eventCode = "flow_workshop";
                        $keterangan = "Barang dikirim sebagian";
                    } else {
                        DetailPermintaan::where('id', $id)->update([
                            'flow_stage' => "selesai",
                            'status'    => 3,
                            'changed_by' => Session::get('userid'),
                            'changed_date' => date('Y-m-d H:i:s')
                        ]);

                        $statusId=3;
                        $eventCode = "permintaan_done";
                        $keterangan = "Barang sudah dikirim ke kapal";
                    }

                    $cek = Gudang::where('id_kapal', $save->id_kapal)
                        ->where('id_barang', $id_barang)
                        ->orderByDesc('id')
                        ->first();
                    if ($cek) {
                        $idgudang = $cek->id;
                        $total= $cek->jumlah + $jumlah;
                        Gudang::where('id', $idgudang)->update([
                            'jumlah' => $total,
                            'changed_date' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                            Gudang::create([
                            'uid' => Str::uuid()->toString(),
                            'id_barang' => $id_barang,
                            'id_kapal' => $save->id_kapal,
                            'jumlah' => $jumlah,
                            'changed_date' => date('Y-m-d H:i:s')
                        ]);
                    } 
                    if($id_gudang) {
                        $show = Gudang::findOrFail($id_gudang);
                        $minus = max(0, $show->jumlah - $jumlah);
                        $show->update([
                            'jumlah' => $minus,
                            'changed_date' => now()
                        ]);
                    }

                    $this->createFlowLog(
                        $id,
                        $save->tanggal,
                        $statusId,
                        $eventCode,
                        $keterangan
                    );
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        return response()->json(['success' => true, 'message' => 'Permintaan berhasil disimpan']);
    }

    public function datakirim(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = KirimBarang::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                });

        if ((int) $roleJenis === 2) {
            $query->whereIn('id_kapal', Kapal::where('pemilik', Session::get('id_perusahaan'))->pluck('id'));
        }

        $query->orderByDesc('tanggal')->orderByDesc('id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('created', function ($row) {
                $created = User::find($row->created_by);
                return $created ? $created->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('permintaan.partials.actionkirim', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function getkirim($id) 
    {

        $result = DB::table('t_detail_kirim as a')
                ->leftjoin('t_detail_permintaan as b', 'b.id', '=', 'a.id_detail_permintaan')
                ->leftjoin('m_barang as c', 'c.id', '=', 'b.id_barang')
                ->select('a.*', 'c.nama as barang', 'c.deskripsi as satuan', 'b.jumlah as jml_minta')
                ->where('id_kirim', $id)->where('a.is_delete', 0)->get();

        return response()->json($result);
    }

    public function pdfkirim ($uid) {
        $show = KirimBarang::where('uid', $uid)->where('is_delete', 0)->first();
        $nama = $show->get_kapal()->call_sign;
        $id_perusahaan = $show->get_kapal()->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 48)->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['perusahaan'] = Perusahaan::find($id_perusahaan);
        $data['item'] =  DB::table('t_detail_kirim as a')
                        ->leftjoin('t_detail_permintaan as b', 'b.id', '=', 'a.id_detail_permintaan')
                        ->leftjoin('m_barang as c', 'c.id', '=', 'b.id_barang')
                        ->select('a.*', 'c.nama as barang', 'c.deskripsi as satuan', 'b.jumlah as jml_minta')
                        ->where('id_kirim', $show->id)->where('a.is_delete', 0)->get();
        $data['created'] = User::find($show->created_by);
        $pdf = Pdf::loadView('permintaan.pdfkirim', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream($form->ket.' '.$nama.'.pdf');
    }

    private function createGudang(int $id_cabang, int $id_barang, int $jumlah): void
    {
        $cek = Gudang::where('id_cabang', $id_cabang)
                    ->where('id_barang', $id_barang)
                    ->orderByDesc('id')
                    ->first();
        if ($cek) {
            $idgudang = $cek->id;
            $total= $cek->jumlah + $jumlah;
            Gudang::whereIn('id', $idgudang)->update([
                'jumlah' => $total,
                'changed_date' => date('Y-m-d H:i:s')
            ]);
        } else {
             Gudang::create([
                'uid' => Str::uuid()->toString(),
                'id_barang' => $id_barang,
                'id_cabang' => $id_cabang,
                'jumlah' => $jumlah,
                'changed_date' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function baranggudang(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');

        $data = DB::table('t_detail_permintaan as a')
        ->leftJoin('m_barang as b', 'a.id_barang', '=', 'b.id')
        ->leftJoin('t_gudang as c', function ($join) {
            $join->on('c.id_barang', '=', 'b.id')
                 ->on('c.id_cabang', '=', 'a.id_cabang');
        })
        ->leftJoin('t_permintaan_barang as d', 'd.id', '=', 'a.id_permintaan')
        ->select('a.id', 'b.id as id_barang', 'b.nama as barang', 'd.nomor', 'd.tanggal', 'a.jumlah as jml_minta', 'c.jumlah as stok', 'c.id as idgudang')
        ->where('d.id_kapal', $kapal)
        ->where('a.flow_stage', 'workshop')
        ->get();
        return DataTables::of($data)->make(true);
    }

    public function elemen($uid)
    {
        $data['active'] = "form_ism";
        $get = FormISM::where('uid', $uid)->first();; 
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $get->id_perusahaan;
        if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } 
        $data['id_perusahaan'] = $id_perusahaan;
        $data['form'] = KodeForm::find($get->id_form);
        return view('permintaan.elemen', $data);
    }

    public function dataByIdp(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $request->input('id_perusahaan');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = DB::table('t_permintaan_barang as a')
                ->leftJoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->select('a.*')
                ->where('a.is_delete', 0)
                ->where('b.pemilik', $id_perusahaan)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('a.tanggal', $tanggal);
                });

        $query->orderByDesc('a.tanggal')->orderByDesc('a.id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('created', function ($row) {
                $created = User::find($row->created_by);
                return $created ? $created->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('permintaan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function pengiriman()
    {
        $data['active'] = "pengiriman";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        return view('permintaan.pengiriman', $data);
    }

    public function elemenkirim($uid)
    {
        $data['active'] = "form_ism";
        $get = FormISM::where('uid', $uid)->first();; 
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $get->id_perusahaan;
        if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } 
        $data['id_perusahaan'] = $id_perusahaan;
        $data['form'] = KodeForm::find($get->id_form);
        return view('permintaan.elemenkirim', $data);
    }

     public function kirimByIdp(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $request->input('id_perusahaan');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = DB::table('t_kirim_barang as a')
                ->leftJoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->select('a.*')
                ->where('a.is_delete', 0)
                ->where('b.pemilik', $id_perusahaan)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('a.tanggal', $tanggal);
                });

        $query->orderByDesc('a.tanggal')->orderByDesc('a.id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('created', function ($row) {
                $created = User::find($row->created_by);
                return $created ? $created->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('permintaan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    function dataPurchas(Request $request, $id) {
        $flow = $request->input('flowStage');
        if($flow=='purchasing') {
            $get = PurchasingBarang::where('id_detail_permintaan', $id)->first();
        } else {
            $get = PoBarang::where('id_detail_permintaan', $id)->first();
        }
        return response()->json($get);
    }
}
