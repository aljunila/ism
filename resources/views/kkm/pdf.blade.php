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

        br {
            line-height:2;
        }
    </style>
</head>
<body>
    <div>
        <table class="table-bordered" width="100%">
            <tr >
                <td colspan="2" width="25%"><img src="{{ public_path('img/logo-als.jpg')}}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
        </table><br>
            <p style="text-align: center; uppercase">{{$form->nama}}<br>
            Nomer : {{$show->nomer}}</p><br>
            <table width="80%" align="center">
                <tr>
                    <td width="20%">Kepada</td>
                    <td width="5%">:</td>
                    <td width="75%">{{$show->get_kepada()->nama}}</td>
                </tr>
                <tr >
                    <td>Perusahaan</td>
                    <td>:</td>
                    <td>{{$show->get_perusahaan()->nama}}</td>
                </tr>
            </table>
            <hr>
            <p>Dengan hormat,<br>
                Bersama ini kami beritahukan bahwa: <br>
                Pada hari ini {{ \Carbon\Carbon::parse($show->tanggal)->translatedFormat('l') }}
                tanggal {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}
                jam {{ $show->jam }} <br>
                Saya KKM {{$show->get_lama()->nama}} <br>
                Atas dasar kemufakatan bersama <br><br>
                ROB bungker pada saat ini adalah sebagai berikut: <br>
                FO {{ $show->fo }} M/T <br>
                DO {{ $show->do }} M/T <br>
                FW {{ $show->fw }} M/T <br><br>
                Ikut diserah terimakan bersamaan surat ini adalah Laporan Kondisi Permesinan Kapal. <br>
                Perubahan komando ini sudah dicatatkan kedalam Log Book.
            </p><br><br><br><br>
            <table width="100%">
            <tr><td colspan="2" style="text-align: center;">Hormat kami,<br><br><br><br></td></tr>
            <tr>
                <td width="50%" style="text-align: center;">
                @if($show->get_lama()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_lama()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$show->get_lama()->nama}}</u><br>
                    <p>KKM Lama</p>
                </td>
                <td  width="50%" style="text-align: center;">
                @if($show->get_baru()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_baru()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$show->get_baru()->nama}}</u><br>
                    <p>KKM Baru</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
