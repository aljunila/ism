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
            padding: 2px;
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
                <td>No</td>
                <td colspan="4">Materi</td>
                <td>Tingkatan</td>
            </tr>
            @foreach($item as $ck)
            @php
                $detail = $child[$ck->id] ?? [];
            @endphp
            <tr>
                <td colspan="6">{!!$ck->item!!}</td>    
            </tr>
            @foreach($detail as $c)
            @php
                $row = $dataItem[$c->id] ?? null;
                if($row['value']==4) {
                    $value = "Sangat Memuaskan";
                } elseif($row['value']==3) {
                    $value = "Cukup Memuaskan";
                } elseif($row['value']==2) {
                    $value = "Tidak Memuaskan";
                } else {
                    $value = "Sangat Tidak memuaskan";
                }
            @endphp
            <tr>
                <td>{{$loop->iteration}}</td>
                <td colspan="4" style="text-align: left;">{!!$c->item!!}</td>
                <td>{{ $value}}</td>
            </tr>
            @endforeach
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
                <td colspan="2">Menyetujui,<br>
                @if($menyetujui->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $menyetujui->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$menyetujui->nama}}
                </td>
                <td colspan="2">Membuat,<br>
                @if($membuat->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $membuat->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$membuat->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
