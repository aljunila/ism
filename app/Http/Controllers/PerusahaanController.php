<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;

class PerusahaanController extends Controller
{
    public function show()
    {
        $data['daftar'] = Perusahaan::get();
        $data['active'] = "perusahaan";
        return view('perusahaan.show', $data);
    }

    public function add() 
    {
        $data['active'] = "perusahaan";
          return view('perusahaan.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Perusahaan::create([
          'uid' => Str::uuid()->toString(),
          'nama' => strtoupper($request->input('nama')),
          'alamat' => $request->input('alamat'),
          'email' => $request->input('email'),
          'telp' => $request->input('telp'),
          'npwp' => $request->input('npwp'),
          'nib' => $request->input('nib'),
          ]);
        // return redirect()->route('perusahaan')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($uid)
    {
        $show = Perusahaan::where('uid', $uid)->first();
         $data['show'] = $show;
        $data['active'] = "perusahaan";
        return view('perusahaan.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Perusahaan::find($id)->update([
          'nama' => strtoupper($request->input('nama')),
          'alamat' => $request->input('alamat'),
          'email' => $request->input('email'),
          'telp' => $request->input('telp'),
          'npwp' => $request->input('npwp'),
          'nib' => $request->input('nib'),
      ]);     
    //   return redirect('/perusahaan')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
       $post = Perusahaan::where('id',$id)->delete();
       return redirect('/perusahaan')->with('danger', 'Data berhasil dihapus');
    }
}