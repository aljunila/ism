@php
    $styleTh = "background-color: #e8e524; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp
<table border="1">
    <tr>
        <th colspan="9"><strong>LAPORAN DATA PERMINTAAN</strong></th>
    </tr>
    <tr>
        <th colspan="9"></th>
    </tr>
    <tr>
        <th style="{{$styleTh}}" rowspan="2">Tanggal Pengajuan</th>
        <th style="{{$styleTh}}" rowspan="2">No Permintaan</th>
        <th style="{{$styleTh}}" rowspan="2">Nama Barang</th>
        <th style="{{$styleTh}}" rowspan="2">Bagian</th>
        <th style="{{$styleTh}}" colspan="2">Permintaan</th>
        <th style="{{$styleTh}}" rowspan="2">Jumlah Realisasi</th>
        <th style="{{$styleTh}}" rowspan="2">Tanggal Pengiriman</th>
        <th style="{{$styleTh}}" rowspan="2">Status</th>
    </tr>
    <tr>
        <th style="{{$styleTh}}">Jumlah</th>
        <th style="{{$styleTh}}">Satuan</th>
    </tr>
    @foreach($data as $show)
    <tr>
        <td style="{{$styleTd}}">{{ $show->tanggal }}</td>
        <td style="{{$styleTd}}">{{ $show->kapal }}<br>{{ $show->nomor }}</td>
        <td style="{{$styleTd}}">{{ $show->barang }} @if($show->kode) ({{$show->kode}}) @endif</td>
        <td style="{{$styleTd}}">@if($show->bagian==1) DECK @elseif($show->bagian==2) MESIN @elseif($show->bagian==3) KELISTRIKAN @else ALAT KEBERSIHAN @endif</td>
        <td style="{{$styleTd}}">{{ $show->jumlah }}</td>
        <td style="{{$styleTd}}">{{ $show->satuan }}</td>
        <td style="{{$styleTd}}">{{ $show->jml_kirim }}</td>
        <td style="{{$styleTd}}">{{ $show->tgl_kirim }}</td>
        <td style="{{$styleTd}}">{{ $show->flow_stage }}</td>
    </tr>
    @endforeach
</table>