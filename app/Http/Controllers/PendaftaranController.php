<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\Periode;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Sk;
use Alert;
use Session;
Use Carbon\Carbon;

class PendaftaranController extends Controller
{
    public function show()
    {
        $id_sekolah = Session::get('id_sekolah');
        $data['daftar'] = Pendaftaran::where('status', 'A')->where('id_sekolah', $id_sekolah)->get();
        $data['active'] = "pendaftaran";
        return view('pendaftaran.show', $data);
    }

    public function add() 
    {
        $data['active'] = "pendaftaran";
        $data['periode'] = Periode::where('status', 'A')->get();
          return view('pendaftaran.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Pendaftaran::create([
          'tgl_mulai' => $request->input('start_date'),
          'tgl_akhir' => $request->input('end_date'),
          'nama' => $request->input('name'),
          'id_periode' => $request->input('periode_id'),
          'fee' => $request->input('fee'),
          'created_by' => $created,
          'created_date' => $date,
          'status' => 'A'
          ]);
        return redirect()->route('pendaftaran')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $show = Pendaftaran::findOrFail($id);
        $data['periode'] = Periode::where('status', 'A')->get();
         $data['show'] = $show;
        $data['active'] = "pendaftaran";
        return view('pendaftaran.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Pendaftaran::find($id)->update($request->all());     
      return redirect('/pendaftaran')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
       $post = Pendaftaran::where('id',$id)->update(['status' => 'D']);
       return redirect('/pendaftaran')->with('danger', 'Data berhasil dihapus');
    }

    public function ppdb($id)
    {
        $date = date('Y-m-d');
        $get = Pendaftaran::findOrFail($id);
        $start = $get->tgl_mulai;
        $end = $get->tgl_akhir;
        $id_sekolah = $get->id_sekolah;

        $data['show'] = $get;
        $data['sekolah'] = Sekolah::where('id', $id_sekolah)->first();
        if(($date>=$start) && ($date<=$end)) {
          return view('pendaftaran.ppdb',$data);
        } else {
          return view('pendaftaran.ppdbclose',$data);
        }
    }

    public function saveppdb(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $id_daftar = $request->input('daftar_id');
        $save = Siswa::create([
          'id_sekolah' => $request->input('id_sekolah'),
          'nama' => $request->input('nama'),
          'panggilan' => $request->input('panggilan'),
          'nik' => $request->input('nik'),
          'nisn' => $request->input('nisn'),
          'jk' => $request->input('jk'),
          'agama' => $request->input('agama'),
          'tmp_lahir' => $request->input('tmp_lahir'),
          'tgl_lahir' => $request->input('tgl_lahir'),
          'alamat' => $request->input('alamat'),
          'email' => $request->input('email'),
          'telp' => $request->input('telp'),
          'id_daftar' => $request->input('daftar_id'),
          'created_date' => $date,
          'status' => 'A',
          'st_peserta' =>'C'
          ]);
        return redirect('ppdbdone/'.$id_daftar);
    }

    public function ppdbdone($id)
    {
        $show = Pendaftaran::findOrFail($id);
        $data['show'] = $show;
        $data['sekolah'] = Sekolah::where('id', $show->id_sekolah)->first();
        $data['daftar'] = Sk::where('id_daftar', $id)->where('status','A')->get();
        return view('pendaftaran.ppdbdone',$data);
    }
  
    public function bill($id)
    {
        $show = Pendaftaran::findOrFail($id);
        $data['show'] = $show;
        $data['daftar'] = Sk::where('id_daftar', $id)->where('status','A')->get();
        $data['active'] = "pendaftaran";
        return view('pendaftaran.bill',$data);
    }

    public function addbill(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $id_daftar = $request->input('id_daftar');
        $save = Sk::create([
          'id_daftar' => $id_daftar,
          'nama' => $request->input('nama'),
          'id_periode' => $request->input('id_periode'),
          'nominal' => $request->input('nominal'),
          'id_kategori' => 1,
          'created_by' => $created,
          'created_date' => $date,
          'status' => 'A'
          ]);
        return redirect('/pendaftaran/bill/'.$id_daftar)->with('success', 'Data berhasil ditambahkan');
    }

    public function deletebill($id)
    {
      $get = Sk::findOrFail($id);
      $id_daftar = $get->id_daftar;
      $post = Sk::where('id',$id)->update(['status' => 'D']);
      return redirect('/pendaftaran/bill/'.$id_daftar)->with('danger', 'Data berhasil dihapus');
    }

     public function cp()
    {
        $id_sekolah = Session::get('id_sekolah');
        $data['daftar'] = Siswa::where('status', 'A')->where('st_pelajar', 'C')->where('id_sekolah', $id_sekolah)->get();
        $data['active'] = "cp";
        return view('pendaftaran.cp', $data);
    }

     public function cpdel($id)
    {
       $post = Siswa::where('id',$id)->update(['status' => 'D']);
       return redirect('/cp')->with('danger', 'Data berhasil dihapus');
    }
}