<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use App\Models\ChecklistData;
use App\Models\ChecklistDataDetail;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Kapal;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
use DB;

class ChecklistController extends Controller
{
    public function getData(Request $request)
    {
        $daftar = DB::table('checklist_data')
                ->leftjoin('karyawan', 'karyawan.id', '=', 'checklist_data.id_karyawan')
                ->leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.id_jabatan')
                ->leftjoin('kapal', 'kapal.id', '=', 'checklist_data.id_kapal')
                ->select('checklist_data.*', 'karyawan.nama as nama', 'jabatan.nama as jabatan', 'kapal.nama as kapal')
                ->where('checklist_data.kode', $request->input('kode'))
                ->where('checklist_data.status','A')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function add($kode)
    {
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $data['kapal'] = Kapal::where('status','A')->get();
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
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $data['kapal'] = Kapal::where('status','A')->get();
        $data['item'] = ChecklistDataDetail::where('checklist_data_id', $show->id)->get();
        $data['active'] = $show->kode;
        $data['show'] = $show;
        return view('checklist.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = ChecklistData::where('id',$id)->update([
          'id_karyawan' => $request->input('id_karyawan'),
          'date' => $request->input('date'),
          'id_kapal' => $request->input('id_kapal'),
          'id_mengetahui' => $request->input('id_mengetahui'),
          'id_mentor' => $request->input('id_mentor'),
          'changed_by' => Session::get('userid'),
        ]);  
        
        ChecklistDataDetail::where('checklist_data_id', $id)->delete();
        $checked = $request->input('item', []);
        foreach ($checked as $iditem => $value) {
           $item = ChecklistDataDetail::insert([
                'uid' => Str::uuid()->toString(),
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

        return $pdf->download($data['form']->ket.' '.$nama.'.pdf');
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
}