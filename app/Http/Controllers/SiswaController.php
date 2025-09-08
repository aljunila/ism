<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\Periode;
use App\Models\Pendaftaran;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Sk;
use Alert;
use Session;
Use Carbon\Carbon;

class SiswaController extends Controller
{
     public function show()
    {
        $id_sekolah = Session::get('id_sekolah');
        $data['daftar'] = Siswa::where('status', 'A')->where('st_pelajar', 'A')->where('id_sekolah', $id_sekolah)->get();
        $data['active'] = "siswa";
        return view('siswa.cp', $data);
    }

    public function profil($id)
    {
        $show = Siswa::findOrFail($id);
         $data['show'] = $show;
        $data['active'] = "siswa";
        return view('siswa.profil',$data);
    }

     public function edit($id)
    {
        $id_sekolah = Session::get('id_sekolah');
        $show = Siswa::findOrFail($id);
        $data['psb'] = Pendaftaran::where('id_sekolah', $id_sekolah)->orderBy('id', 'DESC')->get();
        $data['show'] = $show;
        $data['active'] = "siswa";
        return view('siswa.edit',$data);
    }

    public function update(Request $request, $id):RedirectResponse
    {
      $post = Siswa::find($id)->update($request->all());
      
      if($request['file']) {
        $get = Siswa::findOrFail($id);
        Storage::delete(public_path('file_student/' . $get->file));

        $validatedData = $request->validate([
        'file' => 'file|image|mimes:jpeg,png,jpg|max:20480',
        ]);
        $file = $request->file('file');
        $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
  
      // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'file_student';
        $file->move($tujuan_upload,$nama_file);
        $save = Siswa::find($id)->update(['file' => $nama_file]); 
        }
      return redirect('/siswa/profil/'.$id)->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
       $post = Siswa::where('id',$id)->update(['status' => 'D']);
       return redirect('/siswa')->with('danger', 'Data berhasil dihapus');
    }
}