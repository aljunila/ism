@php
    $styleTh = "background-color: #e8e524; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $cols = ($maxRiwayat*3)+2;
@endphp
<table border="1">
<tr>
    <th colspan="{{$cols}}"><strong>LAPORAN DATA GUDANG KAPAL {{$namakapal}}</strong></th>
</tr>
<tr>
    <td style="{{$styleTd}}" colspan="{{$cols}}"></td>
</tr>
<tr>
    <th style="{{$styleTh}}" rowspan="2">Nama Barang</th>
    <th style="{{$styleTh}}" rowspan="2">Stok Akhir</th>

    @for($i = 1; $i <= $maxRiwayat; $i++)
        <th style="{{$styleTh}}" colspan="3">Riwayat {{ $i }}</th>
    @endfor
</tr>

<tr>

    @for($i = 1; $i <= $maxRiwayat; $i++)
        <th style="{{$styleTh}}">Tanggal</th>
        <th style="{{$styleTh}}">Jumlah</th>
        <th style="{{$styleTh}}">Ket</th>
    @endfor

</tr>

@foreach($kategori as $kel)
<tr>
    <td style="{{$styleTd}}" colspan="{{$cols}}"><b>{{ $kel->nama }} ({{ $kel->kode }})</b></td>
</tr>

@foreach($kel->barang as $barang)

<tr>

    <td style="{{$styleTd}}">{{ $barang->nama }}</td>
    <td style="{{$styleTd}}">{{ $barang->jumlah }}</td>

    @foreach($barang->riwayat as $log)

        <td style="{{$styleTd}}">{{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-y') }}</td>
        <td style="{{$styleTd}}">{{ $log->total }}</td>
        <td style="{{$styleTd}}">{{ $log->keterangan }}</td>

    @endforeach

</tr>

@endforeach

@endforeach

</table>