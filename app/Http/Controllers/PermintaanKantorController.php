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
use App\Models\KodeForm;
use App\Models\User;
use App\Models\StatusBarang;
use App\Models\Cabang;
use App\Models\Currency;
use App\Models\PoBarang;
use App\Models\PurchasingBarang;
use App\Models\Gudang;
use App\Models\FormISM;
use App\Models\Notifikasi;
use App\Models\Karyawan;
use App\Models\Vendor;
use App\Models\LogGudang;
use App\Models\Divisi;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use Carbon\Carbon;

class PermintaanKantorController extends Controller
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

    private function activeUserQuery()
    {
        $query = User::query()->select('user.id');

        if (Schema::hasTable('roles')) {
            $query->leftJoin('roles', 'roles.id', '=', 'user.role_id');
        }

        if (Schema::hasColumn('user', 'is_delete')) {
            $query->where('user.is_delete', 0);
        }

        if (Schema::hasColumn('user', 'status')) {
            $query->where(function ($q) {
                $q->where('user.status', 1)->orWhere('user.status', 'A');
            });
        }

        return $query;
    }


    private function notificationRecipientsForPermintaan(Permintaan $permintaan, ?int $excludeUserId = null): array
    {
        if (!Schema::hasTable('user')) {
            return [];
        }

        $kapal = Kapal::find($permintaan->id_kapal);
        $query = $this->activeUserQuery();

        $hasRolesTable = Schema::hasTable('roles');
        $hasSuperField = $hasRolesTable && Schema::hasColumn('roles', 'is_superadmin');
        $hasJenisField = $hasRolesTable && Schema::hasColumn('roles', 'jenis');

        $query->where(function ($q) use ($permintaan, $kapal, $hasSuperField, $hasJenisField) {
            if ($hasSuperField) {
                $q->orWhere('roles.is_superadmin', 1);
            }

            if ($hasJenisField && $kapal) {
                $q->orWhere(function ($subQuery) use ($kapal) {
                    $subQuery->where('roles.jenis', 1)
                        ->where('user.id_perusahaan', $kapal->pemilik);
                });

                $q->orWhere(function ($subQuery) use ($permintaan) {
                    $subQuery->where('roles.jenis', 2)
                        ->where('user.id_kapal', $permintaan->id_kapal);
                });
            }

            if ($permintaan->created_by) {
                $q->orWhere('user.id', $permintaan->created_by);
            }
        });

        if ($excludeUserId) {
            $query->where('user.id', '!=', $excludeUserId);
        }

        return $query->distinct()->pluck('user.id')->map(fn ($id) => (int) $id)->all();
    }

    private function createNotifications(array $userIds, string $judul, string $pesan, ?string $url = null, string $tipe = 'info'): void
    {
        if (!Schema::hasTable('t_notifikasi')) {
            return;
        }

        $createdBy = Session::get('userid');
        foreach (array_unique(array_filter($userIds)) as $userId) {
            Notifikasi::create([
                'uid' => Str::uuid()->toString(),
                'id_user' => (int) $userId,
                'tipe' => $tipe,
                'judul' => $judul,
                'pesan' => $pesan,
                'url' => $url,
                'is_delete' => 0,
                'created_by' => $createdBy,
                'created_date' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function notifyPermintaan(Permintaan $permintaan, string $judul, string $pesan, string $tipe = 'info', ?string $url = null): void
    {
        $url = $url ?: route('permintaan.edit', $permintaan->uid);
        $recipients = $this->notificationRecipientsForPermintaan($permintaan, (int) Session::get('userid'));
        $this->createNotifications($recipients, $judul, $pesan, $url, $tipe);
    }

    private function flowStageLabel(?string $flowStage): string
    {
        return match ($flowStage) {
            'logistik' => 'Logistik',
            'purchasing' => 'Purchasing',
            'po' => 'PO',
            'selesai' => 'Selesai',
            default => 'Tolak',
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

    private function normalizeQuantity($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value) || (int) $value != (float) $value) {
            return null;
        }

        return (int) $value;
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
        $data['statusbarang'] = StatusBarang::where('is_delete',0)->get();
        $cabang = Session::get('id_cabang');
        $data['vendor'] = Vendor::where('is_delete',0)
                        ->when($cabang, function($query, $cabang) {
                            return $query->where('id_cabang', $cabang);
                        })->get();
        if($roleJenis==6) {
            $data['cabang'] = Cabang::where('is_delete', 0)->where('id', $cabang)->get();
        } else {
            $data['cabang'] = Cabang::where('is_delete',0)->get();
        }
        $data['currencies'] = Currency::where('is_delete', 0)->orderBy('is_base', 'DESC')->orderBy('code')->get();
        $data['cabang'] = Cabang::where('is_delete', 0)->get();
        return view('permintaan.kantor.index', $data);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_cabang = ($roleJenis == 6) ? Session::get('id_cabang') : $request->input('id_cabang');
        $tanggal = $request->input('tanggal');
        $query = Permintaan::where('is_delete', 0)
                ->where('id_kapal',null)
                ->when($id_cabang, function($query, $id_cabang) {
                    return $query->where('id_cabang', $id_cabang);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                });

        if ((int) $roleJenis === 6) {
            $query->whereIn('id_cabang', Cabang::where('id', Session::get('id_cabang'))->pluck('id'));
        }

        $query->orderByDesc('tanggal')->orderByDesc('id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created', function ($row) {
                $created = User::find($row->created_by);
                return $created ? $created->nama : '-';
            })
            ->addColumn('divisi', function ($row) {
                $divisi = Divisi::find($row->bagian);
                return $divisi ? $divisi->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('permintaan.kantor.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function history(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');

        $query = Permintaan::where('is_delete', 0)
            ->whereHas('details', function ($query) {
                $query->where('is_delete', 0);
            })
            ->whereDoesntHave('details', function ($query) {
                $query->where('is_delete', 0)
                    ->where(function ($subQuery) {
                        $subQuery->whereNull('flow_stage')
                            ->orWhere('flow_stage', '!=', 'selesai');
                    });
            })
            ->withCount(['details as item_count' => function ($query) {
                $query->where('is_delete', 0);
            }])
            ->when($id_kapal, function ($query, $id_kapal) {
                return $query->where('id_kapal', $id_kapal);
            })
            ->when($tanggal, function ($query, $tanggal) {
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
            ->make(true);
    }

    public function store(Request $request)
    {   
        $request->validate([
            'id_cabang' => ['required', 'integer'],
            'bagian' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'item' => ['required', 'array'],
            'jumlah' => ['required', 'array'],
        ]);

        $cabang = Cabang::where(['id' => $request->id_cabang, 'is_delete' => 0])->firstOrFail();
        $id_cabang = $request->id_cabang;

        $barangs = (array) $request->input('item', []);
        $jumlah = (array) $request->input('jumlah', []);
        $kets = (array) $request->input('ket', []);
        $satuan = (array) $request->input('satuan', []);
        $validItems = [];
        foreach ($barangs as $item => $value) {
            $jum = $jumlah[$item] ?? null;
            $itemKet = $kets[$item] ?? null;
            $sat = $satuan[$item] ?? null;
            if ($value && $jum !== null && $jum !== '') {
                $validItems[] = [
                    'barang' => $value,
                    'jumlah' => $jum,
                    'satuan' => $sat,
                    'ket' => $itemKet,
                ];
            }
        }

        if (empty($validItems)) {
            return response()->json(['message' => 'Minimal satu barang dan jumlah harus diisi'], 422);
        }

        $bagian = $request->input('bagian');
        $kategori = Divisi::findorFail($bagian);
        $kat = $kategori->nama;

        $bulan = Carbon::parse($request->input('tanggal'))->format('m');
        $bulanRomawi = [
            '01' => 'I',
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI',
            '07' => 'VII',
            '08' => 'VIII',
            '09' => 'IX',
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII',
        ];
        $bln = $bulanRomawi[$bulan] ?? '-';
        $thn = Carbon::parse($request->input('tanggal'))->format('Y');
        $cek = Permintaan::where('id_cabang', $request->id_cabang)->whereYear('tanggal', $thn)->where('bagian', $bagian)->where('is_delete', 0)->orderBy('id', 'DESC')->first();
        if($cek) {
            $exp = explode('/',$cek->nomor);
            $next = $exp[0];
        } else {
            $next=0;
        }
        $num = str_pad($next+1, 3, '0', STR_PAD_LEFT);
        $nomor = $num.'/'.$kat.'/'.$cabang->cabang.'/'.$bln.'/'.$thn;

        $kepala = Karyawan::where('id_cabang', $id_cabang)->where('id_jabatan', 3)->where('status', 'A')->where('resign','N')->first();
        $ttd = [
            'buat' => Session::get('id_karyawan'),
            'mengetahui' => $kepala->id
        ];

        $nama_file = null;
        if($request->hasFile('file')) {
            $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:20480',
            ]);
            $file = $request->file('file');
            $nama_file = 'permintaan-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $tujuan_upload = public_path('file_permintaan_log');
            $file->move($tujuan_upload,$nama_file); 
        }

        $save = null;
        DB::transaction(function () use ($request, $bagian, $nomor, $validItems, $id_cabang, $ttd, $nama_file, &$save) {
            $save = Permintaan::create([
              'uid' => Str::uuid()->toString(),
              'id_cabang' => $request->input('id_cabang'),
              'nomor' => $nomor,
              'bagian' => $bagian,
              'tanggal' => $request->input('tanggal'),
              'ttd' => $ttd,
              'file' => $nama_file,
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
                    'ket' => $payload['ket'],
                    'satuan' => $payload['satuan'],
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

        $this->notifyPermintaan(
            $save,
            'Permintaan barang baru',
            'Permintaan ' . $save->nomor . ' berhasil dibuat',
            'permintaan'
        );

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
        if($roleJenis==6) {
            $data['cabang'] = Cabang::where('is_delete', 0)->where('id', Session::get('id_cabang'))->get();
        } else {
            $data['cabang'] = Cabang::where('is_delete',0)->get();
        }
        $data['barang'] = Barang::where('is_delete', 0)->orderBy('id_kel_barang', 'ASC')->get();
        $data['kelompok'] = KelBarang::where('is_delete', 0)->get();
        $data['permintaanStatusId'] = $this->statusPermintaanId();
        $data['divisi'] = Divisi::where('is_delete',0)->get();
        $data['barang'] = DB::table('m_barang as a')
                        ->leftJoin('m_kel_barang as b', 'a.id_kel_barang', '=', 'b.id')
                        ->select('a.*')
                        ->where('a.is_delete', 0)->where('b.kategori', 5)->get();
        if ($uid) {
            $get = $this->visiblePermintaanByUid($uid);
            abort_unless($get, 404);
            $data['data'] = $get;
            $data['detail'] = DetailPermintaan::where('id_permintaan', $get->id)->where('is_delete',0)->get();
        }
        return view('permintaan.kantor.form', $data);
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
        $satuan = (array) $request->input('satuan', []);
        $ket = (array) $request->input('ket', []);
        $detailJumlah = (array) $request->input('detail_jumlah', []);
        $detailKeterangan = (array) $request->input('detail_keterangan', []);
        $permintaanStatusId = $this->statusPermintaanId();
        $quantityUpdates = [];
        $createdCount = 0;
        $updatedCount = 0;

        foreach ($detailJumlah as $detailId => $rawJumlah) {
            $detail = DetailPermintaan::where('id', (int) $detailId)
                ->where('id_permintaan', $permintaan->id)
                ->where('is_delete', 0)
                ->first();

            if (!$detail) {
                return response()->json(['message' => 'Detail permintaan tidak ditemukan'], 404);
            }

            $newJumlah = $this->normalizeQuantity($rawJumlah);
            if ($newJumlah === null || $newJumlah < 1) {
                return response()->json(['message' => 'Jumlah permintaan wajib berupa angka minimal 1'], 422);
            }

            if ((int) $detail->jumlah === $newJumlah) {
                continue;
            }

            $reason = trim((string) ($detailKeterangan[$detailId] ?? ''));
            if ($reason === '') {
                return response()->json(['message' => 'Keterangan wajib diisi jika jumlah bertambah atau berkurang'], 422);
            }

            $quantityUpdates[] = [
                'detail' => $detail,
                'old_jumlah' => (int) $detail->jumlah,
                'new_jumlah' => $newJumlah,
                'reason' => $reason,
            ];
        }

        $kapal = Kapal::find($permintaan->id_kapal);
        $id_cabang = (int) ($kapal->id_cabang ?? 0);

        DB::transaction(function () use ($permintaan, $barangs, $jumlah, $ket, $quantityUpdates, $permintaanStatusId, $id_cabang, &$createdCount, &$updatedCount) {
            foreach ($quantityUpdates as $payload) {
                $detail = $payload['detail'];
                $detail->update([
                    'jumlah' => $payload['new_jumlah'],
                    'changed_by' => Session::get('userid'),
                    'changed_date' => date('Y-m-d H:i:s')
                ]);

                $this->createFlowLog(
                    $detail->id,
                    date('Y-m-d'),
                    (int) $detail->status,
                    'permintaan_jumlah_updated',
                    'Jumlah permintaan diubah dari ' . $payload['old_jumlah'] . ' menjadi ' . $payload['new_jumlah'] . '. Keterangan: ' . $payload['reason']
                );

                $updatedCount++;
            }

            foreach ($barangs as $item => $value) {
                if (!$value) {
                    continue;
                }

                $jum = $jumlah[$item] ?? null;
                if ($jum === null || $jum === '') {
                    continue;
                }

                $normalizedJumlah = $this->normalizeQuantity($jum);
                if ($normalizedJumlah === null || $normalizedJumlah < 1) {
                    continue;
                }

                $itemKet = $ket[$item] ?? null;
                $savedetail = DetailPermintaan::create([
                    'uid' => Str::uuid()->toString(),
                    'id_permintaan' => $permintaan->id,
                    'id_barang' => $value,
                    'jumlah' => $normalizedJumlah,
                    'ket' => $itemKet,
                    'status' => $permintaanStatusId,
                    'id_cabang' => $id_cabang ?: null,
                    'flow_stage' => 'logistik',
                    'is_delete' => 0,
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d H:i:s')
                ]);

                $createdCount++;
                $this->createFlowLog(
                    $savedetail->id,
                    $permintaan->tanggal,
                    $permintaanStatusId,
                    'permintaan_created',
                    'Permintaan berhasil dibuat'
                );
            }
        });

        if ($createdCount === 0 && $updatedCount === 0) {
            return response()->json(['success' => true, 'message' => 'Tidak ada perubahan yang disimpan']);
        }

        $messageParts = [];
        if ($updatedCount > 0) {
            $messageParts[] = $updatedCount . ' jumlah barang diubah';
        }
        if ($createdCount > 0) {
            $messageParts[] = $createdCount . ' barang ditambahkan';
        }

        $this->notifyPermintaan(
            $permintaan,
            'Permintaan barang diperbarui',
            'Permintaan ' . $permintaan->nomor . ' diperbarui: ' . implode(', ', $messageParts),
            'permintaan'
        );

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
                ->select('a.*', 'a.status as status_id', 'b.nama as barang', 'c.nama as status', 'c.flag_permintaan', 'c.flag_proses', 'c.flag_berlangsung')
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
        $id_cabang = ($roleJenis == 6) ? Session::get('id_cabang') : $request->input('id_cabang');
        $tanggal = $request->input('tanggal');

        $query = DB::table('t_detail_permintaan as a')
                ->leftjoin('t_permintaan_barang as b', 'b.id', '=', 'a.id_permintaan')
                ->leftjoin('m_status_barang as c', 'c.id', '=', 'a.status')
                ->leftjoin('m_barang as d', 'd.id', '=', 'a.id_barang')
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
                ->select('a.*', 'b.tanggal', 'b.nomor', 'c.nama as status_nama', 'c.flag_permintaan', 'c.flag_proses', 'c.flag_berlangsung', 'lg.keterangan as log_keterangan', 'd.nama as as barang')
                ->where('a.is_delete', 0)
                ->where('id_kapal', null)
                ->when($id_cabang, function($query, $id_cabang) {
                    return $query->where('b.id_cabang', $id_cabang);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('b.tanggal', $tanggal);
                });
                $query->orderBy('b.id', 'DESC');

        $this->applyPermintaanVisibility($query, 'b');

        // status tab:
        // 1 => logistik, 2 => purchasing, 3 => po
        if ((string) $status === '1' || (string) $status === 'logistik') {
            $query->whereIn('a.flow_stage', ['logistik', 'gudang']);
        } elseif ((string) $status === '2' || (string) $status === 'purchasing') {
            $query->where('a.flow_stage', 'purchasing');
        } elseif ((string) $status === '3' || (string) $status === 'po') {
            $query->where('a.flow_stage', 'po');
        } elseif ((string) $status === '4' || (string) $status === 'selesai') {
            $query->where('a.flow_stage', 'selesai');
        } elseif ((string) $status === '5' || (string) $status === 'tolak') {
            $query->where('a.flow_stage', 'tolak');
        } 


        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('barang', function ($query, $keyword) {
                $query->where('d.nama', 'LIKE', '%' . $keyword . '%');
            })
            ->addColumn('barang', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->nama : '-';
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
        $jumlah = (float) $request->input('jumlah', 0);
        $kodePo = $request->input('kode_po');
        $ketPurchas = $request->input('ket');
        $id_cabang = $up->id_cabang;
        $id_barang = $up->id_barang;
        $id_kapal = $up->get_permintaan()->id_kapal;

        if($request->input('sedia')==0){
            $target = $request->input('status');
        } else {
            $target = $request->input('sedia');
        }

        if ($effectiveStage === 'logistik' && $actualStage === 'gudang' && (string) $target !== '6') {
            return response()->json(['message' => 'Barang di gudang hanya dapat dilanjutkan ke naik kapal'], 422);
        }

        if ($effectiveStage === 'logistik') {
            if ((string) $target === '1') {
                $flowStage = 'tolak';
                $procurementChannel = 'tolak';
                $id_cabang = null;
                $keterangan = 'Permintaan Ditolak';
            }  elseif ((string) $target === '2') {
                $flowStage = 'purchasing';
                $procurementChannel = 'purchasing';
                $keterangan = 'Barang sedang di PO/Purchasing' . ($ketPurchas ? ' (Keterangan : ' . $ketPurchas . ')' : '');
            } elseif ((string) $target === '3') {
                $flowStage = 'po';
                $procurementChannel = 'po';
                $id_cabang = null;
                $keterangan = 'Barang sedang di PO' . ($kodePo ? ' (Kode PO : ' . $kodePo . ')' : '');
            } else {
                return response()->json(['message' => 'Transisi logistik tidak valid'], 422);
            }
        } elseif ($effectiveStage === 'purchasing') {
            if ($amount <= 0 || !$currencyId || !$vendor || !$jumlah) {
                return response()->json(['message' => 'Vendor, jumlah, nominal, dan mata uang wajib diisi untuk purchasing'], 422);
            }
            $flowStage = 'purchasing';
            $procurementChannel = 'purchasing';
            if ((string) $target === '4') {                             
                $keterangan = 'Barang selesai dibeli';
                $flowStage = 'selesai';                  
            } else {
                $keterangan = 'Barang sedang dibeli';
            }
        } elseif ($effectiveStage === 'po') {
            if ($amount <= 0 || !$currencyId || !$kodePo) {
                return response()->json(['message' => 'Nomor PO, nominal, dan mata uang wajib diisi untuk proses PO'], 422);
            }
            $flowStage = 'po';
            $procurementChannel = 'po';
            if ((string) $target === '4') {
                $flowStage = 'selesai';
                $keterangan = 'Barang telah diterima';
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

        $permintaan = $up->get_permintaan();
        if ($permintaan) {
            $this->notifyPermintaan(
                $permintaan,
                'Status permintaan diperbarui',
                $permintaan->nomor . ': ' . $keterangan,
                'proses',
                url('/permintaan')
            );
        }

        return response()->json(['success' => true, 'message' => 'Proses permintaan berhasil diperbarui']);
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
        $data['show'] = $show;
        $ttd = $show->ttd;
        $tgl = Carbon::parse($show->tanggal)->format('dmY');
        $data['buat'] = Karyawan::find($ttd['buat']);
        $data['mengetahui'] = Karyawan::find($ttd['mengetahui']);
        $data['item'] = DetailPermintaan::where('id_permintaan', $show->id)->where('is_delete', 0)->get(); 
        $data['created'] = User::find($show->created_by);
        $pdf = Pdf::loadView('permintaan.kantor.pdf', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream('Permintaan '.$tgl.'.pdf');
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


    function dataPurchas(Request $request, $id) {
        $flow = $request->input('flowStage');
        if($flow=='purchasing') {
            $get = PurchasingBarang::where('id_detail_permintaan', $id)->first();
        } else {
            $get = PoBarang::where('id_detail_permintaan', $id)->first();
        }
        return response()->json($get);
    }

    function userkapal(Request $request) {
        $id_kapal = $request->input('id_kapal');
        $user = User::where('id_kapal', $id_kapal)->select('id','nama')->where('is_delete', 0)->get();
         return response()->json($user);
    }

}
