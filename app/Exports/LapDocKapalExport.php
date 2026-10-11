<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LapDocKapalExport implements FromView
{
    protected $idKapal;
    protected $startDate;
    protected $endDate;

    public function __construct($id = null, $start = null, $end = null)
    {
        $this->idKapal = $id;
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
            });

        // Filter periode awal berdasarkan bulan dan tahun
        if ($this->startDate) {
            $start = Carbon::createFromFormat(
                'Y-m',
                $this->startDate
            );

            $query->where(function ($q) use ($start) {
                $q->where('a.tahun', '>', $start->year)
                    ->orWhere(function ($sub) use ($start) {
                        $sub->where('a.tahun', $start->year)
                            ->where('a.bulan', '>=', $start->month);
                    });
            });
        }

        // Filter periode akhir berdasarkan bulan dan tahun
        if ($this->endDate) {
            $end = Carbon::createFromFormat(
                'Y-m',
                $this->endDate
            );

            $query->where(function ($q) use ($end) {
                $q->where('a.tahun', '<', $end->year)
                    ->orWhere(function ($sub) use ($end) {
                        $sub->where('a.tahun', $end->year)
                            ->where('a.bulan', '<=', $end->month);
                    });
            });
        }

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
