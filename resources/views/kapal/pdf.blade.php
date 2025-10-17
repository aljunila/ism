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

        tr {
            text-align: left;
            line-height: 1.5;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div>
        <table class="" width="100%">
            <tr>
                <td width="25%" align="center"><img src="{{ public_path('img/'.$show->get_pemilik()->logo) }}" alt="" width="50%"></td>
                <td  colspan="2" style="text-transform: uppercase;" width="50%" align="center"><h3>DATA KAPAL<br>{{$show->get_pemilik()->nama}}</h3></td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            <tr style="text-align: left;">
                <td width="25%">Nama Kapal</td>
                <td width="5%">:</td>
                <td width="70%">{{$show->nama}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>No SIUP</td>
                <td>:</td>
                <td>{{$show->no_siup}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Nama Pendaftaran</td>
                <td>:</td>
                <td>{{$show->pendaftaran}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Grosse Akte Nomor</td>
                <td>:</td>
                <td>{{$show->no_akte}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Dikeluarkan Oleh</td>
                <td>:</td>
                <td>{{ $show->dikeluarkan_di }}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Tanda Selar</td>
                <td>:</td>
                <td>{{ $show->selar }}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Pemilik Kapal</td>
                <td>:</td>
                <td>{!! ($show->pemilik) ? $show->get_pemilik()->nama : '-' !!}</td>
            </tr>
            <tr>
                <td> Call Sign</td>
                <td>:</td>
                <td>{!! ($show->call_sign) ? $show->call_sign : '-' !!}</td>
            </tr>
            <tr>
                <td> Galangan/Tahun buat</td>
                <td>:</td>
                <td>{!! ($show->galangan) ? $show->galangan : '-' !!}</td>
            </tr>
            <tr>
                <td> Bendera</td>
                <td>:</td>
                <td>ID</td>
            </tr>
            <tr>
                <td> Kontruksi</td>
                <td>:</td>
                <td>{!! ($show->kontruksi) ? $show->kontruksi : '-' !!}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;">Dikelaskan pada</td>
            </tr>
            <tr>
                <td> Kode Kelas</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td> Daerah Pelayaran</td>
                <td>:</td>
                <td>Lokal</td>
            </tr>
            <tr>
                <td> Type Kapal</td>
                <td>:</td>
                <td>{!! ($show->type) ? $show->type : '-' !!}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: left;">Ukuran Pokok</th>
            </tr>
            <tr>
                <td> Panjang kapal seluruhnya (LOA)</td>
                <td>:</td>
                <td>{{ $show->loa }} meter</td>
            </tr>
            <tr>
                <td> Panjang antara garis tegak (LBP)</td>
                <td>:</td>
                <td>{{ $show->lbp }} meter</td>
            </tr>
            <tr>
                <td> Dalam Kapal</td>
                <td>:</td>
                <td>{{ $show->dalam }} meter</td>
            </tr>
            <tr>
                <td> Lebar Kapal</td>
                <td>:</td>
                <td>{{ $show->lebar }} meter</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: left;">Draft Kapal</th>
            </tr>
            <tr>
                <td>Sarat musim panas (Summer Draft)</td>
                <td>:</td>
                <td>{{ $show->summer_draft }} meter</td>
            </tr>
            <tr>
                <td>Sarat musim dingin (Winter Draft)</td>
                <td>:</td>
                <td>{{ $show->winter_draft }} meter</td>
            </tr>
            <tr>
                <td>Draft pada air tawar</td>
                <td>:</td> 
                <td>{{ $show->draft_air_tawar }} meter</td>
            </tr>
            <tr>
                <td>Draft pada air tawar</td>
                <td>:</td> 
                <td>{{ $show->draft_air_tawar }} meter</td>
            </tr>
            <tr>
                <td>Sarat Tropik (Tropical Draft)</td>
                <td>:</td> 
                <td>{{ $show->tropical_draft }} meter</td>
            </tr>
            <tr>
                <td>Isi Kotor</td>
                <td>:</td> 
                <td>{{ $show->isi_kotor }}</td>
            </tr>
            <tr>
                <td>Bobot Mati</td>
                <td>:</td> 
                <td>{{ $show->bobot_mati }}</td>
            </tr>
            <tr>
                <td>NT</td>
                <td>:</td> 
                <td>{{ $show->nt }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: left;">Mesin Induk</th>
            </tr>
            <tr>
                <td>Merek</td>
                <td>:</td>
                <td>{{ $show->merk_mesin_induk }}</td>
            </tr>
            <tr>
                <td>Tahun</td>
                <td>:</td>
                <td>{{ $show->tahun_mesin_induk }}</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td>{{ $show->nomor_mesin_induk }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: left;">Mesin Bantu</th>
            </tr>
            <tr>
                <td>Merek</td>
                <td>:</td>
                <td>{{ $show->merk_mesin_bantu }}</td>
            </tr>
            <tr>
                <td>Tahun</td>
                <td>:</td>
                <td>{{ $show->tahun_mesin_bantu }}</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td>{{ $show->nomor_mesin_bantu }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: left;">Kecepatan/Speed</th>
            </tr>
            <tr>
                <td>Maksimum</td>
                <td>:</td>
                <td>{{ $show->max_speed }} knot</td>
            </tr>
            <tr>
                <td>Normal</td>
                <td>:</td>
                <td>{{ $show->normal_speed }} knot</td>
            </tr>
            <tr>
                <td>Minimum</td>
                <td>:</td>
                <td>{{ $show->min_speed }} knot</td>
            </tr>
            <tr>
                <td>Bahan bakar</td>
                <td>:</td>
                <td>{{ $show->bahan_bakar }}</td>
            </tr>
            <tr>
                <td>Kebutuhan /hari</td>
                <td>:</td>
                <td>{{ $show->jml_butuh }} ton</td>
            </tr>
        </table>
    </div>
</body>
</html>
