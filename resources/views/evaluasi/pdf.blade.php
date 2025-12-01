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
                <td colspan="2" width="25%"><img src="{{ public_path('img/'.$master->get_perusahaan()->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="3" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            @if($form->kode=='el0605')
            <tr style="text-align: left;">
                <td colspan="2">Nama Kapal : {{$master->get_kapal()->nama}}</td>
                <td colspan="2">Jabatan : </td>
                <td colspan="2">Tanggal : {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="2">Nama : {{ $master->get_karyawan()->nama }}</td>
                <td colspan="2">Naik di/pada : {{ $show->ket }}</td>
                <td colspan="2">Tanggal Pelatihan: {{ \Carbon\Carbon::parse($master->date)->format('d-m-Y') }}</td>
            </tr>
            @else
            <tr style="text-align: left;">
                <td colspan="3">Nama Kapal : {{$master->get_kapal()->nama}}</td>
                <td colspan="3">Jabatan : </td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="3">Nama : {{ $master->get_karyawan()->nama }}</td>
                <td colspan="3">Tanggal : {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="5">Materi</td>
                <td>Tingkatan</td>
            </tr>
            @foreach($item as $ck)
            @php
                $detail = $child[$ck->checklist_item_id] ?? [];
            @endphp
            <tr >
                <td colspan="6" style="text-align: left;">{!! $ck->item !!}</td>     
            </tr>
            @foreach($detail as $c)
            <tr>
                <td colspan="5" style="text-align: left;">{!! $c->item !!}</td>
                <td style="text-align: center;">
                    {{$text = match ($c->value) {
                        1 => 'Sangat Tidak memuaskan',
                        2 => 'Tidak Memuaskan ',
                        3 => 'Cukup Memuaskan',
                        default  => 'Sangat Memuaskan',
                    };}}
                </td>            
            </tr>
            @endforeach
            @endforeach
             <tr >
                <td colspan="6" style="text-align: left;">Tanggapan:<br>{!! $show->note !!}</td>     
            </tr>
            @if($form->kode=='el0605')
            <tr>
                <td colspan="3">Nahkoda,<br>
                @if($show->get_nahkoda()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_nahkoda()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_nahkoda()->nama}}
                </td>
                <td colspan="3">KKM,<br>
                @if($show->get_instruktur()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_instruktur()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_instruktur()->nama}}
                </td>
            </tr>
            @else
            <tr>
                <td colspan="2">Mengetahui,<br>
                @if($show->get_kepala()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_kepala()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$show->get_kepala()->nama}}</u><br>Kepala Cabang
                </td>
                <td colspan="2">Menyetujui,<br>
                @if($show->get_nahkoda()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_nahkoda()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$show->get_nahkoda()->nama}}</u><br>Nahkoda
                </td>
                <td colspan="2">Yang membuat,<br>
                @if($show->get_instruktur()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_instruktur()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    <u>{{$show->get_instruktur()->nama}}</u><br>Instruktur
                </td>
            </tr>
            @endif
        </table>
    </div>
</body>
</html>
