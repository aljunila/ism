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

class MutasiController extends Controller
{
    public function el0610()
    {
        $data['active'] = "el0610";
        $data['form'] = KodeForm::where('kode', 'el0610')->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
        }
        return view('refrensi.mutasi', $data);
    }

    
    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

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
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('a.dari_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('a.dari_kapal', $kapal);
                })
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
        $kapal = Kapal::findorFail($request->input('ke_kapal'));
        $karyawan = Karyawan::findorFail($request->input('id_karyawan'));

        $save = Mutasi::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'dari_perusahaan' => $request->input('dari_perusahaan'),
          'dari_kapal' => $request->input('dari_kapal'),
          'ke_perusahaan' => $kapal->pemilik,
          'ke_kapal' => $request->input('ke_kapal'),
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $karyawan->id_jabatan,
          'tgl_naik' => $request->input('tgl_naik'),
          'tgl_turun' => $request->input('tgl_turun'),
          'ket' => $request->input('ket'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
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