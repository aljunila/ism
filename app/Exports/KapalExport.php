<?php

namespace App\Exports;

use App\Models\Perusahaan;
use App\Models\Kapal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class KapalExport implements FromView
{
    protected $id_perusahaan;

    public function __construct($id_perusahaan)
    {
        $this->id_perusahaan = $id_perusahaan;
    }

   public function view(): View
    {
        return view('export.kapal', [
            'data' => DB::table('kapal')
                ->leftjoin('perusahaan', 'perusahaan.id', '=', 'kapal.pemilik')
                ->select('kapal.*', 'perusahaan.nama as perusahaan')
                ->where('kapal.status', 'A')
                ->when($this->id_perusahaan, function($query, $id) {
                    return $query->where('perusahaan.id', $id);
                })
                ->get()
        ]);
    }
}
