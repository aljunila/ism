<?php

namespace App\Http\Controllers\Acl;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CabangController extends Controller
{
    public function data()
    {
        $query = Cabang::query()->orderBy('id');
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('acl.partials.cabang_actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function all()
    {
        return Cabang::where('is_delete', 0)->get(['id', 'cabang']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cabang' => 'required|string|max:30',
        ]);
        Cabang::create($validated);
        return response()->json(['message' => 'Cabang ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'cabang' => 'required|string|max:50',
        ]);
        $role = Cabang::findOrFail($id);
        $role->update($validated);
        return response()->json(['message' => 'Cabang diperbarui']);
    }

    public function destroy($id)
    {
        $role = Cabang::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Cabang dihapus']);
    }
}
