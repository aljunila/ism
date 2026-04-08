<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\KelBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class KelBarangController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/kelbarang";
        return view('data_master.kelbarang.index', $data);
    }

     public function data()
    {
        $query = KelBarang::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.kelbarang.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return KelBarang::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        KelBarang::create($validated);
        return response()->json(['message' => 'Data ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        $up = KelBarang::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Data diperbarui']);
    }

    public function destroy($id)
    {
        $up = KelBarang::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Data dihapus']);
    }
}
