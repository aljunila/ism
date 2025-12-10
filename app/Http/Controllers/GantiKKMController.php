<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\GantiKKM;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\User;
use App\Models\KodeForm;
use App\Models\Kapal;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;
use App\Support\RoleContext;

class GantiKKMController extends Controller
{
    public function show()
    {
        $data['active'] = "el0310";
        $data['form'] = KodeForm::where('kode', 'el0310')->first();
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
        return view('kkm.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
        $ctx = RoleContext::get();
        
        $daftar = DB::table('ganti_kkm as a')
                ->leftjoin('karyawan as b', 'b.id', 'a.id_lama')
                ->leftjoin('karyawan as c', 'c.id', 'a.id_baru')
                ->select('a.*', 'b.nama as lama', 'c.nama as baru')
                ->where('a.status','A')
                ->when($perusahaan, fn($query, $perusahaan) => $query->where('a.id_perusahaan', $perusahaan))
                ->when($kapal, fn($query, $kapal) => $query->where('a.id_kapal', $kapal))
                ->when($ctx['jenis']==2 && $ctx['perusahaan_id'], fn($q) => $q->where('a.id_perusahaan', $ctx['perusahaan_id']))
                ->when($ctx['jenis']==3 && $ctx['kapal_id'], fn($q) => $q->where('a.id_kapal', $ctx['kapal_id']))
                ->orderBy('a.id', 'DESC')
                ->get();
        return response()->json([
            'data' => $daftar
        ]);
    }

    public function store(Request $request)
    {
        $id_kepada = $request->input('id_kepada');
        $id = $request->input('id');
        if($id) { 
            $save = GantiKKM::where('id', $id)->update([
                'id_kepada' => $id_kepada,
                'id_perusahaan' => $request->input('idp'),
                'id_kapal' => $request->input('idk'),
                'nomer' => $request->input('nomer'),
                'tanggal' => $request->input('tanggal'),
                'jam' => $request->input('jam'),
                'fo' => $request->input('fo'),
                'do' => $request->input('do'),
                'fw' => $request->input('fw'),
                'id_lama' => $request->input('id_lama'),
                'id_baru' => $request->input('id_baru'),
                'status' => 'A',
                'changed_by' => Session::get('userid'),
            ]);    
        } else {
            $save = GantiKKM::create([
                'uid' => Str::uuid()->toString(),
                'id_kepada' => $id_kepada,
                'id_perusahaan' => $request->input('idp'),
                'id_kapal' => $request->input('idk'),
                'nomer' => $request->input('nomer'),
                'tanggal' => $request->input('tanggal'),
                'jam' => $request->input('jam'),
                'fo' => $request->input('fo'),
                'do' => $request->input('do'),
                'fw' => $request->input('fw'),
                'id_lama' => $request->input('id_lama'),
                'id_baru' => $request->input('id_baru'),
                'status' => 'A',
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s')
            ]);
        }
        
        if($save) {
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $jabatan = GantiKKM::findOrFail($id);
        return response()->json($jabatan);
    }

    public function update(Request $request, $id)
    {
       $save = GantiKKM::where('id', $id)->update([
          'uid' => Str::uuid()->toString(),
          'id_kepada' => $id_kepada,
          'id_perusahaan' => $request->input('idp'),
          'id_kapal' => $request->input('idk'),
          'tanggal' => $request->input('tanggal'),
          'jam' => $request->input('jam'),
          'fo' => $request->input('fo'),
          'do' => $request->input('do'),
          'fw' => $request->input('fw'),
          'id_lama' => $request->input('id_lama'),
          'id_baru' => $request->input('id_baru'),
          'status' => 'A',
          'changed_by' => Session::get('userid'),
          ]);    
      return response()->json(['success' => true]);
    }

    public function delete($id)
    {
       $post = GantiKKM::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

    public function pdf($uid) {
        $show =  GantiKKM::where('uid', $uid)->first();
        $data['form'] = KodeForm::where('kode', 'el0310')->first();
        $data['show'] = $show;
        $pdf = Pdf::loadView('kkm.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream('Form '.$data['form']->ket.'.pdf');
    }
}
