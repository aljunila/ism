<?php

namespace App\Exports;

use App\Models\LogBBM;
use App\Models\BBMKapal;
use App\Models\Kapal;
use App\Models\Trip;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class LapyearBBMExport implements FromView
{
    protected $id;
    protected $year;

    public function __construct($id, $year)
    {
        $this->id = $id;
        $this->start = $year;
    }

   public function view(): View
    {
        // 1. Ambil daftar bulan unik yang ada di database untuk tahun tersebut
        $months = LogBBM::query()
            ->when(isset($this->year), function ($query) {
                return $query->whereYear('tanggal', $this->year);
            })
            ->where('is_delete', 0)
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->orderBy('bulan', 'ASC')
            ->pluck('bulan')
            ->unique()
            ->values();

        // 2. Ambil daftar kapal (fleet) aktif
        $fleet = Kapal::where('status', 'A')
            ->when($this->id, function ($query, $id) {
                return $query->where('id', $id);
            })
            ->orderBy('pemilik', 'ASC')
            ->get();

        $fleetIds = $fleet->pluck('id');

        // 3. Ambil SEMUA LogBBM dan Trip dalam rentang tahun tersebut sekaligus
        $allLogBBM = LogBBM::query()
            ->when(isset($this->year), function ($query) {
                return $query->whereYear('tanggal', $this->year);
            })
            ->where('is_delete', 0)
            ->whereIn('id_kapal', $fleetIds)
            ->get();

        $allTrip = Trip::query()
            ->when(isset($this->year), function ($query) {
                return $query->whereYear('tanggal', $this->year);
            })
            ->where('is_delete', 0)
            ->whereIn('id_kapal', $fleetIds)
            ->get();

        // 4. Mapping data per bulan (Group by Month)
        $date = $months->map(function ($bulanVal) use ($fleet, $allLogBBM, $allTrip) {
            
            $dt = new \stdClass();
            $dt->bulan = $bulanVal; // Format: "2025-10"

            $dt->kapal = $fleet->map(function ($kp) use ($bulanVal, $allLogBBM, $allTrip) {
                
                // Filter log khusus kapal ini dan di bulan tersebut (Y-m)
                $logsKapalBulan = $allLogBBM->filter(function ($log) use ($kp, $bulanVal) {
                    return $log->id_kapal == $kp->id 
                        && \Carbon\Carbon::parse($log->tanggal)->format('Y-m') === $bulanVal;
                });

                // Filter trip khusus kapal ini dan di bulan tersebut
                $tripsKapalBulan = $allTrip->filter(function ($trip) use ($kp, $bulanVal) {
                    return $trip->id_kapal == $kp->id 
                        && \Carbon\Carbon::parse($trip->tanggal)->format('Y-m') === $bulanVal;
                });

                $clonedKp = clone $kp;

                // Saldo Awal (Ambil data paling awal di bulan tersebut)
                $clonedKp->saldo_awal = $logsKapalBulan->sortBy('id')->first();

                // Saldo Akhir (Ambil data paling akhir di bulan tersebut)
                $clonedKp->saldo_akhir = $logsKapalBulan->sortByDesc('id')->first();

                // Total Trip dalam sebulan
                $clonedKp->trip = $tripsKapalBulan->count();

                // SUM (Jumlahkan) ME, AE, dan Tambah (id_jenis = 2) selama sebulan
                $clonedKp->sum_me_1 = $logsKapalBulan->where('jns_pakai', 'ME')->where('status',1)->sum('jumlah');
                $clonedKp->sum_ae_1 = $logsKapalBulan->where('jns_pakai', 'AE')->where('status',1)->sum('jumlah');

                $clonedKp->sum_me_2 = $logsKapalBulan->where('jns_pakai', 'ME')->where('status',2)->sum('jumlah');
                $clonedKp->sum_ae_2 = $logsKapalBulan->where('jns_pakai', 'AE')->where('status',2)->sum('jumlah');
                $clonedKp->sum_tambah = $logsKapalBulan->where('id_jenis', 2)->sum('jumlah');
                $clonedKp->count = count($logsKapalBulan->where('status',2));

                return $clonedKp;
            });

            return $dt;
        })->filter(function ($dt) {
            return $dt->kapal->isNotEmpty();
        });

        return view('export.lapyearbbm', compact('date', 'fleet'));
    }
}
