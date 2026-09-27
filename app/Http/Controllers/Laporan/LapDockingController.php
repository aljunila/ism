<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Docking;
use App\Models\BiayaDocking;
use App\Models\KelDock;
use App\Models\SubJobDock;
use App\Models\Kapal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;
use App\Exports\LapDockingExport;
use Maatwebsite\Excel\Facades\Excel;

class LapDockingController extends Controller
{
    public function laporan()
    {
        $data['active'] = "lapdocking";
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('laporan.docking.index', $data);
    }

    public function datalaporan(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $query = DB::table('t_biaya_docking as a')
                ->leftjoin('t_docking as b', 'b.id', '=', 'a.id_docking')
                ->leftJoin('m_subjob_dock as c', 'a.id_subjob', '=', 'c.id')
                ->leftJoin('m_kel_dock as d', 'c.id_job', '=', 'd.id')
                ->leftJoin('kapal as e', 'b.id_kapal', '=', 'e.id')
                ->select('a.*', 'e.nama as kapal', 'b.bulan', 'b.tahun', 'b.tempat', 'b.tgl_mulai', 'tgl_selesai','b.durasi', 'c.nama as subjob', 'd.nama as job')
                ->where('a.is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('b.id_kapal', $id_kapal);
                })
                ->when($start_date, function($query, $start_date) {
                    return $query->where('b.tgl_mulai', '>=', $start_date);
                })
                ->when($end_date, function($query, $end_date) {
                    return $query->where('b.tgl_selesai', '<=', $end_date);
                })
                ->orderBy('b.tgl_mulai', 'DESC');
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

        return Excel::download(new LapDockingExport($id, $start, $end), 'lap_docking.xlsx');
    }
}
