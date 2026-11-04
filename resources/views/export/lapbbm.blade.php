@php
    $styleTh = "background-color: #e8e524; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp
<table border="1">
    <tr>
        <th colspan="{{ 1 + ($fleet->count() * 13) }}">
            <strong>LAPORAN DATA BBM</strong>
        </th>
    </tr>
    <tr>
        <td style="{{ $styleTd }}" colspan="{{ 1 + ($fleet->count() * 13) }}"></td>
    </tr>
    <tr>
        <th style="{{ $styleTh }}" rowspan="3">Tanggal</th>

        @foreach($fleet as $f)
            <th style="{{ $styleTh }}" colspan="13">
                {{ $f->nama }}
            </th>
        @endforeach
    </tr>
    <tr>
    @foreach($fleet as $f)
        <th style="{{$styleTh}}" rowspan="2">Status</th>
        <th style="{{$styleTh}}" rowspan="2">Saldo Awal</th>
        <th style="{{$styleTh}}" colspan="3">Pemakaian ME</th>
        <th style="{{$styleTh}}" colspan="3">Pemakaian AE</th>
        <th style="{{$styleTh}}" rowspan="2">Total Pemakaian</th>
        <th style="{{$styleTh}}" rowspan="2">Panambahan</th>
        <th style="{{$styleTh}}" rowspan="2">Saldo Akhir</th>
        <th style="{{$styleTh}}" rowspan="2">Jumlah Trip</th>
        <th style="{{$styleTh}}" rowspan="2">Rata-Rata</th>
    @endforeach
    </tr>
    <tr>
    @foreach($fleet as $f)
        <th style="{{$styleTh}}">Jumlah</th>
        <th style="{{$styleTh}}">Jam Operasi</th>
        <th style="{{$styleTh}}">Rata-Rata</th>
        <th style="{{$styleTh}}">Jumlah</th>
        <th style="{{$styleTh}}">Jam Operasi</th>
        <th style="{{$styleTh}}">Rata-Rata</th>
    @endforeach
    </tr>
    @php
        $rekap = [];

        foreach ($fleet as $f) {
            $rekap[$f->id] = [
                'me' => 0,
                'ae' => 0,
                'jam_me' => 0,
                'jam_ae' => 0,
                'pakai' => 0,
                'tambah' => 0,
                'trip' => 0,

                'me_1' => 0,
                'ae_1' => 0,
                'jam_me_1' => 0,
                'jam_ae_1' => 0,
                'pakai_1' => 0,
                'trip_1' => 0,

                'me_2' => 0,
                'ae_2' => 0,
                'jam_me_2' => 0,
                'jam_ae_2' => 0,
                'pakai_2' => 0,
                'trip_2' => 0,
            ];
        }
    @endphp

