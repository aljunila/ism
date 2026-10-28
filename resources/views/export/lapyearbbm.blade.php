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
    <th style="{{$styleTh}}" rowspan="2">Bulan</th>
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
    $tot_me_1 = 0;    $tot_ae_1 = 0;    $oa_pakai_1 = 0;   $tot_trip_1 = 0;    $tot_rata_1 = 0;
    $tot_me_2 = 0;    $tot_ae_2 = 0;    $oa_pakai_2 = 0;   $tot_trip_2 = 0;    $tot_rata_2 = 0;
    $tot_tambah = 0;
@endphp

@foreach($date as $dt)
    @foreach($dt->kapal as $kp)
        @php
            $me1 = $kp->sum_me_1 ?? 0;
            $ae1 = $kp->sum_ae_1 ?? 0;
            $tot_pakai1 = $me1 + $ae1;
            $trip = $kp->trip ?? 0;
            $rata = ($trip > 0) ? ($tot_pakai1 / $trip) : 0;
            $tot_me_1 += $me1;
            $tot_ae_1 += $ae1;
            $oa_pakai_1 += $tot_pakai1;
            $tot_trip_1 +=  $trip;
            $tot_rata_1 = round($oa_pakai_1 / $tot_trip_1);

            $me2 = $kp->sum_me_2 ?? 0;
            $ae2 = $kp->sum_ae_2 ?? 0;
            $tot_pakai2 = $me2 + $ae2;
            $count = $kp->count ?? 0;
            $rata2 = ($count > 0) ? ($tot_pakai2 / $count) : 0;
            $tot_me_2 += $me2;
            $tot_ae_2 += $ae2;
            $oa_pakai_2 += $tot_pakai2;
            $tot_trip_2 +=  $count;
            $tot_rata_2 = round($oa_pakai_2 / $tot_trip_2);

            $oa_rata = round(($oa_pakai_2 + $oa_pakai_1) / $tot_trip_1);
            $tot_tambah += $kp->sum_tambah;
        @endphp
    <tr>
        <!-- Kolom Tanggal di awal baris -->
        <td style="{{ $styleTd }}" rowspan="2">
            {{ $dt->bulan ? \Carbon\Carbon::parse($dt->bulan . '-01')->format('F Y') : '' }}
        </td>
        <td style="{{ $styleTd }}">In Operation</td>
        <td style="{{ $styleTd }}" rowspan="2">{{ $kp->saldo_awal ? $kp->saldo_awal->saldo_awal : 0 }}</td>
        <td style="{{ $styleTd }}">{{ $me1 }}</td>
        <td style="{{ $styleTd }}">{{ $ae1 }}</td>
        <td style="{{ $styleTd }}">{{ $tot_pakai1 }}</td>
        <td style="{{ $styleTd }}" rowspan="2">{{ $kp->sum_tambah }}</td>
        <td style="{{ $styleTd }}" rowspan="2">{{ $kp->saldo_akhir ? $kp->saldo_akhir->saldo_akhir : 0 }}</td>
        <td style="{{ $styleTd }}">{{ $trip }}</td>
        <td style="{{ $styleTd }}">{{ round($rata) }}</td>
    </tr>
    <tr>
        <td style="{{ $styleTd }}">Anchorage</td>
        <td style="{{ $styleTd }}">{{ $me2 }}</td>
        <td style="{{ $styleTd }}">{{ $ae2 }}</td>
        <td style="{{ $styleTd }}">{{ $tot_pakai2 }}</td>
        <td style="{{ $styleTd }}">{{ $count }} </td>
        <td style="{{ $styleTd }}">{{ round($rata2) }}</td>
    </tr>
    @endforeach
@endforeach
    <tr>
    <th style="{{$styleTh}}" rowspan="3">Total</th>
    <th style="{{$styleTh}}">Overall</th>
    <th style="{{$styleTh}}" rowspan="3"></th>
    <th style="{{$styleTh}}">{{ $tot_me_1 + $tot_me_2 }}</th>
    <th style="{{$styleTh}}">{{ $tot_ae_1 + $tot_ae_2 }}</th>
    <th style="{{$styleTh}}">{{ $oa_pakai_1 + $oa_pakai_2 }}</th>
    <th style="{{$styleTh}}" rowspan="3">{{ $tot_tambah }}</th>
    <th style="{{$styleTh}}" rowspan="3"></th>
    <th style="{{$styleTh}}">{{ $tot_trip_1 }}</th>
    <th style="{{$styleTh}}">{{ $oa_rata }}</th>
</tr>
<tr>
    <th style="{{$styleTh}}">In Operation</th>
    <th style="{{$styleTh}}">{{ $tot_me_1 }}</th>
    <th style="{{$styleTh}}">{{ $tot_ae_1 }}</th>
    <th style="{{$styleTh}}">{{ $oa_pakai_1 }}</th>
    <th style="{{$styleTh}}">{{ $tot_trip_1 }}</th>
    <th style="{{$styleTh}}">{{ $tot_rata_1 }}</th>
</tr>
<tr>
    <th style="{{$styleTh}}">Anchorage</th>
    <th style="{{$styleTh}}">{{ $tot_me_2 }}</th>
    <th style="{{$styleTh}}">{{ $tot_ae_2 }}</th>
    <th style="{{$styleTh}}">{{ $oa_pakai_2 }}</th>
    <th style="{{$styleTh}}">{{ $tot_trip_2 }}</th>
    <th style="{{$styleTh}}">{{ $tot_rata_2 }}</th>
</tr>
</table>