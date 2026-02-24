<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\JenisCuti;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class JenisCutiController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/cuti";
        $data['jenis'] = JenisCuti::where('is_delete', 0)->get();
        return view('data_master.cuti.index', $data);
    }

     public function data()
    {
        $query = JenisCuti::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_master.cuti.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return JenisCuti::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'jumlah' => 'required|integer|max:200',
        ]);
        JenisCuti::create($validated);
        return response()->json(['message' => 'Jenis Cuti ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
           'nama' => 'required|string|max:50',
            'jumlah' => 'required|integer|max:200',
        ]);
        $up = JenisCuti::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Jenis Cuti diperbarui']);
    }

    public function destroy($id)
    {
        $up = JenisCuti::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Jenis Cuti dihapus']);
    }
}


