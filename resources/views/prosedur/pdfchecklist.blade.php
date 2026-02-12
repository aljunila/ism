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
            <tr style="text-align: left;">
                <td colspan="5">{!!$form->intruksi!!}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="5">Unit Kerja / Nama Kapal : {{ $kapal }}</td>
            </tr>
            <tr>
                <td width="5%">No</td>
                <td width="20%">Tanggal</td>
                <td width="35%">Nama</td>
                <td width="20%">Jabatan</td>
                <td width="20%">Tanda Tangan</td>
            </tr>
            @foreach($show as $data)
            <tr style="text-align: left;">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data->last_seen ? \Carbon\Carbon::parse($data->last_seen)->format('d-m-Y') : '' }}</td>
                <td>{{ $data->nama }}</td>
                <td>{{ $data->jabatan }}</td>
                <td>@if($data->last_seen)
                    <img src="file://{{ public_path('ttd_karyawan/'.$data->tanda_tangan) }}" width="85px">
                @endif</td>
            </tr>
            @endforeach

        </table>
    </div>
</body>
</html>
