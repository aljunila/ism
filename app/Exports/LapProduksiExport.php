<?php

namespace App\Exports;

use App\Models\Trip;
use App\Models\Kapal;
use App\Models\Pelabuhan;
use App\Models\Kendaraan;
use App\Models\BiayaPenumpang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class LapProduksiExport implements FromView
{
    protected $id;
    protected $start;
    protected $end;
    protected $smonth;
    protected $emonth;
    protected $syear;
    protected $eyear;

    public function __construct($id, $start, $end, $smonth, $emonth, $syear, $eyear)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->smonth = $smonth;
        $this->emonth = $emonth;
        $this->syear = $syear;
        $this->eyear = $end;
    }

   public function view(): View
    {
        $data = DB::table('t_trip as a')
                ->leftjoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->leftjoin('m_pelabuhan as c', 'a.id_pelabuhan', '=', 'c.id')
                ->select('a.*', 'b.nama as kapal', 'c.nama as pelabuhan')
                ->where('a.is_delete', 0)
                ->when($this->id, function ($query, $id) {
                    return $query->where('a.id_kapal', $id);
                })
                ->when($this->start, function ($query, $start) {
                    return $query->whereDate('a.tanggal', '>=', $start);
                })
                ->when($this->end, function ($query, $end) {
                    return $query->whereDate('a.tanggal', '<=', $end);
                })
                ->when($this->smonth, function ($query, $smonth) {
                    return $query->where('a.tanggal','>=',$smonth . '-01'
                    );
                })
                ->when($this->emonth, function ($query, $emonth) {
                    return $query->where('a.tanggal','<',
                        \Carbon\Carbon::createFromFormat('Y-m', $emonth)
                            ->addMonth()
                            ->startOfMonth()
                            ->toDateString()
                    );
                })
                ->when($this->syear, function ($query, $syear) {
                    return $query->where('a.tanggal','>=',$syear . '-01-01');
                })
                ->when($this->eyear, function ($query, $eyear) {
                    return $query->where('a.tanggal','<',((int) $eyear + 1) . '-01-01');
                })
                ->orderBy('a.tanggal', 'ASC')
                ->get();
        $kend = Kendaraan::where('is_delete',0)->get();

        return view('export.lapproduksi',compact('data', 'kend'));
    }
}
