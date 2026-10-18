
<table>
    <thead>
        <tr>
            <th style="background-color: #e8e524; color: black; border: 1px solid #000;">Job / Subjob</th>

            @foreach ($kolom as $item)
                <th style="background-color: #e8e524; color: black; border: 1px solid #000; text-align:right;">
                    {{ $mode === 'tahun' ? $item : $item->nama }}
                </th>
            @endforeach

            <th style="background-color: #e8e524; color: black; border: 1px solid #000; text-align:right;">
                Grand Total
            </th>
        </tr>
    </thead>

    <tbody>
        @php
            $grandKolom = [];
            $grandTotal = 0;

            foreach ($kolom as $item) {
                $key = $mode === 'tahun' ? $item : $item->id;
                $grandKolom[$key] = 0;
            }

            $jobs = $data->groupBy('id_job');
        @endphp

        @foreach ($jobs as $rowsJob)
            @php
                $namaJob = $rowsJob->first()->job;
            @endphp

            {{-- Baris induk Job --}}
            <tr>
                <td style="border:1px solid #000; font-weight:bold;">
                    {{ $namaJob }}
                </td>

                @foreach ($kolom as $item)
                    <td style="border:1px solid #000;"></td>
                @endforeach

                <td style="border:1px solid #000;"></td>
            </tr>

            {{-- Baris rincian Subjob --}}
            @foreach ($rowsJob->groupBy('id_subjob') as $rowsSubjob)
                @php
                    $namaSubjob = $rowsSubjob->first()->subjob;
                    $totalSubjob = 0;
                @endphp

                <tr>
                    <td style="border:1px solid #000; padding-left:25px;">
                        {{ $namaSubjob }}
                    </td>

                    @foreach ($kolom as $item)
                        @php
                            if ($mode === 'tahun') {
                                $key = $item;

                                $nilai = $rowsSubjob
                                    ->where('kolom', $item)
                                    ->sum('total_biaya');
                            } else {
                                $key = $item->id;

                                $nilai = $rowsSubjob
                                    ->where('id_kapal', $item->id)
                                    ->sum('total_biaya');
                            }

                            $totalSubjob += $nilai;
                            $grandKolom[$key] += $nilai;
                        @endphp

                        <td style="border:1px solid #000; text-align:right;">
                            {{ $nilai != 0
                                ? 'Rp ' . number_format($nilai, 2, ',', '.')
                                : '' }}
                        </td>
                    @endforeach

                    @php
                        $grandTotal += $totalSubjob;
                    @endphp

                    <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                        {{ $totalSubjob != 0
                            ? 'Rp ' . number_format($totalSubjob, 2, ',', '.')
                            : '' }}
                    </td>
                </tr>
            @endforeach
        @endforeach

        {{-- Grand Total --}}
        <tr>
            <td style="border:1px solid #000; font-weight:bold;">
                Grand Total
            </td>

            @foreach ($kolom as $item)
                @php
                    $key = $mode === 'tahun' ? $item : $item->id;
                @endphp

                <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                    {{ 'Rp ' . number_format(
                        $grandKolom[$key],
                        2,
                        ',',
                        '.'
                    ) }}
                </td>
            @endforeach

            <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                {{ 'Rp ' . number_format($grandTotal, 2, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>
