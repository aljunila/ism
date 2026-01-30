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

        foreach ($data as $id => $row) {
            $kend = Kendaraan::where('id', $id)->first();
            $grandTotal += $row['total'];

            $result[] = [
                'gol'     => $kend->kode,
                'jumlah'       => $row['jumlah'],
                'nominal'      => $row['nominal'],
                'total'        => $row['total']
            ];
        }

        return view('export.tripbyid', [
            'rows'       => $result,
            'grandTotal' => $grandTotal,
            'trip'       => $trip
        ]);
    }
}

