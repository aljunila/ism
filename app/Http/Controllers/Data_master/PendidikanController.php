<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PendidikanController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/pendidikan";
        return view('data_master.pendidikan.index', $data);
    }

     public function data()
    {
        $query = Pendidikan::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.pendidikan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Pendidikan::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        Pendidikan::create($validated);
        return response()->json(['message' => 'pendidikan ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        $up = Pendidikan::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'pendidikan diperbarui']);
    }

    public function destroy($id)
    {
        $up = Pendidikan::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'pendidikan dihapus']);
    }
}
