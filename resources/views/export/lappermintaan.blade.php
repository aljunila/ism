<table border="1">
    <tr>
        <th colspan="5"><strong>LAPORAN DATA PERMINTAAN</strong></th>
    </tr>
    <tr>
        <th>Nama Barang</th>
        <th>Jumlah</th>
        <th>Tanggal Pengajuan</th>
        <th>Permintaan Dari</th>
        <th>Status</th>
    </tr>
    @foreach($data as $show)
    <tr>

        <td>{{ $show->barang }} @if($show->kode) ({{$show->kode}}) @endif</td>
        <td>{{ $show->jumlah }}</td>
        <td>{{ $show->tanggal }}</td>
        <td>{{ $show->kapal }}<br>{{ $show->nomor }}</td>
        <td>{{ $show->procurement_channel ?? 'Logistik' }}</td>
    </tr>
    @endforeach
</table>