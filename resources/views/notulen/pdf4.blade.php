<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 40px; /* jarak isi dengan border */
        }

        body {
            font-family: arial, sans-serif;
            font-size: 12px;
            border: 2px solid #655dd6ff; /* garis tepi hitam */
            padding: 40px; /* jarak isi dengan garis */
            padding-top: 80px;
            padding-bottom: 80px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            text-align: center;
        }

        .table-bordered td, 
        .table-bordered th {
            border: 1px solid #000; /* pakai garis titik-titik */
            padding: 6px;
            vertical-align: top;
        }

        .table-bordered td:first-child {
            text-align: left; /* kolom pertama rata kiri */
            width: 40%;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table class="table-bordered" width="100%">
        <tr>
            <td colspan="2" width="25%"><img src="{{ public_path('img/logo-als.jpg')}}" alt="" width="50%"></td>
            <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>NOTULEN RAPAT FAMILIARISASI / PENYULUHAN MANAJEMEN KESELAMATAN </h3></td>
            <td width="25%" style="text-align: center;"><b>{{$show->kode}}</b><br> Hal <span class="page-number"></span></td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="2">Tanggal : Tanggal : {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}</td>
            <td colspan="2">Tempat : {{$show->tempat}}</td>
            <td colspan="2">Hal : {{$show->hal}}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="4">Agenda Rapat</td>
            <td colspan="2">Keterangan</td>
        </tr>
        @foreach($agenda as $value)
        <tr>
            <td colspan="4">{{$value->agenda}}</td>
            <td colspan="2">{{$value->ket}}</td>
        </tr>
        @endforeach
        <tr style="text-align: left;">
            <td colspan="6">Materi Famialirisasi/Penyuluhan<br>
            {!!$materi!!}<br><br><br><br>
            </td>
        </tr>
        <tr>
            <td colspan="3">DPA/Nahkoda,<br>
            @if($show->get_nahkoda()->tanda_tangan)
            <img src="file://{{ public_path('ttd_karyawan/' . $show->get_nahkoda()->tanda_tangan) }}" width="100px" height="75px"><br>
            @else
            <br><br><br>
            @endif
                {{$show->get_nahkoda()->nama}}
            </td>
            <td colspan="3">Notulen,<br>
            @if($show->get_notulen()->tanda_tangan)
            <img src="file://{{ public_path('ttd_karyawan/' . $show->get_notulen()->tanda_tangan) }}" width="100px" height="75px"><br>
            @else
            <br><br><br>
            @endif
            {{$show->get_notulen()->nama}}
            </td>
        </tr>
    </table>
</body>
</html>
