<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\KelDock;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JobDockController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/pendidikan";
        return view('data_master.jobdock.index', $data);
    }

     public function data()
    {
        $query = KelDock::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.jobdock.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return KelDock::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        KelDock::create($validated);
        return response()->json(['message' => 'jobdock ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
        ]);
        $up = KelDock::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'jobdock diperbarui']);
    }

    public function destroy($id)
    {
        $up = KelDock::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'jobdock dihapus']);
    }
}
