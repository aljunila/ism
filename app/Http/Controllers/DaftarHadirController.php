<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\DaftarHadir;
use App\Models\DaftarHadirDetail;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Kapal;
use App\Models\KodeForm;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class DaftarHadirController extends Controller
{
    public function el0306()
    {
        $data['active'] = "el0306";
        $data['form'] = KodeForm::where('kode', 'el0306')->first();
        return view('hadir.el0306', $data);
    }

    public function getData(Request $request) {
        $get = DB::table('daftar_hadir as a')
                ->leftjoin('kapal as b', 'b.id', '=', 'a.id_kapal')
                ->select('a.*', 'b.nama as kapal')
                ->where('a.status', 'A')
                ->where('a.kode', $request->input('kode'))
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add($kode) 
    {
        $data['active'] = $kode;
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['kapal'] = Kapal::where('status','A')->get();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('hadir.add', $data);
    }
  
    public function store(Request $request)
    {
        $save = DaftarHadir::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_kapal' => $request->input('id_kapal'),
          'agenda' => $request->input('agenda'),
          'date' => $request->input('date'),
          'tampat' => $request->input('tampat'),
          'status' => 'A',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s'),
        ]);

        if($request->input('id_karyawan')){
            $karyawan = $request->input('id_karyawan');
            $tanggal = $request->input('tanggal');
            foreach ($karyawan as $i => $value) {
                $tgl = $tanggal[$i] ?? null;
                $get = Karyawan::where('id', $value)->first(); 
                $item = DaftarHadirDetail::insert([
                        'uid' => Str::uuid()->toString(),
                        'id_daftar_hadir' => $save->id,
                        'id_karyawan' => $value,
                        'id_jabatan' => $get->id_jabatan,
                        'tanggal' => $tgl,
                        'status' => 'A',
                        'created_by' => Session::get('userid'),
                        'created_date' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        return;
    }

    public function edit($uid)
    {
        $show = DaftarHadir::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = $show->kode;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['kapal'] = Kapal::where('status','A')->get();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $data['detail'] = DaftarHadirDetail::where('id_daftar_hadir', $show->id)->get();
        return view('hadir.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $post = DaftarHadir::find($id)->update($request->all());    
        if($request->input('id_karyawan')){
            $karyawan = $request->input('id_karyawan');
            $tanggal = $request->input('tanggal');
            foreach ($karyawan as $i => $value) {
                $tgl = $tanggal[$i] ?? null;
                $get = Karyawan::where('id', $value)->first(); 
                $item = DaftarHadirDetail::insert([
                        'uid' => Str::uuid()->toString(),
                        'id_daftar_hadir' => $id,
                        'id_karyawan' => $value,
                        'id_jabatan' => $get->id_jabatan,
                        'tanggal' => $tgl,
                        'status' => 'A',
                        'created_by' => Session::get('userid'),
                        'created_date' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function delete($id)
    {
       $delhadir = DaftarHadir::where('id',$id)->update(['status' => 'D']);
       $deldetail = DaftarHadirDetail::where('id_daftar_hadir',$id)->update(['status' => 'D']);
       return ;
    }

    public function hadirPdf($uid) {
        $show = DaftarHadir::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['detail'] = DaftarHadirDetail::where('id_daftar_hadir', $show->id)->where('status', 'A')->get();
        $pdf = Pdf::loadView('hadir.pdf', $data)
                ->setPaper('a3', 'portrait');
        if($show->kode=='el0306'){
            $judul = $show->kode.' '.$show->get_kapal()->nama.'.pdf';
        }
        return $pdf->download($judul);
    }

    public function KaryawanHadir(Request $request) {
        $get = DB::table('daftar_hadir_detail as a')
                ->leftjoin('karyawan as b', 'b.id', '=', 'a.id_karyawan')
                ->leftjoin('jabatan as c', 'c.id', '=', 'a.id_jabatan')
                ->select('a.*', 'b.nama as karyawan', 'b.nama as jabatan')
                ->where('a.status', 'A')
                ->where('a.id_daftar_hadir', $request->input('id'))
                ->get();
        return response()->json(['data' => $get]);
    }

     public function deletedetail($id)
    {
       $deldetail = DaftarHadirDetail::where('id',$id)->update(['status' => 'D']);
       return ;
    }
}