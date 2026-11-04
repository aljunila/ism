<?php

namespace App\Exports;

use App\Models\LogBBM;
use App\Models\BBMKapal;
use App\Models\Kapal;
use App\Models\Trip;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Carbon\Carbon;

class LapyearBBMExport implements FromView
{
    protected $id;
    protected $year;

    public function __construct($id, $year)
    {
        $this->id = $id;
        $this->year = $year;
    }

    public function view(): View
    {
        // Semua kapal aktif
        $fleet = Kapal::where('status', 'A')
            ->when($this->id, function ($query, $id) {
                return $query->where('id', $id);
            })
            ->orderBy('pemilik', 'ASC')
            ->get();

        $fleetIds = $fleet->pluck('id');

        // Ambil log BBM dalam tahun yang dipilih
        $logs = LogBBM::whereIn('id_kapal', $fleetIds)
            ->whereYear('tanggal', $this->year)
            ->where('is_delete', 0)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->groupBy('id_kapal');

        // Ambil seluruh trip
        $trips = Trip::whereIn('id_kapal', $fleetIds)
            ->whereYear('tanggal', $this->year)
            ->where('is_delete', 0)
            ->get()
            ->groupBy('id_kapal');

        $months = collect();

        for ($m = 1; $m <= 12; $m++) {

            $bulan = Carbon::create($this->year, $m, 1);

            $dataKapal = $fleet->map(function ($kp) use ($logs, $trips, $m) {

                $logKapal = ($logs->get($kp->id) ?? collect())
                    ->filter(function ($log) use ($m) {
                        return Carbon::parse($log->tanggal)->month == $m;
                    })
                    ->sortBy('id')
                    ->values();

                $tripKapal = ($trips->get($kp->id) ?? collect())
                    ->filter(function ($trip) use ($m) {
                        return Carbon::parse($trip->tanggal)->month == $m;
                    });

                // Saldo awal dan akhir bulanan
                $saldoAwal = $logKapal->first();
                $saldoAkhir = $logKapal->last();

                // Pisahkan berdasarkan status
                $operation = $logKapal->where('status', 1);
                $anchorage = $logKapal->where('status', 2);

                // Hitung trip berdasarkan status harian
                $tripOperation = 0;
                $tripAnchorage = 0;

                $tanggalLog = $logKapal->groupBy(function ($log) {
                    return Carbon::parse($log->tanggal)->format('Y-m-d');
                });

                foreach ($tanggalLog as $tanggal => $logsTanggal) {

                    $status = $logsTanggal->first()->status;

                    $jumlahTrip = $tripKapal->filter(function ($trip) use ($tanggal) {
                        return Carbon::parse($trip->tanggal)->format('Y-m-d') == $tanggal;
                    })->count();

                    if ($status == 1) {
                        $tripOperation += $jumlahTrip;
                    } else {
                        $tripAnchorage++;
                    }
                }

                // Helper perhitungan
                $hitung = function ($data) {
                    $pakai = $data->where('id_jenis', 1);
                    $tambah = $data->where('id_jenis', 2);

                    return [
                        'me' => $pakai->sum('me'),
                        'jam_me' => $pakai->sum('jam_me'),
                        'ae' => $pakai->sum('ae'),
                        'jam_ae' => $pakai->sum('jam_ae'),
                        'pakai' => $pakai->sum('jumlah'),
                        'tambah' => $tambah->sum('jumlah'),
                    ];
                };

                $op = $hitung($operation);
                $an = $hitung($anchorage);

                // Rata-rata ME dan AE berdasarkan jam operasional
                $op['rata_me'] = $op['jam_me'] > 0
                    ? round($op['me'] / $op['jam_me'], 2)
                    : 0;

                $op['rata_ae'] = $op['jam_ae'] > 0
                    ? round($op['ae'] / $op['jam_ae'], 2)
                    : 0;

                $an['rata_me'] = $an['jam_me'] > 0
                    ? round($an['me'] / $an['jam_me'], 2)
                    : 0;

                $an['rata_ae'] = $an['jam_ae'] > 0
                    ? round($an['ae'] / $an['jam_ae'], 2)
                    : 0;

                return [
                    'id' => $kp->id,
                    'nama' => $kp->nama,

                    'saldo_awal' => $saldoAwal ? $saldoAwal->saldo_awal : 0,
                    'saldo_akhir' => $saldoAkhir ? $saldoAkhir->saldo_akhir : 0,

                    'operation' => array_merge($op, [
                        'trip' => $tripOperation,
                        'rata' => $tripOperation > 0
                            ? round($op['pakai'] / $tripOperation)
                            : 0,
                    ]),

                    'anchorage' => array_merge($an, [
                        'trip' => $tripAnchorage,
                        'rata' => $tripAnchorage > 0
                            ? round($an['pakai'] / $tripAnchorage)
                            : 0,
                    ]),
                ];
            });

            // Lewati bulan yang tidak memiliki data BBM
            if ($logs->flatten()->contains(function ($log) use ($m) {
                return Carbon::parse($log->tanggal)->month == $m;
            })) {
                $months->push([
                    'bulan' => $bulan->translatedFormat('F Y'),
                    'kapal' => $dataKapal,
                ]);
            }
        }

        return view('export.lapyearbbm', [
            'months' => $months,
            'fleet' => $fleet,
            'year' => $this->year,
        ]);
    }
}
