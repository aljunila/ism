
<table>
    <thead>
        <tr>
            <th style="background-color: #e8e524; color: black; border: 1px solid #000;">Job</th>

            @foreach ($kolom as $item)
                <th style="background-color: #e8e524; color: black; border: 1px solid #000;">
                    {{ $mode === 'tahun' ? $item : $item->nama }}
                </th>
            @endforeach

            <th style="background-color: #e8e524; color: black; border: 1px solid #000;">
                Grand Total
            </th>
        </tr>
    </thead>

    <tbody>
        @php
            $grandTotal = 0;
            $grandKolom = [];

            foreach ($kolom as $item) {
                $key = $mode === 'tahun' ? $item : $item->id;
                $grandKolom[$key] = 0;
            }

            $jobs = $data->groupBy('id_job');
        @endphp

        @foreach ($jobs as $rowsJob)
            @php
                $first = $rowsJob->first();
                $totalJob = 0;
            @endphp

            <tr>
                <td style="border:1px solid #000;">
                    {{ $first->job }}
                </td>

                @foreach ($kolom as $item)
                    @php
                        if ($mode === 'tahun') {
                            $key = $item;
                            $nilai = $rowsJob
                                ->where('kolom', $item)
                                ->sum('total_biaya');
                        } else {
                            $key = $item->id;
                            $nilai = $rowsJob
                                ->where('id_kapal', $item->id)
                                ->sum('total_biaya');
                        }

                        $totalJob += $nilai;
                        $grandKolom[$key] += $nilai;
                    @endphp

                    <td style="border:1px solid #000; text-align:right;">
                        {{ $nilai != 0
                            ? $nilai
                            : '' }}
                    </td>
                @endforeach

                @php
                    $grandTotal += $totalJob;
                @endphp

                <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                    {{ $totalJob }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td style="border:1px solid #000; font-weight:bold;">
                Grand Total
            </td>

            @foreach ($kolom as $item)
                @php
                    $key = $mode === 'tahun' ? $item : $item->id;
                @endphp

                <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                    {{ $grandKolom[$key] }}
                </td>
            @endforeach

            <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                {{ $grandTotal }}
            </td>
        </tr>
    </tbody>
</table>
