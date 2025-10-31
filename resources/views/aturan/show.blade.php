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
            url: "/aturan/data",
            type: "POST",
            data: function(d){
                d.id_perusahaan= $('#id_perusahaan').val(),
                d._token= "{{ csrf_token() }}"
            },
        },
        columns: [
            { data: null, 
                render: function (data, type, row, meta) {
                     return meta.row + 1;
                },
                orderable: false,
                searchable: false
            },
            { data: 'kode' },
            { data: 'nama' },
            { data: 'enforced' },
            { data: null,
                render: function(data, type, row) {
                    if(row.publish) {
                        return `Iya`;
                    } else {
                        return `Tidak`;
                    }
                }
             },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function (data, type, row) {
                    if(row.isi){
                        return `
                        <a href="/aturan/pdf/${row.uid}" type="button" target="_blank" class="btn btn-icon btn-xs btn-flat-primary download" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        `;
                    } else if(row.file) {
                        return `
                        <a href="{{ asset('file_elemen') }}/${row.file}" target="_blank" type="button" class="btn btn-icon btn-xs btn-flat-success" title="Buka File">
                                <i data-feather='file'></i>
                            </a>
                        `;
                    } else {
                        return ``;
                    }
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="edit-3" class="dropdown-icon"></i></button>
                            <div class="dropdown-menu">
                                <a href="/aturan/edit/${row.uid}" class="dropdown-item">Edit</a>
                                <a type="button" data-id="${row.id}" class="dropdown-item delete-btn">Hapus</a>
                            </div>
                        </div>
                    `;
                }
            }
        ],
         drawCallback: function(settings) {
            feather.replace(); 
        }
    });
  });

  $(document).on("click", ".delete-btn", function(){
    let id = $(this).data("id");

    Swal.fire({
        title: "Yakin mau hapus?",
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/aturan/delete/" + id,
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res){
                    Swal.fire({
                        icon: "success",
                        title: "Terhapus!",
                        text: "Data berhasil dihapus",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    table.ajax.reload();
                },
                error: function(err){
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: "Data gagal dihapus"
                    });
                }
            });
        }
    });
  });

  $(document).on('change', '#id_perusahaan', function() {
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
                        <div class="col-sm-12"><h4 class="card-title">Daftar Elemen 2</h4></div>
                        <div class="col-sm-8">@include('perusahaan')</div>
                        <div class="col-sm-4">
                            <a href="/aturan/add" class="btn btn-primary btn-sm">Tambah Data</a>
                        </div>
                    </div>
                    <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Form ID</th>
                          <th>Nama</th>
                          <th>Diberlakukan Oleh</th>
                          <th>Publish</th>
                          <th>File</th>
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