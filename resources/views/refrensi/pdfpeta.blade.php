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
            font-family: DejaVu Sans, sans-serif;
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

        .table-bordered td {
            border: 1px solid #000; 
            padding: 6px;
            vertical-align: top;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div>
        <table class="table-bordered" width="100%">
            <tr>
                <td colspan="2" width="25%"><img src="{{ public_path('img/'.$perusahaan->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="4" width="50%"><h3>{{$refrensi->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$refrensi->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            <tr>
                <td colspan="7">{!!$refrensi->intruksi!!}</td>
            </tr>
            <tr style="font-weight: bold;">
                <td width="5%">No</td>
                <td width="20%">No BPI</td>
                <td width="15%">Tanggal Terima</td>
                <td width="15%">Tanggal Koreksi</td>
                <td width="10%">Jumlah titik yang dikoreksi</td>
                <td width="10%">No peta yang dikoreksi</td>
                <td width="25%">Paraf Nahkoda</td>
            </tr>
            @foreach($show as $data)
            <tr style="text-align: left;">
                <td>{{$loop->iteration}}</td>
                <td>{{$data->no_bpi}}</td>
                <td>{{ \Carbon\Carbon::parse($data->tgl_terima)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($data->tgl_koreksi)->format('d-m-Y') }}</td>
                <td>{{$data->jml}}</td>
                <td>{{$data->no_peta}}</td>
                <td>@if($data->get_pj()->tanda_tangan)
                    <img src="file://{{ public_path('ttd_karyawan/' . $data->get_pj()->tanda_tangan) }}" width="100px" height="75px"><br>
                    @endif
                    {{$data->get_pj()->nama}}
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
