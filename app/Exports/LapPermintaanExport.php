<?php

namespace App\Exports;

use App\Models\Permintaan;
use App\Models\DetailPermintaan;
use App\Models\Barang;
use App\Models\KelBarang;
use App\Models\Kapal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class LapPermintaanExport implements FromView
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
        $data = DB::table('t_detail_permintaan as a')
                        ->leftJoin('t_permintaan_barang as b', 'a.id_permintaan', '=', 'b.id')
                        ->leftJoin('m_barang as c', 'a.id_barang', '=', 'c.id')
                        ->leftJoin('kapal as d', 'b.id_kapal', '=', 'd.id')
                        ->select('a.*', 'b.bagian', 'b.nomor', 'b.tanggal', 'c.nama as barang', 'c.kode', 'd.nama as kapal')
                        ->where('a.is_delete', 0)
                        ->when($this->start, function ($query, $start) {
                            return $query->whereDate('b.tanggal', '>=', $start);
                        })
                        ->when($this->end, function ($query, $end) {
                            return $query->whereDate('b.tanggal', '<=', $end);
                        })
                        ->when($this->id, function ($query, $id) {
                            return $query->where('b.id_kapal', $id);
                        })
                        ->get();

        return view('export.lappermintaan',compact('data'));
    }
}
