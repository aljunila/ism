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
        <table class="table-bordered" width="100%">
            <tr style="text-align: left;">
                <td colspan="2">Nama Kapal : {{$show->get_kapal()->nama}}</td>
                <td colspan="3">Bagian : {{$show->bagian}}</td>
                <td>Tanggal : {{ \Carbon\Carbon::parse($show->date)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td width="5">No</td>
                <td width="35">Jenis Barang</td>
                <td width="10">Satuan</td>
                <td width="10">Jumlah Satuan</td>
                <td width="10">Sisa Stok</td>
                <td width="30">Keterangan</td>
            </tr>
            @foreach($item as $row)
            <?php $sisa = $row->jml_minta-$row->jumlah; ?>
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$row->barang}}</td>
                <td>{{$row->satuan}}</td>
                <td>{{$row->jumlah}}</td>
                <td>{{$sisa}}</td>
                <td></td>
            </tr>
            @endforeach
        </table>
        <table class="table-bordered" width="100%">
            <tr>
                <td>Pengirim Barang<br>
                </td>
                <td>Disetujui Oleh<br>
                    
                    <br><br><br>
                    
                </td>
                <td>Penerima Barang<br>
                    
                    <br><br><br>
                    
                </td>
                <td>Mengetahui 1,<br>
                    
                    <br><br><br>
                    
                </td>
                <td>Mengetahui 2,<br>
                    
                    <br><br><br>
                    
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
