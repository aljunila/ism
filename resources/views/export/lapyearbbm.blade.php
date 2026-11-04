
@php
    $styleTh = "background-color:#e8e524;color:black;border:1px solid #000;padding:8px 10px;font-family:Arial;font-size:10px;";
    $styleTd = "border:1px solid #000;padding:8px 10px;font-family:Arial;font-size:10px;";

    // Rekap tahunan per kapal
    $rekap = [];

    foreach ($fleet as $f) {
        $rekap[$f->id] = [
            // Overall
            'me' => 0,
            'jam_me' => 0,
            'ae' => 0,
            'jam_ae' => 0,
            'pakai' => 0,
            'tambah' => 0,
            'trip' => 0,

            // In Operation
            'me_1' => 0,
            'jam_me_1' => 0,
            'ae_1' => 0,
            'jam_ae_1' => 0,
            'pakai_1' => 0,
            'trip_1' => 0,

            // Anchorage
            'me_2' => 0,
            'jam_me_2' => 0,
            'ae_2' => 0,
            'jam_ae_2' => 0,
            'pakai_2' => 0,
            'trip_2' => 0,
        ];
    }
@endphp

<table border="1">
    {{-- JUDUL --}}
    <tr>
        <th colspan="{{ 1 + ($fleet->count() * 13) }}"
            style="{{ $styleTh }}">
            LAPORAN DATA BBM TAHUNAN {{ $year }}
        </th>
    </tr>

    {{-- HEADER KAPAL --}}
    <tr>
        <th style="{{ $styleTh }}" rowspan="2">Bulan</th>

        @foreach($fleet as $f)
            <th style="{{ $styleTh }}" colspan="13">
                {{ $f->nama }}
            </th>
        @endforeach
    </tr>

    {{-- HEADER KOLOM --}}
    <tr>
        @foreach($fleet as $f)
            <th style="{{ $styleTh }}">Status</th>
            <th style="{{ $styleTh }}">Saldo Awal</th>

            <th style="{{ $styleTh }}">ME Jumlah</th>
            <th style="{{ $styleTh }}">ME Jam Operasi</th>
            <th style="{{ $styleTh }}">ME Rata-Rata</th>

            <th style="{{ $styleTh }}">AE Jumlah</th>
            <th style="{{ $styleTh }}">AE Jam Operasi</th>
            <th style="{{ $styleTh }}">AE Rata-Rata</th>

            <th style="{{ $styleTh }}">Total Pemakaian</th>
            <th style="{{ $styleTh }}">Penambahan</th>
            <th style="{{ $styleTh }}">Saldo Akhir</th>
            <th style="{{ $styleTh }}">Jumlah Trip/Hari</th>
            <th style="{{ $styleTh }}">Rata-Rata</th>
        @endforeach
    </tr>

    {{-- DATA BULANAN --}}
    @foreach($months as $month)

        {{-- BARIS IN OPERATION --}}
        <tr>
            <td style="{{ $styleTd }}" rowspan="2">
                {{ $month['bulan'] }}
            </td>

            @foreach($month['kapal'] as $kp)
                @php
                    $op = $kp['operation'];
                    $an = $kp['anchorage'];

                    // Akumulasi Overall
                    $rekap[$kp['id']]['me'] += $op['me'] + $an['me'];
                    $rekap[$kp['id']]['jam_me'] += $op['jam_me'] + $an['jam_me'];

                    $rekap[$kp['id']]['ae'] += $op['ae'] + $an['ae'];
                    $rekap[$kp['id']]['jam_ae'] += $op['jam_ae'] + $an['jam_ae'];

                    $rekap[$kp['id']]['pakai'] += $op['pakai'] + $an['pakai'];
                    $rekap[$kp['id']]['tambah'] += $op['tambah'] + $an['tambah'];
                    $rekap[$kp['id']]['trip'] += $op['trip'] + $an['trip'];

                    // Akumulasi In Operation
                    $rekap[$kp['id']]['me_1'] += $op['me'];
                    $rekap[$kp['id']]['jam_me_1'] += $op['jam_me'];

                    $rekap[$kp['id']]['ae_1'] += $op['ae'];
                    $rekap[$kp['id']]['jam_ae_1'] += $op['jam_ae'];

                    $rekap[$kp['id']]['pakai_1'] += $op['pakai'];
                    $rekap[$kp['id']]['trip_1'] += $op['trip'];

                    // Akumulasi Anchorage
                    $rekap[$kp['id']]['me_2'] += $an['me'];
                    $rekap[$kp['id']]['jam_me_2'] += $an['jam_me'];

                    $rekap[$kp['id']]['ae_2'] += $an['ae'];
                    $rekap[$kp['id']]['jam_ae_2'] += $an['jam_ae'];

                    $rekap[$kp['id']]['pakai_2'] += $an['pakai'];
                    $rekap[$kp['id']]['trip_2'] += $an['trip'];
                @endphp

                <td style="{{ $styleTd }}">In Operation</td>
                <td style="{{ $styleTd }}"></td>

                <td style="{{ $styleTd }}">{{ $op['me'] }}</td>
                <td style="{{ $styleTd }}">{{ $op['jam_me'] }}</td>
                <td style="{{ $styleTd }}">{{ $op['rata_me'] }}</td>

                <td style="{{ $styleTd }}">{{ $op['ae'] }}</td>
                <td style="{{ $styleTd }}">{{ $op['jam_ae'] }}</td>
                <td style="{{ $styleTd }}">{{ $op['rata_ae'] }}</td>

                <td style="{{ $styleTd }}">{{ $op['pakai'] }}</td>
                <td style="{{ $styleTd }}">{{ $op['tambah'] }}</td>
                <td style="{{ $styleTd }}"></td>

                <td style="{{ $styleTd }}">{{ $op['trip'] }}</td>
                <td style="{{ $styleTd }}">{{ $op['rata'] }}</td>
            @endforeach
        </tr>

        {{-- BARIS ANCHORAGE --}}
        <tr>
            @foreach($month['kapal'] as $kp)
                @php
                    $an = $kp['anchorage'];
                @endphp

                <td style="{{ $styleTd }}">Anchorage</td>
                <td style="{{ $styleTd }}">{{ $kp['saldo_awal'] }}</td>

                <td style="{{ $styleTd }}">{{ $an['me'] }}</td>
                <td style="{{ $styleTd }}">{{ $an['jam_me'] }}</td>
                <td style="{{ $styleTd }}">{{ $an['rata_me'] }}</td>

                <td style="{{ $styleTd }}">{{ $an['ae'] }}</td>
                <td style="{{ $styleTd }}">{{ $an['jam_ae'] }}</td>
                <td style="{{ $styleTd }}">{{ $an['rata_ae'] }}</td>

                <td style="{{ $styleTd }}">{{ $an['pakai'] }}</td>
                <td style="{{ $styleTd }}">{{ $an['tambah'] }}</td>
                <td style="{{ $styleTd }}">{{ $kp['saldo_akhir'] }}</td>

                <td style="{{ $styleTd }}">{{ $an['trip'] }}</td>
                <td style="{{ $styleTd }}">{{ $an['rata'] }}</td>
            @endforeach
        </tr>
    @endforeach

    {{-- TOTAL OVERALL --}}
    <tr>
        <th style="{{ $styleTh }}" rowspan="3">Total</th>

        @foreach($fleet as $f)
            @php
                $r = $rekap[$f->id];

                $rataMe = $r['jam_me'] > 0
                    ? round($r['me'] / $r['jam_me'], 2)
                    : 0;

                $rataAe = $r['jam_ae'] > 0
                    ? round($r['ae'] / $r['jam_ae'], 2)
                    : 0;

                $rata = $r['trip'] > 0
                    ? round($r['pakai'] / $r['trip'])
                    : 0;
            @endphp

            <th style="{{ $styleTh }}">Overall</th>
            <th style="{{ $styleTh }}"></th>

            <th style="{{ $styleTh }}">{{ $r['me'] }}</th>
            <th style="{{ $styleTh }}">{{ $r['jam_me'] }}</th>
            <th style="{{ $styleTh }}">{{ $rataMe }}</th>

            <th style="{{ $styleTh }}">{{ $r['ae'] }}</th>
            <th style="{{ $styleTh }}">{{ $r['jam_ae'] }}</th>
            <th style="{{ $styleTh }}">{{ $rataAe }}</th>

            <th style="{{ $styleTh }}">{{ $r['pakai'] }}</th>
            <th style="{{ $styleTh }}">{{ $r['tambah'] }}</th>
            <th style="{{ $styleTh }}"></th>

            <th style="{{ $styleTh }}">{{ $r['trip'] }}</th>
            <th style="{{ $styleTh }}">{{ $rata }}</th>
        @endforeach
    </tr>

    {{-- TOTAL IN OPERATION --}}
    <tr>
        @foreach($fleet as $f)
            @php
                $r = $rekap[$f->id];

                $rataMe = $r['jam_me_1'] > 0
                    ? round($r['me_1'] / $r['jam_me_1'], 2)
                    : 0;

                $rataAe = $r['jam_ae_1'] > 0
                    ? round($r['ae_1'] / $r['jam_ae_1'], 2)
                    : 0;

                $rata = $r['trip_1'] > 0
                    ? round($r['pakai_1'] / $r['trip_1'])
                    : 0;
            @endphp

            <th style="{{ $styleTh }}">In Operation</th>
            <th style="{{ $styleTh }}"></th>

            <th style="{{ $styleTh }}">{{ $r['me_1'] }}</th>
            <th style="{{ $styleTh }}">{{ $r['jam_me_1'] }}</th>
            <th style="{{ $styleTh }}">{{ $rataMe }}</th>

            <th style="{{ $styleTh }}">{{ $r['ae_1'] }}</th>
            <th style="{{ $styleTh }}">{{ $r['jam_ae_1'] }}</th>
            <th style="{{ $styleTh }}">{{ $rataAe }}</th>

            <th style="{{ $styleTh }}">{{ $r['pakai_1'] }}</th>
            <th style="{{ $styleTh }}">0</th>
            <th style="{{ $styleTh }}"></th>

            <th style="{{ $styleTh }}">{{ $r['trip_1'] }}</th>
            <th style="{{ $styleTh }}">{{ $rata }}</th>
        @endforeach
    </tr>

    {{-- TOTAL ANCHORAGE --}}
    <tr>
        @foreach($fleet as $f)
            @php
                $r = $rekap[$f->id];

                $rataMe = $r['jam_me_2'] > 0
                    ? round($r['me_2'] / $r['jam_me_2'], 2)
                    : 0;

                $rataAe = $r['jam_ae_2'] > 0
                    ? round($r['ae_2'] / $r['jam_ae_2'], 2)
                    : 0;

                $rata = $r['trip_2'] > 0
                    ? round($r['pakai_2'] / $r['trip_2'])
                    : 0;
            @endphp

            <th style="{{ $styleTh }}">Anchorage</th>
            <th style="{{ $styleTh }}"></th>

            <th style="{{ $styleTh }}">{{ $r['me_2'] }}</th>
            <th style="{{ $styleTh }}">{{ $r['jam_me_2'] }}</th>
            <th style="{{ $styleTh }}">{{ $rataMe }}</th>

            <th style="{{ $styleTh }}">{{ $r['ae_2'] }}</th>
            <th style="{{ $styleTh }}">{{ $r['jam_ae_2'] }}</th>
            <th style="{{ $styleTh }}">{{ $rataAe }}</th>

            <th style="{{ $styleTh }}">{{ $r['pakai_2'] }}</th>
            <th style="{{ $styleTh }}">0</th>
            <th style="{{ $styleTh }}"></th>

            <th style="{{ $styleTh }}">{{ $r['trip_2'] }}</th>
            <th style="{{ $styleTh }}">{{ $rata }}</th>
        @endforeach
    </tr>
</table>
