<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KendaraanController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/kendaraan";
        return view('data_master.kendaraan.index', $data);
    }

     public function data()
    {
        $query = Kendaraan::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.kendaraan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Kendaraan::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:30',
        ]);
        Kendaraan::create($validated);
        return response()->json(['message' => 'Kendaraan ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:30',
        ]);
        $up = Kendaraan::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Kendaraan diperbarui']);
    }

    public function destroy($id)
    {
        $up = Kendaraan::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Kendaraan dihapus']);
    }
}
