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
            url: "/prosedur/data",
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
            { data: 'judul' },
            { data: 'prepered' },
            { data: 'enforced' },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function (data, type, row) {
                    if(row.cover){
                        return `
                        <a href="/prosedur/pdf/${row.uid}" type="button" class="btn btn-icon btn-xs btn-flat-primary download" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        `;
                    } else if(row.file) {
                        let link = "{{ asset('file_prosedur') }}";
                        return `
                        <a href="/view-file/${row.file}" target="_blank" type="button" class="btn btn-icon btn-xs btn-flat-success" title="Buka File">
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
                            <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"></button>
                            <div class="dropdown-menu">
                                <a href="/prosedur/edit/${row.uid}" class="dropdown-item">Edit</a>
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

    $('#tableuser').DataTable({  
        processing: true,
        searchable: true,
        ajax: "{{ url('/prosedur/viewuser') }}",
        columns: [
            { data: null, 
                render: function (data, type, row, meta) {
                     return meta.row + 1;
                },
                orderable: false,
                searchable: false
            },
            { data: 'nama' },
            { data: 'kapal' },
            { data: 'lihat' },
            { data: 'download' },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function (data, type, row) {
                    return `
                         <a type="button" data-id="${row.id}" data-nama="${row.nama}" class="btn btn-icon btn-xs btn-flat-success form-btn" title="Lihat Detail">
                                <i data-feather='edit'></i>
                            </a>
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
                url: "/prosedur/delete/" + id,
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
                    if (err.status === 422) {
                        let msg = err.responseJSON?.errors?.file
                            ? err.responseJSON.errors.file[0]
                            : 'File tidak valid atau gagal diupload';

                        Swal.fire({
                            icon: "error",
                            title: "File tidak mendukung. Gunakan file PDF!",
                            text: msg
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: "Data gagal dihapus"
                        });
                    }
                }
            });
        }
    });
  });

  $(document).on("click", ".form-btn", function() {
        let id = $(this).attr("data-id");
        let nama = $(this).attr("data-nama");
        console.log("ID:", id);

        $.ajax({
            url: "/prosedur/viewdetail",
            type: "POST",
            data: {
                id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function(respons) {
                console.log("Response:", respons);

                formitem(respons.data);
                $('#FormIsi').modal('show');
                $('#nama').html(nama);
            },
            error: function(err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: "Gagal memuat data"
                });
            }
        });
    });

    function formitem(data) {
        $('#tabledetail').DataTable({
            destroy: true, 
            processing: false,
            searchable: false,
            data: data,
            columns: [
                { data: 'kode' },
                { data: 'jml_lihat' },
                { data: 'update_lihat' },
                { data: 'jml_download' },
                { data: 'update_download' },
            ],
        });
    }

    $('#id_perusahaan').on('change', function () {
         table.ajax.reload();
    });
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Daftar Prosedur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="akses-tab" data-bs-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Frekuensi Akses Prosedur</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                    <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                        <div class="card-header border-bottom">
                            <div class="col-sm-10">
                                @include('perusahaan')
                            </div>
                            <div class="col-sm-2">
                                <a href="/prosedur/add" class="btn btn-primary btn-sm float-right">Tambah Data</a>
                            </div>
                        </div>
                        <table id="table" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                <th>No.</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Dibuat Oleh</th>
                                <th>Diberlakukan Oleh</th>
                                <th>File PDF</th>
                                <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="profile" aria-labelledby="akses-tab" role="tabpanel">
                        <table id="tableuser" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Kapal</th>
                                    <th>Lihat</th>
                                    <th>Download</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade text-start" id="FormIsi" tabindex="-1" aria-labelledby="myModalLabel17" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="nama"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    @csrf
                    <div class="modal-body">
                        <table id="tabledetail" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <td>Prosedur</td>
                                <td>Lihat</td>
                                <td>Terakhir Lihat</td>
                                <td>Download</td>
                                <td>Terakhir Download</td>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
</section>
@endsection