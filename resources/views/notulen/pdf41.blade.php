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
            font-size: 16px;
            border: 4px solid #655dd6ff; /* garis tepi hitam */
            padding: 30px; /* jarak isi dengan garis */
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
            font-size: 16px;
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
            <td width="25%" style="text-align: center;"><img src="{{ public_path('img/'.$show->logo) }}" alt="" width="50%"></td>
            <td width="50%"><h3>JADWAL RAPAT MANAGEMENT REVIEW <br>RAPAT TIM MANAJEMEN KESELAMATAN TABLE TOP DRILL </h3></td>
            <td width="25%" style="text-align: center;" ><b>EL-04-01</b><br> Hal <span class="page-number"></span></td>
        </tr>
    </table><br><br>
    <table border="1" style="border-collapse: collapse; border: solid #000; padding: 6px;" width="100%">  
        <tr style="text-align: center;">
            <th rowspan="2" width="20%">KEGIATAN</th>
            <th rowspan="2" width="8%">TANGGUNG JAWAB</th>
            <th colspan="12" width="72%">JADWAL TAHUN {{ $tahun }}</th>
        </tr>
        <tr style="text-align: center;">
            <td>Jan</td>
            <td>Feb</td>
            <td>Mar</td>
            <td>Apr</td>
            <td>Mei</td>
            <td>Jun</td>
            <td>Jul</td>
            <td>Ags</td>
            <td>Sept</td>
            <td>Okt</td>
            <td>Nov</td>
            <td>Des</td>
        </tr>
        <tr>
            <td>Rapat Manajemen Review</td>
            <td>Direktur</td>
            @for($i = 1; $i <= 12; $i++)
                @php
                    $bulan = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $detail = $get42[$bulan] ?? collect();
                @endphp

                <td style="text-align: center;">
                    @if($detail->count() > 0)
                     {{ \Carbon\Carbon::parse($detail[0]->tanggal)->format('d-m-Y') }}
                    @else
                        &nbsp;
                    @endif
                </td>
            @endfor
        </tr>
        <tr>
            <td>Rapat Tim Manajemen Keselamatan / Safety Meeting</td>
            <td>DPA</td>
            @for($a = 1; $a <= 12; $a++)
                @php
                    $bln = str_pad($a, 2, '0', STR_PAD_LEFT);
                    $detail3 = $get43[$bln] ?? collect();
                @endphp

                <td style="text-align: center;">
                    @if($detail3->count() > 0)
                     {{ \Carbon\Carbon::parse($detail3[0]->tanggal)->format('d-m-Y') }}
                    @else
                        &nbsp;
                    @endif
                </td>
            @endfor
        </tr>
    </table>
</body>
</html>
