<table border="1">
<tr>
    <th colspan="5"><strong>LAPORAN DATA GUDANG KAPAL {{$namakapal}}</strong></th>
</tr>
<tr>
    <th rowspan="2">Nama Barang</th>
    <th rowspan="2">Stok Akhir</th>

    @for($i = 1; $i <= $maxRiwayat; $i++)
        <th colspan="3">Riwayat {{ $i }}</th>
    @endfor
</tr>

<tr>

    @for($i = 1; $i <= $maxRiwayat; $i++)
        <th>Tanggal</th>
        <th>Jumlah</th>
        <th>Ket</th>
    @endfor

</tr>

@foreach($kategori as $kel)

<tr>
    <td colspan="32"><b>{{ $kel->nama }} ({{ $kel->kode }})</b></td>
</tr>

@foreach($kel->barang as $barang)

<tr>

    <td>{{ $barang->nama }}</td>
    <td>{{ $barang->jumlah }}</td>

    @foreach($barang->riwayat as $log)

        <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-y') }}</td>
        <td>{{ $log->total }}</td>
        <td>{{ $log->keterangan }}</td>

    @endforeach

</tr>

@endforeach

@endforeach

</table>