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

        table.lebar td,
            table.lebar th {
                padding: 4px 10px; /* atas-bawah | kiri-kanan */
            }
    </style>
</head>
<body>
    <div>
        <table class="table-bordered" width="100%">
            <tr>
                <td width="25%" style="text-align: center;" rowspan="3"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
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
        <table width="100%" class="lebar">
            <tr style="text-align: center;">
                <td colspan="2">LAPORAN PENGGANTIAN KKM <br>
                                (CE’s Hand Over Note) <br>
                                No : {{ $dataItem['no'] ?? '' }}
                </td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="2">Kepada Yth &nbsp;: General Manajer<br>
                                Perusahaan :  {{$show->get_perusahaan()->nama}}<br>
                                Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: -
                </td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr style="text-align: left;"><td colspan="2">Dengan hormat</td></tr>
            <tr style="text-align: left;"><td colspan="2">Bersama ini kami beritahukan bahwa : </td></tr>
            <tr style="text-align: left;"><td colspan="2">Pada hari ini </td></tr>
            <tr style="text-align: left;"><td colspan="2">{{ \Carbon\Carbon::parse($show->date)->locale('id')->translatedFormat('l') }} tanggal {{ \Carbon\Carbon::parse($show->date)->format('d-m-Y') }}
                     jam {{$show->time}} {{ $keterangan['wi'] ?? '' }} </td></tr>
            <tr style="text-align: left;"><td colspan="2">Saya KKM {{$show->get_karyawan()->nama}} </td></tr>
            <tr style="text-align: left;"><td colspan="2">Atas dasar kemufakatan bersama </td></tr>
            <tr style="text-align: left;"><td colspan="2">ROB bungker pada saat ini adalah sebagai berikut : <br><br></td></tr>
            <tr style="text-align: left;"><td colspan="2">FO {{ $dataItem['fo'] ?? '' }}  M / T</td></tr>
            <tr style="text-align: left;"><td colspan="2">DO {{ $dataItem['do'] ?? '' }} M / T </td></tr>
            <tr style="text-align: left;"><td colspan="2">FW {{ $dataItem['fw'] ?? '' }} M / T <br><br></td></tr>
            <tr style="text-align: left;"><td colspan="2">Ikut diserah terimakan bersamaan surat ini adalah Laporan Kondisi Permesinan Kapal. </td></tr>
            <tr style="text-align: left;"><td colspan="2">Perubahan komando ini sudah dicatatkan ke dalam Log Book.  <br><br><br><br></td></tr>
            <tr style="text-align: center;">
                <td colspan="2">Hormat kami <br><br></td>
            </tr>
             <tr style="text-align: center;">
                <td >{{$show->get_jabatan()->nama}} yang diganti,<br>
                @if($show->get_karyawan()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_karyawan()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_karyawan()->nama}}
                </td>
                 <td>{{$show->get_jabatan()->nama}} Pengganti,<br>
                @if($show->get_karyawan2()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_karyawan2()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_karyawan2()->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
