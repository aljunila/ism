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
                <td style="text-transform: uppercase;" width="70%" rowspan="2"><h3>PERMINTAAN BARANG UMUM</td>
                <td width="30%" style="text-align: center;"><b>Tanggal : {{ \Carbon\Carbon::parse($show->date)->format('d-m-Y') }}</b></td>
            </tr>
            <tr>
                <td><b>Nomor : {{$show->nomor}}</b></td>
            </tr>
        </table>
        <br><br>
        <table class="table-bordered" width="100%">
            <tr>
                <td width="5">No</td>
                <td width="35">Jenis Barang</td>
                <td width="15">Satuan</td>
                <td width="15">Jumlah Satuan</td>
                <td width="30">Keterangan</td>
            </tr>
            @foreach($item as $row)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$row->get_barang()->nama}} @if($row->get_barang()->kode) ({{ $row->get_barang()->kode }}) @endif</td>
                <td>{{$row->satuan}}</td>
                <td>{{$row->jumlah}}</td>
                <td>{{$row->ket}}</td>
            </tr>
            @endforeach
        </table>
        <table class="table-bordered" width="100%">
            <tr>
                <td>Yang Meminta,<br>
                @if($buat->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $buat->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$buat->nama}}</u><br>
                    {{$buat->get_jabatan()->nama}}
                </td>
                <td>Diperiksa Oleh,<br>
                
                <br><br><br>
               
                </td>
                <td>Menyetujui,<br>
                @if($mengetahui->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $mengetahui->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$mengetahui->nama}}</u><br>
                    KEPALA CABANG
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
