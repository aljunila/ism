<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
use DB;

class KapalController extends Controller
{
    public function show()
    {
        $data['daftar'] = Kapal::where('status', 'A')->get();
        $data['active'] = "kapal";
        return view('kapal.show', $data);
    }

    public function getData() {
        $get = DB::table('kapal')
                ->leftjoin('perusahaan', 'perusahaan.id', '=', 'kapal.pemilik')
                ->select('kapal.*', 'perusahaan.nama as perusahaan')
                ->where('kapal.status', 'A')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add() 
    {
        $data['active'] = "kapal";
        $data['perusahaan'] = Perusahaan::get();
          return view('kapal.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Kapal::create([
          'uid' => Str::uuid()->toString(),
          'nama' => $request->input('nama'),
          'pendaftaran' => $request->input('pendaftaran'),
          'no_siup' => $request->input('no_siup'),
          'no_akte' => $request->input('no_akte'),
          'dikeluarkan_di' => $request->input('dikeluarkan_di'),
          'selar' => $request->input('selar'),
          'pemilik' => $request->input('pemilik'),
          'call_sign' => $request->input('call_sign'),
          'galangan' => $request->input('galangan'),
          'konstruksi' => $request->input('konstruksi'),
          'type' => $request->input('type'),
          'loa' => $request->input('loa'),
          'lbp' => $request->input('lbp'),
          'lebar' => $request->input('lebar'),
          'dalam' => $request->input('dalam'),
          'summer_draft' => $request->input('summer_draft'),
          'winter_draft' => $request->input('winter_draft'),
          'draft_air_tawar' => $request->input('draft_air_tawar'),
          'tropical_draft' => $request->input('tropical_draft'),
          'isi_kotor' => $request->input('isi_kotor'),
          'bobot_mati' => $request->input('bobot_mati'),
          'nt' => $request->input('nt'),
          'merk_mesin_induk' => $request->input('merk_mesin_induk'),
          'tahun_mesin_induk' => $request->input('tahun_mesin_induk'),
          'no_mesin_induk' => $request->input('no_mesin_induk'),
          'merk_mesin_bantu' => $request->input('merk_mesin_bantu'),
          'tahun_mesin_bantu' => $request->input('tahun_mesin_bantu'),
          'no_mesin_bantu' => $request->input('no_mesin_bantu'),
          'max_speed' => $request->input('max_speed'),
          'normal_speed' => $request->input('normal_speed'),
          'min_speed' => $request->input('min_speed'),
          'bahan_bakar' => $request->input('bahan_bakar'),
          'jml_butuh' => $request->input('jml_butuh'),
          'status' => 'A',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s'),
          ]);
        return redirect()->route('kapal')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($uid)
    {
        $show = Kapal::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "Kapal";
        $data['perusahaan'] = Perusahaan::get();
        return view('kapal.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Kapal::find($id)->update($request->all());     
      return redirect('/kapal')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($uid)
    {
       $post = Kapal::where('uid',$uid)->update(['status' => 'D']);
       return redirect('/kapal')->with('danger', 'Data berhasil dihapus');
    }

    public function profil($uid)
    {
        $show = Kapal::where('uid',$uid)->first();
        $data['show'] = $show;
        $data['active'] = "kapal";
        return view('kapal.profile',$data);
    }
}