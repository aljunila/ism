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
            <table class="table-bordered" width="100%">
            <tr>
                <td width="25%" style="text-align: center;" rowspan="3"><img src="{{ public_path('img/'.$perusahaan->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" width="50%" rowspan="3"><h3>{{$form->nama}}</h3> <br>Nomor : {{$show->nomor}}</td>
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
            <tr>
                <td colspan="2">Permohonan Pengisian Bahan Bakar Minyak untuk kapal dengan rincian sebagai berikut :</td>
            </tr>
            <tr style="text-align: left;">
                <td>1. Nama Kapal</td>
                <td> : {{$show->get_kapal()->nama}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>2. Hari/Tanggal/Jam</td>
                <td> :  {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }} {{$show->jam}} </td>
            </tr>
            <tr style="text-align: left;">
                <td>3. Lintasan Penyebrangan</td>
                <td> : {{$show->lintasan}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>4. Jarak Lintasan</td>
                <td> : {{$show->jarak}} Mil</td>
            </tr>
            <tr style="text-align: left;">
                <td>5. Jumlah</td>
                <td> : {{$show->jumlah}} Liter</td>
            </tr>
             <tr style="text-align: left;">
                <td>6. ROB BBM Sebelum Bunker</td>
                <td> : {{$show->saldo_awal}} Liter</td>
            </tr>
            <tr style="text-align: left;">
                <td>6. ROB BBM Setelah Bunker</td>
                <td> : {{$show->saldo_akhir}} Liter</td>
            </tr>
        </table>
        <table class="table-bordered" width="100%">
            <tr>
                <td>Menyetujui,<br>
                @if($cabang->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $cabang->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$cabang->nama}}</u><br>
                    Kepala Cabang
                </td>
                <td>Mengetahui,<br>
                @if($nahkoda->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $nahkoda->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$nahkoda->nama}}</u><br>
                    Nahkoda
                </td>
                <td>Yang Mengajukan,<br>
                @if($kkm->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $kkm->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$kkm->nama}}</u><br>
                    Kepala Kamar Mesin
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
