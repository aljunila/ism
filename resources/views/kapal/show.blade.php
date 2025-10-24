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
            url: "/kapal/data",
            type: "POST",
            data: function(d){
                d.id_perusahaan= $('#id_perusahaan').val(),
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
                { data: 'no_siup' },
                { data: 'call_sign' },
                { data: 'perusahaan' },
                { 
                    data: null, 
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather='edit-3'></i></button>
                                <div class="dropdown-menu">
                                    <a href="/kapal/profil/${row.uid}" class="dropdown-item">Profil</a>
                                    <a type="button" href="/kapal/edit/${row.uid}" class="dropdown-item resign-btn">Edit</a>
                                    <a type="button" data-id="${row.id}" class="dropdown-item delete-btn">Hapus</a>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            drawCallback: function(settings) {
            feather.replace(); // supaya icon feather muncul ulang
        }
        });
    });

     $('#id_perusahaan').on('change', function () {
         table.ajax.reload();
    });

    $(document).on('click', '#download', function() {
        $.ajax({
            url: "/kapal/export",
            method: "POST",
            xhrFields: { responseType: 'blob' },
            data: {
                id_perusahaan: $('#id_perusahaan').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(data){
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = "data_kapal.xlsx";
                link.click();
            }
        })
    });
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="col-sm-12"><h4 class="card-title">Daftar Kapal</h4></div>
                        @include('perusahaan')
                        <div class="col-sm-5"></div>
                        <div class="col-sm-3">
                        <button type="button" class="btn btn-warning btn-sm" id="download"><i data-feather='download'></i> Unduh Data</button>
                        @if(Session::get('previllage')<=2)
                        <a href="/kapal/add" class="btn btn-primary btn-sm">Tambah Data</a>
                        @endif
                        </div>
                    </div>
                    <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Kapal</th>
                          <th>No SIUP</th>
                          <th>Call Sign</th>
                          <th>Pemilik</th>
                          <th>Aksi</th>
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