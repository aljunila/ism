<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\PeriodeKondite;
use App\Models\Kondite;
use App\Models\KonditeDetail;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Karyawan;
use App\Models\Jabatan;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
use DB;

class KonditeController extends Controller
{
    public function el0608()
    {
        $data['active'] = "el0608";
        $data['form'] = KodeForm::where('kode', 'el0608')->first();
        $data['perusahaan'] = Perusahaan::where('status', 'A')->get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('kondite.show', $data);
    }

    public function getData()
    {
        $daftar = DB::table('periode_kondite as a')
                ->leftJoin('perusahaan as b', 'a.id_perusahaan', 'b.id')
                ->leftJoin('kapal as c', 'a.id_kapal', 'c.id')
                ->select('a.id', 'a.uid', 'a.id_kapal', 'bulan', 'tahun', 'c.nama as kapal')
                ->where('a.status','A')
                ->orderBy('a.id', 'DESC')->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $cek = PeriodeKondite::where('id_kapal', $request->input('id_kapal'))->where('bulan', $request->input('bulan'))->where('tahun', $request->input('tahun'))->where('status', 'A')->exists();
        if($cek) {    
            return response()->json(['error' => true]);
        } else {
            $save = PeriodeKondite::create([
                'uid' => Str::uuid()->toString(),
                'kode' => $request->input('kode'),
                'id_perusahaan' => $request->input('id_perusahaan'),
                'id_kapal' => $request->input('id_kapal'),
                'bulan' => $request->input('bulan'),
                'tahun' => $request->input('tahun'),
                'status' => 'A',
                'created_by' => $created,
                'created_date' => $date
            ]);

            $karyawan = DB::table('karyawan')->where('id_kapal', $request->input('id_kapal'))->where('status', 'A')->where('resign','N')->get();
            foreach($karyawan as $data) {
                $insert = Kondite::create([
                    'uid' => Str::uuid()->toString(),
                    'id_periode' => $save->id,
                    'id_karyawan' => $data->id,
                    'id_jabatan' => $data->id_jabatan,
                    'status' => 'A',
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d'),
                ]);
            }
            if($save){
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => false]);
            }
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $kondite = PeriodeKondite::findOrFail($id);
        return response()->json($kondite);
    }

    public function update(Request $request, $id)
    {
       $cek = PeriodeKondite::where('id_kapal', $request->input('id_kapal'))->where('bulan', $request->input('bulan'))
            ->where('tahun', $request->input('tahun'))->where('status', 'A')->where('id', '!=', $id)->exists();
        if($cek) {    
           return response()->json(['error' => true]);
        } else {
            $save = PeriodeKondite::where('id',$id)->update([
                'bulan' => $request->input('bulan'),
                'tahun' => $request->input('tahun'),
                'changed_by' => Session::get('userid'),
            ]);
            return response()->json(['success' => true]);
        }
    }

    public function delete($id)
    {
        $post = PeriodeKondite::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

    public function detail($uid)
    {
        $periode = PeriodeKondite::where('uid', $uid)->first();
        $data['active'] = "el0608";
        $data['form'] = KodeForm::where('kode', 'el0608')->first();
        $data['karyawan'] = Kondite::where('status', 'A')->where('id_periode', $periode->id)->get();
        $data['penilai'] = Karyawan::where('id_perusahaan', $periode->id_perusahaan)->where(function ($q) use ($periode) 
                        { $q->where('id_kapal', $periode->id_kapal)->orWhere('id_kapal', 0);})->get();
        $data['periode'] = $periode;
        return view('kondite.detail', $data);
    }

    public function getDetail(Request $request)
    {
        $daftar = DB::table('kondite as a')
                ->leftJoin('karyawan as b', 'a.id_karyawan', 'b.id')
                ->leftJoin('jabatan as c', 'a.id_jabatan', 'c.id')
                ->select('a.*', 'b.nama as karyawan', 'c.nama as jabatan')
                ->where('a.status','A')->where('id_periode', $request->input('id'))
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

     public function getChecklist(Request $request) {
        $id   = $request->input('id');  
        $kode = $request->input('kode');
        $data = DB::table('checklist_item as a')
                    ->leftJoin('checklist_kondite_detail as b', function($join) use ($id) {
                        $join->on('a.id', '=', 'b.checklist_item_id')
                            ->where('b.kondite_id', '=', $id);
                    })
                    ->select('a.*', 'b.value', 'b.ket', 'b.kondite_id')
                    ->where('a.kode', $kode)
                    ->where('a.status', 'A')
                    ->orderBy('a.id', 'ASC')
                    ->get();
        return response()->json(['data' => $data]);
    }

    public function getKondite(Request $request)
    {
        $id = $request->id;
        $kondite = Kondite::findOrFail($id);
        return response()->json($kondite);
    }

    public function saveform(Request $request)
    {
        $id = $request->input('id');
        $save = Kondite::where('id',$id)->update([
                'tgl_nilai' => date('Y-m-d'),
                'rekomendasi' => $request->input('rekomendasi'),
                'note' => $request->input('note'),
                'id_penilai_1' => $request->input('id_penilai_1'),
                'id_penilai_2' => $request->input('id_penilai_2'),
                'id_mengetahui' => $request->input('id_mengetahui'),
            ]);

        KonditeDetail::where('kondite_id', $id)->delete();

        $items = $request->input('item'); 
        $kets  = $request->input('ket');
        foreach ($items as $iditem => $value) {
        $keterangan = $kets[$iditem] ?? null;
           $item = KonditeDetail::insert([
                'uid' => Str::uuid()->toString(),
                'kode' => $request->input('kode'),
                'kondite_id' => $id,
                'checklist_item_id' => $iditem,
                'value' => $value,
                'status' => 'A',
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d')
            ]);
        }
    }

    public function pdf($uid) {
        $kode = "el0608";
        $show =  Kondite::where('uid', $uid)->first();
        $nama = $show->get_karyawan()->nama;
        $data['periode'] = PeriodeKondite::findorFail($show->id_periode);
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['item'] = KonditeDetail::where('kondite_id', $show->id)->get();
        $data['show'] = $show;
        $pdf = Pdf::loadView('kondite.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }
}
