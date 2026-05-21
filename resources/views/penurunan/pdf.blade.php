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
                <td style="text-transform: uppercase;" width="50%" rowspan="3"><h3>{{$form->nama}}<br>(SPB)<br>(DEPARTEMEN  {!! ($show->bagian==1) ? 'Deck' : 'Mesin' !!})</h3></td>
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
                <td colspan="5">Hari ini {{ \Carbon\Carbon::parse($show->tanggal)->locale('id')->translatedFormat('l') }}
                tanggal {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }} telah diturunkan barang-barang ini, 
                dari kapal ke kantor {!! ($show->id_cabang) ? $show->get_cabang()->nama : '' !!}</td>
            </tr>
            <tr>
                <td width="5">No</td>
                <td width="30">Nama Barang</td>
                <td width="25">Kondisi Barang</td>
                <td width="15">Jumlah</td>
                <td width="25">Keterangan</td>
            </tr>
            @foreach($item as $row)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$row->barang}}</td>
                <td>{{$row->kondisi}}</td>
                <td>{{$row->jumlah}}</td>
                <td></td>
            </tr>
            @endforeach
        </table>
        <table class="table-bordered" width="100%">
            <tr>
                <td>Yang menurunkan,<br>
                @if($buat->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $buat->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$buat->nama}}</u><br>
                    {{$buat->get_jabatan()->nama}}
                </td>

                <td>Mengetahui,<br>
                @if($setuju->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $setuju->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$setuju->nama}}</u><br>
                    NAHKODA
                </td>
                <td>Yang Menerima,<br>
                @if($terima->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $terima->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$terima->nama}}</u><br>
                    {{$terima->get_jabatan()->nama}}
                </td>
                
                <td>Mengetahui,<br>
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
