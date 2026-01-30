@extends('main')
@section('scriptheader')
  <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Vendor CSS-->
@endsection

@section('scriptfooter')
<!-- jQuery -->
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ url('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->
<script>
    let table;

    $(function () {
		table = $('#table').DataTable({
        processing: true,
        searchable: true,
        ajax:{
            url: "/data_crew/recruitment/getData",
            type: "POST",
            data: function(d){
                d.id_perusahaan= "{{$id_perusahaan}}",
                d._token= "{{ csrf_token() }}"
            },
        },
        columns: [
            { data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + 1; // auto numbering
                },
                orderable: false,
                searchable: false
            },
            { data: 'nama' },
            { data: 'jabatan' },
            { data: 'note' },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row) {
                    if (data === 'N') return 'Baru';
                    if (data === 'A') return 'Diterima';
                    if (data === 'D') return 'Ditolak';
                    return '-';
                }
            },
            { 
                data: null, 
                render: function (data, type, row) {
                    if(row.data) {
                        return `
                        <a href="/data_crew/recruitment/pdf/${row.uid}" type="button" class="btn btn-icon btn-xs btn-flat-primary download" target="_blank" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        `;
                    }
                    return `-`;
                }
            },
        ],
         drawCallback: function(settings) {
            feather.replace(); 
        }
    });
    });

</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="col-12"><h4 class="card-title">{{$form->nama}}</h4></div>
                        
                        <div class="col-3">
                            <!-- <a href="/checklist/add/{{$form->kode}}" class="btn btn-primary btn-sm">Tambah Data</a> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Catatan</th>
                            <th>Status</th>
                            <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection