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
        <table class="table-bordered" width="100%">
            <tr>
                <td colspan="6">{!!$form->intruksi!!}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="3">Nama : {{$show->get_karyawan()->nama}}</td>
                <td colspan="3">Jabatan : {{$show->get_jabatan()->nama}}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="3">Kapal : {{$show->get_kapal()->nama}}</td>
                <td colspan="3">Tanggal : {{ \Carbon\Carbon::parse($show->date)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td colspan="4">Materi</td>
                <td><img src="file://{{ public_path('img/check.png') }}" width="25px"></td>
                <td><img src="file://{{ public_path('img/silang.jpg') }}" width="25px"></td>
            </tr>
            @foreach($item as $ck)
             @php
                $row = $dataItem[$ck->id] ?? null;
            @endphp
            <tr >
                <td colspan="4" style="text-align: left;">{!!$ck->item!!}</td>
                <td style="text-align: center;">
                    @if ($row['value'] == 1)
                    <img src="file://{{ public_path('img/check.png') }}" width="25px">
                    @endif
                </td>
                <td style="text-align: center;">
                    @if ($row['value'] == 0)
                    <img src="file://{{ public_path('img/silang.jpg') }}" width="25px">
                    @endif
                </td>
            </tr>
            @endforeach
            <tr style="text-align: left;">
                <td colspan="6">Catatan :<br>{!!$show->note!!}<br><br><br><br><br><br></td>
            </tr>
             <tr>
                <td colspan="2">Mengetahui,<br>
                @if($mengetahui->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $mengetahui->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$mengetahui->nama}}
                </td>
                <td colspan="2">Yang memberi penyuluhan,<br>
                @if($memberi->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $memberi->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$memberi->nama}}
                </td>
                <td colspan="2">Yang menerima penyuluhan,<br>
                @if($menerima->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $menerima->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$menerima->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
