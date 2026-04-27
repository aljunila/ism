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
            url: "/ck_kapal/berlayar/databyIdp",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->id}}",
                d.id_perusahaan= "{{ $id_perusahaan}}",
                d.id_kapal= $('#id_kapal').val(),
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
            { data: 'date',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { data: 'kapal', name: 'kapal' },
            { data: 'pelabuhan', name: 'pelabuhan' },
            { 
                data: null,
                render: function(data, type, row){
                    if(row.file) {
                    return `
                    <a href="{{ asset('checklist') }}/${row.file}" target="_blank" type="button" class="btn btn-icon btn-xs btn-flat-success" title="Buka File">
                            <i data-feather='file'></i>
                        </a>
                    `;
                } else {
                    return ``;
                }
                }
            },
            ],
                drawCallback: function(settings) {
                feather.replace(); 
            }
        });
    });

    $('#id_kapal').on('change', function () {
         table.ajax.reload();
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
                        @include('kapal')
                        <div class="col-3">
                            <!-- <a href="/checklist/add/{{$form->kode}}" class="btn btn-primary btn-sm">Tambah Data</a> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Kapal</th>
                            <th>Pembuat Permintaan</th>
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