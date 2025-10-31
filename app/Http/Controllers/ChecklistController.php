<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use App\Models\ChecklistData;
use App\Models\ChecklistGanti;
use App\Models\ChecklistDataDetail;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class ChecklistController extends Controller
{
    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

        $daftar = DB::table('checklist_data')
                ->leftjoin('karyawan', 'karyawan.id', '=', 'checklist_data.id_karyawan')
                ->leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.id_jabatan')
                ->leftjoin('kapal', 'kapal.id', '=', 'checklist_data.id_kapal')
                ->select('checklist_data.*', 'karyawan.nama as nama', 'jabatan.nama as jabatan', 'kapal.nama as kapal')
                ->where('checklist_data.kode', $request->input('kode'))
                ->where('checklist_data.status','A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('checklist_data.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('checklist_data.id_kapal', $kapal);
                })
                ->orderBy('checklist_data.id', 'DESC')
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
        $data['checklist'] = ChecklistItem::where('kode', $kode)->where('status', 'A')->get();
        $data['active'] = $kode;
        return view('checklist.add',$data);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $karyawan = Karyawan::where('id', $request->input('id_karyawan'))->first();

        $save = ChecklistData::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $karyawan->id_jabatan,
          'date' => $request->input('date'),
          'id_kapal' => $request->input('id_kapal'),
          'id_mengetahui' => $request->input('id_mengetahui'),
          'id_mentor' => $request->input('id_mentor'),
          'note' => $request->input('note'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);

        $checked = $request->input('item', []);
        foreach ($checked as $id => $value) {
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $save->kode,
                'checklist_data_id' => $save->id,
                'checklist_item_id' => $id,
                'value' => $value,
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
        $show =  ChecklistData::where('uid', $uid)->first();
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
        $data['item'] = ChecklistDataDetail::where('checklist_data_id', $show->id)->where('kode', $show->kode)->get();
        $data['active'] = $show->kode;
        $data['show'] = $show;
        return view('checklist.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $kode = $request->input('kode');
        $post = ChecklistData::where('id',$id)->update([
          'id_karyawan' => $request->input('id_karyawan'),
          'date' => $request->input('date'),
          'id_kapal' => $request->input('id_kapal'),
          'id_mengetahui' => $request->input('id_mengetahui'),
          'id_mentor' => $request->input('id_mentor'),
          'changed_by' => Session::get('userid'),
        ]);  
        
        ChecklistDataDetail::where('checklist_data_id', $id)->where('kode', $kode)->delete();
        $checked = $request->input('item', []);
        foreach ($checked as $iditem => $value) {
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $kode,
                'checklist_data_id' => $id,
                'checklist_item_id' => $iditem,
                'value' => $value,
                'status' => 'A',
                'created_by' => Session::get('userid')
            ]);
        }
      return response()->json(['success' => true]);
    }

    public function delete($id)
    {
       $post = ChecklistData::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

    public function el0302()
    {
        $data['active'] = "el0302";
        $data['form'] = KodeForm::where('kode', 'el0302')->first();
        return view('checklist.show', $data);
    }

    public function el0303()
    {
        $data['active'] = "el0303";
        $data['form'] = KodeForm::where('kode', 'el0303')->first();
        return view('checklist.show', $data);
    }

    public function el0304()
    {
        $data['active'] = "el0304";
        $data['form'] = KodeForm::where('kode', 'el0304')->first();
        return view('checklist.show', $data);
    }

    
    public function el0305()
    {
        $data['active'] = "el0305";
        $data['form'] = KodeForm::where('kode', 'el0305')->first();
        return view('checklist.show', $data);
    }

    public function pdf($uid) {
        $show =  ChecklistData::where('uid', $uid)->first();
        $nama = $show->get_karyawan()->nama;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['item'] = ChecklistDataDetail::where('checklist_data_id', $show->id)->get();
        $data['show'] = $show;
        $pdf = Pdf::loadView('checklist.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function item($kode)
    {
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['item'] = ChecklistItem::where('status','A')->where('kode', $kode)->get();
        $data['active'] = $kode;
        return view('checklist.item',$data);
    }

    public function getItem(Request $request)
    {
        $daftar = ChecklistItem::where('kode', $request->input('kode'))
                ->where('status','A')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function saveform(Request $request)
    {
        $intruksi = $request->input('intruksi');
        $kode = $request->input('kode');

        
        $post = KodeForm::where('kode',$kode)->update([
            'intruksi' => $request->input('intruksi'),
            ]);     
    }

    public function storeitem(Request $request)
    {   
        $post = ChecklistItem::create([
            'uid' => Str::uuid()->toString(),
            'kode' => $request->input('kode'),
            'item' => $request->input('item'),
            'date' => $request->input('date'),
            'status' => 'A',
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d')
            ]);     
    }

     public function edititem(Request $request)
    {
        $id = $request->id;
        $data = ChecklistItem::findOrFail($id);
        return response()->json($data);
    }

    public function updateitem(Request $request, $id)
    {
      $post = ChecklistItem::where('id',$id)->update([
          'item' => $request->input('item'),
        ]);     
      return response()->json(['success' => true]);
    }

    public function deleteitem($id)
    {
       $post = ChecklistItem::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

    public function el0307()
    {
        $data['active'] = "el0307";
        $data['form'] = KodeForm::where('kode', 'el0307')->first();
        return view('checklist.ganti', $data);
    }

    public function el0311()
    {
        $data['active'] = "el0311";
        $data['form'] = KodeForm::where('kode', 'el0311')->first();
        return view('checklist.ganti', $data);
    }

    public function el0312()
    {
        $data['active'] = "el0312";
        $data['form'] = KodeForm::where('kode', 'el0312')->first();
        return view('checklist.ganti', $data);
    }

    public function getGanti(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

        $daftar = DB::table('checklist_penggantian as a')
                ->leftjoin('karyawan as b', 'b.id', '=', 'a.id_dari')
                ->leftjoin('karyawan as c', 'c.id', '=', 'a.id_kepada')
                ->leftjoin('kapal as d', 'd.id', '=', 'a.id_kapal')
                ->select('a.*', 'b.nama as dari', 'c.nama as kepada', 'd.nama as kapal')
                ->where('a.kode', $request->input('kode'))
                ->where('a.status','A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('a.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('a.id_kapal', $kapal);
                })
                ->orderBy('a.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function addganti($kode)
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
        $data['checklist'] = ChecklistItem::where('kode', $kode)->where('status', 'A')->get();
        $data['active'] = $kode;
        return view('checklist.addganti',$data);
    }

    public function storeganti(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $save = ChecklistGanti::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'id_dari' => $request->input('id_dari'),
          'id_kepada' => $request->input('id_kepada'),
          'date' => $request->input('date'),
          'jam' => $request->input('jam'),
          'id_kapal' => $request->input('id_kapal'),
          'pelabuhan' => $request->input('pelabuhan'),
          'note' => $request->input('note'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);

        $items = $request->input('item'); 
        $kets  = $request->input('ket');
        foreach ($items as $id => $value) {
        $keterangan = $kets[$id] ?? null;
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $save->kode,
                'checklist_data_id' => $save->id,
                'checklist_item_id' => $id,
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

    public function editganti($uid)
    {
        $show =  ChecklistGanti::where('uid', $uid)->first();
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
        $data['item'] = ChecklistDataDetail::where('checklist_data_id', $show->id)->where('kode', $show->kode)->get();
        $data['active'] = $show->kode;
        $data['show'] = $show;
        return view('checklist.editganti',$data);
    }

    public function updateganti(Request $request, $id)
    {
        $kode = $request->input('kode');
      $post = ChecklistGanti::where('id',$id)->update([
          'id_perusahaan' => $request->input('id_perusahaan'),
          'id_dari' => $request->input('id_dari'),
          'id_kepada' => $request->input('id_kepada'),
          'date' => $request->input('date'),
          'jam' => $request->input('jam'),
          'id_kapal' => $request->input('id_kapal'),
          'pelabuhan' => $request->input('pelabuhan'),
          'note' => $request->input('note'),
          'changed_by' => Session::get('userid'),
        ]);  
        
        ChecklistDataDetail::where('checklist_data_id', $id)->where('kode', $kode)->delete();
        $items = $request->input('item'); 
        $kets  = $request->input('ket');
        foreach ($items as $iditem => $value) {
        $keterangan = $kets[$iditem] ?? null;
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $kode,
                'checklist_data_id' => $id,
                'checklist_item_id' => $iditem,
                'value' => $value,
                'ket' => $keterangan,
                'status' => 'A',
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d')
            ]);
        }
      return response()->json(['success' => true]);
    }

    public function deleteganti($id)
    {
       $post = ChecklistGanti::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

     public function gantipdf($uid) {
        $show =  ChecklistGanti::where('uid', $uid)->first();
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['item'] = ChecklistDataDetail::where('checklist_data_id', $show->id)->where('kode', $show->kode)->get();
        $data['show'] = $show;
        $pdf = Pdf::loadView('checklist.gantipdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$show->date.'.pdf');
    }

    public function getKaryawan($id_kapal)
    {
        $get = Kapal::findOrFail($id_kapal);
        $karyawan = DB::table('karyawan as a')
                    ->select('a.id', 'a.nama')
                    ->where('a.id_perusahaan', $get->pemilik)
                    ->where(function($q) use ($id_kapal) {
                        $q->where('a.id_kapal', $id_kapal)
                        ->orWhereNull('a.id_kapal');
                    })
                    ->where('a.status','A')->where('a.resign', 'N')
                    ->get();
        return response()->json($karyawan);
    }

    public function el0308()
    {
        $data['active'] = "el0308";
        $data['form'] = KodeForm::where('kode', 'el0308')->first();
        return view('checklist.nahkoda', $data);
    }

    public function el0309()
    {
        $data['active'] = "el0309";
        $data['form'] = KodeForm::where('kode', 'el0309')->first();
        $data['item'] = DB::table('checklist_item as a')
                        ->leftjoin('checklist_data_detail as b', 'a.id', '=', 'b.checklist_item_id', 'left')
                        ->select('a.*', 'b.value', 'b.ket')
                        ->where('a.kode', 'el0309')->where('a.status', 'A')->get();
        return view('checklist.nahkoda', $data);
    }

    public function getChecklist(Request $request) {
        $id   = $request->input('id');  
        $kode = $request->input('kode');
        $data = DB::table('checklist_item as a')
                    ->leftJoin('checklist_data_detail as b', function($join) use ($id) {
                        $join->on('a.id', '=', 'b.checklist_item_id')
                            ->on('a.kode', '=', 'b.kode')
                            ->where('b.checklist_data_id', '=', $id);
                    })
                    ->select('a.*', 'b.value', 'b.ket', 'b.checklist_data_id')
                    ->where('a.kode', $kode)
                    ->where('a.status', 'A')
                    ->orderBy('a.id', 'ASC')
                    ->get();
        return response()->json(['data' => $data]);
    }

    function save(Request $request) {
        $id = $request->input('id');
        $kode = $request->input('kode');
        $items = $request->input('item'); 
        $kets  = $request->input('ket');

        ChecklistDataDetail::where('checklist_data_id', $id)->where('kode', $kode)->delete();
        foreach ($items as $iditem => $value) {
        $keterangan = $kets[$iditem] ?? null;
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $kode,
                'checklist_data_id' => $id,
                'checklist_item_id' => $iditem,
                'value' => $value,
                'ket' => $keterangan,
                'status' => 'A',
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d')
            ]);
        }
    }

    public function nahkodapdf($uid, $kode) {
        $show =  ChecklistGanti::where('uid', $uid)->first();
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['item'] = ChecklistDataDetail::where('checklist_data_id', $show->id)->where('kode', $kode)->get();
        $data['show'] = $show;
        $pdf = Pdf::loadView('checklist.nahkodapdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$show->date.'.pdf');
    }
}