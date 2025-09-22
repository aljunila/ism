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
                <td style="text-transform: uppercase;" colspan="6">{!!$form->nama!!}<br>kapal {{$show->get_kapal()->nama}}</td>
            </tr>
            <tr>
                <td colspan="6">{!!$form->intruksi!!}</td>
            </tr>
            <tr>
                <td colspan="3" rowspan="2" width="50%">Uraian</td>
                <td colspan="3" width="50%">Kondisi</td>
            </tr>
            <tr>
                <td width="15%">Ya</td>
                <td width="15%">Tidak</td>
                <td width="20%">Keterangan</td>
            </tr>
            @foreach($item as $ck)
            <tr >
                <td colspan="3" style="text-align: left;" width="50%">{{$ck->get_item()->item}}</td>
                <td style="text-align: center;" width="15%">
                    @if($ck->value==1)
                    <img src="file://{{ public_path('img/check.png') }}" width="25px"><br>
                    @endif
                </td>
                <td style="text-align: center;" width="15%">
                    @if($ck->value==0)
                    <img src="file://{{ public_path('img/silang.jpg') }}" width="25px"><br>
                    @endif
                </td>
                <td width="20%" style="text-align: left;">{{$ck->ket}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" width="50%">
                @if($show->get_dari()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_dari()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_dari()->nama}}
                </td>
                <td colspan="3" width="50%">
                @if($show->get_kepada()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_kepada()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_kepada()->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
