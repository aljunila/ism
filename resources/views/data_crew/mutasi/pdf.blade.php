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
            font-size: 14px;
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
            font-size: 14px;
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
            <tr style="font-weight: bold;">
                <td width="5%" rowspan="2">No</td>
                <td width="25%" rowspan="2"> Nama</td>
                <td width="10%" rowspan="2"> Jabatan</td>
                <td width="20%" colspan="2" >Mutasi</td>
                <td width="10%" rowspan="2">Tgl Naik</td>
                <td width="10%" rowspan="2">Tgl TUrun</td>
                <td width="20%" rowspan="2">Keterangan</td>
            </tr>
            <tr style="font-weight: bold;">
                <td width="10%">Dari</td>
                <td width="10%">Ke</td>
            </tr>
            @foreach($show as $data)
            <tr style="text-align: left;">
                <td>{{$loop->iteration}}</td>
                <td>{{$data->get_karyawan()->nama}}</td>
                <td>{{$data->get_jabatan()->nama}}</td>
                <td>{{$data->get_dari_kapal()->nama}}</td>
                <td>{{$data->get_ke_kapal()->nama}}</td>
                <td>{{ \Carbon\Carbon::parse($data->tgl_naik)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($data->tgl_turun)->format('d-m-Y') }}</td>
                <td>{{$data->ket}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
