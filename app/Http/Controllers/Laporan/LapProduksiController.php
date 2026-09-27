<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Kapal;
use App\Models\Pelabuhan;
use App\Models\Kendaraan;
use App\Models\BiayaPenumpang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;
use App\Exports\LapProduksiExport;
use Maatwebsite\Excel\Facades\Excel;

class LapProduksiController extends Controller
{
    public function laporan()
    {
        $data['active'] = "lapproduksi";
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('laporan.produksi.index', $data);
    }

    public function datalaporan(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_perusahaan = ($roleJenis == 2) ? Session::get('id_perusahaan') : null;
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $query = DB::table('t_trip as a')
                ->leftjoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->leftjoin('m_pelabuhan as c', 'a.id_pelabuhan', '=', 'c.id')
                ->select('a.*', 'b.nama as kapal', 'c.nama as pelabuhan')
                ->where('a.is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->when($start_date, function($query, $start_date) {
                    return $query->where('a.tanggal', '>=', $start_date);
                })
                ->when($end_date, function($query, $end_date) {
                    return $query->where('a.tanggal', '<=', $end_date);
                })
                ->orderBy('a.tanggal', 'DESC');
        if ((int) $roleJenis === 2) {
            $query->whereIn('b.id_kapal', Kapal::where('pemilik', Session::get('id_perusahaan'))->pluck('id'));
        } else if ((int) $roleJenis === 3) {
            $query->whereIn('b.id_kapal', Kapal::where('id', Session::get('id_kapal'))->pluck('id'));
        } else if ((int) $roleJenis === 6) {
            $query->whereIn('b.id_kapal', Kapal::where('id_cabang', Session::get('id_cabang'))->pluck('id'));
        };

        return DataTables::of($query)
            ->addIndexColumn()
           ->make(true);
    }


    public function export(Request $request)
    {
        $id= $request->input('id_kapal');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        return Excel::download(new LapProduksiExport($id, $start, $end), 'lap_produksi.xlsx');
    }
}
