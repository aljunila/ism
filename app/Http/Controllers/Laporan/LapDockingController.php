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
use App\Exports\LapDocKapalExport;
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
            ->leftJoin('t_docking as b', 'b.id', '=', 'a.id_docking')
            ->leftJoin('m_subjob_dock as c', 'a.id_subjob', '=', 'c.id')
            ->leftJoin('m_kel_dock as d', 'c.id_job', '=', 'd.id')
            ->leftJoin('kapal as e', 'b.id_kapal', '=', 'e.id')
            ->select(
                'a.*',
                'e.nama as kapal',
                'b.bulan',
                'b.tahun',
                'b.tempat',
                'b.tgl_mulai',
                'b.tgl_selesai',
                'b.durasi',
                'c.nama as subjob',
                'd.nama as job'
            )
            ->where('a.is_delete', 0)
            ->when($id_kapal, function ($query, $id_kapal) {
                return $query->where('b.id_kapal', $id_kapal);
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $start = \Carbon\Carbon::createFromFormat('Y-m', $start_date);
                $end   = \Carbon\Carbon::createFromFormat('Y-m', $end_date);

                return $query->where(function ($q) use ($start, $end) {
                    $q->where('b.tahun', '>', $start->year)
                        ->where('b.tahun', '<', $end->year)
                        ->orWhere(function ($q2) use ($start) {
                            $q2->where('b.tahun', $start->year)
                                ->where('b.bulan', '>=', $start->month);
                        })
                        ->orWhere(function ($q2) use ($end) {
                            $q2->where('b.tahun', $end->year)
                                ->where('b.bulan', '<=', $end->month);
                        })
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('b.tahun', $start->year)
                                ->where('b.tahun', $end->year)
                                ->whereBetween('b.bulan', [
                                    $start->month,
                                    $end->month
                                ]);
                        });
                });
            })
            ->orderBy('b.tahun', 'DESC')
            ->orderBy('b.bulan', 'DESC');

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

        return Excel::download(new LapDockingExport($id, $start, $end), 'lap_docking_biaya.xlsx');
    }

    public function dockkapal(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        
        $totalBiaya = DB::table('t_biaya_docking')
            ->select('id_docking')
            ->selectRaw('COALESCE(SUM(total), 0) as total_biaya')
            ->where(function ($q) {
                $q->whereNull('is_delete')
                ->orWhere('is_delete', 0);
            })
            ->groupBy('id_docking');

        $query = DB::table('t_docking as a')
            ->leftJoin('kapal as b', 'b.id', '=', 'a.id_kapal')
            ->leftJoinSub($totalBiaya, 'c', function ($join) {
                $join->on('c.id_docking', '=', 'a.id');
            })
            ->select(
                'a.*',
                'b.nama as kapal',
                'b.pemilik as id_perusahaan',
                DB::raw('COALESCE(c.total_biaya, 0) as total_biaya')
            )
            ->where('a.is_delete', 0)
            ->when($id_kapal, function ($q, $id_kapal) {
                $q->where('a.id_kapal', $id_kapal);
            })
            ->when($start_date, function ($q, $start_date) {
                $start = \Carbon\Carbon::createFromFormat('Y-m', $start_date);

                $q->where(function ($sub) use ($start) {
                    $sub->where('a.tahun', '>', $start->year)
                        ->orWhere(function ($sub2) use ($start) {
                            $sub2->where('a.tahun', $start->year)
                                ->where('a.bulan', '>=', $start->month);
                        });
                });
            })
            ->when($end_date, function ($q, $end_date) {
                $end = \Carbon\Carbon::createFromFormat('Y-m', $end_date);

                $q->where(function ($sub) use ($end) {
                    $sub->where('a.tahun', '<', $end->year)
                        ->orWhere(function ($sub2) use ($end) {
                            $sub2->where('a.tahun', $end->year)
                                ->where('a.bulan', '<=', $end->month);
                        });
                });
            })
            ->orderBy('a.tahun', 'DESC')
            ->orderBy('a.bulan', 'DESC');

        if ((int) $roleJenis === 2) {
            $query->whereIn('a.id_kapal', Kapal::where('pemilik', Session::get('id_perusahaan'))->pluck('id'));
        } else if ((int) $roleJenis === 3) {
            $query->whereIn('a.id_kapal', Kapal::where('id', Session::get('id_kapal'))->pluck('id'));
        } else if ((int) $roleJenis === 6) {
            $query->whereIn('a.id_kapal', Kapal::where('id_cabang', Session::get('id_cabang'))->pluck('id'));
        };

        return DataTables::of($query)
            ->addIndexColumn()
           ->make(true);
    }

    public function export2(Request $request)
    {
        $id= $request->input('id_kapal');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        return Excel::download(new LapDocKapalExport($id, $start, $end), 'lap_docking_kapal.xlsx');
    }
}
