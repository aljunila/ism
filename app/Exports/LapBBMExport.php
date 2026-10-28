<?php

namespace App\Exports;

use App\Models\LogBBM;
use App\Models\BBMKapal;
use App\Models\Kapal;
use App\Models\Trip;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class LapBBMExport implements FromView
{
    protected $id;
    protected $start;
    protected $end;
    protected $smonth;

    public function __construct($id, $start, $end, $smonth)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->smonth = $smonth;
    }

   public function view(): View
{
    $dates = LogBBM::query()
        ->when($this->start, function ($query, $start) {
            return $query->whereDate('tanggal', '>=', $start);
        })
        ->when($this->end, function ($query, $end) {
            return $query->whereDate('tanggal', '<=', $end);
        })
        ->when($this->smonth, function ($query, $smonth) {
            return $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$smonth]);
        })
        ->where('is_delete', 0)
        ->orderBy('tanggal', 'ASC')
        ->pluck('tanggal')
        ->unique()
        ->values(); // Reset index collection

    $fleet = Kapal::where('status', 'A')
        ->when($this->id, function ($query, $id) {
            return $query->where('id', $id);
        })
        ->orderBy('pemilik', 'ASC')
        ->get();

    $fleetIds = $fleet->pluck('id');

    $allLogBBM = LogBBM::whereIn('tanggal', $dates)
        ->where('is_delete', 0)
        ->whereIn('id_kapal', $fleetIds)
        ->get();

    $allTrip = Trip::whereIn('tanggal', $dates)
        ->where('is_delete', 0)
        ->whereIn('id_kapal', $fleetIds)
        ->get();

    $date = $dates->map(function ($tanggalVal) use ($fleet, $allLogBBM, $allTrip) {
        
        $dt = new \stdClass();
        $dt->tanggal = $tanggalVal;

        $dt->kapal = $fleet->map(function ($kp) use ($tanggalVal, $allLogBBM, $allTrip) {
            
            $logsKapal = $allLogBBM->where('id_kapal', $kp->id)
                                 ->where('tanggal', $tanggalVal);

            $tripsKapal = $allTrip->where('id_kapal', $kp->id)
                                ->where('tanggal', $tanggalVal);

            $clonedKp = clone $kp;

            $clonedKp->saldo_awal = $logsKapal->sortBy('id')->first();
            $clonedKp->saldo_akhir = $logsKapal->sortByDesc('id')->first();
            $clonedKp->trip = $tripsKapal->count();
            $clonedKp->me = $logsKapal->where('jns_pakai', 'ME')->first();
            $clonedKp->ae = $logsKapal->where('jns_pakai', 'AE')->first();
            $clonedKp->tambah = $logsKapal->where('id_jenis', 2)->first();

            return $clonedKp;
        });

        return $dt;
    })->filter(function ($dt) {
        // Hanya ambil tanggal yang kapalnya memiliki data/tidak kosong (opsional)
        return $dt->kapal->isNotEmpty();
    });

    return view('export.lapbbm', compact('date', 'fleet'));
}
}
