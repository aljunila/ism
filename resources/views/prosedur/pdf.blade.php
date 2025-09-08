<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 40px; /* jarak isi dengan border */
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            border: 2px solid #655dd6ff; /* garis tepi hitam */
            padding: 20px; /* jarak isi dengan garis */
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
            font-size: 12px;
            text-align: center;
        }

        .table-bordered td, 
        .table-bordered th {
            border: 1px dotted #000; /* pakai garis titik-titik */
            padding: 6px;
            vertical-align: top;
        }

        .table-bordered td:first-child {
            text-align: left; /* kolom pertama rata kiri */
            width: 40%;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div>
        {!! $cover !!}
    </div><br><br><br><br><br>
    <div>
        <table class="table-bordered" width="50%">
            <tr>
                <td>
                    No Dokumen&nbsp;&nbsp;:{{$show->no_dokumen}}<br>
                    Edisi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:{{$show->edisi}}<br>
                    Tanggal Terbit :{{ \Carbon\Carbon::parse($show->tgl_terbit)->translatedFormat('d F Y') }}<br>
                    Status Manual&nbsp;&nbsp;:{{$show->status_manual}}<br>
                </td>
                <td>
                    Diarsipkan oleh<br>
                    <img src="file://{{ public_path('ttd_karyawan/' . $show->get_prepered()->tanda_tangan) }}" width="100px" height="75px"><br>
                    {{$show->get_prepered()->nama}}
                </td>
                <td>
                    Diberlakukan oleh<br>
                    <img src="file://{{ public_path('ttd_karyawan/' . $show->get_enforced()->tanda_tangan) }}" width="100px" height="75px"><br>
                    {{$show->get_enforced()->nama}}
                </td>
            </tr>
        </table>
    </div>
    <div style="page-break-before: always;"></div>
    <div>
        {!! $isi !!}
    </div>
</body>
</html>
