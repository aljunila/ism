<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Kapal;
use App\Models\LgoBBM;
use App\Models\BBMKapal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;
use App\Exports\LapBBMExport;
use App\Exports\LapyearBBMExport;
use Maatwebsite\Excel\Facades\Excel;

class LapBBMController extends Controller
{
    public function laporan()
    {
        $data['active'] = "lapbbm";
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('laporan.bbm.index', $data);
    }

    public function datalaporan(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $start_month = $request->input('start_month');
        $start_year = $request->input('start_year');
        $id_perusahaan = ($roleJenis == 2) ? Session::get('id_perusahaan') : null;
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $query = DB::table('t_log_bbm as a')
                ->leftjoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->select('a.*', 'b.nama as kapal')
                ->where('a.is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->when($start_date, function($query, $start_date) {
                    return $query->where('a.tanggal', '>=', $start_date);
                })
                ->when($start_month, function ($query, $start_month) {
                    return $query->whereRaw(
                        "DATE_FORMAT(a.tanggal, '%Y-%m') = ?",
                        [$start_month]
                    );
                })
                ->when($start_year, function ($query, $start_year) {
                    return $query->whereYear('a.tanggal', $start_year);
                })
                ->orderBy('a.tanggal', 'DESC')
                ->orderBy('a.id', 'DESC');
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
        $smonth = $request->input('start_month');
        $year = $request->input('start_year');
        $filter = $request->input('filter');
        if ($filter==3) {
            return Excel::download(new LapyearBBMExport($id, $year), 'lap_bbm.xlsx');
        } else {
            return Excel::download(new LapBBMExport($id, $start, $end, $smonth, $year), 'lap_bbm.xlsx');
        }
    }
}
