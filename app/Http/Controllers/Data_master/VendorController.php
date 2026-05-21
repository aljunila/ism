<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Session;

class VendorController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_master/vendor";
        $data['cabang'] = Cabang::where('is_delete', 0)->get();
        return view('data_master.vendor.index', $data);
    }

     public function data()
    {
        $query = Vendor::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('cabang', function ($row) {
                $cabang = Cabang::find($row->id_cabang);
                return $cabang ? $cabang->cabang : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_master.vendor.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Vendor::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function quickStore(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:50']);

        $idCabang = Session::get('id_cabang');
        if (!$idCabang) {
            return response()->json(['message' => 'Cabang tidak ditemukan di sesi'], 422);
        }

        // Kembalikan yang sudah ada daripada membuat duplikat
        $existing = Vendor::whereRaw('LOWER(nama) = ?', [strtolower(trim($request->nama))])
            ->where('id_cabang', $idCabang)
            ->where('is_delete', 0)
            ->first();

        if ($existing) {
            return response()->json(['id' => $existing->id, 'nama' => $existing->nama]);
        }

        $vendor = Vendor::create([
            'nama'      => trim($request->nama),
            'id_cabang' => $idCabang,
            'is_delete' => 0,
        ]);

        return response()->json(['id' => $vendor->id, 'nama' => $vendor->nama], 201);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'alamat' => 'nullable|string|max:100',
            'telp' => 'nullable|string|max:25',
            'id_cabang' => 'required|integer|max:10',
        ]);
        Vendor::create($validated);
        return response()->json(['message' => 'Vendor ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'telp' => 'required|string|max:25',
            'id_cabang' => 'required|integer|max:10',
        ]);
        $up = Vendor::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Vendor diperbarui']);
    }

    public function destroy($id)
    {
        $up = Vendor::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Vendor dihapus']);
    }

}