@foreach($date as $dt)
    <tr>
        <!-- Kolom Tanggal di awal baris -->
        <td style="{{ $styleTd }}">
            {{ $dt->tanggal ? \Carbon\Carbon::parse($dt->tanggal)->format('d-m-Y') : '' }}
        </td>
        
        @foreach($dt->kapal as $kp)
            @php
                $me = $kp->pakai ? $kp->pakai->me : 0;
                $jam_me = $kp->pakai ? $kp->pakai->jam_me : 0;
                $ae = $kp->pakai ? $kp->pakai->ae : 0;
                $jam_ae = $kp->pakai ? $kp->pakai->jam_ae : 0;
                $tot_pakai = $kp->pakai ? $kp->pakai->jumlah : 0;
                $trip = $kp->trip ?? 0;
                $tambah = $kp->tambah ? $kp->tambah->jumlah : 0;

                $rata = $trip > 0 ? ($tot_pakai / $trip) : 0;
                $rata_jam_me = $jam_me > 0 ? ($me / $jam_me) : 0;
                $rata_jam_ae = $jam_ae > 0 ? ($ae / $jam_ae) : 0;

                $statuskapal = $kp->saldo_awal ? $kp->saldo_awal->status : null;

                $rekap[$kp->id]['me'] += $me;
                $rekap[$kp->id]['jam_me'] += $jam_me;
                $rekap[$kp->id]['ae'] += $ae;
                $rekap[$kp->id]['jam_ae'] += $jam_ae;
                $rekap[$kp->id]['pakai'] += $tot_pakai;
                $rekap[$kp->id]['tambah'] += $tambah;
                $rekap[$kp->id]['trip'] += $trip;

                if ($statuskapal == 1) {
                    $rekap[$kp->id]['me_1'] += $me;
                    $rekap[$kp->id]['jam_me_1'] += $jam_me;
                    $rekap[$kp->id]['ae_1'] += $ae;
                    $rekap[$kp->id]['jam_ae_1'] += $jam_ae;
                    $rekap[$kp->id]['pakai_1'] += $tot_pakai;
                    $rekap[$kp->id]['trip_1'] += $trip;

                } elseif ($statuskapal == 2) {
                    $rekap[$kp->id]['me_2'] += $me;
                    $rekap[$kp->id]['jam_me_2'] += $jam_me;
                    $rekap[$kp->id]['ae_2'] += $ae;
                    $rekap[$kp->id]['jam_ae_2'] += $jam_ae;
                    $rekap[$kp->id]['pakai_2'] += $tot_pakai;

                    // Jumlah hari Anchorage
                    $rekap[$kp->id]['trip_2']++;
                }
            @endphp

            <td style="{{ $styleTd }}">{{ ($kp->saldo_awal && $kp->saldo_awal->status == 1) ? 'In Operation' : 'Anchorage' }}</td>
            <td style="{{ $styleTd }}">{{ $kp->saldo_awal ? $kp->saldo_awal->saldo_awal : 0 }}</td>
            <td style="{{ $styleTd }}">{{ $me }}</td>
            <td style="{{ $styleTd }}">{{ $jam_me }}</td>
            <td style="{{ $styleTd }}">{{ round($rata_jam_me) }}</td>
            <td style="{{ $styleTd }}">{{ $ae }}</td>
            <td style="{{ $styleTd }}">{{ $jam_ae }}</td>
            <td style="{{ $styleTd }}">{{ round($rata_jam_ae) }}</td>
            <td style="{{ $styleTd }}">{{ $tot_pakai }}</td>
            <td style="{{ $styleTd }}">{{ $kp->tambah ? $kp->tambah->jumlah : 0 }}</td>
            <td style="{{ $styleTd }}">{{ $kp->saldo_akhir ? $kp->saldo_akhir->saldo_akhir : 0 }}</td>
            <td style="{{ $styleTd }}">{{ $trip }}</td>
            <td style="{{ $styleTd }}">{{ round($rata) }}</td>
        @endforeach
        </tr>
        @endforeach
        @foreach(['overall', 'operation', 'anchorage'] as $jenis => $label)
        @endforeach

    <tr>
    <th style="{{ $styleTh }}" rowspan="3">Total</th>

    @foreach($fleet as $f)
        @php
            $r = $rekap[$f->id];

            $rata = $r['trip'] > 0
                ? round($r['pakai'] / $r['trip'])
                : 0;

            $rata_jam_me = $r['jam_me'] > 0
                ? round($r['me'] / $r['jam_me'], 2)
                : 0;

            $rata_jam_ae = $r['jam_ae'] > 0
                ? round($r['ae'] / $r['jam_ae'], 2)
                : 0;
        @endphp

        <th style="{{ $styleTh }}">Overall</th>
        <th style="{{ $styleTh }}" rowspan="3"></th>
        <th style="{{ $styleTh }}">{{ $r['me'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['jam_me'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata_jam_me }}</th>
        <th style="{{ $styleTh }}">{{ $r['ae'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['jam_ae'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata_jam_ae }}</th>
        <th style="{{ $styleTh }}">{{ $r['pakai'] }}</th>
        <th style="{{ $styleTh }}" rowspan="3">{{ $r['tambah'] }}</th>
        <th style="{{ $styleTh }}" rowspan="3"></th>
        <th style="{{ $styleTh }}">{{ $r['trip'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata }}</th>
    @endforeach
    </tr>
    <tr>
    @foreach($fleet as $f)
        @php
            $r = $rekap[$f->id];

            $rata = $r['trip_1'] > 0
                ? round($r['pakai_1'] / $r['trip_1'])
                : 0;

            $rata_jam_me = $r['jam_me_1'] > 0
                ? round($r['me_1'] / $r['jam_me_1'], 2)
                : 0;

            $rata_jam_ae = $r['jam_ae_1'] > 0
                ? round($r['ae_1'] / $r['jam_ae_1'], 2)
                : 0;
        @endphp

        <th style="{{ $styleTh }}">In Operation</th>
        <th style="{{ $styleTh }}">{{ $r['me_1'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['jam_me_1'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata_jam_me }}</th>
        <th style="{{ $styleTh }}">{{ $r['ae_1'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['jam_ae_1'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata_jam_ae }}</th>
        <th style="{{ $styleTh }}">{{ $r['pakai_1'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['trip_1'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata }}</th>
    @endforeach
    </tr>
    <tr>
    @foreach($fleet as $f)
        @php
            $r = $rekap[$f->id];

            $rata = $r['trip_2'] > 0
                ? round($r['pakai_2'] / $r['trip_2'])
                : 0;

            $rata_jam_me = $r['jam_me_2'] > 0
                ? round($r['me_2'] / $r['jam_me_2'], 2)
                : 0;

            $rata_jam_ae = $r['jam_ae_2'] > 0
                ? round($r['ae_2'] / $r['jam_ae_2'], 2)
                : 0;
        @endphp

        <th style="{{ $styleTh }}">Anchorage</th>
        <th style="{{ $styleTh }}">{{ $r['me_2'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['jam_me_2'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata_jam_me }}</th>
        <th style="{{ $styleTh }}">{{ $r['ae_2'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['jam_ae_2'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata_jam_ae }}</th>
        <th style="{{ $styleTh }}">{{ $r['pakai_2'] }}</th>
        <th style="{{ $styleTh }}">{{ $r['trip_2'] }}</th>
        <th style="{{ $styleTh }}">{{ $rata }}</th>
    @endforeach
    </tr>
</table>