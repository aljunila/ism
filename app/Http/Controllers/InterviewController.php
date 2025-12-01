<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use App\Models\InterviewDetail;
use App\Models\Interview;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Jabatan;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class InterviewController extends Controller
{
    public function el0607()
    {
        $data['active'] = "el0607";
        $data['form'] = KodeForm::where('kode', 'el0607')->first();
        return view('interview.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');

        $daftar = DB::table('interview')
                ->leftjoin('perusahaan', 'perusahaan.id', '=', 'interview.id_perusahaan')
                ->leftJoin('jabatan', 'jabatan.id', '=', 'interview.id_jabatan')
                ->select('interview.*', 'perusahaan.nama as perusahaan', 'jabatan.nama as jabatan')
                ->where('interview.kode', $request->input('kode'))
                ->where('interview.status','A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('interview.id_perusahaan', $perusahaan);
                })
                ->orderBy('interview.id', 'DESC')
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
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } else {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        }
        $data['jabatan'] = Jabatan::where('kel', 1)->where('status','A')->get();
        $data['item'] = ChecklistItem::where('kode', $kode)->where('status', 'A')->where('parent_id',0)->get();
        $data['active'] = $kode;
        return view('interview.add',$data);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $periksa::

        $save = Interview::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'nama' => $request->input('nama'),
          'id_jabatan' => $request->input('id_jabatan'),
          'note' => $request->input('note'),
          'id_periksa' => Session::get('id_karyawan'),
          'tgl_periksa' => $request->input('tgl_periksa'),
          'id_menyetujui' => $request->input('id_menyetujui'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);

        $items = $request->input('item'); 
        $kets  = $request->input('ket');
        foreach ($items as $iditem => $value) {
        $keterangan = $kets[$iditem] ?? null;
           $item = InterviewDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $save->kode,
                'interview_id' => $save->id,
                'checklist_item_id' => $iditem,
                'value' => $value,
                'ket' => $keterangan,
                'status' => 'A',
                'created_by' => $created,
                'created_date' => $date
            ]);
        }
        if($item) {
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }

    public function edit($uid)
    {
        $show =  Interview::where('uid', $uid)->first();
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } else {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        }
        $data['jabatan'] = Jabatan::where('kel', 1)->where('status','A')->get();
        $data['item'] = InterviewDetail::where('interview_id', $show->id)->where('kode', $show->kode)->get();
        $data['show'] = $show;
        $data['active'] = $show->kode;
        return view('interview.edit',$data);
    }

    public function pdf($uid) {
        $show =  Interview::where('uid', $uid)->first();
        $nama = $show->nama;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['item'] = InterviewDetail::where('interview_id', $show->id)->where('kode', $show->kode)->get();
        $data['show'] = $show;
        $pdf = Pdf::loadView('interview.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function update(Request $request, $id)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $kode = $request->input('kode');
        $save = Interview::where('id',$id)->update([
          'id_perusahaan' => $request->input('id_perusahaan'),
          'nama' => $request->input('nama'),
          'id_jabatan' => $request->input('id_jabatan'),
          'note' => $request->input('note'),
          'id_periksa' => $request->input('id_periksa'),
          'tgl_periksa' => $request->input('tgl_periksa'),
          'id_menyetujui' => $request->input('id_menyetujui'),
          'changed_by' => Session::get('userid'),
        ]); 

        InterviewDetail::where('interview_id', $id)->where('kode', $kode)->delete();
        $items = $request->input('item'); 
        $kets  = $request->input('ket');
        foreach ($items as $iditem => $value) {
        $keterangan = $kets[$iditem] ?? null;
           $item = InterviewDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $kode,
                'interview_id' => $id,
                'checklist_item_id' => $iditem,
                'value' => $value,
                'ket' => $keterangan,
                'status' => 'A',
                'created_by' => $created,
                'created_date' => $date
            ]);
        }
        if($item) {
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }

    public function delete($id)
    {
       $post = Interview::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }
}