<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 60px; /* jarak isi dengan border */
        }

        .page-number:before {
        content: counter(page);
        }

        body {
            font-family: aria, sans-serif;
            font-size: 14px;
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
            font-size: 14px;
            text-align: center;
        }

        .table-bordered td {
            border: 1px solid #000; 
            padding: 6px;
            vertical-align: top;
        }

        tr {
            text-align: left;
            line-height: 1.5;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div>
        <table class="" width="100%">
            <tr>
                <td width="25%" align="center"><img src="{{ public_path('img/'.$show->logo) }}" alt="" width="50%"></td>
                <td  colspan="2" style="text-transform: uppercase;" width="50%" align="center"><h3>DATA PERUSAHAAN</h3></td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            <tr style="text-align: left;">
                <td width="25%">Nama Perusahaan</td>
                <td width="5%">:</td>
                <td width="70%">{{$show->nama}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Kode Perusahaan</td>
                <td>:</td>
                <td>{{$show->kode}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>NPWP</td>
                <td>:</td>
                <td>{{$show->npwp}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>NIB</td>
                <td>:</td>
                <td>{{$show->nib}}</td>
            </tr>
            <tr>
                <td> No Telp</td>
                <td>:</td>
                <td>{{ $show->telp }}</td>
            </tr>
            <tr>
                <td> Email</td>
                <td>:</td>
                <td>{{ $show->email }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{!! $show->alamat !!}</td>
            </tr>
        </table>
    </div>
</body>
</html>
