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
                <td rowspan="2">No</td>
                <td rowspan="2">Nama</td>
                <td rowspan="2">Jabatan</td>
                <td>Ijazah Pelaut</td>
                <td colspan="2">Endorsment</td>
                <td colspan="2">Buku Pelaut</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>Nomor</td>
                <td>Berlaku</td>
                <td>Nomor</td>
                <td>Berlaku</td>
            </tr>
            @foreach($crew as $row)
            <tr >
                <td style="text-align: left;">{{$loop->iteration}}</td> 
                <td>{{ $row->nama }}</td>
                <td>{{ $row->jabatan }}</td>

                <td>{{ $row->ijazah_no ?? '-' }}</td>

                <td>{{ $row->endorse_no ?? '-' }}</td>
                 @php
                    if ($row->endorse_berlaku) {
                        $tglEndorse = \Carbon\Carbon::parse($row->endorse_berlaku)->startOfDay();
                        $hariSisa = now()->startOfDay()->diffInDays($tglEndorse, false);
                        $expired_endors = $tglEndorse->format('d-m-Y');
                        $style = '';

                        if ($Sisa <= 7) {
                            $style = 'danger';
                        } elseif ($Sisa <= 14) {
                            $style = 'warning';
                        } elseif ($Sisa <= 30) {
                            $style = 'success';
                        }
                    } else {
                        $expired_endors = '-';
                        $style= '';
                    }
                    
                @endphp
                <td class="{{ $style }}">
                    {{ $expired_endors }}
                </td> 

                <td>{{ $row->buku_no ?? '-' }}</td>
                @php
                if ($row->buku_berlaku) {
                    $tglBuku = \Carbon\Carbon::parse($row->buku_berlaku)->startOfDay();
                    $hariSisa = now()->startOfDay()->diffInDays($tglBuku, false);
                    $expired_buku = $tglBuku->format('d-m-Y');
                    $class = '';

                    if ($hariSisa <= 30) {
                        $class = 'danger';
                    } elseif ($hariSisa <= 60) {
                        $class = 'warning';
                    } elseif ($hariSisa <= 90) {
                        $class = 'success';
                    }
                } else {
                    $expired_buku = '-';
                    $class = '';
                }
                @endphp

                <td class="{{ $class }}">
                    {{ $expired_buku }}
                </td> 
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
