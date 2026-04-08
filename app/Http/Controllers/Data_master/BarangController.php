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
        $validated = ([
            'id_kel_barang' => $request->post('id_kel_barang'),
            'kode' => $request->post('kode'),
            'nama' => $request->post('nama'),
            'deskripsi' => $request->post('deskripsi')
        ]);
        $cek = Barang::where('kode', $request->post('kode'))->where('is_delete', 0)->exists();
        if($cek){
             return response()->json(['status' => 'error', 'message' => 'Maaf, kode tidak boleh sama'],422);
        } else {
            Barang::create($validated);
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
}
