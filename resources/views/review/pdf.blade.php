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
                <td colspan="2" width="25%"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="3">No Master Review : {{$show->no_review}}</td>
                <td colspan="3">Tanggal Review : {{ \Carbon\Carbon::parse($show->tgl_review)->format('d-m-Y') }}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="3">Nama Kapal: {{$show->get_kapal()->nama}}</td>
                <td colspan="3">Tanggal Diterima : {{ \Carbon\Carbon::parse($show->tgl_diterima)->format('d-m-Y') }}</td>
            </tr>
            <tr style="text-align: left;"> 
                <td colspan="6">Untuk memenuhi Manual SMK Elemen 5 / B.1.g, Nakhoda di kapal harus menyampaikan hasil review terhadap prosedur dan formulir dari 
                    Sistem Manajemen Keselamatan Perusahaan yang ada di kapal, untuk perbaikan berkelanjutan dan meningkatkan keselamatan dan perlindungan terhadap lingkungan.</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="6">Hasil review Nakhoda terhadap prosedur dan formulir dari Sistem Manajemen Keselamatan (SMK) :<br>{!! $show->hasil !!}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="6">Tanggapan DPA terhadap hasil review Nakhoda :<br>{!! $show->ket !!}</td>
            </tr>
            <tr>
                <td colspan="3">Nahkoda<br>
                @if($show->get_nahkoda()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_nahkoda()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_nahkoda()->nama}}
                </td>
                <td colspan="3">DPA<br>
                @if($show->get_dpa()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_dpa()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_dpa()->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
