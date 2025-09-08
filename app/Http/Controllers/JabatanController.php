<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;

class JabatanController extends Controller
{
    public function show()
    {
        $data['active'] = "jabatan";
        return view('jabatan.show', $data);
    }

    public function getData()
    {
        $daftar = Jabatan::select('id', 'nama')->where('status','A')->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function add() 
    {
        $data['active'] = "jabatan";
          return view('jabatan.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Jabatan::create([
          'uid' => Str::uuid()->toString(),
          'nama' => $request->input('nama'),
          'status' => 'A',
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
        $jabatan = Jabatan::findOrFail($id);
        return response()->json($jabatan);
    }

    public function update(Request $request, $id)
    {
      $nama = $request->input('nama');
      $post = Jabatan::where('id',$id)->update(['nama'=>$nama]);     
      return response()->json(['success' => true]);
    }

    public function delete($id)
    {
       $post = Jabatan::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }
}