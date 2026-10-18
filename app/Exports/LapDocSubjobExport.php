<?php 

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;

class LapDocSubjobExport implements FromView
{
    protected $idKapal;
    protected $idGal;
    protected $startDate;
    protected $endDate;

    public function __construct(
        $id = null,
        $id_gal = null,
        $start = null,
        $end = null
    ) 
    {
        $this->idKapal = $id;
        $this->idGal = $id_gal;
        $this->startDate = $start;
        $this->endDate = $end;
    }

    private function filterDocking($query)
    {
        return $query
            ->where('b.is_delete', 0)
            ->when($this->idKapal, function ($q) {
                $q->where('b.id_kapal', $this->idKapal);
            })
            ->when($this->idGal, function ($q) {
                $q->where('b.tempat', $this->idGal);
            })
            ->when($this->startDate && !$this->endDate, function ($q) {
                $q->where('b.tahun', $this->startDate);
            })
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereBetween('b.tahun', [
                    $this->startDate,
                    $this->endDate
                ]);
            })
            ->when(!$this->startDate && $this->endDate, function ($q) {
                $q->where('b.tahun', '<=', $this->endDate);
            });
    }

    public function view(): View
    {
        $mode = $this->idKapal ? 'tahun' : 'kapal';

        // Daftar kolom dinamis berdasarkan jadwal docking
        $jadwal = DB::table('t_docking as b');
        $this->filterDocking($jadwal);

        if ($mode === 'tahun') {
            $kolom = (clone $jadwal)
                ->select('b.tahun')
                ->distinct()
                ->orderBy('b.tahun')
                ->pluck('b.tahun')
                ->map(fn ($tahun) => (int) $tahun)
                ->values();
        } else {
            $kolom = (clone $jadwal)
                ->join('kapal as k', 'k.id', '=', 'b.id_kapal')
                ->where('k.status', 'A')
                ->select('k.id', 'k.nama')
                ->distinct()
                ->orderBy('k.nama')
                ->get();
        }

        // Rekap biaya per Job, Subjob, dan kolom dinamis
        $query = DB::table('m_kel_dock as a')
            ->join('t_docking as b', 'b.tempat', '=', 'a.id_gal')
            ->join('m_subjob_dock as e', 'e.id_job', '=', 'a.id')
            ->leftJoin('t_biaya_docking as c', function ($join) {
                $join->on('c.id_docking', '=', 'b.id')
                    ->on('c.id_subjob', '=', 'e.id')
                    ->where(function ($q) {
                        $q->whereNull('c.is_delete')
                          ->orWhere('c.is_delete', 0);
                    });
            })
            ->where('a.is_delete', 0)
            ->where('e.is_delete', 0);

        $this->filterDocking($query);

        if ($mode === 'tahun') {
            $query->select(
                'a.id as id_job',
                'a.nama as job',
                'e.id as id_subjob',
                'e.nama as subjob',
                'b.tahun as kolom',
                DB::raw('COALESCE(SUM(c.total), 0) as total_biaya')
            )->groupBy(
                'a.id',
                'a.nama',
                'e.id',
                'e.nama',
                'b.tahun'
            );
        } else {
            $query->select(
                'a.id as id_job',
                'a.nama as job',
                'e.id as id_subjob',
                'e.nama as subjob',
                'b.id_kapal as id_kapal',
                DB::raw('COALESCE(SUM(c.total), 0) as total_biaya')
            )->groupBy(
                'a.id',
                'a.nama',
                'e.id',
                'e.nama',
                'b.id_kapal'
            );
        }

        $data = $query
            ->orderBy('a.nama')
            ->orderBy('e.nama')
            ->get();

        return view('export.lapdocsubjob', compact(
            'data',
            'kolom',
            'mode'
        ));
    }
}
