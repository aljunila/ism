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
                <td colspan="2" width="25%" style="text-align: center;"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            <tr>
                <td colspan="6">{!!$form->intruksi!!}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="3" width="50%">Nama : {{$show->nama}}</td>
                <td colspan="3" width="50%">Jabatan : {{$show->get_jabatan()->nama}}</td>
            </tr>
            <tr>
                <td colspan="3" width="50%">Pertanyaan</td>
                <td width="15%">Ya/Tidak</td>
                <td width="20%" colspan="2" >Keterangan</td>
            </tr>
            @foreach($item as $ck)
            <tr >
                <td colspan="3" style="text-align: left;" width="50%">{!!$ck->get_item()->item!!}</td>
                <td style="text-align: center;" width="15%">
                    {!! ($ck->value==1) ? 'Ya' : 'Tidak' !!}
                </td>
                <td width="20%" style="text-align: left;" colspan="2">{!!$ck->ket!!}</td>
            </tr>
            @endforeach
            <tr style="text-align: left;">
                <td colspan="6">Catatan / Komentar Interview:<br>{!!$show->note!!}<br><br><br><br><br><br></td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="6">Tanggal Periksa : {{ \Carbon\Carbon::parse($show->tgl_periksa)->format('d-m-Y') }}</td>
            </tr>
             <tr>
                <td colspan="3" width="50%">Mengetahui dan Menyetujui<br>
                @if($show->get_menyetujui()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_menyetujui()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_menyetujui()->nama}}
                </td>
                <td colspan="3" width="50%">Pemeriksa<br>
                @if($show->get_periksa()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_periksa()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_periksa()->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
