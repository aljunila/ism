<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 40px; /* jarak isi dengan border */
        }

        body {
            font-family: arial, sans-serif;
            font-size: 12px;
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
            font-size: 12px;
            text-align: center;
        }

        .table-bordered td, 
        .table-bordered th {
            border: 1px solid #000; /* pakai garis titik-titik */
            padding: 6px;
            vertical-align: top;
        }

        .table-bordered td:first-child {
            text-align: left; /* kolom pertama rata kiri */
            width: 40%;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
        .col-no {
        width: 10%;
        text-align: left;
        }
        .col-nama {
        width: 30%;
        text-align: left;
        }
        .col-jab {
        width: 30%;
        text-align: left;
        }
        .col-ttd {
        width: 30%;
        text-align: left;
        }
    </style>
</head>
<body>
    <table class="table-bordered" width="100%">
        <tr>
            <td colspan="2" width="25%" style="text-align: center;"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
            <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$form->nama}}</h3></td>
            <td width="25%" style="text-align: center;"><b>{{$show->kode}}</b><br> Hal <span class="page-number"></span></td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="2">Tanggal : Tanggal : {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}</td>
            <td colspan="2">Tempat : {{$show->tempat}}</td>
            <td colspan="2">Hal : {{$show->hal}}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="4">Agenda Rapat</td>
            <td colspan="2">Keterangan</td>
        </tr>
        @foreach($agenda as $value)
        <tr>
            <td colspan="4">{{$value->agenda}}</td>
            <td colspan="2">{{$value->ket}}</td>
        </tr>
        @endforeach
        <tr style="text-align: left;">
            <td colspan="6">Materi Famialirisasi/Penyuluhan<br>
            {!!$materi!!}<br><br><br><br>
            </td>
        </tr>
        @if($form->kode=='el0403')
            <tr style="text-align: center;">
                <td colspan="3">DPA<br>
                    @if($show->get_nahkoda()->tanda_tangan)
                    <img src="file://{{ public_path('ttd_karyawan/' . $show->get_nahkoda()->tanda_tangan) }}" width="100px" height="75px"><br>
                    @else
                    <br><br><br>
                    @endif
                        {{$show->get_nahkoda()->nama}}
                </td>
                <td colspan="3">Notulis,<br>
                    @if($show->get_notulen()->tanda_tangan)
                    <img src="file://{{ public_path('ttd_karyawan/' . $show->get_notulen()->tanda_tangan) }}" width="100px" height="75px"><br>
                    @else
                    <br><br><br>
                    @endif
                    {{$show->get_notulen()->nama}}
                </td>
            </tr>
        @else
            <tr style="text-align: center;">
                <td colspan="2">{!! ($form->kode=='el0402') ? 'Direktur' : 'Nahkoda' !!}<br>
                    @if($show->get_nahkoda()->tanda_tangan)
                    <img src="file://{{ public_path('ttd_karyawan/' . $show->get_nahkoda()->tanda_tangan) }}" width="100px" height="75px"><br>
                    @else
                    <br><br><br>
                    @endif
                        {{$show->get_nahkoda()->nama}}
                </td>
                <td colspan="2">{!! ($form->kode=='el0402') ? 'DPA' : 'Mualim' !!}<br>
                    @if($show->get_dpa()->tanda_tangan)
                    <img src="file://{{ public_path('ttd_karyawan/' . $show->get_dpa()->tanda_tangan) }}" width="100px" height="75px"><br>
                    @else
                    <br><br><br>
                    @endif
                        {{$show->get_dpa()->nama}}
                </td>
                <td colspan="2">{!! ($form->kode=='el0402') ? 'Notulis' : 'KKM' !!}<br>
                    @if($show->get_notulen()->tanda_tangan)
                    <img src="file://{{ public_path('ttd_karyawan/' . $show->get_notulen()->tanda_tangan) }}" width="100px" height="75px"><br>
                    @else
                    <br><br><br>
                    @endif
                    {{$show->get_notulen()->nama}}
                </td>
            </tr>
        @endif
    </table>
    <div style="page-break-before: always;"></div>
    <table class="table-bordered" width="100%">
        <tr>
            <td colspan="2" style="text-align: center;" width="25%"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
            <td style="text-transform: uppercase;" colspan="3" width="45%"><h3>DAFTAR HADIR</h3></td>
            <td style="text-align: center;" width="30%"><b>EL-04-05</b><br> Hal <span class="page-number"></span></td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="6">Agenda Rapat : {{$form->nama}}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="6">Tanggal : {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="6">Tempat : {{$show->tempat}}</td>
        </tr>
        <tr style="text-align: left;">
            <td colspan="3">Nama</td>
            <td colspan="2">Jabatan</td>
            <td colspan="1">Tanda Tangan</td>
        </tr>
        @foreach($detail as $p)
        <tr style="text-align: left;">
            <td colspan="3">{{$p->get_karyawan()->nama}}</td>
            <td colspan="2">{{$p->get_jabatan()->nama}}</td>
            <td colspan="1">@if($p->get_karyawan()->tanda_tangan)
            <img src="file://{{ public_path('ttd_karyawan/' . $p->get_karyawan()->tanda_tangan) }}" width="100px" height="75px">
            @endif</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
