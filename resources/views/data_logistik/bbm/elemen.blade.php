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
            url: "/data_logistik/bbm/dataByIdp",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->id}}",
                d.id_perusahaan= "{{ $id_perusahaan}}",
                d.tanggal= $('#tanggal').val(),
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
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function (data, type, row) {
                    return `${row.kapal}`;
                }
            },
            { data: 'tanggal',
                render: function (data, type, row) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return `${parts[2]}-${parts[1]}-${parts[0]} ${row.jam}`; 

                }
            },
            { 
                data: 'lintasan', 
                name : 'lintasan',
                render: function (data, type, row) {
                    return `${row.lintasan} (${row.jarak} Mil)`;
                }
            },
            { data: 'jumlah', name: 'jumlah' },
            { 
                data: 'uid', 
                name : 'uid',
                render: function (data, type, row) {
                    return `<a type="button" href="/data_logistik/bbm/pdf/${row.uid}" target="_blank" class="btn btn-sm btn-outline-success">Cetak PDF</a>`;
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

    $('#tanggal').on('change', function () {
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
                        <div class="col-sm-3">
                            <input type="date" name="tanggal" id="tanggal" class="form-control">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kapal</th>
                                <th>Tanggal</th>
                                <th>Lintasan</th>
                                <th>Jumlah</th>
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