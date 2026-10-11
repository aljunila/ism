@php
    $styleTh = "background-color: #e8e524; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp
<table border="1">
<tr>
    <th colspan="5"><strong>LAPORAN STOK DATA GUDANG KAPAL {{$namakapal}}</strong></th>
</tr>
<tr>
    <th colspan="5"></th>
</tr>
<tr>
    <th style="{{$styleTh}}">Nama Barang</th>
    <th style="{{$styleTh}}">Stok Akhir</th>
</tr>

@foreach($kategori as $kel)

<tr>
    <td style="{{$styleTd}}" colspan="2"><b>{{ $kel->nama }} ({{ $kel->kode }})</b></td>
</tr>

@foreach($kel->barang as $barang)

<tr>

    <td style="{{$styleTd}}">{{ $barang->nama }}</td>
    <td style="{{$styleTd}}">{{ $barang->jumlah }}</td>

</tr>

@endforeach

@endforeach

</table>