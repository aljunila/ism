@php
    $styleTh = "background-color: #007bff; color: white; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp

<table id="table" class="table custom-table" width="100%" border="1" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px;">
    <thead>
    <tr>
        <th style="{{ $styleTh }}">No.</th>
        <th style="{{ $styleTh }}">NIP</th>
        <th style="{{ $styleTh }}">Nama</th>
        <th style="{{ $styleTh }}">NIK</th>
        <th style="{{ $styleTh }}">Jns Kelamin</th>
        <th style="{{ $styleTh }}">Tempat, Tanggal Lahir</th>
        <th style="{{ $styleTh }}">Status Perkawinan</th>
        <th style="{{ $styleTh }}">Agama</th>
        <th style="{{ $styleTh }}">Gol Darah</th>
        <th style="{{ $styleTh }}">Pendidikan</th>
        <th style="{{ $styleTh }}">Institusi Pendidikan</th>
        <th style="{{ $styleTh }}">Jurusan</th>
        <th style="{{ $styleTh }}">Sertifikat</th>
        <th style="{{ $styleTh }}">No Telp</th>
        <th style="{{ $styleTh }}">Email</th>
        <th style="{{ $styleTh }}">Alamat</th>
        <th style="{{ $styleTh }}">Nama Bank</th>
        <th style="{{ $styleTh }}">Nama Pemegang Rekening</th>
        <th style="{{ $styleTh }}">No Rekening</th>
        <th style="{{ $styleTh }}">NPWP</th>
        <th style="{{ $styleTh }}">Status PTKP</th>
        <th style="{{ $styleTh }}">No BPJS Kesehatan</th>
        <th style="{{ $styleTh }}">No BPJS Ketenagakerjaan</th>
        <th style="{{ $styleTh }}">Tgl Mulai Gabung</th>
        <th style="{{ $styleTh }}">Status karyawan</th>
        <th style="{{ $styleTh }}">Perusahaan</th>
        <th style="{{ $styleTh }}">Penempatan</th>
        <th style="{{ $styleTh }}">Jabatan</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data as $show)
            <tr>
                <td style="{{ $styleTd }}">{{$loop->iteration}}</td>
                <td style="{{ $styleTd }}">{{$show->nip}}</td>
                <td style="{{ $styleTd }}">{{$show->nama}}</td>
                <td style="{{ $styleTd }}">{{$show->nik}}</td>
                <td style="{{ $styleTd }}">{{ ($show->jk=='P') ? 'Pria' : 'Wanita' }}</td>
                <td style="{{ $styleTd }}">{!! ($show->tmp_lahir) ? $show->tmp_lahir : '-' !!}, {!! ($show->tgl_lahir) ? $show->tgl_lahir : '-' !!}</td>
                <td style="{{ $styleTd }}">{{ ($show->status_kawin=='M') ? 'Menikah' : 'Lajang' }}</td>
                <td style="{{ $styleTd }}">{{ $show->agama }}</td>
                <td style="{{ $styleTd }}">{{ $show->gol_darah }}</td>
                <td style="{{ $styleTd }}">{{ $show->pend }}</td>
                <td style="{{ $styleTd }}">{{$show->institusi_pend}}</td>
                <td style="{{ $styleTd }}">{{$show->jurusan}}</td>
                <td style="{{ $styleTd }}">{{$show->sertifikat}}</td>
                <td style="{{ $styleTd }}">{{$show->telp}}</td>
                <td style="{{ $styleTd }}">{{$show->email}}</td>
                <td style="{{ $styleTd }}">{{$show->alamat}}</td>
                <td style="{{ $styleTd }}">{{$show->nama_bank}}</td>
                <td style="{{ $styleTd }}">{{$show->nama_rekening}}</td>
                <td style="{{ $styleTd }}">{{$show->no_rekening}}</td>
                <td style="{{ $styleTd }}">{{$show->npwp}}</td>
                <td style="{{ $styleTd }}">{{$show->kode}}</td>
                <td style="{{ $styleTd }}">{{$show->bpjs_kes}}</td>
                <td style="{{ $styleTd }}">{{$show->bpjs_tk}}</td>
                <td style="{{ $styleTd }}">{{$show->tgl_mulai}}</td>
                <td style="{{ $styleTd }}">@switch($show->status_karyawan)
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
                                                @endswitch</td>
                <td style="{{ $styleTd }}">{{$show->perusahaan}}</td>
                <td style="{{ $styleTd }}">{{ $show->kapal ?? 'Office' }}</td>
                <td style="{{ $styleTd }}">{{$show->jabatan}}</td>
            </tr>
        @endforeach
    </tbody>
</table>