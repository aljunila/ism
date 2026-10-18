<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LapDocKapalExport implements FromView
{
    protected $idKapal;
    protected $idGal;
    protected $startDate;
    protected $endDate;

    public function __construct($id = null, $id_gal = null, $start = null, $end = null)
    {
        $this->idKapal = $id;
        $this->idGal = $id_gal;
        $this->startDate = $start;
        $this->endDate = $end;
    }

    public function view(): View
    {
        // Daftar perusahaan untuk kolom dinamis Excel
        $perusahaan = DB::table('perusahaan')
            ->orderBy('kode')
            ->get();

        // Query rekap biaya per kapal, bulan, dan tahun
        $query = DB::table('t_docking as a')
            ->leftJoin('kapal as b', 'b.id', '=', 'a.id_kapal')
            ->leftJoin('perusahaan as p', 'p.id', '=', 'b.pemilik')
            ->leftJoin('t_biaya_docking as c', function ($join) {
                $join->on('c.id_docking', '=', 'a.id')
                    ->where(function ($q) {
                        $q->whereNull('c.is_delete')
                          ->orWhere('c.is_delete', 0);
                    });
            })
            ->select(
                'b.id as id_kapal',
                'b.nama as kapal',
                'b.pemilik as id_perusahaan',
                'p.kode as nama_perusahaan',
                'a.bulan',
                'a.tahun',
                DB::raw('COALESCE(SUM(c.total), 0) as total_biaya')
            )
            ->where('a.is_delete', 0)
            ->when($this->idKapal, function ($q, $id) {
                $q->where('a.id_kapal', $id);
            })
             ->when($this->idGal, function ($q) {
                $q->where('a.tempat', $this->idGal);
            })
            ->when($this->startDate && !$this->endDate, function ($query) {
                return $query->where('a.tahun', '=', $this->startDate);
            })
            ->when($this->startDate && $this->endDate, function ($query)  {
                return $query->whereBetween('a.tahun', [$this->startDate, $this->endDate]);
            })
            ->when(!$this->startDate && $this->endDate, function ($query) {
                return $query->where('a.tahun', '<=', $this->endDate);
            });

        $data = $query
            ->groupBy(
                'b.id',
                'b.nama',
                'b.pemilik',
                'p.kode',
                'a.bulan',
                'a.tahun'
            )
            ->orderBy('b.nama')
            ->orderBy('a.tahun')
            ->orderBy('a.bulan')
            ->get();

        return view('export.lapdockapal', compact(
            'data',
            'perusahaan'
        ));
    }
}
