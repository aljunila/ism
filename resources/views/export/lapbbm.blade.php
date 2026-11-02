@php
    $styleTh = "background-color: #e8e524; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp
<table border="1">
<tr>
    <th colspan="10"><strong>LAPORAN DATA BBM</strong></th>
</tr>
<tr>
    <td style="{{$styleTd}}" colspan="10"></td>
</tr>
<tr>
    <th style="{{$styleTh}}" rowspan="2">Tanggal</th>
    @foreach($fleet as $f)
        <th style="{{$styleTh}}" colspan="9">{{$f->nama}}</th>
    @endforeach
</tr>
<tr>
    @foreach($fleet as $f)
        <th style="{{$styleTh}}">Status</th>
        <th style="{{$styleTh}}">Saldo Awal</th>
        <th style="{{$styleTh}}">Pemakaian (ME)</th>
        <th style="{{$styleTh}}">Pemakaian (AE)</th>
        <th style="{{$styleTh}}">Total Pemakaian</th>
        <th style="{{$styleTh}}">Panambahan</th>
        <th style="{{$styleTh}}">Saldo Akhir</th>
        <th style="{{$styleTh}}">Jumlah Trip</th>
        <th style="{{$styleTh}}">Rata-Rata</th>
    @endforeach
</tr>
@php
    $rekap = [];

    foreach ($fleet as $f) {
        $rekap[$f->id] = [
            'me' => 0,
            'ae' => 0,
            'pakai' => 0,
            'tambah' => 0,
            'trip' => 0,

            'me_1' => 0,
            'ae_1' => 0,
            'pakai_1' => 0,
            'trip_1' => 0,

            'me_2' => 0,
            'ae_2' => 0,
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
            $ae = $kp->pakai ? $kp->pakai->ae : 0;
            $tot_pakai = $kp->pakai ? $kp->pakai->jumlah : 0;
            $trip = $kp->trip ?? 0;
            $tambah = $kp->tambah ? $kp->tambah->jumlah : 0;

            $rata = $trip > 0 ? ($tot_pakai / $trip) : 0;

            $statuskapal = $kp->saldo_awal ? $kp->saldo_awal->status : null;

            $rekap[$kp->id]['me'] += $me;
            $rekap[$kp->id]['ae'] += $ae;
            $rekap[$kp->id]['pakai'] += $tot_pakai;
            $rekap[$kp->id]['tambah'] += $tambah;
            $rekap[$kp->id]['trip'] += $trip;

            if ($statuskapal == 1) {
                // In Operation
                $rekap[$kp->id]['me_1'] += $me;
                $rekap[$kp->id]['ae_1'] += $ae;
                $rekap[$kp->id]['pakai_1'] += $tot_pakai;
                $rekap[$kp->id]['trip_1'] += $trip;
            } else {
                // Anchorage
                $rekap[$kp->id]['me_2'] += $me;
                $rekap[$kp->id]['ae_2'] += $ae;
                $rekap[$kp->id]['pakai_2'] += $tot_pakai;

                // Mengikuti perhitungan sebelumnya
                $rekap[$kp->id]['trip_2']++;
            }
        @endphp

        <td style="{{ $styleTd }}">{{ ($kp->saldo_awal && $kp->saldo_awal->status == 1) ? 'In Operation' : 'Anchorage' }}</td>
        <td style="{{ $styleTd }}">{{ $kp->saldo_awal ? $kp->saldo_awal->saldo_awal : 0 }}</td>
        <td style="{{ $styleTd }}">{{ $me }}</td>
        <td style="{{ $styleTd }}">{{ $ae }}</td>
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
    <th style="{{$styleTh}}" rowspan="3">Total</th>

    @foreach($fleet as $f)
        @php
            $r = $rekap[$f->id];

            $rata = $r['trip'] > 0
                ? round($r['pakai'] / $r['trip'])
                : 0;
        @endphp

        <th style="{{$styleTh}}">Overall</th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}">{{ $r['me'] }}</th>
        <th style="{{$styleTh}}">{{ $r['ae'] }}</th>
        <th style="{{$styleTh}}">{{ $r['pakai'] }}</th>
        <th style="{{$styleTh}}">{{ $r['tambah'] }}</th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}">{{ $r['trip'] }}</th>
        <th style="{{$styleTh}}">{{ $rata }}</th>
    @endforeach
</tr>

<tr>
    @foreach($fleet as $f)
        @php
            $r = $rekap[$f->id];

            $rata = $r['trip_1'] > 0
                ? round($r['pakai_1'] / $r['trip_1'])
                : 0;
        @endphp

        <th style="{{$styleTh}}">In Operation</th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}">{{ $r['me_1'] }}</th>
        <th style="{{$styleTh}}">{{ $r['ae_1'] }}</th>
        <th style="{{$styleTh}}">{{ $r['pakai_1'] }}</th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}">{{ $r['trip_1'] }}</th>
        <th style="{{$styleTh}}">{{ $rata }}</th>
    @endforeach
</tr>

<tr>
    @foreach($fleet as $f)
        @php
            $r = $rekap[$f->id];

            $rata = $r['trip_2'] > 0
                ? round($r['pakai_2'] / $r['trip_2'])
                : 0;
        @endphp

        <th style="{{$styleTh}}">Anchorage</th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}">{{ $r['me_2'] }}</th>
        <th style="{{$styleTh}}">{{ $r['ae_2'] }}</th>
        <th style="{{$styleTh}}">{{ $r['pakai_2'] }}</th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}"></th>
        <th style="{{$styleTh}}">{{ $r['trip_2'] }}</th>
        <th style="{{$styleTh}}">{{ $rata }}</th>
    @endforeach
</tr>
</table>