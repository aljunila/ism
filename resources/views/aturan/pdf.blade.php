<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 40px; /* jarak isi dengan border */
        }

        body {
            font-family: DejaVu Sans, sans-serif;
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
            <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$show->nama}}</h3></td>
            <td width="25%" style="text-align: center;"><b>{{$show->kode}}</b><br> Hal <span class="page-number"></span></td>
        </tr>
    </table>
    <div>
        {!! $isi !!}
    </div>
</body>
</html>
