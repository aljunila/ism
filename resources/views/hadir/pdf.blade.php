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
                <td colspan="2" width="25%"><img src="{{ public_path('img/logo-als.jpg')}}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            <tr>
                <td colspan="6">{!!$form->intruksi!!}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="6">Unit Kerja/Nama Kapal : {{$show->get_kapal()->nama}}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td colspan="2" >Nama</td>
                <td>Jabatan</td>
                <td colspan="2">tanda Tandan</td>
            </tr>
            @foreach($detail as $d)
            <tr >
                <td style="text-align: left;">{{ \Carbon\Carbon::parse($d->tanggal)->format('d-m-Y') }}</td>
                <td colspan="2" style="text-align: left;">{{ $d->get_karyawan()->nama }}</td>
                <td style="text-align: left;">{{ $d->get_jabatan()->nama }}</td>
                <td colspan="2" style="text-align: center;">
                    @if($d->get_karyawan()->tanda_tangan)
                        <img src="file://{{ public_path('ttd_karyawan/' . $d->get_karyawan()->tanda_tangan) }}" width="100px" height="75px"><br>
                    @else
                        <br><br><br>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
