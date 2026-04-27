<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\DIvisi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DivisiController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/divisi";
        return view('data_master.divisi.index', $data);
    }

     public function data()
    {
        $query = Divisi::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.divisi.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Divisi::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        Divisi::create($validated);
        return response()->json(['message' => 'divisi ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        $up = Divisi::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'divisi diperbarui']);
    }

    public function destroy($id)
    {
        $up = Divisi::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'divisi dihapus']);
    }

    public function getdivisi($id_kapal)
    {
        $data = Kapal::findOrFail($id_kapal);
        $get = Divisi::where('id_cabang', $data->id_cabang)->get();
        return response()->json($get);
    }
}
