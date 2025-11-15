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
                <td colspan="2" width="25%"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            <tr>
                <td colspan="6">{!!$form->intruksi!!}</td>
            </tr>
        </table>
        <br>
        <table width="75%" align="center">
            <tr style="text-align: left;">
                <td width="45%" colspan="2">Nama Kapal</td>
                <td width="10%">:</td>
                <td width="50%"> {{ $show->get_kapal()->nama }}</td>
            </tr>
            <tr>
                <td colspan="2">Tanggal Pekerjaan</td>
                <td>:</td>
                <td colspan="2">{{ \Carbon\Carbon::parse($show->date)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td colspan="2">Di Pelabuhan</td>
                <td>:</td>
                <td colspan="2">{{ $show->ket}}</td>
            </tr>
            <tr>
                <td colspan="2">Jenis Pekerjaan</td>
                <td>:</td>
                <td colspan="2"> {{ $show->note }}</td>
            </tr>
            <tr>
                <td colspan="2">Jumlah Personil </td>
                <td>:</td>
                <td colspan="2">{{ $jml }}</td>
            </tr>
            <tr>
                <td colspan="2">Nama Personil</td>
                <td>:</td>
                <td colspan="2"> @foreach($personil as $p) {{ $p->nama }} <br> @endforeach </td>
            </tr>
            <tr>
                <td colspan="5"><br><br></td>
            </tr>
            @foreach($item as $ck)
            <tr>
                <td width="5%">{{$loop->iteration}}</td>
                <td colspan="3" width="85%">{!! $ck->get_item()->item !!}</td>
                <td width="10%">
                    {{ ($ck->value==1) ? 'Ya' : 'Tidak'; }}
                </td>
            </tr>
            @endforeach
             <tr>
                <td colspan="5"><br><br><br><br></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">Nahkoda<br>
                @if($show->get_mengetahui()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_mengetahui()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_mengetahui()->nama}}
                </td>
                <td colspan="3" style="text-align: center;">KKM<br>
                @if($show->get_mentor()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_mentor()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_mentor()->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
