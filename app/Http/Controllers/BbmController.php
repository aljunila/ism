<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use App\Models\ChecklistDataDetail;
use App\Models\Bbm;
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

class BbmController extends Controller
{
    public function el0504()
    {
        $data['active'] = "el0504";
        $data['form'] = KodeForm::where('kode', 'el0504')->first();
        return view('bbm.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
        $ctx = RoleContext::get();

        $daftar = DB::table('bbm')
                ->leftjoin('kapal', 'kapal.id', '=', 'bbm.id_kapal')
                ->select('bbm.*', 'kapal.nama as kapal')
                ->where('bbm.kode', $request->input('kode'))
                ->where('bbm.status','A')
                ->when($perusahaan, fn($query, $perusahaan) => $query->where('bbm.id_perusahaan', $perusahaan))
                ->when($kapal, fn($query, $kapal) => $query->where('bbm.id_kapal', $kapal))
                ->when($ctx['jenis']==2 && $ctx['perusahaan_id'], fn($q) => $q->where('bbm.id_perusahaan', $ctx['perusahaan_id']))
                ->when($ctx['jenis']==3 && $ctx['kapal_id'], fn($q) => $q->where('bbm.id_kapal', $ctx['kapal_id']))
                ->orderBy('bbm.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function add($kode)
    {
        $data['form'] = KodeForm::where('kode', $kode)->first();
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
        $item = ChecklistItem::where('kode', $kode)->where('status', 'A')->where('parent_id',0)->get();
        foreach ($item as $ck) {
            $get[$ck->id] = ChecklistItem::where('kode', $kode)->where('status', 'A')->where('parent_id', $ck->id)->get();
        }
        $data['item'] = $item;
        $data['child'] = $get;
        $data['active'] = $kode;
        return view('bbm.add',$data);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $save = Bbm::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'tanggal' => $request->input('date'),
          'waktu' => $request->input('waktu'),
          'no_pelayaran' => $request->input('no_pelayaran'),
          'pelabuhan' => $request->input('pelabuhan'),
          'fo' => $request->input('fo'),
          'mdo' => $request->input('mdo'),
          'ket' => $request->input('ket'),
          'id_kapal' => $request->input('id_kapal'),
          'id_nahkoda' => $request->input('id_nahkoda'),
          'id_kkm' => $request->input('id_kkm'),
          'id_jaga' => $request->input('id_jaga'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);

        $items = $request->input('item'); 
        $kets  = $request->input('ket');
        foreach ($items as $iditem => $value) {
        $keterangan = $kets[$iditem] ?? null;
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $save->kode,
                'bbm_id' => $save->id,
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
        $show =  Bbm::where('uid', $uid)->first();
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
        $item = DB::table('checklist_data_detail as a')
                       ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                       ->select('a.*', 'b.item')
                       ->where('a.bbm_id', $show->id)->where('a.kode', $show->kode)->where('b.parent_id', 0)->get();
        foreach ($item as $ck) {
            $get[$ck->checklist_item_id] = DB::table('checklist_data_detail as a')
                       ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                       ->select('a.*', 'b.item')
                       ->where('a.bbm_id', $show->id)->where('a.kode', $show->kode)->where('b.parent_id', $ck->checklist_item_id)->get();
        }
        $data['item'] = $item;
        $data['child'] = $get;
        $data['active'] = $show->kode;
        $data['show'] = $show;
        return view('bbm.edit',$data);
    }

    public function pdf($uid) {
        $show =  Bbm::where('uid', $uid)->first();
        $nama = $show->get_kapal()->nama;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $item = DB::table('checklist_data_detail as a')
                       ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                       ->select('a.*', 'b.item')
                       ->where('a.bbm_id', $show->id)->where('a.kode', $show->kode)->where('b.parent_id', 0)->get();
        foreach ($item as $ck) {
            $get[$ck->checklist_item_id] = DB::table('checklist_data_detail as a')
                       ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                       ->select('a.*', 'b.item')
                       ->where('a.bbm_id', $show->id)->where('a.kode', $show->kode)->where('b.parent_id', $ck->checklist_item_id)->get();
        }
        $data['item'] = $item;
        $data['child'] = $get;
        $data['show'] = $show;
        $pdf = Pdf::loadView('bbm.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function update(Request $request, $id)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $kode = $request->input('kode');
        $save = Bbm::where('id',$id)->update([
          'id_perusahaan' => $request->input('id_perusahaan'),
          'tanggal' => $request->input('tanggal'),
          'waktu' => $request->input('waktu'),
          'no_pelayaran' => $request->input('no_pelayaran'),
          'pelabuhan' => $request->input('pelabuhan'),
          'fo' => $request->input('fo'),
          'mdo' => $request->input('mdo'),
          'ket' => $request->input('ket'),
          'id_kapal' => $request->input('id_kapal'),
          'id_nahkoda' => $request->input('id_nahkoda'),
          'id_kkm' => $request->input('id_kkm'),
          'id_jaga' => $request->input('id_jaga'),
          'changed_by' => Session::get('userid'),
        ]); 

        ChecklistDataDetail::where('bbm_id', $id)->where('kode', $kode)->delete();
        $items = $request->input('item'); 
        $kets  = $request->input('ket');
        foreach ($items as $iditem => $value) {
        $keterangan = $kets[$iditem] ?? null;
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $kode,
                'bbm_id' => $id,
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
       $post = Bbm::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }
}
