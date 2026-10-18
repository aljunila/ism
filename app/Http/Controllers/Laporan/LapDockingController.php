<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Docking;
use App\Models\BiayaDocking;
use App\Models\KelDock;
use App\Models\SubJobDock;
use App\Models\Kapal;
use App\Models\Galangan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;
use App\Exports\LapDockingExport;
use App\Exports\LapDocKapalExport;
use App\Exports\LapDocJobExport;
use App\Exports\LapDocSubjobExport;
use Maatwebsite\Excel\Facades\Excel;

class LapDockingController extends Controller
{
    public function laporan()
    {
        $data['active'] = "lapdocking";
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['galangan'] = Galangan::where('is_delete', 0)->get();
        return view('laporan.docking.index', $data);
    }

    public function datalaporan(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_gal = $request->input('id_gal');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        
        $query = DB::table('t_biaya_docking as a')
            ->leftJoin('t_docking as b', 'b.id', '=', 'a.id_docking')
            ->leftJoin('m_subjob_dock as c', 'a.id_subjob', '=', 'c.id')
            ->leftJoin('m_kel_dock as d', 'c.id_job', '=', 'd.id')
            ->leftJoin('kapal as e', 'b.id_kapal', '=', 'e.id')
            ->leftJoin('m_galangan as f', 'b.tempat', '=', 'f.id')
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
                'd.nama as job',
                'f.nama as galangan'
            )
            ->where('a.is_delete', 0)
            ->when($id_kapal, function ($query, $id_kapal) {
                return $query->where('b.id_kapal', $id_kapal);
            })
            ->when($id_gal, function ($query, $id_gal) {
                return $query->where('b.tempat', $id_gal);
            })
            ->when($start_date && !$end_date, function ($query) use ($start_date) {
                return $query->where('b.tahun', '=', $start_date);
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('b.tahun', [$start_date, $end_date]);
            })
            ->when(!$start_date && $end_date, function ($query) use ($end_date) {
                return $query->where('b.tahun', '<=', $end_date);
            })
            ->orderBy('b.tahun', 'DESC')
            ->orderBy('a.id', 'ASC');

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
        $id_gal= $request->input('id_gal');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        return Excel::download(new LapDockingExport($id, $id_gal, $start, $end), 'lap_docking_biaya.xlsx');
    }

    public function dockkapal(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_gal = $request->input('id_gal');
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
            ->leftJoin('m_galangan as d', 'a.tempat', '=', 'd.id')
            ->leftJoinSub($totalBiaya, 'c', function ($join) {
                $join->on('c.id_docking', '=', 'a.id');
            })
            ->select(
                'a.*',
                'b.nama as kapal',
                'b.pemilik as id_perusahaan',
                'd.nama as galangan',
                DB::raw('COALESCE(c.total_biaya, 0) as total_biaya')
            )
            ->where('a.is_delete', 0)
            ->when($id_kapal, function ($q, $id_kapal) {
                $q->where('a.id_kapal', $id_kapal);
            })
            ->when($id_gal, function ($query, $id_gal) {
                return $query->where('a.tempat', $id_gal);
            })
            ->when($start_date && !$end_date, function ($query) use ($start_date) {
                return $query->where('a.tahun', '=', $start_date);
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('a.tahun', [$start_date, $end_date]);
            })
            ->when(!$start_date && $end_date, function ($query) use ($end_date) {
                return $query->where('a.tahun', '<=', $end_date);
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
        $id_gal= $request->input('id_gal');
        $excel= $request->input('excel');
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        if($excel==1){
            return Excel::download(new LapDocKapalExport($id, $id_gal, $start, $end), 'lap_docking_kapal.xlsx');
        } elseif($excel==2) {
            return Excel::download(new LapDocJobExport($id, $id_gal, $start, $end), 'lap_docking_kapal.xlsx');
        } else {
            return Excel::download(new LapDocSubjobExport($id, $id_gal, $start, $end), 'lap_docking_kapal.xlsx');
        }
    }
}
