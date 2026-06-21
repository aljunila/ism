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
    
    @foreach($kel as $k)
        @php
            $get = $gudang[$k->id] ?? [];
        @endphp
    @if(count($get))
    <div>
        <table class="table-bordered" width="100%">
            <tr>
                <td width="25%" style="text-align: center;" rowspan="3"><img src="{{ public_path('img/'.$perusahaan->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" width="50%"><h3>{{$form->nama}}</h3></td>
                <td width="25%" style="text-align: center;" colspan="3"><b>{{$form->judul}}</b></td>
            </tr>
            <tr>
                <td rowspan="2">{{$k->nama}}<br>{{$k->kode}}<br>{{$k->ket}}</td>
                <td>{{$form->pj}}</td>
                <td>{{$form->kode_file}}</td>
                <td>{{$form->periode}}</td>
            </tr>
            <tr style="text-align: left;"><td colspan="3"> Hal <span class="page-number"></span></td></tr>
        </table>
        <table class="table-bordered" width="100%">
            <tr style="text-align: left;">
                <td>Nama Kapal: {{$show->nama}}</td>
                <td>Periode : </td>
            </tr>
            <tr style="text-align: left;">
                <td>Bagian : {{ ($show->kategori=='1') ? 'DECK' : 'MESIN' }}</td>
                <td></td>
            </tr>
        </table>
        <table class="table-bordered" width="100%">
            <tr >
                <td rowspan="2">No</td>
                <td rowspan="2">Nama Barang</td>
                <td rowspan="2">Part Number</td>
                <td rowspan="2">Jumlah</td>
                <td colspan="2">Kondisi</td>
            </tr>
            <tr>
                <td>Baik</td>
                <td>Rusak</td>
            </tr>
            @foreach($get as $g)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td style="text-align: left;">{{$g->barang}}</td>
                <td style="text-align: left;">{{$g->kode}}</td>
                <td>{{$g->jumlah}} {{$g->des}}</td>
                <td>{{$g->baik}}</td>
                <td>{{$g->habis}}</td>
            </tr>
            @endforeach
        </table>
        <div style="page-break-before: always;"></div>
    </div>
    @endif
    @endforeach
</body>
</html>
