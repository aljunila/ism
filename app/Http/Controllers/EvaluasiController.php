<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Evaluasi;
use App\Models\ChecklistData;
use App\Models\ChecklistItem;
use App\Models\ChecklistEvaluasiDetail;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class EvaluasiController extends Controller
{
    public function el0604()
    {
        $data['active'] = "el0604";
        $data['form'] = KodeForm::where('kode', 'el0604')->first();
        return view('evaluasi.show', $data);
    }

    public function el0605()
    {
        $data['active'] = "el0605";
        $data['form'] = KodeForm::where('kode', 'el0605')->first();
        return view('evaluasi.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
       
        $kodeInput = $request->input('kode');
        $kode = match($kodeInput) {
            'el0604' => 'el0303',
            'el0605' => 'el0304',
            default  => $kodeInput,
        };
        
        $daftar = DB::table('checklist_data')
                ->leftjoin('karyawan', 'karyawan.id', '=', 'checklist_data.id_karyawan')
                ->leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.id_jabatan')
                ->leftjoin('kapal', 'kapal.id', '=', 'checklist_data.id_kapal')
                ->leftjoin('evaluasi', 'evaluasi.checklist_data_id', '=', 'checklist_data.id')
                ->select('checklist_data.*', 'karyawan.nama as nama', 'jabatan.nama as jabatan', 'kapal.nama as kapal', 'evaluasi.uid as evaluasi')
                ->where('checklist_data.kode', $kode)
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

    public function edit($uid)
    {
        $show =  DB::table('checklist_data as a')
                ->leftJoin('evaluasi as b', 'a.id', 'b.checklist_data_id')
                ->leftJoin('karyawan as c', 'a.id_karyawan', 'c.id')
                ->leftJoin('kapal as d', 'a.id_kapal', 'd.id')
                ->select('b.*', 'a.id as checklist_id', 'c.nama as karyawan', 'a.date', 'a.kode', 'd.nama as kapal')
                ->where('a.uid', $uid)
                ->first();
        $kodeInput = $show->kode;
        $kode = match($kodeInput) {
            'el0303' => 'el0604',
            'el0304' => 'el0605',
            default  => $kodeInput,
        };
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
        if($show->id) {
            $item = DB::table('checklist_evaluasi_detail as a')
                       ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                       ->select('a.*', 'b.item')
                       ->where('a.evaluasi_id', $show->id)->where('a.kode', $kode)->where('b.parent_id', 0)->get();
            foreach ($item as $ck) {
                $get[$ck->checklist_item_id] = DB::table('checklist_evaluasi_detail as a')
                        ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                        ->select('a.*', 'b.item')
                        ->where('a.evaluasi_id', $show->id)->where('a.kode', $kode)->where('b.parent_id', $ck->checklist_item_id)->get();
            }
        } else {
             $item = ChecklistItem::where('kode', $kode)->where('status', 'A')->where('parent_id',0)->get();
            foreach ($item as $ck) {
                $get[$ck->id] = ChecklistItem::where('kode', $kode)->where('status', 'A')->where('parent_id', $ck->id)->get();
            }
        }
        
        $data['item'] = $item;
        $data['child'] = $get;
        $data['active'] = $show->kode;
        $data['show'] = $show;
        return view('evaluasi.edit',$data);
    }

    public function pdf($uid) {
        $show =  Evaluasi::where('uid', $uid)->first();  
        $master = ChecklistData::where('id', $show->checklist_data_id)->first();
        $kodeInput = $master->kode;
        $kode = match($kodeInput) {
            'el0303' => 'el0604',
            'el0304' => 'el0605',
            default  => $kodeInput,
        };
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $nama = $master->get_karyawan()->nama;
        $item = DB::table('checklist_evaluasi_detail as a')
                       ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                       ->select('a.*', 'b.item')
                       ->where('a.evaluasi_id', $show->id)->where('a.kode', $kode)->where('b.parent_id', 0)->get();
            foreach ($item as $ck) {
                $get[$ck->checklist_item_id] = DB::table('checklist_evaluasi_detail as a')
                        ->leftJoin('checklist_item as b', 'b.id', 'a.checklist_item_id')
                        ->select('a.*', 'b.item')
                        ->where('a.evaluasi_id', $show->id)->where('a.kode', $kode)->where('b.parent_id', $ck->checklist_item_id)->get();
            }
        $data['item'] = $item;
        $data['child'] = $get;
        $data['show'] = $show;
        $data['master'] = $master;
        $pdf = Pdf::loadView('evaluasi.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function update(Request $request, $id)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $kode = $request->input('kode');
        $data = ChecklistData::findOrFail($id);
        $get = Evaluasi::where('checklist_data_id', $id)->first();
        $get_nahkoda = Karyawan::where('id_kapal', $data->id_kapal)->where('id_jabatan', 5)->first();
        $get_cabang = Karyawan::where('id_perusahaan', $data->id_perusahaan)->where('id_jabatan', 3)->first();
        if($request->input('id_instruktur')) {
            $instruktur = $request->input('id_instruktur');
        } else {
            $instruktur = Karyawan::where('id_kapal', $data->id_kapal)->where('id_jabatan', 16)->first();
        }
        if($get) {
            $save = Evaluasi::where('checklist_data_id',$id)->update([
            'checklist_data_id' => $id,
            'tanggal' => $request->input('tanggal'),
            'ket' => $request->input('ket'),
            'note' => $request->input('note'),
            'id_nahkoda' => $get_nahkoda->id,
            'id_instruktur' => $instruktur,
            'id_kepala' => $get_cabang->id,
            'changed_by' => Session::get('userid'),
            ]); 

            ChecklistEvaluasiDetail::where('evaluasi_id', $get->id)->where('kode', $kode)->delete();
            $checked = $request->input('item', []);
            foreach ($checked as $iditem => $value) {
            $item = ChecklistEvaluasiDetail::insert([
                    'uid' => Str::uuid()->toString(),
                    'kode' => $kode,
                    'evaluasi_id' => $get->id,
                    'checklist_item_id' => $iditem,
                    'value' => $value,
                    'status' => 'A',
                    'created_by' => Session::get('userid')
                ]);
            }
        } else {
             $save = Evaluasi::create([
            'uid' => Str::uuid()->toString(),
            'checklist_data_id' => $id,
            'tanggal' => $request->input('tanggal'),
            'ket' => $request->input('ket'),
            'note' => $request->input('note'),
            'id_nahkoda' => $get_nahkoda->id,
            'id_instruktur' => $get_kkm->id,
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d'),
            'status' => 'A'
            ]); 

            $checked = $request->input('item', []);
            foreach ($checked as $iditem => $value) {
            $item = ChecklistEvaluasiDetail::insert([
                    'uid' => Str::uuid()->toString(),
                    'kode' => $kode,
                    'evaluasi_id' => $save->id,
                    'checklist_item_id' => $iditem,
                    'value' => $value,
                    'status' => 'A',
                    'created_by' => Session::get('userid')
                ]);
            }
        }
            return response()->json(['success' => true]);
    }
}