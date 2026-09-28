<table border="1">
    <tr>
        <th colspan="5"><strong>LAPORAN DATA PRODUKSI</strong></th>
    </tr>
    @php
        $col = count($kend);
    @endphp
    <tr>
        <th rowspan="2">Kapal</th>
        <th rowspan="2">Tanggal</th>
        <th rowspan="2">Pelabuhan</th>
        <th rowspan="2">Trip</th>
        <th rowspan="2">Jam</th>
        <th colspan="{{$col}}">Jumlah</th>
        <th rowspan="2">Total Tiket</th>
        <th rowspan="2">Gross (Rp)</th>
    </tr>
    <tr>
        @foreach($kend as $k)
        <th>{{$k->kode}}</th>
        @endforeach
    </tr>
    @foreach($data as $show)
    @php
        $json = json_decode($show->data, true) ?? [];
    @endphp
    <tr>
        <td>{{ $show->kapal }}</td>
        <td>{{ $show->tanggal ? \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') : '' }} </td>
        <td>{{ $show->pelabuhan }}</td>
        <td>{{ $show->trip }}</td>
        <td>{{ $show->jam }}</td>
        @php $jml =0; $total=0; @endphp
        @foreach($kend as $k)
        @php 
            $jml = $jml+$json[$k->id]['jumlah'];
            $total= $total+$json[$k->id]['total']; 
        @endphp
        <td>{{ $json[$k->id]['jumlah'] ?? 0 }}</td>
        @endforeach
        <td>{{ $jml }}</td>
        <td>{{ number_format($total ?? 0, 0, ',', '.') }}</td>
    </tr>
    @endforeach
</table>