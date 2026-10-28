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
    $tot_me = 0;    $tot_ae = 0;    $oa_pakai = 0;    $tot_tambah = 0;    $tot_trip = 0;    $tot_rata = 0;
    $tot_me_1 = 0;    $tot_ae_1 = 0;    $oa_pakai_1 = 0;   $tot_trip_1 = 0;    $tot_rata_1 = 0;
    $oa_pakai_2 = 0;   $tot_trip_2 = 0;    $tot_rata_2 = 0;
@endphp

@foreach($date as $dt)
<tr>
    <!-- Kolom Tanggal di awal baris -->
    <td style="{{ $styleTd }}">
        {{ $dt->tanggal ? \Carbon\Carbon::parse($dt->tanggal)->format('d-m-Y') : '' }}
    </td>

    @foreach($dt->kapal as $kp)
        @php
            $me = $kp->me ? $kp->me->jumlah : 0;
            $ae = $kp->ae ? $kp->ae->jumlah : 0;
            $tot_pakai = $me + $ae;
            $trip = $kp->trip ?? 0;
            // Gunakan pembagian dan cegah division by zero
            $rata = ($trip > 0) ? ($tot_pakai / $trip) : 0;
            $tot_me += $me;
            $tot_ae += $ae;
            $oa_pakai += $tot_pakai;
            $tot_tambah += $kp->tambah ? $kp->tambah->jumlah : 0;
            $tot_trip += $trip;
            $tot_rata = round($oa_pakai / $tot_trip);

            $statuskapal = $kp->saldo_awal ? $kp->saldo_awal->status : null;
            if($statuskapal==1) {
                $tot_me_1 += $me;
                $tot_ae_1 += $ae;
                $oa_pakai_1 += $tot_pakai;
                $tot_trip_1 +=  $trip;
                $tot_rata_1 = round($oa_pakai_1 / $tot_trip_1);
            } else {
                $oa_pakai_2 += $tot_pakai;
                $tot_trip_2 = $tot_trip_2 + 1;
                $tot_rata_2 = round($oa_pakai_2 / $tot_trip_2);
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
<tr>
    <th style="{{$styleTh}}" rowspan="3">Total</th>
    <th style="{{$styleTh}}">Overall</th>
    <th style="{{$styleTh}}"></th>
    <th style="{{$styleTh}}">{{ $tot_me }}</th>
    <th style="{{$styleTh}}">{{ $tot_ae }}</th>
    <th style="{{$styleTh}}">{{ $oa_pakai }}</th>
    <th style="{{$styleTh}}">{{ $tot_tambah }}</th>
    <th style="{{$styleTh}}"> </th>
    <th style="{{$styleTh}}">{{ $tot_trip }}</th>
    <th style="{{$styleTh}}">{{ $tot_rata }}</th>
</tr>
<tr>
    <th style="{{$styleTh}}">In Operation</th>
    <th style="{{$styleTh}}"></th>
    <th style="{{$styleTh}}">{{ $tot_me_1 }}</th>
    <th style="{{$styleTh}}">{{ $tot_ae_1 }}</th>
    <th style="{{$styleTh}}">{{ $oa_pakai_1 }}</th>
    <th style="{{$styleTh}}"></th>
    <th style="{{$styleTh}}"> </th>
    <th style="{{$styleTh}}">{{ $tot_trip_1 }}</th>
    <th style="{{$styleTh}}">{{ $tot_rata_1 }}</th>
</tr>
<tr>
    <th style="{{$styleTh}}">Anchorage</th>
    <th style="{{$styleTh}}"></th>
    <th style="{{$styleTh}}">{{ $tot_me - $tot_me_1 }}</th>
    <th style="{{$styleTh}}">{{ $tot_ae - $tot_ae_1 }}</th>
    <th style="{{$styleTh}}">{{ $oa_pakai_2 }}</th>
    <th style="{{$styleTh}}"></th>
    <th style="{{$styleTh}}"> </th>
    <th style="{{$styleTh}}">{{ $tot_trip_2 }}</th>
    <th style="{{$styleTh}}">{{ $tot_rata_2 }}</th>
</tr>

</table>