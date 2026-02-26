@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Laporan - Permintaan</h4>
    </div>
    <div class="card-body">
        <table id="table-permintaan" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Permintaan Dari</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
                            <th>Riwayat Permintaan</th>
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
<script src="{{ url('/app-assets/vendors/js/tom-select.min.js') }}"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-permintaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('lappermintaan.data') }}',
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
                { data: 'status', name: 'status' },
                { data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `<button type="button"  onclick="openLog(${row.id})" class="btn btn-sm btn-outline-primary" title="Detail">
                        Detail Permintaan</button>`;
                    } 
                },
            ],
            
        });
    });

    function openLog(id) {
        currentId = id;
        $('#DetailModal').modal('show');
        if ($.fn.DataTable.isDataTable('#tableDetail')) {
            DetailTable.ajax.url(`permintaan/getlog/${id}`).load();
            return;
        }
        DetailTable = $('#tableDetail').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `permintaan/getlog/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `${row.status} <br><br>
                        Tanggal : ${row.tanggal}<br>
                        Diproses oleh : ${row.created}`;
                    }
                },
            ]
        });
    }
</script>
@endsection
