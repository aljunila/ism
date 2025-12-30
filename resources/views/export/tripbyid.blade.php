<table>
    <tr>
        <th colspan="5"><strong>LAPORAN TRIP KAPAL</strong></th>
    </tr>
    <tr>
        <td colspan="2">Kapal : </td>
        <td colspan="3">
            {{ $trip->get_kapal()->nama }}
        </td>
    </tr>
    <tr>
         <td colspan="2">Tanggal :</td>
        <td colspan="3">
             {{ \Carbon\Carbon::parse($trip->tanggal)->format('d-m-Y') }}
        </td>
    </tr>
    <tr>
         <td colspan="2">Pelabuhan :</td>
        <td colspan="3">
             {{ $trip->get_pelabuhan()->nama }}
        </td>
    </tr>
    <tr>
        <td colspan="2">Trip Ke : </td>
        <td colspan="3">
             {{ $trip->trip }}
        </td>
    </tr>
    <tr>
        <td colspan="2">Jam :  </td>
        <td colspan="3">
            {{ $trip->jam }}
        </td>
    </tr>
    <tr><td colspan="5"></td></tr>
</table>
<table border="1">
    <thead>
        <tr>
            <th><strong>No</strong></th>
            <th><strong>Gol Kendaraan</strong></th>
            <th><strong>Jumlah</strong></th>
            <th><strong>Nominal</strong></th>
            <th><strong>Total</strong></th>
        </tr>
    </thead>

    <tbody>
        @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['gol'] }}</td>
                <td>{{ $row['jumlah'] }}</td>
                <td>Rp. {{ number_format($row['nominal'], 0, ',', '.')}}</td>
                <td>Rp. {{ number_format($row['total'], 0, ',', '.')}}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4">Grand Total</th>
            <th>Rp. {{ number_format($grandTotal, 0, ',', '.')}}</th>
        </tr>
    </tfoot>
</table>
