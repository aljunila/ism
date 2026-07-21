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
            'kategori' => 'required|integer|max:2',
            'nama' => 'required|string|max:50',
            'kode' => 'nullable|string|max:50',
            'ket' => 'nullable|string|max:100',
        ]);
        KelBarang::create($validated);
        return response()->json(['message' => 'Data ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kategori' => 'required|integer|max:2',
            'nama' => 'required|string|max:50',
            'kode' => 'nullable|string|max:50',
            'ket' => 'nullable|string|max:100',
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

    function getKelompok(Request $request) {
        $idbagian = $request->input('idbagian');
        $id_kapal = $request->input('id_kapal');
        if($idbagian!=2) {
            $get = KelBarang::where('kategori', $idbagian)->where('is_delete', 0)->get();
        } else {
            $get = DB::table('m_kel_barang as a')
                ->leftJoin('m_barang as b', 'a.id', '=', 'b.id_kel_barang')
                ->leftJoin('t_gudang as c', 'b.id', '=', 'c.id_barang')
                ->select('a.id', 'a.nama', 'a.kode')
                ->where('c.id_kapal', $id_kapal)
                ->where('c.is_delete', 0)
                ->where('a.kategori', 2)
                ->distinct()
                ->orderBy('a.nama')
                ->get();
        }
        return response()->json($get);
    }
}
