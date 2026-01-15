<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\BiayaPenumpang;
use App\Models\Pelabuhan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class BiayaController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/biaya";
        $data['pelabuhan'] = Pelabuhan::where('is_delete', 0)->get();
        $data['kendaraan'] = Kendaraan::where('is_delete', 0)->get();
        return view('data_master.biaya.index', $data);
    }

     public function data()
    {
        $query = BiayaPenumpang::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pelabuhan', function ($row) {
                $pelabuhan = Pelabuhan::find($row->id_pelabuhan);
                return $pelabuhan ? $pelabuhan->nama : '-';
            })
            ->addColumn('kendaraan', function ($row) {
                $kendaraan = Kendaraan::find($row->id_kendaraan);
                return $kendaraan ? $kendaraan->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_master.biaya.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return BiayaPenumpang::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas' => 'required|string|max:50',
            'nominal' => 'required|integer',
            'id_pelabuhan' => 'required|integer|max:20',
            'id_kendaraan' => 'required|integer|max:20',
        ]);
        BiayaPenumpang::create($validated);
        return response()->json(['message' => 'Biaya ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kelas' => 'required|string|max:50',
            'nominal' => 'required|integer',
            'id_pelabuhan' => 'required|integer|max:20',
            'id_kendaraan' => 'required|integer|max:20',
        ]);
        $up = BiayaPenumpang::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Biaya diperbarui']);
    }

    public function destroy($id)
    {
        $up = BiayaPenumpang::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Biaya dihapus']);
    }
}
