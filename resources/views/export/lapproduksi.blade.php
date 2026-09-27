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
        <th colspan="{{$col}}">Total</th>
    </tr>
    <tr>
        @foreach($kend as $k)
        <th>{{$k->kode}}</th>
        @endforeach
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
        @foreach($kend as $k)
        <td>{{ $json[$k->id]['jumlah'] ?? 0 }}</td>
        @endforeach
        @foreach($kend as $k)
        <td style="text-align: right;">{{ number_format($json[$k->id]['total'] ?? 0, 0, ',', '.') }}</td>
        @endforeach
    </tr>
    @endforeach
</table>