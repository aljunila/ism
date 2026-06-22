<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KelBarang;
use App\Models\Kapal;
use App\Models\Perusahaan;
use App\Models\ChecklistData;
use App\Models\KodeForm;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\TurunBarang;
use App\Models\DetailTurun;
use App\Models\TurunOtp;
use App\Models\FormISM;
use App\Models\Gudang;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;

class PenurunanController extends Controller
{
     public function index()
    {
        $data['active'] = "penurunan";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        }else if($roleJenis==6) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id_cabang', Session::get('id_cabang'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        return view('penurunan.index', $data);
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
        $data['penerima'] = $this->turunReceiverQuery()
            ->orderBy('user.nama')
            ->limit(200)
            ->get();
        if ($uid) {
            $get = TurunBarang::where('uid', $uid)->where('is_delete', 0)->first();
            $data['data'] = $get;
            $data['detail'] = DetailTurun::where('id_turun', $get->id)->where('is_delete',0)->get();
        }
        return view('penurunan.form', $data);
    }

     private function currentRoleJenis(): int
    {
        return (int) Session::get('previllage');
    }

    private function turunReceiverQuery()
    {
        $query = User::query()->select('user.id', 'user.nama', 'user.username', 'user.id_perusahaan', 'user.id_kapal');

        if (Schema::hasColumn('user', 'is_delete')) {
            $query->where('user.is_delete', 0);
        }

        if (Schema::hasColumn('user', 'status')) {
            $query->where(function ($q) {
                $q->where('user.status', 1)->orWhere('user.status', 'A');
            });
        }

        $roleJenis = $this->currentRoleJenis();
        if ($roleJenis === 2) {
            $query->where('user.id_perusahaan', Session::get('id_perusahaan'));
        } elseif ($roleJenis === 3) {
            $query->where('user.id_kapal', Session::get('id_kapal'));
        } elseif ($roleJenis === 4) {
            $query->where('user.id', Session::get('userid'));
        }

        return $query;
    }

    private function validTurunReceiver(int $receiverId): bool
    {
        return Schema::hasTable('user')
            && $this->turunReceiverQuery()->where('user.id', $receiverId)->exists();
    }

    public function generateTurunOtp(Request $request)
    {
        $request->validate([
            'id_penerima' => ['required', 'integer'],
        ]);

        if (!Schema::hasTable('t_turun_otp')) {
            return response()->json(['message' => 'Tabel OTP penurunan belum tersedia. Jalankan migration terlebih dahulu.'], 500);
        }

        $receiverId = (int) $request->input('id_penerima');
        $receiver = $this->turunReceiverQuery()
            ->where('user.id', $receiverId)
            ->first();

        if (!$receiver) {
            return response()->json(['message' => 'User penerima tidak valid untuk akses aktif'], 422);
        }

        $senderId = (int) Session::get('userid');
        TurunOtp::where('id_penerima', $receiverId)
            ->where('created_by', $senderId)
            ->whereNull('used_at')
            ->where('is_delete', 0)
            ->update([
                'is_delete' => 1,
                'changed_by' => $senderId,
            ]);

        $otpCode = sprintf('%06d', random_int(0, 999999));
        $expiresAt = Carbon::now()->addMinutes(10);
        TurunOtp::create([
            'uid' => Str::uuid()->toString(),
            'id_penerima' => $receiverId,
            'otp_code' => $otpCode,
            'expires_at' => $expiresAt,
            'is_delete' => 0,
            'created_by' => $senderId,
            'created_date' => date('Y-m-d H:i:s'),
        ]);

        app(NotificationService::class)->sendToTargets([
            'id_user' => $receiverId,
            'tipe' => 'otp_turun',
            'judul' => 'OTP Penurunan Barang',
            'pesan' => 'Kode OTP penurunan Anda: ' . $otpCode . '. Berlaku sampai ' . $expiresAt->format('d-m-Y H:i') . '.',
            'url' => route('show'),
            'created_by' => $senderId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sudah dikirim ke dashboard dan notifikasi penerima',
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function store(Request $request)
    {   
        $request->validate([
            'id_kapal' => ['required', 'integer'],
            'id_penerima' => ['required', 'integer'],
            'otp_code' => ['required', 'regex:/^[0-9]{6}$/'],
            'bagian' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'jumlah.*' => 'nullable|numeric|min:0'
        ]);

        if (!Schema::hasTable('t_turun_otp')) {
            return response()->json(['message' => 'Tabel OTP penurunan belum tersedia. Jalankan migration terlebih dahulu.'], 500);
        }

        $kapal = Kapal::where('status', 'A')->findOrFail($request->input('id_kapal'));
        $id_cabang = $kapal->id_cabang;
        if ((int) $this->currentRoleJenis() === 2 && (int) $kapal->pemilik !== (int) Session::get('id_perusahaan')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }
        if ((int) $this->currentRoleJenis() === 3 && (int) $kapal->id !== (int) Session::get('id_kapal')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }

        $receiverId = (int) $request->input('id_penerima');
        if (!$this->validTurunReceiver($receiverId)) {
            return response()->json(['message' => 'User penerima tidak valid untuk akses aktif'], 422);
        }

        $senderId = (int) Session::get('userid');
        $otp = TurunOtp::where('id_penerima', $receiverId)
            ->where('created_by', $senderId)
            ->where('otp_code', $request->input('otp_code'))
            ->whereNull('used_at')
            ->where('is_delete', 0)
            ->where('expires_at', '>=', Carbon::now())
            ->orderByDesc('id')
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Kode OTP tidak valid atau sudah expired'], 422);
        }

        $barangs = (array) $request->input('item', []);
        $jumlah = (array) $request->input('jumlah', []);
        $keterangan = (array) $request->input('ket', []);
        $validItems = [];
        foreach ($barangs as $item => $value) {
            $jum = $jumlah[$item] ?? null;
            $ket = $keterangan[$item] ?? null;
            if ($value && $jum !== null && $jum !== '') {
                $validItems[] = [
                    'barang' => $value,
                    'jumlah' => (int) $jum,
                    'ket' => $ket,
                ];
            }
        }

        if (empty($validItems)) {
            return response()->json(['message' => 'Minimal satu barang dan jumlah harus diisi'], 422);
        }

        foreach ($validItems as $payload) {
            $cek = Gudang::where('id_kapal', $kapal->id)
                ->where('id_barang', $payload['barang'])
                ->orderByDesc('id')
                ->first();

            if (!$cek || $payload['jumlah'] > (int) $cek->jumlah) {
                return response()->json(['message' => 'Jumlah melebihi stok barang yang tersedia'], 422);
            }
        }

        $bagian = $request->input('bagian');
        $kat = ((int) $bagian === 1) ? 'Deck' : 'Mesin';
        $tanggal = Carbon::parse($request->input('tanggal'))->format('dmY');
        $nomor = $kapal->call_sign.'/'.$kat.'/'.$tanggal;

        $get_nahkoda = Karyawan::where('id_kapal', $request->input('id_kapal'))->where('id_jabatan', 5)->where('status', 'A')->where('resign','N')->first();
        $kepala = Karyawan::where('id_cabang', $id_cabang)->where('id_jabatan', 3)->where('status', 'A')->where('resign','N')->first();
        $ttd = [
            'buat' => Session::get('id_karyawan'),
            'setuju' => $get_nahkoda->id,
            'mengetahui'   => $kepala->id,
        ];

        DB::beginTransaction();
        try {
            $save = TurunBarang::create([
                'uid' => Str::uuid()->toString(),
                'id_kapal' => $request->input('id_kapal'),
                'id_penerima' => $receiverId,
                'otp_code' => $request->input('otp_code'),
                'otp_verified_at' => Carbon::now(),
                'id_cabang' => $id_cabang,
                'nomor' => $nomor,
                'bagian' => $kat,
                'tanggal' => $request->input('tanggal'),
                'ttd' => $ttd,
                'is_delete' => 0,
                'created_by' => $senderId,
                'created_date' => date('Y-m-d H:i:s')
            ]);

            foreach ($validItems as $payload) {
                DetailTurun::create([
                    'uid' => Str::uuid()->toString(),
                    'id_turun' => $save->id,
                    'id_barang' => $payload['barang'],
                    'jumlah' => $payload['jumlah'],
                    'kondisi' => $payload['ket'],
                    'is_delete' => 0,
                    'created_by' => $senderId,
                    'created_date' => date('Y-m-d H:i:s')
                ]);

                $cek = Gudang::where('id_kapal', $save->id_kapal)
                    ->where('id_barang', $payload['barang'])
                    ->orderByDesc('id')
                    ->first();

                $total = max(0, ((int) $cek->jumlah) - $payload['jumlah']);
                Gudang::where('id', $cek->id)->update([
                    'jumlah' => $total,
                    'changed_date' => date('Y-m-d H:i:s')
                ]);
            }

            $otp->update([
                'used_at' => Carbon::now(),
                'id_turun' => $save->id,
                'changed_by' => $senderId,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(['success' => true, 'message' => 'Penurunan berhasil disimpan']);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = TurunBarang::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                });

        if ((int) $roleJenis === 2) {
            $query->whereIn('id_kapal', Kapal::where('pemilik', Session::get('id_perusahaan'))->pluck('id'));
        } else if ((int) $roleJenis === 3) {
            $query->whereIn('id_kapal', Kapal::where('id', Session::get('id_kapal'))->pluck('id'));
        } else if ((int) $roleJenis === 6) {
            $query->whereIn('id_kapal', Kapal::where('id_cabang', Session::get('id_perusahaan'))->pluck('id'));
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
            ->addColumn('penerima', function ($row) {
                $penerima = User::find($row->id_penerima);
                return $penerima ? $penerima->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('penurunan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function get($id) 
    {

        $result = DB::table('t_detail_turun as a')
                ->leftjoin('m_barang as c', 'c.id', '=', 'b.id_barang')
                ->select('a.*', 'c.nama as barang', 'c.deskripsi as satuan')
                ->where('id_turun', $id)->where('a.is_delete', 0)->get();

        return response()->json($result);
    }

    public function pdf ($uid) {
        $show = TurunBarang::where('uid', $uid)->where('is_delete', 0)->first();
        $nama = $show->get_kapal()->call_sign;
        $id_perusahaan = $show->get_kapal()->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 55)->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['perusahaan'] = Perusahaan::find($id_perusahaan);
        $ttd = $show->ttd;
        $data['mengetahui'] = Karyawan::find($ttd['mengetahui']);
        $data['setuju'] = Karyawan::find($ttd['setuju']);
        $data['buat'] = Karyawan::find($ttd['buat']);
        $data['terima'] = Karyawan::find($show->id_penerima);
        $data['item'] =  DB::table('t_detail_turun as a')
                        ->leftjoin('m_barang as c', 'c.id', '=', 'a.id_barang')
                        ->select('a.*', 'c.nama as barang', 'c.deskripsi as satuan')
                        ->where('id_turun', $show->id)->where('a.is_delete', 0)->get();
        $pdf = Pdf::loadView('penurunan.pdf', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream($form->ket.' '.$nama.'.pdf');
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

    public function datagudang(Request $request) {
        $id_kapal = $request->input('id_kapal');
        $item = $request->input('item');

        $cek = Gudang::where('id_kapal', $id_kapal)->where('id_barang', $item)->orderByDesc('id')->first();
        return response()->json([
            'stok' => $cek ? $cek->jumlah : 0
        ]);
    }

}
