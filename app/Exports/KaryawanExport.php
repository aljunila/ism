<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class KaryawanExport implements FromView
{
    protected $id_perusahaan;
    protected $id_kapal;

    public function __construct($id_perusahaan, $id_kapal)
    {
        $this->id_perusahaan = $id_perusahaan;
        $this->id_kapal = $id_kapal;
    }

   public function view(): View
    {
        return view('export.karyawan', [
            'data' =>DB::table('karyawan')
                ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id')
                ->leftJoin('perusahaan', 'perusahaan.id', '=', 'karyawan.id_perusahaan')
                ->leftJoin('kapal', 'kapal.id', '=', 'karyawan.id_kapal')
                ->leftJoin('status_ptkp', 'status_ptkp.id', '=', 'karyawan.status_ptkp')
                ->select(
                    'karyawan.*',
                    'kapal.nama as kapal',
                    'jabatan.nama as jabatan',
                    'perusahaan.nama as perusahaan',
                    'status_ptkp.kode'
                )
                ->where('karyawan.resign', 'N')
                ->where('karyawan.status','A')
                ->when($this->id_perusahaan, function($query, $id) {
                    return $query->where('perusahaan.id', $id);
                })
                ->when($this->id_kapal, function($query, $id) {
                    return $query->where('kapal.id', $id);
                })
                ->get()
        ]);
    }
}
