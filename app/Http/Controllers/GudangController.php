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
use App\Models\KelBarang;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
Use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\RoleContext;
use App\Services\NotificationService;

class GudangController extends Controller
{
    public function show()
    {
        $data['active'] = "gudang";
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['cabang'] = Cabang::where('is_delete', 0)->get();
        $data['kelompok'] = KelBarang::where('is_delete', 0)->get();
        return view('gudang.index', $data);
    }

    public function getData(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $cabang = $request->input('id_cabang');
        $kel = $request->input('kel');

        $data = DB::table('t_gudang as a')
                ->leftJoin('m_barang as b', 'a.id_barang', '=', 'b.id')
                ->leftJoin('m_kel_barang as c', 'b.id_kel_barang', '=', 'c.id')
                ->select('a.id', 'b.nama as barang', 'b.kode', 'c.nama as kelompok','c.kode as part', 'a.jumlah', 'a.baik', 'a.habis', 'a.keterangan')
                ->where('a.is_delete',0)
                ->when($kel, function($query, $kel) {
                    return $query->where('b.id_kel_barang', $kel);
                })
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

    public function getPemakaian($id)
    {
        $rows = DB::table('t_gudang_pemakaian as p')
            ->join('t_gudang as g', 'p.id_gudang', '=', 'g.id')
            ->join('m_barang as b', 'g.id_barang', '=', 'b.id')
            ->select('p.id', 'p.qty', 'p.tanggal', 'p.keterangan', 'p.created_by', 'p.created_date', 'b.nama as barang')
            ->where('p.id_gudang', $id)
            ->orderByDesc('p.tanggal')
            ->orderByDesc('p.id')
            ->get();

        return response()->json($rows);
    }

    public function storePemakaian(Request $request, $id)
    {
        $gudang = Gudang::findOrFail($id);
        $maxQty = (int) $gudang->jumlah;

        $validated = $request->validate([
            'qty'         => "required|integer|min:1|max:{$maxQty}",
            'tanggal'     => 'required|date',
            'keterangan'  => 'nullable|string|max:255',
        ], [
            'qty.max' => "QTY tidak boleh melebihi stok tersedia ({$maxQty}).",
        ]);

        $pembuat = Session::get('name') ?: Session::get('username') ?: 'Unknown';

        DB::table('t_gudang_pemakaian')->insert([
            'id_gudang'    => $id,
            'qty'          => $validated['qty'],
            'tanggal'      => $validated['tanggal'],
            'keterangan'   => $validated['keterangan'] ?? null,
            'created_by'   => $pembuat,
            'created_date' => Carbon::now(),
        ]);
        $jum = $maxQty-$validated['qty'];
        $update = Gudang::find($id)->update(['jumlah' => $jum]); 

        $barang = $gudang->get_barang()?->nama ?? '-';
        $tanggalFormatted = Carbon::parse($validated['tanggal'])->format('d-m-Y');

        // Kirim notifikasi ke semua role yang punya akses menu gudang (menu_id = 81)
        $roleIds = DB::table('role_menu')->where('menu_id', 81)->pluck('role_id')->toArray();
        if (!empty($roleIds)) {
            (new NotificationService())->sendToTargets([
                'judul'    => 'Pemakaian Barang Gudang',
                'pesan'    => "{$barang} digunakan berjumlah {$validated['qty']} pada {$tanggalFormatted} oleh {$pembuat}",
                'tipe'     => 'info',
                'url'      => '/gudang',
                'role_ids' => $roleIds,
            ]);
        }

        return response()->json(['message' => 'Pemakaian berhasil disimpan']);
    }
}