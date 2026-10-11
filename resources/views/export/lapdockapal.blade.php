<table>
    <thead>
        <tr>
            <th style="border:1px solid #000;">
                Kapal (Tahun)
            </th>

            @foreach ($perusahaan as $p)
                <th style="border:1px solid #000; text-align:right;">
                    {{ $p->kode }}
                </th>
            @endforeach

            <th style="border:1px solid #000; text-align:right;">
                Grand Total
            </th>
        </tr>
    </thead>

    <tbody>
        @php
            $grandPerusahaan = [];
            $grandTotal = 0;

            // Inisialisasi total berdasarkan ID perusahaan
            foreach ($perusahaan as $p) {
                $grandPerusahaan[$p->id] = 0;
            }

            // Kelompokkan data berdasarkan kapal
            $kapalGroups = $data->groupBy('id_kapal');
        @endphp

        @foreach ($kapalGroups as $idKapal => $rowsKapal)
            @php
                $namaKapal = $rowsKapal->first()->kapal;
                $totalKapal = 0;
            @endphp

            {{-- Baris subtotal kapal --}}
            <tr>
                <td style="border:1px solid #000; font-weight:bold;">
                    {{ $namaKapal }}
                </td>

                @foreach ($perusahaan as $p)
                    @php
                        // Cocokkan ID perusahaan, bukan kode
                        $totalPerusahaanKapal = $rowsKapal
                            ->where('id_perusahaan', $p->id)
                            ->sum('total_biaya');

                        $totalKapal += $totalPerusahaanKapal;

                        $grandPerusahaan[$p->id] +=
                            $totalPerusahaanKapal;
                    @endphp

                    <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                        {{ $totalPerusahaanKapal != 0
                            ? $totalPerusahaanKapal
                            : '' }}
                    </td>
                @endforeach

                @php
                    $grandTotal += $totalKapal;
                @endphp

                <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                    {{ $totalKapal }}
                </td>
            </tr>

            {{-- Rincian per tahun --}}
            @foreach ($rowsKapal->groupBy('tahun')->sortKeys() as $tahun => $rowsTahun)
                @php
                    $totalTahun = 0;
                @endphp

                <tr>
                    <td style="border:1px solid #000; text-align:right;">
                        {{ $tahun }}
                    </td>

                    @foreach ($perusahaan as $p)
                        @php
                            // Total per tahun dan ID perusahaan
                            $nilai = $rowsTahun
                                ->where('id_perusahaan', $p->id)
                                ->sum('total_biaya');

                            $totalTahun += $nilai;
                        @endphp

                        <td style="border:1px solid #000; text-align:right;">
                            {{ $nilai != 0
                                ? $nilai
                                : '' }}
                        </td>
                    @endforeach

                    <td style="border:1px solid #000; text-align:right;">
                        {{ $totalTahun != 0
                            ? $totalTahun
                            : '' }}
                    </td>
                </tr>
            @endforeach
        @endforeach

        {{-- Grand total seluruh kapal --}}
        <tr>
            <td style="border:1px solid #000; font-weight:bold;">
                Grand Total
            </td>

            @foreach ($perusahaan as $p)
                <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                    {{ $grandPerusahaan[$p->id] }}
                </td>
            @endforeach

            <td style="border:1px solid #000; text-align:right; font-weight:bold;">
                {{ $grandTotal }}
            </td>
        </tr>
    </tbody>
</table>