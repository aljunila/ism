<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Mutasi;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Jabatan;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;
use App\Support\RoleContext;

class MutasiController extends Controller
{
    public function el0610()
    {
        $data['active'] = "el0610";
        $data['form'] = KodeForm::where('kode', 'el0610')->first();
        $ctx = RoleContext::get();
        $id_perusahaan = $ctx['perusahaan_id'];
        if($ctx['is_superadmin']) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif($ctx['jenis']==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $id_kapal = $ctx['kapal_id'];
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $id_kapal)->get();
        }
        return view('refrensi.mutasi', $data);
    }

    
    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
        $ctx = RoleContext::get();

        $daftar = DB::table('mutasi as a')
                ->leftjoin('perusahaan as b', 'a.dari_perusahaan', '=', 'b.id')
                ->leftjoin('kapal as c', 'a.dari_kapal', '=', 'c.id')
                ->leftjoin('perusahaan as d', 'a.ke_perusahaan', '=', 'd.id')
                ->leftjoin('kapal as e', 'a.ke_kapal', '=', 'e.id')
                ->leftJoin('karyawan as f', 'f.id', '=', 'a.id_karyawan')
                ->leftJoin('jabatan as g', 'g.id', '=', 'a.id_jabatan')
                ->select('a.*', 'b.nama as dari_perusahaan', 'c.nama as dari_kapal', 'f.nama as karyawan', 'g.nama as jabatan', 'd.nama as ke_perusahaan', 'e.nama as ke_kapal')
                ->where('a.kode', $request->input('kode'))
                ->where('a.status','A')
                ->when($perusahaan, fn($query, $perusahaan) => $query->where('a.dari_perusahaan', $perusahaan))
                ->when($kapal, fn($query, $kapal) => $query->where('a.dari_kapal', $kapal))
                ->when($ctx['jenis']==2 && $ctx['perusahaan_id'], fn($q) => $q->where('a.dari_perusahaan', $ctx['perusahaan_id']))
                ->when($ctx['jenis']==3 && $ctx['kapal_id'], fn($q) => $q->where('a.dari_kapal', $ctx['kapal_id']))
                ->orderBy('a.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $kapal = Kapal::findorFail($request->input('id_kapal'));
        $karyawan = Karyawan::findorFail($request->input('id_karyawan'));

        $save = Mutasi::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'dari_perusahaan' => $karyawan->id_perusahaan,
          'dari_kapal' => $karyawan->id_kapal,
          'ke_perusahaan' => $request->input('id_perusahaan'),
          'ke_kapal' => $request->input('id_kapal'),
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $karyawan->id_jabatan,
          'tgl_naik' => $request->input('tgl_naik'),
          'tgl_turun' => $request->input('tgl_turun'),
          'ket' => $request->input('ket'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);

         $save = Karyawan::where('id',$save->id_karyawan)->update([
          'id_perusahaan' => $save->ke_perusahaan,
          'id_kapal' => $save->ke_kapal,
          'changed_by' => Session::get('userid'),
        ]); 
        if($save) {
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }

     public function edit(Request $request)
    {
        $id = $request->id;
        $data = Mutasi::findOrFail($id);
        return response()->json($data);
    }

    public function pdf(Request $request) {
        $kode = "el0610";
        $id_perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal');
        $start = $request->input('start');
        if($request->input('end')) {
            $end = $request->input('end');
        } else {
            $end = date("Y-m-d");
        }

        $perusahaan = Perusahaan::findOrFail($id_perusahaan);
        $show =  Mutasi::where('dari_perusahaan', $id_perusahaan)->where('kode', $kode)
                ->where('tgl_naik', '>=', $start)->where('tgl_naik', '<=', $end)->where('status','A')->get();
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['show'] = $show;
        $data['perusahaan'] = $perusahaan;
        $pdf = Pdf::loadView('refrensi.pdfmutasi', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$perusahaan->kode.'.pdf');
    }

    public function update(Request $request, $id)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $kode = $request->input('kode');
        $save = Mutasi::where('id',$id)->update([
          'ke_kapal' => $request->input('ke_kapal'),
          'tgl_naik' => $request->input('tgl_naik'),
          'tgl_turun' => $request->input('tgl_turun'),
          'ket' => $request->input('ket'),
          'changed_by' => Session::get('userid'),
        ]); 
        if($save) {
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }

    public function delete($id)
    {
       $post = Mutasi::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

}
