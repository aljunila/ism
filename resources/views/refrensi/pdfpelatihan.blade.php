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
                <td style="text-transform: uppercase;" colspan="4   " width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            <tr style="font-weight: bold;">
                <td width="5%" rowspan="2">No</td>
                <td width="20%" rowspan="2"> Nama Peserta</td>
                <td width="20%" rowspan="2">Nama Pelatihan</td>
                <td width="20%" colspan="2" >Lokasi</td>
                <td width="15%" rowspan="2">Tempat</td>
                <td width="20%" rowspan="2">Hasil</td>
            </tr>
            <tr style="font-weight: bold;">
                <td width="10%">Tgl Mulai</td>
                <td width="10%">Tgl Selesai</td>
            </tr>
            @foreach($show as $data)
            <tr style="text-align: left;">
                <td>{{$loop->iteration}}</td>
                <td>{{$data->get_karyawan()->nama}}<br>({{$data->get_karyawan()->get_jabatan()->nama}})</td>
                <td>{{$data->nama}}</td>
                <td>{{ \Carbon\Carbon::parse($data->tgl_mulai)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($data->tgl_selesai)->format('d-m-Y') }}</td>
                <td>{{$data->tempat}}</td>
                <td>{{$data->hasil}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
