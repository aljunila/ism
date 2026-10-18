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
    protected $idgal;
    protected $start;
    protected $end;

    public function __construct($id = null, $id_gal = null, $start = null, $end = null)
    {
        $this->id = $id;
        $this->idgal = $id_gal;
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
            ->when($this->id, function ($query, $id) {
                return $query->where('b.id_kapal', $id);
            })
            ->when($this->idgal, function ($query) {
                return $query->where('b.tempat', $this->idgal);
            })
            ->when($this->start && !$this->end, function ($query) {
                return $query->where('b.tahun', '=', $this->start);
            })
            ->when($this->start && $this->end, function ($query)  {
                return $query->whereBetween('b.tahun', [$this->start, $this->end]);
            })
            ->when(!$this->start && $this->end, function ($query) {
                return $query->where('b.tahun', '<=', $this->end);
            })
            ->orderBy('b.tahun', 'DESC')
            ->orderBy('b.id', 'ASC')
            ->get();


        return view('export.lapdocking',compact('data'));
    }
}
