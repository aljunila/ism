@php
    $styleTh = "background-color: #e8e524; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp
<table border="1">
    <tr>
        <th colspan="5"><strong>LAPORAN DATA PRODUKSI</strong></th>
    </tr>
    @php
        $col = count($kend);
    @endphp
    <tr>
        <th style="{{ $styleTh }}">Kapal</th>
        <th style="{{ $styleTh }}">Tanggal</th>
        <th style="{{ $styleTh }}">Pelabuhan</th>
        <th style="{{ $styleTh }}">Trip</th>
        <th style="{{ $styleTh }}">Jam</th>
        @foreach($kend as $k)
        <th style="{{ $styleTh }}">{{$k->kode}}</th>
        @endforeach
        <th style="{{ $styleTh }}">Total Tiket</th>
        <th style="{{ $styleTh }}">Gross (Rp)</th>
        <th style="{{ $styleTh }}">Ticket</th>
        <th style="{{ $styleTh }}">Call</th>
        <th style="{{ $styleTh }}">Sandar</th>
        <th style="{{ $styleTh }}">Kepil</th>
        <th style="{{ $styleTh }}">Pendapatan</th>
    </tr>
    @foreach($data as $show)
    @php
        $json = json_decode($show->data, true) ?? [];
    @endphp
    <tr>
        <td style="{{ $styleTd }}">{{ $show->kapal }}</td>
        <td style="{{ $styleTd }}">{{ $show->tanggal ? \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') : '' }} </td>
        <td style="{{ $styleTd }}">{{ $show->pelabuhan }}</td>
        <td style="{{ $styleTd }}">{{ $show->trip }}</td>
        <td style="{{ $styleTd }}">{{ $show->jam }}</td>
        @php $jml =0; $total=0; @endphp
        @foreach($kend as $k)
        @php 
            $jml = $jml+$json[$k->id]['jumlah'];
            $total= $total+$json[$k->id]['total']; 
            $tiket = $jml*$show->tiket;
            $pend = $total - $tiket - $show->sandar - $show->kepil;
        @endphp
        <td style="{{ $styleTd }}">{{ $json[$k->id]['jumlah'] ?? 0 }}</td>
        @endforeach
        <td style="{{ $styleTd }}">{{ $jml }}</td>
        <td style="{{ $styleTd }}">{{ $total }}</td>
        <td style="{{ $styleTd }}">{{ $tiket }}</td>
        <td style="{{ $styleTd }}">{{ $show->call }}</td>
        <td style="{{ $styleTd }}">{{ $show->sandar }}</td>
        <td style="{{ $styleTd }}">{{ $show->kepil }}</td>
        <td style="{{ $styleTd }}">{{ $pend }}</td>
    </tr>
    @endforeach
</table>