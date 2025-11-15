<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class ReviewController extends Controller
{
    public function el0512()
    {
        $data['active'] = "el0512";
        $data['form'] = KodeForm::where('kode', 'el0512')->first();
        return view('review.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

        $daftar = DB::table('review')
                ->leftjoin('kapal', 'kapal.id', '=', 'review.id_kapal')
                ->select('review.*', 'kapal.nama as kapal')
                ->where('review.kode', $request->input('kode'))
                ->where('review.status','A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('review.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('review.id_kapal', $kapal);
                })
                ->orderBy('review.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function add($kode)
    {
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $id_kapal)->get();
        } 
        $data['active'] = $kode;
        return view('review.add',$data);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $get_nahkoda = Karyawan::where('id_perusahaan', $request->input('id_perusahaan'))->where('id_kapal', $request->input('id_kapal'))->where('id_jabatan', 5)->first();
        $get_dpa = Karyawan::where('id_perusahaan', $request->input('id_perusahaan'))->where('id_jabatan', 4)->first();
        $save = Review::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'id_kapal' => $request->input('id_kapal'),
          'no_review' => $request->input('no_review'),
          'tgl_review' => $request->input('tgl_review'),
          'tgl_diterima' => $request->input('tgl_diterima'),
          'hasil' => $request->input('hasil'),
          'ket' => $request->input('ket'),
          'id_nahkoda' => $get_nahkoda->id,
          'id_dpa' => $get_dpa->id,
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

    public function edit($uid)
    {
        $show =  Review::where('uid', $uid)->first();
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $id_kapal)->get();
        }
        $data['active'] = $show->kode;
        $data['show'] = $show;
        return view('review.edit',$data);
    }

    public function pdf($uid) {
        $show =  Review::where('uid', $uid)->first();
        $nama = $show->no_review;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['show'] = $show;
        $pdf = Pdf::loadView('review.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function update(Request $request, $id)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $get_nahkoda = Karyawan::where('id_perusahaan', $request->input('id_perusahaan'))->where('id_kapal', $request->input('id_kapal'))->where('id_jabatan', 5)->first();
        $get_dpa = Karyawan::where('id_perusahaan', $request->input('id_perusahaan'))->where('id_jabatan', 4)->first();

        $save = Review::where('id',$id)->update([
          'id_perusahaan' => $request->input('id_perusahaan'),
          'id_kapal' => $request->input('id_kapal'),
          'no_review' => $request->input('no_review'),
          'tgl_review' => $request->input('tgl_review'),
          'tgl_diterima' => $request->input('tgl_diterima'),
          'hasil' => $request->input('hasil'),
          'ket' => $request->input('ket'),
          'id_nahkoda' => $get_nahkoda->id,
          'id_dpa' => $get_dpa->id,
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
       $post = Review::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

    public function get(Request $request)
    {
        $id = $request->id;
        $get = Review::findOrFail($id);
        return response()->json($get);
    }

    public function updatedpa(Request $request, $id)
    {
        $date = date('Y-m-d H:i:s');

        $save = Review::where('id',$id)->update([
          'tgl_diterima' => $request->input('tgl_diterima'),
          'ket' => $request->input('ket'),
          'changed_by' => Session::get('userid'),
        ]); 
        if($save) {
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }
}