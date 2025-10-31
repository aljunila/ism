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
        .col-no {
        width: 10%;
        text-align: left;
        }
        .col-nama {
        width: 30%;
        text-align: left;
        }
        .col-jab {
        width: 30%;
        text-align: left;
        }
        .col-ttd {
        width: 30%;
        text-align: left;
        }
    </style>
</head>
<body>
    <table class="table-bordered" width="100%">
        <tr>
            <td colspan="2" style="text-align: center;" width="25%"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
            <td style="text-transform: uppercase;" colspan="3" width="45%"><h3>DAFTAR HADIR</h3></td>
            <td style="text-align: center; text-transform: uppercase;" width="30%"><br><b>EL-04-05</b><br> Hal <span class="page-number"></span></td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="6">Agenda Rapat : {{$show->get_kode()->ket}}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="6">Tanggal : {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="6">Tempat : {{$show->tempat}}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="3">Nama</td>
            <td colspan="2">Jabatan</td>
            <td colspan="1">Tanda Tangan</td>
        </tr>
        @foreach($detail as $p)
        <tr style="text-align: left;">
            <td colspan="3">{{$p->get_karyawan()->nama}}</td>
            <td colspan="2">{{$p->get_jabatan()->nama}}</td>
            <td colspan="1">@if($p->get_karyawan()->tanda_tangan)
            <img src="file://{{ public_path('ttd_karyawan/' . $p->get_karyawan()->tanda_tangan) }}" width="100px" height="75px">
            @endif</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
