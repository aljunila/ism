<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Galangan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GalanganController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/galangan";
        return view('data_master.galangan.index', $data);
    }

     public function data()
    {
        $query = Galangan::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.galangan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        Galangan::create($validated);
        return response()->json(['message' => 'galangan ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        $up = Galangan::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'galangan diperbarui']);
    }

    public function destroy($id)
    {
        $up = Galangan::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'galangan dihapus']);
    }
}
