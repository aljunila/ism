<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Pelatihan;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;
use App\Support\RoleContext;

class PelatihanController extends Controller
{
    public function el0602()
    {
        $data['active'] = "el0602";
        $data['form'] = KodeForm::where('kode', 'el0602')->first();
        $ctx = RoleContext::get();
        $id_perusahaan = $ctx['perusahaan_id'];
        if($ctx['is_superadmin']) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif($ctx['jenis']==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $ctx['kapal_id'])->get();
        }
        return view('refrensi.pelatihan', $data);
    }

    public function el0603()
    {
        $data['active'] = "el0603";
        $data['form'] = KodeForm::where('kode', 'el0603')->first();
        $ctx = RoleContext::get();
        $id_perusahaan = $ctx['perusahaan_id'];
        if($ctx['is_superadmin']) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif($ctx['jenis']==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $ctx['kapal_id'])->get();
        }
        return view('refrensi.pelatihan', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $ctx = RoleContext::get();

        $daftar = DB::table('pelatihan')
                ->leftjoin('perusahaan', 'perusahaan.id', '=', 'pelatihan.id_perusahaan')
                ->leftJoin('karyawan', 'karyawan.id', '=', 'pelatihan.id_karyawan')
                ->select('pelatihan.*', 'perusahaan.nama as perusahaan', 'karyawan.nama as karyawan')
                ->where('pelatihan.kode', $request->input('kode'))
                ->where('pelatihan.status','A')
                ->when($perusahaan, fn($query, $perusahaan) => $query->where('pelatihan.id_perusahaan', $perusahaan))
                ->when($ctx['jenis']==2 && $ctx['perusahaan_id'], fn($q) => $q->where('pelatihan.id_perusahaan', $ctx['perusahaan_id']))
                ->orderBy('pelatihan.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $save = Pelatihan::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('idp'),
          'id_karyawan' => $request->input('idk'),
          'nama' => $request->input('nama'),
          'tgl_mulai' => $request->input('tgl_mulai'),
          'tgl_selesai' => $request->input('tgl_selesai'),
          'tempat' => $request->input('tempat'),
          'hasil' => $request->input('hasil'),
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
        $data = Pelatihan::findOrFail($id);
        return response()->json($data);
    }

    public function pdf(Request $request) {
        $kode = $request->input('kode');
        $id_perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal');
        $start = $request->input('start');
        if($request->input('end')) {
            $end = $request->input('end');
        } else {
            $end = date("Y-m-d");
        }

        $perusahaan = Perusahaan::findOrFail($id_perusahaan);
        $show =  Pelatihan::where('id_perusahaan', $id_perusahaan)->where('kode', $kode)
                ->where('tgl_mulai', '>=', $start)->where('tgl_mulai', '<=', $end)->where('status','A')->get();
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['show'] = $show;
        $data['perusahaan'] = $perusahaan;
        $pdf = Pdf::loadView('refrensi.pdfpelatihan', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$perusahaan->kode.'.pdf');
    }

    public function update(Request $request, $id)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $kode = $request->input('kode');
        $save = Pelatihan::where('id',$id)->update([
          'id_perusahaan' => $request->input('idp'),
          'id_karyawan' => $request->input('idk'),
          'nama' => $request->input('nama'),
          'tgl_mulai' => $request->input('tgl_mulai'),
          'tgl_selesai' => $request->input('tgl_selesai'),
          'tempat' => $request->input('tempat'),
          'hasil' => $request->input('hasil'),
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
       $post = Pelatihan::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

    public function getKaryawan(Request $request)
    {
        $id_perusahaan = $request->input('id_perusahaan');
        $kel = $request->input('kode') == 'el0602' ? 2 : 1;

        $karyawan = DB::table('karyawan as a')
                    ->leftJoin('jabatan as b', 'b.id', 'a.id_jabatan')
                    ->select('a.id', 'a.nama')
                    ->where('a.id_perusahaan', $id_perusahaan)
                    ->where('a.status', 'A')
                    ->where('a.resign', 'N')
                    ->where('b.kel', $kel)
                    ->get();

        return response()->json($karyawan);
    }

}
