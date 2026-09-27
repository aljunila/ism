<table border="1">
    <tr>
        <th colspan="5"><strong>LAPORAN DATA DOCKING</strong></th>
    </tr>
    <tr>
        <th>Kapal</th>
        <th>Jadwal</th>
        <th>Tempat</th>
        <th>Tgl Mulai</th>
        <th>Tgl Selesai</th>
        <th>Job</th>
        <th>SubJob</th>
        <th>Deskripsi</th>
        <th>Unit</th>
        <th>Volume</th>
        <th>Harga</th>
        <th>Total</th>
        <th>Keterangan</th>
    </tr>
    @foreach($data as $show)
    <tr>
        <td>{{ $show->kapal }}</td>
        <td>{{ $show->bulan }} - {{ $show->tahun }}</td>
        <td>{{ $show->tempat }}</td>
        <td>{{ $show->tgl_mulai ? \Carbon\Carbon::parse($show->tgl_mulai)->format('d-m-Y') : '' }} </td>
        <td>{{ $show->tgl_selesai ? \Carbon\Carbon::parse($show->tgl_selesai)->format('d-m-Y') : '-' }}</td>
        <td>{{ $show->job }}</td>
        <td>{{ $show->subjob }}</td>
        <td>{{ $show->deskripsi }}</td>
        <td>{{ $show->unit }}</td>
        <td>{{ $show->volume }}</td>
        <td>Rp. {{ number_format($show->harga, 0, ',', '.') }}</td>
        <td>Rp. {{ number_format($show->total, 0, ',', '.') }}</td>
        <td>{{ $show->keterangan }}</td>
    </tr>
    @endforeach
</table>