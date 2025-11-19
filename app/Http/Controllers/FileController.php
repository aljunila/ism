<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;

class FileController extends Controller
{
    public function show()
    {
        $data['active'] = "file";
        return view('file.show', $data);
    }

    public function getData(Request $request)
    {
        $kode = $request->input('kode');

        $daftar = MasterFile::select('id', 'type', 'nama', 'ket')->where('status','A')
                ->when($kode, function($query, $kode) {
                    return $query->where('type', $kode);
                })
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function add() 
    {
        $data['active'] = "file";
          return view('file.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');

        if($request->input('type')=='K') {
            $ket = $request->input('kapal');
        } elseif($request->input('type')=='S') {
            $ket = $request->input('karyawan');
        } else{
            $ket = '';
        }

        $save = MasterFile::create([
          'type' => $request->input('type'),
          'nama' => $request->input('nama'),
          'ket' => $ket,
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
        $file = MasterFile::findOrFail($id);
        return response()->json($file);
    }

    public function update(Request $request, $id)
    {
      $nama = $request->input('nama');
       if($request->input('type')=='K') {
            $ket = $request->input('kapal');
        } elseif($request->input('type')=='S') {
            $ket = $request->input('karyawan');
        } else{
            $ket = '';
        }
      $post = MasterFile::where('id',$id)->update(['nama'=>$nama, 'ket'=>$ket]);     
      return response()->json(['success' => true]);
    }

    public function delete($id)
    {
       $post = MasterFile::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }
}