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
            padding: 3px;
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
                <td colspan="2" width="25%"><img src="{{ public_path('img/'.$periode->get_perusahaan()->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" colspan="4" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;"><b>{{$form->ket}}</b><br> Hal <span class="page-number"></span></td>
            </tr>
            <tr>
                <td colspan="7">{!!$form->intruksi!!}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="4">Nama Kapal : {{ $periode->get_kapal()->nama }}</td>
                <td colspan="3">Tanggal : {{ \Carbon\Carbon::parse($show->tgl_nilai)->format('d-m-Y') }}</td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="4">Nama Awak Kapal : {{ $show->get_karyawan()->nama }}</td>
                <td colspan="3">Jabatan : {{ $show->get_jabatan()->nama }}</td>
            </tr>
            <tr>
                <td colspan="3">Kriteria</td>
                <td>Kurang</td>
                <td>Sedang</td>
                <td>Baik</td>
                <td>Sangat Baik</td>
            </tr>
            @foreach($item as $ck)
            <tr >
                <td colspan="3" style="text-align: left;">{!! $ck->get_item()->item !!}</td>
                <td style="text-align: center;">
                    @if($ck->value==1)
                    <img src="file://{{ public_path('img/check.png') }}" width="25px"><br>
                    @endif
                </td>
                <td style="text-align: center;">
                    @if($ck->value==2)
                    <img src="file://{{ public_path('img/check.png') }}" width="25px"><br>
                    @endif
                </td>
                <td style="text-align: center;">
                    @if($ck->value==3)
                    <img src="file://{{ public_path('img/check.png') }}" width="25px"><br>
                    @endif
                </td>
                <td style="text-align: center;">
                    @if($ck->value==4)
                    <img src="file://{{ public_path('img/check.png') }}" width="25px"><br>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr style="text-align: left;">
                <td colspan="7">Rekomendasi Penilai :<br>
                @if($show->rekomendasi==1) Dapat dipromosikan
                @elseif($show->rekomendasi==2) Dapat dipertahankan
                @elseif($show->rekomendasi==3) Perlu peningkatan kemampuan profesinya 
                @elseif($show->rekomendasi==4) Perlu peningkatan akhlak dan budi pekertinya 
                @else Direkomendasikan turun dari kapal @endif
            </tr>
            <tr style="text-align: left;">
                <td colspan="7">Catatan :<br>{!!$show->note!!}<br><br><br>
            </tr>
            <tr>
                <td colspan="3">Penilai I,<br>
                @if($show->get_penilai1()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_penilai1()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                {{$show->get_penilai1()->nama}}
                </td>
                <td colspan="2">Penilai II,<br>
                @if($show->get_penilai2()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_penilai2()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_penilai2()->nama}}
                </td>
                <td colspan="2">Mengetahui,<br>
                @if($show->get_mengetahui()->tanda_tangan)
                <img src="file://{{ public_path('ttd_karyawan/' . $show->get_mengetahui()->tanda_tangan) }}" width="100px" height="75px"><br>
                @else
                <br><br><br>
                @endif
                    {{$show->get_mengetahui()->nama}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
