@php
    $styleTh = "background-color: #2684e9ff; color: black; border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
    $styleTd = "border: 1px solid #000; padding: 6px; font-family: Arial, sans-serif; font-size: 10px; padding: 8px 10px;";
@endphp

<table id="table" class="table custom-table" width="100%" border="1" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px;">
    <thead>
    <tr>
        <th style="{{ $styleTh }}">No.</th>
        <th style="{{ $styleTh }}">Nama Perusahaan</th>
        <th style="{{ $styleTh }}">Kode</th>
        <th style="{{ $styleTh }}">NPWP</th>
        <th style="{{ $styleTh }}">NIB</th>
        <th style="{{ $styleTh }}">Alamat</th>
        <th style="{{ $styleTh }}">Email</th>
        <th style="{{ $styleTh }}">No Telphone</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data as $show)
            <tr>
                <td style="{{ $styleTd }}">{{$loop->iteration}}</td>
                <td style="{{ $styleTd }}">{{$show->nama}}</td>
                <td style="{{ $styleTd }}">{{$show->kode}}</td>
                <td style="{{ $styleTd }}">{{$show->npwp}}</td>
                <td style="{{ $styleTd }}">{{ $show->nib }}</td>
                <td style="{{ $styleTd }}">{{ $show->alamat }}</td>
                <td style="{{ $styleTd }}">{{ $show->email }}</td>
                <td style="{{ $styleTd }}">{{$show->telp}}</td>
            </tr>
        @endforeach
    </tbody>
</table>