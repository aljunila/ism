<?php

namespace App\Http\Controllers\Data_master;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

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
