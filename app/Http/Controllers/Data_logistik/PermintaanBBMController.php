<?php

namespace App\Http\Controllers\Data_logistik;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\BBMKapal;
use App\Models\LogBBM;
use App\Models\Kapal;
use App\Models\PermintaanBBM;
use App\Models\Pelabuhan;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\KirimBarang;
use App\Models\DetailKirim;
use App\Models\KodeForm;
use App\Models\FormISM;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Str;
use Session;
use Carbon\Carbon;

class PermintaanBBMController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_logistik/bbm";
        if(Session::get('previllage')==2) {
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', Session::get('id_perusahaan'))->get(); 
        } else if(Session::get('previllage')==3) {
          $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get(); 
        } else if(Session::get('previllage')==6) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id_cabang', Session::get('id_cabang'))->get(); 
        } else {
            $data['kapal'] = Kapal::where('status', 'A')->get();  
        }
        return view('data_logistik.bbm.index', $data);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');

        $query = PermintaanBBM::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                })
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_logistik.bbm.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {  
        $kapal = Kapal::where(['id' => $request->id_kapal, 'status' => 'A'])->firstOrFail();
        $id_cabang = (int) $kapal->id_cabang;

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
        $perusahaan = Perusahaan::findorFail($kapal->pemilik);
        $thn = Carbon::parse($request->input('tanggal'))->format('Y');
        $cek = PermintaanBBM::where('id_kapal', $request->id_kapal)->whereYear('tanggal', $thn)->where('is_delete', 0)->orderBy('id', 'DESC')->first();
        if($cek) {
            $exp = explode('/',$cek->nomor);
            $next = $exp[0];
        } else {
            $next=0;
        }
        $num = str_pad($next+1, 3, '0', STR_PAD_LEFT);
        $nomor = $num.'/BBM/'.$perusahaan->kode.'/'.$kapal->nama.'/'.$bln.'/'.$thn;

        $get_nahkoda = Karyawan::where('id_kapal', $request->input('id_kapal'))->where('id_jabatan', 5)->where('status', 'A')->where('resign','N')->first();
        $kepala = Karyawan::where('id_cabang', $id_cabang)->where('id_jabatan', 3)->where('status', 'A')->where('resign','N')->first();
        $kkm = Karyawan::where('id_kapal', $request->input('id_kapal'))->where('id_jabatan', 16)->where('status', 'A')->where('resign','N')->first();
        $ttd = [
            'nahkoda' => $get_nahkoda->id,
            'cabang'   => $kepala->id,
            'kkm' => $kkm->id
        ];
        $validated = PermintaanBBM::create([
            'uid' => Str::uuid()->toString(),
            'id_kapal' => $request->post('id_kapal'),
            'nomor' => $nomor,
            'status' => 1,
            'tanggal' => $request->post('tanggal'),
            'jam' => $request->post('jam'),
            'lintasan' => $request->post('lintasan'),
            'jarak' => $request->post('jarak'),
            'jumlah' => $request->post('jumlah'),
            'ttd' => $ttd,
            'is_delete' => 0,
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s'),
        ]);
        if($validated) {
            return response()->json(['status' => 'success', 'message' => 'Data bbm berhasil disimpan'],200);
        }
    }

    public function update(Request $request, $id)
    {
        $cek = PermintaanBBM::findorFail($id);
        if($cek->status==2){
             return response()->json(['status' => 'error', 'message' => 'Maaf, permintaan tidak bisa diubah dikarenakan sudah diproses tim Logistik'],422);
        } else {
            $validated = PermintaanBBM::where('id', $id)->update([
                'id_kapal' => $request->post('id_kapal'),
                'tanggal' => $request->post('tanggal'),
                'jam' => $request->post('jam'),
                'lintasan' => $request->post('lintasan'),
                'jarak' => $request->post('jarak'),
                'jumlah' => $request->post('jumlah'),
                'changed_by' => Session::get('userid'),
            ]);
            if($validated){
                return response()->json(['status' => 'success', 'message' => 'Data bbm berhasil diubah'],200);
            }
        }
    }

    public function destroy($id)
    {
        $up = PermintaanBBM::findOrFail($id);
        if($up->status==1) {
            $up->update(['is_delete' => 1]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Maaf, permintaan tidak bisa dihapus dikarenakan sudah diproses tim Logistik'],422);
        } 
        return response()->json(['message' => 'Data dihapus']);
    }

     public function pdf($uid) {
        $show = PermintaanBBM::where('uid', $uid)->first();
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
        $ttd = $show->ttd;
        $data['nahkoda'] = Karyawan::find($ttd['nahkoda']);
        $data['kkm'] = Karyawan::find($ttd['kkm']);
        $data['cabang'] = Karyawan::find($ttd['cabang']);
        $data['perusahaan'] = Perusahaan::find($id_perusahaan);
        $pdf = Pdf::loadView('data_logistik.bbm.pdf', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream($form->ket.' '.$nama.'.pdf');
    }

    public function proses(Request $request, $id)
    {   
        $show = PermintaanBBM::findorFail($id);
        $kapal = Kapal::where('status', 'A')->findOrFail($show->id_kapal);
        $id_cabang = (int) $kapal->id_cabang;
        if ( (Session::get('previllage') == 2) && (int) $kapal->pemilik !== (int) Session::get('id_perusahaan')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }
        if ((Session::get('previllage') == 3) && (int) $kapal->id !== (int) Session::get('id_kapal')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }

        $kat = "BBM";
        $tanggal = Carbon::parse($request->input('tanggal'))->format('dmY');
        $nomor = $kapal->call_sign.'/'.$kat.'/'.$tanggal;

        $get_nahkoda = Karyawan::where('id_kapal', $show->id_kapal)->where('id_jabatan', 5)->where('status', 'A')->where('resign','N')->first();
        $kepala = $id_cabang ? Karyawan::where('id_cabang', $id_cabang)->where('id_jabatan', 3)->where('status', 'A')->where('resign','N')->first() : null;
        $logistik = $id_cabang ? Karyawan::where('id_cabang', $id_cabang)->where('id_jabatan', 35)->where('status', 'A')->where('resign','N')->first() : null;
        $kkm = Karyawan::where('id_kapal', $show->id_kapal)->where('id_jabatan', 16)->where('status', 'A')->where('resign','N')->first();
        $penerima = User::where('id_karyawan', $kkm->id)->first();
        $ttd = [
            'setuju'     => $get_nahkoda?->id,
            'mengetahui' => $kepala?->id,
            'logistik'   => $logistik?->id,
        ];

        $save = KirimBarang::create([
            'uid' => Str::uuid()->toString(),
            'id_kapal' => $show->id_kapal,
            'id_penerima' => $penerima->id,
            'nomor' => $nomor,
            'bagian' => $kat,
            'tanggal' => $request->input('tanggal'),
            'ttd' => $ttd,
            'is_delete' => 0,
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s')
        ]);

        if($save) {

            $savedetail = DetailKirim::create([
                'uid' => Str::uuid()->toString(),
                'id_kirim' => $save->id,
                'jumlah' => $request->input('jumlah'),
                'is_delete' => 0,
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s')
            ]);  

            $rob_awal = BBMKapal::where('id_kapal', $show->id_kapal)->first();
            $saldo_awal = $rob_awal->saldo;
            $saldo_akhir = $saldo_awal+($request->input('jumlah'));
            $updatebbm = BBMKapal::where('id', $rob_awal->id)->update([
                'saldo'       => $saldo_akhir,
                'changed_by'   => Session::get('userid'),
            ]);
                    
            $updatereq = PermintaanBBM::where('id',$id)->update([
                'jumlah'       => $request->input('jumlah'),
                'status'        => 2,
                'saldo_awal' => $saldo_awal,
                'saldo_akhir' => $saldo_akhir,
                'uid_kirim'    => $save->uid,
                'created_by' => Session::get('userid'),
            ]);

            $validated = ([
                'uid' => Str::uuid()->toString(),
                'id_kapal' => $show->id_kapal,
                'saldo_awal' => $saldo_awal,
                'status' => 1,
                'id_jenis' => 2,
                'tanggal' => $request->post('tanggal'),
                'jumlah' => $request->post('jumlah'),
                'saldo_akhir' => $saldo_akhir,
                'is_delete' => 0,
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s'),
            ]);
            $save = LogBBM::create($validated);
           return response()->json(['status' => 'success', 'message' => 'Permintaan telah diselesaikan'],200);
        }
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
        return view('data_logistik.bbm.elemen', $data);
    }

     public function dataByIdp(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $request->input('id_perusahaan');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = DB::table('t_permintaan_bbm as a')
                ->leftJoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->select('a.*', 'b.nama as kapal')
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
            ->make(true);
    }
}