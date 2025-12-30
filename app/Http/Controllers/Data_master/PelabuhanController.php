<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Pelabuhan;
use App\Models\Cabang;
use App\Models\Kapal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class PelabuhanController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/pelabuhan";
        $data['cabang'] = Cabang::where('is_delete', 0)->get();
        return view('data_master.pelabuhan.index', $data);
    }

     public function data()
    {
        $query = Pelabuhan::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('cabang', function ($row) {
                $cabang = Cabang::find($row->id_cabang);
                return $cabang ? $cabang->cabang : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_master.pelabuhan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Pelabuhan::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'id_cabang' => 'required|integer|max:10',
        ]);
        Pelabuhan::create($validated);
        return response()->json(['message' => 'Pelabuhan ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'id_cabang' => 'required|integer|max:10',
        ]);
        $up = Pelabuhan::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Pelabuhan diperbarui']);
    }

    public function destroy($id)
    {
        $up = Pelabuhan::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Pelabuhan dihapus']);
    }

    public function getPelabuhan($id_kapal)
    {
        $data = Kapal::findOrFail($id_kapal);
        $get = Pelabuhan::where('id_cabang', $data->id_cabang)->get();
        return response()->json($get);
    }
}
