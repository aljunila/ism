@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
  <style>
    .permintaan-track-modal .modal-dialog {
        max-width: 1100px;
    }

    .permintaan-track-wrap {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        min-height: 520px;
    }

    .permintaan-track-left {
        padding: 1.25rem 1.35rem 1.15rem;
        border-right: 1px solid #ebe9f1;
        background: #fafafd;
    }

    .permintaan-track-title {
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .permintaan-track-meta {
        margin-bottom: 1rem;
        padding-bottom: .85rem;
        border-bottom: 1px solid #ebe9f1;
    }

    .permintaan-track-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .9rem 1rem;
    }

    .permintaan-track-meta-item {
        min-width: 0;
    }

    .permintaan-track-label {
        font-size: .82rem;
        color: #6e6b7b;
        margin-bottom: .2rem;
    }

    .permintaan-track-value {
        font-size: 1.02rem;
        font-weight: 600;
        color: #3b3a45;
    }

    .permintaan-track-right {
        padding: 1.25rem 1.35rem 1.15rem;
        background: #f5f6fa;
    }

    .permintaan-track-status {
        font-size: 1.35rem;
        margin-bottom: 1rem;
        color: #6e6b7b;
    }

    .permintaan-track-status strong {
        color: #3b3a45;
    }

    .permintaan-track-timeline {
        max-height: 420px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: .6rem;
        padding: .75rem;
    }

    .timeline-item {
        display: grid;
        grid-template-columns: 92px 16px 1fr;
        gap: .75rem;
        align-items: start;
        position: relative;
        padding: .45rem 0 .9rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 109.2px;
        top: 24px;
        bottom: -4px;
        width: 2px;
        border-left: 2px dashed #e0deea;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-time {
        text-align: right;
        font-size: .83rem;
        color: #6e6b7b;
        line-height: 1.2;
    }

    .timeline-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #b7bfd4;
        margin-top: .2rem;
        box-shadow: 0 0 0 4px rgba(170, 177, 198, 0.18);
        position: relative;
    }

    .timeline-content {
        font-size: .93rem;
        color: #3b3a45;
        line-height: 1.45;
    }

    .timeline-content .small {
        color: #8e8aa1;
    }

    .timeline-item.is-active .timeline-dot {
        background: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
    }

    .timeline-item.is-active .timeline-dot::after {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 50%;
        border: 2px solid rgba(13, 110, 253, 0.32);
        animation: timelinePulse 1.8s ease-out infinite;
    }

    @keyframes timelinePulse {
        0% {
            transform: scale(0.85);
            opacity: 0.85;
        }
        70% {
            transform: scale(1.2);
            opacity: 0;
        }
        100% {
            transform: scale(1.2);
            opacity: 0;
        }
    }

    .timeline-empty {
        text-align: center;
        color: #8e8aa1;
        padding: 1.2rem .5rem;
    }

    @media (max-width: 991px) {
        .permintaan-track-wrap {
            grid-template-columns: 1fr;
        }

        .permintaan-track-left {
            border-right: 0;
            border-bottom: 1px solid #ebe9f1;
        }

        .permintaan-track-meta-grid {
            grid-template-columns: 1fr;
        }
    }
  </style>
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="biaya-tab" data-bs-toggle="tab" href="#biaya" aria-controls="biaya" role="tab" aria-selected="true"><i data-feather="biY"></i>Lap Biaya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="kapal-tab" data-bs-toggle="tab" href="#kapal" aria-controls="kapal" role="tab" aria-selected="true" data-status="kapal"><i data-feather="file"></i>Lap Kapal</a>
                    </li>
                </ul>
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-sm-2">
                        <select name="id_kapal" id="id_kapal" class="form-control">
                            <option value="">Pilih Kapal</option>
                        @foreach($kapal as $kp)
                            <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
                        @endforeach
                        </select>
                    </div>  
                    <div class="col-sm-2">
                        <select id="id_gal" class="form-control">
                            <option value="">-Pilih Galangan-</option>
                            @foreach ($galangan as $g)
                                <option value="{{$g->id}}">{{$g->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select id="start_date" class="form-control">
                            <option value="">-Pilih-</option>
                            @for($a=2020; $a<=2036; $a++)
                                <option value="{{$a}}">{{$a}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select id="end_date" class="form-control">
                            <option value="">-Pilih-</option>
                            @for($a=2020; $a<=2036; $a++)
                                <option value="{{$a}}">{{$a}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="biaya" aria-labelledby="biaya-tab" role="tabpanel"> 
                        <div class="card-body">
                                         
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-warning btn-sm" id="download"><i data-feather='download'></i> Unduh Data</button>
                        </div>
                            <table id="table-biaya" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kapal</th>
                                        <th>Jadwal</th>
                                        <th>Tempat</th>
                                        <th>Tgl Docking</th>
                                        <th>Job/SubJob</th>
                                        <th>Deskripsi</th>
                                        <th>Volume</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="kapal" aria-labelledby="kapal-tab" role="tabpanel">
                        <div class="card-body">
                             <button type="button" class="btn btn-warning btn-sm" id="download-modal"><i data-feather='download2'></i> Unduh Data</button>
                            <table id="table-kapal" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kapal</th>
                                        <th>Jadwal</th>
                                        <th>Tempat</th>
                                        <th>Tgl Mulai</th>
                                        <th>Tgl Selesai</th>
                                        <th>Durasi (Hari)</th>
                                        <th>Biaya</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-download" tabindex="-1" aria-labelledby="modal-download-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-download-label">Unduh Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Kapal</label>
                        </div>
                        <div class="col-9">
                            <select id="excel" class="form-control">
                                <option value="">-Pilih Laporan-</option>
                                <option value="1">Laporan Kapal</option>
                                <option value="2">Laporan Job</option>
                                <option value="3">Laporan SubJob</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="download2">Unduh Data</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptfooter')
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('/app-assets/vendors/js/tom-select.min.js') }}"></script>
<script>
    let table;
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-biaya').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/laporan/docking/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.id_gal= $('#id_gal').val(),
                    d.start_date= $('#start_date').val(),
                    d.end_date= $('#end_date').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },     
                {data: 'kapal', name: 'kapal'},
                { 
                        data: null, 
                        render: function (data, type, row) {
                            return `${row.bulan} - ${row.tahun}`;
                        }
                },
                { data: 'galangan', name: 'galangan' },
                { data: 'tgl_mulai',
                    render: function(data) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                },
                { 
                        data: null, 
                        render: function (data, type, row) {
                            return `${row.job} - ${row.subjob}`;
                        }
                },
                { data: 'deskripsi', name: 'deskripsi' },
                { data: 'volume', name: 'volume' },
                {
                    data: 'harga',
                    name: 'harga',
                    render: function(data, type, row) {
                        if (data == null || data === '') return '';

                            return 'Rp ' +  Number(data).toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2
                        });
                    }
                },
                {
                    data: 'total',
                    name: 'total',
                    render: function(data, type, row) {
                        if (data == null || data === '') return '';

                            return 'Rp ' +  Number(data).toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2
                        });
                    }
                },
            ],
            
        });

        const table2 = $('#table-kapal').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/laporan/docking/dockkapal",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.id_gal= $('#id_gal').val(),
                    d.start_date= $('#start_date').val(),
                    d.end_date= $('#end_date').val(),
                    d._token= "{{ csrf_token() }}"
                },
                dataSrc: "data"
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        render: function (data, type, row) {
                            return `${row.kapal}`;
                        }
                },
               {
                    data: null,
                    name: 'periode',
                    render: function (data, type, row) {
                        let bulan = row.bulan ?? '';
                        let tahun = row.tahun ?? '';

                        // Nama bulan Indonesia
                        const namaBulan = [
                            '', 'Januari', 'Februari', 'Maret',
                            'April', 'Mei', 'Juni', 'Juli',
                            'Agustus', 'September', 'Oktober',
                            'November', 'Desember'
                        ];

                        return namaBulan[Number(bulan)] 
                            ? namaBulan[Number(bulan)] + ' ' + tahun
                            : bulan + ' - ' + tahun;
                    }
                },
                {
                    data: 'galangan',
                    name: 'galangan'
                },
                {
                    data: 'tgl_mulai',
                    name: 'tgl_mulai',
                    render: function (data) {
                        if (!data) return '';

                        let tanggal = data.split(' ')[0].split('-');
                        return tanggal[2] + '-' + tanggal[1] + '-' + tanggal[0];
                    }
                },
                {
                    data: 'tgl_selesai',
                    name: 'tgl_selesai',
                    render: function (data) {
                        if (!data) return '';

                        let tanggal = data.split(' ')[0].split('-');
                        return tanggal[2] + '-' + tanggal[1] + '-' + tanggal[0];
                    }
                },
                {
                    data: 'durasi',
                    name: 'durasi'
                },
                {
                    data: 'total_biaya',
                    name: 'total_biaya',
                    render: function (data, type, row) {
                        if (data == null || data === '') return 'Rp 0';

                        return 'Rp ' + Math.round(Number(data))
                            .toLocaleString('id-ID');
                    }
                }
            ]
        });

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
            table2.ajax.reload();
        });

        $('#end_date').on('change', function () {
            table.ajax.reload();
            table2.ajax.reload();
        });

        $('#start_date').on('change', function () {
            table.ajax.reload();
            table2.ajax.reload();
        });

         $('#id_gal').on('change', function () {
            table.ajax.reload();
            table2.ajax.reload();
        });

    });


    
    $(document).on('click', '#download', function() {
        $.ajax({
            url: "/laporan/docking/export",
            method: "POST",
            xhrFields: { responseType: 'blob' },
            data: {
                id_kapal: $('#id_kapal').val(),
                id_gal: $('#id_gal').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(data){
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = "lap_docking_biaya.xlsx";
                link.click();
            }
        })
    });

    $(document).on('click', '#download2', function() {
        $.ajax({
            url: "/laporan/docking/export2",
            method: "POST",
            xhrFields: { responseType: 'blob' },
            data: {
                id_kapal: $('#id_kapal').val(),
                id_gal: $('#id_gal').val(),
                excel: $('#excel').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(data){
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = "lap_docking_kapal.xlsx";
                link.click();
            }
        })
    });

     $('#download-modal').on('click', function () {     
            $('#modal-download').modal('show');
        });
</script>
@endsection
