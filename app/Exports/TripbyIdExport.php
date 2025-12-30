<?php

namespace App\Exports;

use App\Models\Trip;
use App\Models\BiayaPenumpang;
use App\Models\Kendaraan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TripbyIdExport implements FromView
{
    protected $id_trip;
    protected $no = 0;

    public function __construct($id_trip)
    {
        $this->id_trip = $id_trip;
    }

    public function view(): View
    {
        $trip = Trip::findOrFail($this->id_trip);
        $kelas = $trip->get_kapal()->gol;
        $data = $trip->data; // ARRAY karena $casts

        $result = [];
        $grandTotal = 0;

        foreach ($data as $id => $jumlah) {
            $kend = Kendaraan::where('id', $id)->first();
            $biaya = BiayaPenumpang::where('id_pelabuhan', $trip->id_pelabuhan)
                ->where('id_kendaraan', $id)
                ->where('kelas', $kelas)
                ->get()
                ->keyBy('id_kendaraan');
            $nominal = $biaya[$id]->nominal ?? 0;
            $total   = $jumlah * $nominal;

            $grandTotal += $total;

            $result[] = [
                'gol'     => $kend->kode,
                'jumlah'  => $jumlah,
                'nominal' => $nominal,
                'total'   => $total,
            ];
        }

        return view('export.tripbyid', [
            'rows'       => $result,
            'grandTotal' => $grandTotal,
            'trip'       => $trip
        ]);
    }
}

