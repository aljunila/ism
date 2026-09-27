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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Laporan - Permintaan</h4>
        <div class="col-sm-2">
            <select name="id_kapal" id="id_kapal" class="form-control">
                <option value="">Pilih Kapal</option>
            @foreach($kapal as $kp)
                <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
            @endforeach
            </select>
        </div>  
        <div class="col-sm-2">
            <input type="date" name="start_date" id="start_date" class="form-control" placeholder="start date">
        </div>
        <div class="col-sm-2">
            <input type="date" name="end_date" id="end_date" class="form-control" placeholder="end date">
        </div>
        <div class="col-sm-2">
            <button type="button" class="btn btn-warning btn-sm" id="download"><i data-feather='download'></i> Unduh Data</button>
        </div>
    </div>
    <div class="card-body">
        <table id="table-permintaan" class="table table-striped w-100">
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

        table = $('#table-permintaan').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/laporan/docking/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
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
                { data: 'tempat', name: 'tempat' },
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

    });

    $('#id_kapal').on('change', function () {
        table.ajax.reload();
    });

    $('#end_date').on('change', function () {
        table.ajax.reload();
    });

    $('#start_date').on('change', function () {
        table.ajax.reload();
    });

    
    $(document).on('click', '#download', function() {
        $.ajax({
            url: "/laporan/docking/export",
            method: "POST",
            xhrFields: { responseType: 'blob' },
            data: {
                id_kapal: $('#id_kapal').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(data){
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = "lap_docking.xlsx";
                link.click();
            }
        })
    });
</script>
@endsection
