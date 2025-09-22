<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GantiKKM;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\User;
use App\Models\KodeForm;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class GantiKKMController extends Controller
{
    public function show()
    {
        $data['active'] = "el0310";
        $data['form'] = KodeForm::where('kode', 'el0310')->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('kkm.show', $data);
    }

    public function getData()
    {
        $daftar = DB::table('ganti_kkm as a')
                ->leftjoin('karyawan as b', 'b.id', 'a.id_lama')
                ->leftjoin('karyawan as c', 'c.id', 'a.id_baru')
                ->select('a.*', 'b.nama as lama', 'c.nama as baru')
                ->where('status','A')->get();
        return response()->json([
            'data' => $daftar
        ]);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $id_kepada = $request->input('id_kepada');
        $perusahaan = User::where('id_karyawan', $id_kepada)->first();
        $save = GantiKKM::create([
          'uid' => Str::uuid()->toString(),
          'id_kepada' => $id_kepada,
          'id_perusahaan' => $perusahaan->id_perusahaan,
          'tanggal' => $request->input('tanggal'),
          'jam' => $request->input('jam'),
          'fo' => $request->input('fo'),
          'do' => $request->input('do'),
          'fw' => $request->input('fw'),
          'id_lama' => $request->input('id_lama'),
          'id_baru' => $request->input('id_baru'),
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
        $jabatan = GantiKKM::findOrFail($id);
        return response()->json($jabatan);
    }

    public function update(Request $request, $id)
    {
       $save = GantiKKM::where('id', $id)->update([
          'uid' => Str::uuid()->toString(),
          'id_kepada' => $id_kepada,
          'id_perusahaan' => $perusahaan->id_perusahaan,
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
}