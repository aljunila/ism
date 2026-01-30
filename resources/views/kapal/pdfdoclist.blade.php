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

        .danger { background-color: #dc3545; color: white; }
        .warning { background-color: #ffc107; }
        .success { background-color: #198754; color: white; }
    </style>
</head>
<body>
    <div>
        <table class="table-bordered" width="100%">
            <tr>
                <td width="25%" style="text-align: center;" rowspan="3"><img src="{{ public_path('img/'.$show->get_pemilik()->logo) }}" alt="" width="50%"></td>
                <td style="text-transform: uppercase;" width="45%" rowspan="3"><h3>{{$form->nama}} <br> KAPAL {{$show->nama}}</h3></td>
                <td width="30%" style="text-align: center;" colspan="3"><b>{{$form->judul}}</b></td>
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
                <td width="5%">No</td>
                <td width="20%">Surat Kapal</td>
                <td width="10%">Penerbit</td>
                <td width="15%">Nomor</td>
                <td width="12%">Tgl Terbit</td>
                <td width="15%">Tgl Expired</td>
                <td width="15%">Keterangan</td>
            </tr>
            @foreach($doc as $row)
            <tr >
                <td style="text-align: left;">{{$loop->iteration}}</td> 
                <td style="text-align: left;">{{ $row->nama }}</td>
                <td style="text-align: left;">{{ $row->ket }}</td>

                <td>{{ $row->no ?? '-' }}</td>

                <td>{{ \Carbon\Carbon::parse($row->tgl_terbit)->format('d-m-Y') }}</td>
                 @php
                    if ($row->tgl_expired) {
                        $tglExpired = \Carbon\Carbon::parse($row->tgl_expired)->startOfDay();
                        $Sisa = now()->startOfDay()->diffInDays($tglExpired, false);
                        $expired = $tglExpired->format('d-m-Y');
                        $style = '';

                        if ($Sisa <= 30) {
                            $style = 'danger';
                        } elseif ($Sisa <= 60) {
                            $style = 'warning';
                        } elseif ($Sisa <= 90) {
                            $style = 'success';
                        }
                    } else {
                        $expired = '-';
                        $style= '';
                    }
                    
                @endphp
                <td class="{{ $style }}">
                    {{ $expired }}
                </td> 
                <td></td> 
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
