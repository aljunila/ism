<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\KelBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class BarangController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/barang";
        $data['kelompok'] = KelBarang::where('is_delete', 0)->get();
        return view('data_master.barang.index', $data);
    }

     public function data()
    {
        $query = Barang::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kelompok', function ($row) {
                $kel = KelBarang::find($row->id_kel_barang);
                return $kel ? $kel->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_master.barang.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Barang::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $cek = Barang::where('kode', $request->post('kode'))->where('is_delete', 0)->exists();
        if($cek){
             return response()->json(['status' => 'error', 'message' => 'Maaf, kode tidak boleh sama'],422);
        } else {
            $validated = ([
                'id_kel_barang' => $request->post('id_kel_barang'),
                'kode' => $request->post('kode'),
                'nama' => $request->post('nama'),
                'deskripsi' => $request->post('deskripsi'),
                'min' => $request->post('min'),
                'max' => $request->post('max'),
            ]);
            $save = Barang::create($validated);
            
            if($request->hasFile('file')) {
                $request->validate([
                'file' => 'required|file|mimes:jpg,jpeg,png|max:20480',
                ]);
                $file = $request->file('file');
                $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
            
                // isi dengan nama folder tempat kemana file diupload
                $tujuan_upload = 'file_barang';
                $file->move($tujuan_upload,$nama_file);
                $save = Barang::find($save->id)->update(['img' => $nama_file]); 
            }
            return response()->json(['status' => 'success', 'message' => 'Data barang berhasil disimpan'],200);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = ([
            'id_kel_barang' => $request->post('id_kel_barang'),
            'nama' => $request->post('nama'),
            'deskripsi' => $request->post('deskripsi')
        ]);
        $up = Barang::findOrFail($id);
        $up->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Data barang berhasil diubah'],200);
    }

    public function destroy($id)
    {
        $up = Barang::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Data dihapus']);
    }
   
    public function databyKat(Request $request)
    {
        $id_bagian = $request->input('bagian');
        $get = KelBarang::where('kategori', $id_bagian)->where('is_delete', 0)->get();
        return response()->json($get);
    }

    public function barangbyKel(Request $request)
    {
        $id_kel = $request->input('id_kel_barang');
        $get = Barang::where('id_kel_barang', $id_kel)->where('is_delete', 0)->get();
        return response()->json($get);
    }
}
