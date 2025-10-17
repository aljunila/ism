@php
    $styleTh = "background-color: #9fc5eeff; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp

<table id="table" class="table custom-table" width="100%" border="1" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px;">
    <thead>
    <tr>
        <th style="{{ $styleTh }}" rowspan="3">No.</th>
        <th style="{{ $styleTh }}" rowspan="3">Nama Kapal</th>
        <th style="{{ $styleTh }}" rowspan="3">No SIUP</th>
        <th style="{{ $styleTh }}" rowspan="3">Nama Pendaftaran</th>
        <th style="{{ $styleTh }}" rowspan="3">Grosse Akte Nomor</th>
        <th style="{{ $styleTh }}" rowspan="3">Dikeluarkan Oleh</th>
        <th style="{{ $styleTh }}" rowspan="3">Tanda Selar</th>
        <th style="{{ $styleTh }}" rowspan="3">Pemilik Kapal</th>
        <th style="{{ $styleTh }}" rowspan="3">Call Sign</th>
        <th style="{{ $styleTh }}" rowspan="3">Galangan / Tahun Buat</th>
        <th style="{{ $styleTh }}" rowspan="3">Bendera</th>
        <th style="{{ $styleTh }}" rowspan="3">Konstruksi</th>
        <th style="{{ $styleTh }}" colspan="2">Dikelaskan pada</th>
        <th style="{{ $styleTh }}" rowspan="3">Type Kapal</th>
        <th style="{{ $styleTh }}" colspan="16">Ukuran Pokok</th>
        <th style="{{ $styleTh }}" colspan="6">Mesin</th>
        <th style="{{ $styleTh }}" colspan="3">Kecepatan</th>
        <th style="{{ $styleTh }}" colspan="2">Bahan Bakar</th>
    </tr>
    <tr>
        <th style="{{ $styleTh }}" rowspan="2">Kode Kelas</th>
        <th style="{{ $styleTh }}" rowspan="2">Daerah Pelayaran</th>
        <th style="{{ $styleTh }}" rowspan="2">LOA</th>
        <th style="{{ $styleTh }}" rowspan="2">LBP</th>
        <th style="{{ $styleTh }}" rowspan="2">Lebar Kapal</th>
        <th style="{{ $styleTh }}" rowspan="2">Dalam</th>
        <th style="{{ $styleTh }}" colspan="4">Draft Kapal</th>
        <th style="{{ $styleTh }}" colspan="2">Isi</th>
        <th style="{{ $styleTh }}" rowspan="2">Bobot Mati</th>
        <th style="{{ $styleTh }}" colspan="5">Kapasitas</th>
        <th style="{{ $styleTh }}" colspan="3">Mesin Induk</th>
        <th style="{{ $styleTh }}" colspan="3">Mesin Bantu</th>
        <th style="{{ $styleTh }}" rowspan="2">Maksimum</th>
        <th style="{{ $styleTh }}" rowspan="2">Normal</th>
        <th style="{{ $styleTh }}" rowspan="2">Ekonomis</th>
        <th style="{{ $styleTh }}" rowspan="2">Jenis Bahan Bakar</th>
        <th style="{{ $styleTh }}" rowspan="2">Kebutuhan perhari</th>
    </tr>
    <tr>
        <th style="{{ $styleTh }}">Summer Draft</th>
        <th style="{{ $styleTh }}">Winter Draft</th>
        <th style="{{ $styleTh }}">Draft pada air tawar</th>
        <th style="{{ $styleTh }}">Tropical Draft</th>
        <th style="{{ $styleTh }}">Isi Kotor</th>
        <th style="{{ $styleTh }}">NT</th>
        <th style="{{ $styleTh }}">Penumpang</th>
        <th style="{{ $styleTh }}">Mobil/Truck</th>
        <th style="{{ $styleTh }}">Kontainer</th>
        <th style="{{ $styleTh }}">Grain Space</th>
        <th style="{{ $styleTh }}">Bale Space</th>
        <th style="{{ $styleTh }}">Merek</th>
        <th style="{{ $styleTh }}">Tahun</th>
        <th style="{{ $styleTh }}">Nomor</th>
        <th style="{{ $styleTh }}">Merek</th>
        <th style="{{ $styleTh }}">Tahun</th>
        <th style="{{ $styleTh }}">Nomor</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data as $show)
            <tr>
                <td style="{{ $styleTd }}">{{$loop->iteration}}</td>
                <td style="{{ $styleTd }}">{{$show->nama}}</td>
                <td style="{{ $styleTd }}">{{$show->no_siup}}</td>
                <td style="{{ $styleTd }}">{{$show->pendaftaran}}</td>
                <td style="{{ $styleTd }}">{{$show->no_akte}}</td>
                <td style="{{ $styleTd }}">{{$show->dikeluarkan_di}}</td>
                <td style="{{ $styleTd }}">{{$show->selar}}</td>
                <td style="{{ $styleTd }}">{{$show->perusahaan}}</td>
                <td style="{{ $styleTd }}">{{$show->call_sign}}</td>
                <td style="{{ $styleTd }}">{{$show->galangan}}</td>
                <td style="{{ $styleTd }}">ID</td>
                <td style="{{ $styleTd }}">{{$show->konstruksi}}</td>
                <td style="{{ $styleTd }}">-</td>
                <td style="{{ $styleTd }}">Lokal</td>
                <td style="{{ $styleTd }}">{{$show->type}}</td>
                <td style="{{ $styleTd }}">{{$show->loa}} m</td>
                <td style="{{ $styleTd }}">{{$show->lbp}} m</td>
                <td style="{{ $styleTd }}">{{$show->lebar}} m</td>
                <td style="{{ $styleTd }}">{{$show->dalam}} m</td>
                <td style="{{ $styleTd }}">{{$show->summer_draft}} m</td>
                <td style="{{ $styleTd }}">{{$show->winter_draft}} m</td>
                <td style="{{ $styleTd }}">{{$show->draft_air_tawar}} m</td>
                <td style="{{ $styleTd }}">{{$show->tropical_draft}} m</td>
                <td style="{{ $styleTd }}">{{$show->isi_kotor}}</td>
                <td style="{{ $styleTd }}">{{$show->nt}}</td>
                <td style="{{ $styleTd }}">{{$show->bobot_mati}}</td>
                <td style="{{ $styleTd }}">- orang</td>
                <td style="{{ $styleTd }}">- unit</td>
                <td style="{{ $styleTd }}">0 teus</td>
                <td style="{{ $styleTd }}">- ton</td>
                <td style="{{ $styleTd }}">-</td>
                <td style="{{ $styleTd }}">{{$show->merk_mesin_induk}}</td>
                <td style="{{ $styleTd }}">{{$show->tahun_mesin_induk}}</td>
                <td style="{{ $styleTd }}">{{$show->no_mesin_induk}}</td>
                <td style="{{ $styleTd }}">{{$show->merk_mesin_bantu}}</td>
                <td style="{{ $styleTd }}">{{$show->tahun_mesin_bantu}}</td>
                <td style="{{ $styleTd }}">{{$show->no_mesin_bantu}}</td>
                <td style="{{ $styleTd }}">{{$show->max_speed}} knot</td>
                <td style="{{ $styleTd }}">{{$show->normal_speed}} knot</td>
                <td style="{{ $styleTd }}">{{$show->min_speed}} knot</td>
                <td style="{{ $styleTd }}">{{$show->bahan_bakar}}</td>
                <td style="{{ $styleTd }}">{{$show->jml_butuh}} ton</td>
        @endforeach   
    </tbody>
</table>