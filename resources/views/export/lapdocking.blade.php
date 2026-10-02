@php
    $styleTh = "background-color: #e8e524; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp
<table border="1">
    <tr>
        <th colspan="5"><strong>LAPORAN DATA DOCKING</strong></th>
    </tr>
    <tr>
        <th style="{{$styleTh}}">Kapal</th>
        <th style="{{$styleTh}}">Jadwal</th>
        <th style="{{$styleTh}}">Tempat</th>
        <th style="{{$styleTh}}">Tgl Mulai</th>
        <th style="{{$styleTh}}">Tgl Selesai</th>
        <th style="{{$styleTh}}">Job</th>
        <th style="{{$styleTh}}">SubJob</th>
        <th style="{{$styleTh}}">Deskripsi</th>
        <th style="{{$styleTh}}">Unit</th>
        <th style="{{$styleTh}}">Volume</th>
        <th style="{{$styleTh}}">Harga</th>
        <th style="{{$styleTh}}">Total</th>
        <th style="{{$styleTh}}">Keterangan</th>
    </tr>
    @foreach($data as $show)
    <tr>
        <td style="{{$styleTd}}">{{ $show->kapal }}</td>
        <td style="{{$styleTd}}">{{ $show->bulan }} - {{ $show->tahun }}</td>
        <td style="{{$styleTd}}">{{ $show->tempat }}</td>
        <td style="{{$styleTd}}">{{ $show->tgl_mulai ? \Carbon\Carbon::parse($show->tgl_mulai)->format('d-m-Y') : '' }} </td>
        <td style="{{$styleTd}}">{{ $show->tgl_selesai ? \Carbon\Carbon::parse($show->tgl_selesai)->format('d-m-Y') : '-' }}</td>
        <td style="{{$styleTd}}">{{ $show->job }}</td>
        <td style="{{$styleTd}}">{{ $show->subjob }}</td>
        <td style="{{$styleTd}}">{{ $show->deskripsi }}</td>
        <td style="{{$styleTd}}">{{ $show->unit }}</td>
        <td style="{{$styleTd}}">{{ $show->volume }}</td>
        <td style="{{$styleTd}}">{{ $show->harga }}</td>
        <td style="{{$styleTd}}">{{ $show->total }}</td>
        <td style="{{$styleTd}}">{{ $show->keterangan }}</td>
    </tr>
    @endforeach
</table>