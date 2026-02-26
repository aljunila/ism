@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="permintaan-tab" data-bs-toggle="tab" href="#permintaan" aria-controls="permintaan" role="tab" aria-selected="true"><i data-feather="permintaan"></i>Permintaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="logistik-tab" data-bs-toggle="tab" href="#logistik" aria-controls="logistik" role="tab" aria-selected="true" data-status="1"><i data-feather="user"></i>Logistik</a>
                    <!-- </li>
                        <li class="nav-item">
                        <a class="nav-link" id="cabang-tab" data-bs-toggle="tab" href="#cabang" aria-controls="cabang" role="tab" aria-selected="true" data-status="2"><i data-feather="file"></i>Cabang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="workshop-tab" data-bs-toggle="tab" href="#workshop" aria-controls="workshop" role="tab" aria-selected="true"><i data-feather="log-out"></i>Workshop</a>
                    </li> -->
                </ul>
                <div class="card-header border-bottom">
                    <div class="col-sm-3">
                        <select name="id_kapal" id="id_kapal" class="form-control">
                            <option value="">Pilih Kapal</option>
                        @foreach($kapal as $kp)
                            <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
                        @endforeach
                        </select>
                    </div>  
                    <div class="col-sm-3">
                        <input type="date" name="tanggal" id="tanggal" class="form-control">
                    </div>
                    <div class="col-sm-6"></div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="permintaan" aria-labelledby="permintaan-tab" role="tabpanel">
                        <div class="card-body">
                        <a href="/permintaan/form" class="btn btn-primary btn-sm pull-right">Tambah Data</a>
                            <table id="table-permintaan" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kapal</th>
                                        <th>Bagian</th>
                                        <th>Pembuat Permintaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="logistik" aria-labelledby="logistik-tab" role="tabpanel" data-status="1">
                           @include('permintaan/logistik')
                    </div>
                    <div class="tab-pane" id="cabang" aria-labelledby="cabang-tab" role="tabpanel" data-status="2">
                            @include('permintaan/logistik')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="DetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableDetail" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
<script>
    let logTable;

    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        const table = $('#table-permintaan').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/permintaan/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.tanggal= $('#tanggal').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal' },
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `${row.kapal} <button type="button"  onclick="openDetail(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Barang">
                        Detail Permintaan</button><br>
                        No : ${row.nomor}`;
                    }
                },    
                { data: 'bagian', name: 'bagian' },
                { data: 'created', name: 'created' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        function initLogTable() {
            if ($.fn.DataTable.isDataTable('#table-logistik')) {
                logTable = $('#table-logistik').DataTable();
                return logTable;
            }

            logTable = $('#table-logistik').DataTable({
                processing: true,
                serverSide: true,
                ajax:{
                    url: "/permintaan/datalog",
                    type: "POST",
                    data: function(d){
                        let activePane = document.querySelector('.tab-pane.active');
                        d.status   = activePane?.getAttribute('data-status');
                        d.id_kapal = $('#id_kapal').val();
                        d.tanggal  = $('#tanggal').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'barang', name: 'barang' },
                    {
                        data: null,
                        name: null,
                        render: function (data, type, row) {
                            return `${row.jumlah} ${row.satuan}`;
                        }
                    },    
                    { data: 'tanggal', name: 'tanggal' },
                    {
                        data: null,
                        name: null,
                        render: function (data, type, row) {
                            return `${row.kapal} <br>
                            No : ${row.nomor}`;
                        }
                    }, 
                    { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-outline-primary proses-btn" data-id="${row.id}">
                                    Proses
                                </button>
                            `;
                        }
                    }
                ]
            });

            return logTable;
        }

        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function () {
                let activePane = document.querySelector('.tab-pane.active');
                let status = activePane?.getAttribute('data-status');

                if (!status) {
                    return;
                }

                if (!logTable) {
                    initLogTable();
                } else {
                    logTable.ajax.reload();
                }
            });
        });

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
            if (logTable) {
                logTable.ajax.reload();
            }
        });

        $('#tanggal').on('change', function () {
            if (logTable) {
                logTable.ajax.reload();
            }
            table.ajax.reload();
        });

        $(document).on('click', '.btn-delete-permintaan', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('permintaan/destroy') }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                })
                .done(res => {
                    Swal.fire(res.status, res.message, res.status)
                        .then(() => {
                            if (res.status === 'success') {
                                $('#modal-cuti').modal('hide');
                                table.ajax.reload(null, false);
                            }
                        });
                })
                .fail(xhr => {
                    Swal.fire(
                        'Gagal',
                        xhr.responseJSON?.message || 'Error',
                        'error'
                    );
                });
            });
        });
    });

    function openDetail(id) {
        currentId = id;
        $('#DetailModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetail')) {
            DetailTable.ajax.url(`permintaan/get/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetail').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `permintaan/get/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                { data: 'barang', },
                { data: 'jumlah', },
                { data: 'status', },
            ]
        });
    }

    $(document).on('click', '.proses-btn', function () {

        let id = $(this).data('id');
        let activePane = document.querySelector('.tab-pane.active');
        let sbarang = activePane?.getAttribute('data-status');

        $('#proses_id').val(id);
        $('#current_status').val(sbarang); // hidden input baru

        loadStatusBarang(sbarang); // load dropdown sesuai status

        $('#prosesModal').modal('show');
    });

    function loadStatusBarang(sbarang)
    {
        $.get(`/permintaan/statusbarang/${sbarang}`, function(res){
            let html = '';
            res.forEach(function(item){
                html += `<option value="${item.id}">${item.nama}</option>`;
            });
            $('#status').html(html);
        });
    }

    $('#form_proses').on('submit', function(e){
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "/permintaan/proses",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                $('#prosesModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message
                });
                if (logTable) {
                    logTable.ajax.reload(); // reload table logistik
                }
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message ?? 'Terjadi kesalahan'
                });
            }
        });
    });
</script>
@endsection
