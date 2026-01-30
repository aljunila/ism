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
            font-size: 16px;
            border: 4px solid #655dd6ff; /* garis tepi hitam */
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
                <td width="25%" style="text-align: center;" rowspan="3"><img src="{{ public_path('img/'.$perusahaan->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" width="50%" rowspan="3"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;" colspan="3"><b>{{$form->judul}}</b></td>
            </tr>
            <tr>
                <td>{{$form->pj}}</td>
                <td>{{$form->kode_file}}</td>
                <td>{{$form->periode}}</td>
            </tr>
            <tr style="text-align: left;"><td colspan="3"> Hal <span class="page-number"></span></td></tr>
        </table>
        <table class="table-bordered" width="100%">
            <tr>
                <td widtd="5%" rowspan="2">No.</td>
                <td widtd="20%" rowspan="2">Nama Peserta</td>
                <td widtd="20%" rowspan="2">Nama Pelatihan</td>
                <td widtd="10%" colspan="2">Waktu (Tanggal)</td>
                <td widtd="15%" rowspan="2">Tempat</td>
                <td widtd="15%" rowspan="2">Hasil</td>
            </tr>
            <tr>
                <td>Mulai</td>
                <td>Selesai</td>
            </tr>
            @foreach($show as $data)
            <tr style="text-align: left;">
                <td>{{$loop->iteration}}</td>
                <td>{{$data->get_karyawan()->nama}} br {{$data->get_jabatan()->nama}}</td>
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
