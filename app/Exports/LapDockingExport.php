<?php

namespace App\Exports;

use App\Models\Docking;
use App\Models\BiayaDocking;
use App\Models\KelDock;
use App\Models\SubJobDock;
use App\Models\Kapal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class LapDockingExport implements FromView
{
    protected $id;
    protected $start;
    protected $end;

    public function __construct($id, $start, $end)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
    }

   public function view(): View
    {
        
        $data = DB::table('t_biaya_docking as a')
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
            ->when($this->start, function ($query, $start) {
                $startDate = \Carbon\Carbon::createFromFormat('Y-m', $start);

                return $query->where(function ($q) use ($startDate) {
                    $q->where('b.tahun', '>', $startDate->year)
                        ->orWhere(function ($sub) use ($startDate) {
                            $sub->where('b.tahun', $startDate->year)
                                ->where('b.bulan', '>=', $startDate->month);
                        });
                });
            })
            ->when($this->end, function ($query, $end) {
                $endDate = \Carbon\Carbon::createFromFormat('Y-m', $end);

                return $query->where(function ($q) use ($endDate) {
                    $q->where('b.tahun', '<', $endDate->year)
                        ->orWhere(function ($sub) use ($endDate) {
                            $sub->where('b.tahun', $endDate->year)
                                ->where('b.bulan', '<=', $endDate->month);
                        });
                });
            })
            ->when($this->id, function ($query, $id) {
                return $query->where('b.id_kapal', $id);
            })
            ->orderBy('b.tahun', 'DESC')
            ->orderBy('b.bulan', 'DESC')
            ->orderBy('b.tgl_mulai', 'DESC')
            ->get();


        return view('export.lapdocking',compact('data'));
    }
}
