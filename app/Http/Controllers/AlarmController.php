<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use App\Models\ChecklistData;
use App\Models\ChecklistAlarmDetail;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class AlarmController extends Controller
{
    public function el0506()
    {
        $data['active'] = "el0506";
        $data['form'] = KodeForm::where('kode', 'el0506')->first();
        return view('alarm.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

        $daftar = DB::table('checklist_data')
                ->leftjoin('kapal', 'kapal.id', '=', 'checklist_data.id_kapal')
                ->select('checklist_data.*', 'kapal.nama as kapal')
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
        $data['item'] = ChecklistItem::where('kode', $kode)->where('status', 'A')->get();
        $data['active'] = $kode;
        return view('alarm.add',$data);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $save = ChecklistData::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'date' => $request->input('date'),
          'ket' => $request->input('keterangan'),
          'id_kapal' => $request->input('id_kapal'),
          'id_mengetahui' => $request->input('id_mengetahui'),
          'id_mentor' => $request->input('id_mentor'),
          'id_karyawan' => $request->input('id_karyawan'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);

        $periodes  = $request->input('periode');
        $uji_trk  = $request->input('uji_terakhir');
        $uji_yad  = $request->input('uji_yad');
        $kets  = $request->input('ket');
        foreach ($periodes as $iditem => $per) {
        $trk = $uji_trk[$iditem] ?? null;
        $yad = $uji_yad[$iditem] ?? null;
        $keterangan = $kets[$iditem] ?? null;
           $item = ChecklistAlarmDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $save->kode,
                'checklist_data_id' => $save->id,
                'checklist_item_id' => $iditem,
                'periode' => $per,
                'uji_terakhir' => $trk,
                'uji_yad' => $yad,
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
        $data['item'] = ChecklistAlarmDetail::where('checklist_data_id', $show->id)->where('kode', $show->kode)->get();
        $data['active'] = $show->kode;
        $data['show'] = $show;
        return view('alarm.edit',$data);
    }

    public function pdf($uid) {
        $show =  ChecklistData::where('uid', $uid)->first();
        $nama = $show->get_kapal()->nama;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['item'] = ChecklistAlarmDetail::where('checklist_data_id', $show->id)->get();
        $data['show'] = $show;
        $pdf = Pdf::loadView('alarm.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function update(Request $request, $id)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $kode = $request->input('kode');
        $save = ChecklistData::where('id',$id)->update([
          'id_perusahaan' => $request->input('id_perusahaan'),
          'date' => $request->input('date'),
          'ket' => $request->input('keterangan'),
          'id_kapal' => $request->input('id_kapal'),
          'id_mengetahui' => $request->input('id_mengetahui'),
          'id_mentor' => $request->input('id_mentor'),
          'id_karyawan' => $request->input('id_karyawan'),
          'changed_by' => Session::get('userid'),
        ]); 

        ChecklistAlarmDetail::where('checklist_data_id', $id)->where('kode', $kode)->delete();
        $periodes  = $request->input('periode');
        $uji_trk  = $request->input('uji_terakhir');
        $uji_yad  = $request->input('uji_yad');
        $kets  = $request->input('ket');
        foreach ($periodes as $iditem => $per) {
        $trk = $uji_trk[$iditem] ?? null;
        $yad = $uji_yad[$iditem] ?? null;
        $keterangan = $kets[$iditem] ?? null;
           $item = ChecklistAlarmDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $kode,
                'checklist_data_id' => $id,
                'checklist_item_id' => $iditem,
                'periode' => $per,
                'uji_terakhir' => $trk,
                'uji_yad' => $yad,
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
       $post = ChecklistData::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }
}