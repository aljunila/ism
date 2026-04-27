<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Gudang;
use App\Models\Cabang;
use App\Models\Kapal;
use App\Models\Barang;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
Use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\RoleContext;

class GudangController extends Controller
{
    public function show()
    {
        $data['active'] = "gudang";
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['cabang'] = Cabang::where('is_delete', 0)->get();
        return view('gudang.index', $data);
    }

    public function getData(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $kapal = $request->input('id_kapal');
        $cabang = $request->input('id_cabang');

        $data = DB::table('t_gudang as a')
                ->leftJoin('m_barang as b', 'a.id_barang', '=', 'b.id')
                ->leftJoin('m_kel_barang as c', 'b.id_kel_barang', '=', 'c.id')
                ->select('a.id', 'b.nama as barang', 'b.kode', 'c.nama as kelompok', 'a.jumlah', 'a.baik', 'a.habis', 'a.keterangan')
                ->where('a.is_delete',0)
                ->when($kapal, function($query, $kapal) {
                    return $query->where('id_kapal', $kapal);
                })
                ->when($cabang, function($query, $cabang) {
                    return $query->where('id_cabang', $cabang);
                });
        return DataTables::of($data)
        ->filterColumn('barang', function($query, $keyword) {
            $query->where('b.nama', 'like', "%{$keyword}%");
        })
        ->filterColumn('kode', function($query, $keyword) {
            $query->where('b.kode', 'like', "%{$keyword}%");
        })
        ->filterColumn('kelompok', function($query, $keyword) {
            $query->where('c.nama', 'like', "%{$keyword}%");
        })
        ->addColumn('aksi', function ($row) {
            return view('gudang.partials.actions', compact('row'))->render();
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer',
            'baik' => 'required|integer',
            'habis' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);
        $up = Gudang::findOrFail($id);
        $up->update($validated);
        return response()->json(['message' => 'Data diperbarui']);
    }
}