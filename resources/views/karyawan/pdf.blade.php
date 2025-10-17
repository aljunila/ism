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

        tr {
            text-align: left;
            line-height: 1.5;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div>
        <table class="" width="100%">
            <tr>
                <td width="25%" align="center"><img src="{{ public_path('img/'.$show->get_perusahaan()->logo) }}" alt="" width="50%"></td>
                <td  colspan="2" style="text-transform: uppercase;" width="50%" align="center"><h3>DATA KARYAWAN<br>{{$show->get_perusahaan()->nama}}</h3></td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            <tr style="text-align: left;">
                <td width="25%">Nama</td>
                <td width="5%">:</td>
                <td width="70%">{{$show->nama}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>NIP</td>
                <td>:</td>
                <td>{{$show->nip}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>NIK</td>
                <td>:</td>
                <td>{{$show->nik}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{($show->jk=='P') ? 'Pria' : 'Wanita'}}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{!! ($show->tmp_lahir) ? $show->tmp_lahir : '-' !!}, {!! ($show->tgl_lahir) ? $show->tgl_lahir : '-' !!}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Status Perkawinan</td>
                <td>:</td>
                <td>{{ ($show->status_kawin=='M') ? 'Menikah' : 'Lajang' }}</td>
            </tr>
            <tr style="text-align: left;">
                <td>Agama</td>
                <td>:</td>
                <td>{!! ($show->agama) ? $show->agama : '-' !!}</td>
            </tr>
            <tr>
                <td> Golongan Darah</td>
                <td>:</td>
                <td>{!! ($show->gol_darah) ? $show->gol_darah : '-' !!}</td>
            </tr>
            <tr>
                <td> Pendidikan</td>
                <td>:</td>
                <td>{!! ($show->pend) ? $show->pend : '-' !!}</td>
            </tr>
            <tr>
                <td> Institusi Pendidikan</td>
                <td>:</td>
                <td>{!! ($show->institusi_pend) ? $show->institusi_pend : '-' !!}</td>
            </tr>
            <tr>
                <td> Jurusan</td>
                <td>:</td>
                <td>{!! ($show->jurusan) ? $show->jurusan : '-' !!}</td>
            </tr>
            <tr>
                <td> Sertifikat</td>
                <td>:</td>
                <td>{!! ($show->sertifikat) ? $show->sertifikat : '-' !!}</td>
            </tr>
            <tr>
                <td> No Telp</td>
                <td>:</td>
                <td>{!! ($show->telp) ? $show->telp : '-' !!}</td>
            </tr>
            <tr>
                <td> Email</td>
                <td>:</td>
                <td>{!! ($show->email) ? $show->email : '-' !!}</td>
            </tr>
            <tr>
                <td> Alamat</td>
                <td>:</td>
                <td>{!! ($show->alamat) ? $show->alamat : '-' !!}</td>
            </tr>
            <tr>
                <td> Nama Bank</td>
                <td>:</td>
                <td>{!! ($show->nama_bank) ? $show->nama_bank : '-' !!}</td>
            </tr>
            <tr>
                <td> Nama Pemilik Rekening</td>
                <td>:</td>
                <td>{!! ($show->nama_rekening) ? $show->nama_rekening : '-' !!}</td>
            </tr>
            <tr>
                <td> No Rekening</td>
                <td>:</td>
                <td>{!! ($show->no_rekening) ? $show->no_rekening : '-' !!}</td>
            </tr>
            <tr>
                <td> No Telp</td>
                <td>:</td>
                <td>{!! ($show->telp) ? $show->telp : '-' !!}</td>
            </tr>
            <tr>
                <td> Status PTKP</td>
                <td>:</td>
                <td>{!! ($show->status_ptkp) ? $show->status_ptkp : '-' !!}</td>
            </tr>
            <tr>
                <td> BPJS Kesehatan</td>
                <td>:</td>
                <td>{!! ($show->bpjs_kes) ? $show->bpjs_kes : '-' !!}</td>
            </tr>
            <tr>
                <td> BPJS Ketenagakerjaan</td>
                <td>:</td>
                <td>{!! ($show->bpjs_tk) ? $show->bpjs_tk : '-' !!}</td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            <tr>
                <td> Perusahaan</td>
                <td>:</td>
                <td>{!! ($show->id_perusahaan) ? $show->get_perusahaan()->nama : '-' !!}</td>
            </tr>
            <tr>
                <td> Ditempatkan di</td>
                <td>:</td>
                <td>{!! ($show->kapal) ? $show->kapal : 'Office' !!}</td>
            </tr>
            <tr>
                <td> Jabatan</td>
                <td>:</td>
                <td>{!! ($show->id_jabatan) ? $show->get_jabatan()->nama : '-' !!}</td>
            </tr>
            <tr>
                <td>Status Karyawan</td>
                <td>:</td>
                <td>@switch($show->status_karyawan)
                        @case('TP')
                            Tetap Permanen
                            @break

                        @case('TC')
                            Tetap Percobaan
                            @break

                        @case('K')
                            Kontrak
                            @break

                        @case('F')
                            Freelance
                            @break

                        @case('M')
                            Magang
                            @break

                        @default
                            -
                    @endswitch
                </td>
            </tr>
            <tr>
                <td> Tanggal Mulai Gabung</td>
                <td>:</td>
                <td>{!! ($show->tgl_mulai) ? $show->tgl_mulai : '-' !!}</td>
            </tr>
        </table>
    </div>
</body>
</html>
