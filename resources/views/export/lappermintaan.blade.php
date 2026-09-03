<table border="1">
    <tr>
        <th colspan="5"><strong>LAPORAN DATA PERMINTAAN</strong></th>
    </tr>
    <tr>
        <th rowspan="2">Tanggal Pengajuan</th>
        <th rowspan="2">No Permintaan</th>
        <th rowspan="2">Nama Barang</th>
        <th rowspan="2">Bagian</th>
        <th colspan="2">Permintaan</th>
        <th rowspan="2">Jumlah Realisasi</th>
        <th rowspan="2">Tanggal Pengiriman</th>
        <th rowspan="2">Status</th>
    </tr>
    <tr>
        <th>Jumlah</th>
        <th>Satuan</th>
    </tr>
    @foreach($data as $show)
    <tr>
        <td>{{ $show->tanggal }}</td>
        <td>{{ $show->kapal }}<br>{{ $show->nomor }}</td>
        <td>{{ $show->barang }} @if($show->kode) ({{$show->kode}}) @endif</td>
        <td>{{ $show->bagian }}</td>
        <td>{{ $show->jumlah }}</td>
        <td>{{ $show->satuan }}</td>
        <td>{{ $show->jml_kirim }}</td>
        <td>{{ $show->tgl_kirim }}</td>
        <td>{{ $show->flow_stage }}</td>
    </tr>
    @endforeach
</table>