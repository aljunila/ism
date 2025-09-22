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
    $(function () {
		$('#table').DataTable({
        processing: true,
        searchable: true,
        ajax:{
            url: "/checklist/data",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->kode}}",
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
            { data: 'nama' },
            { data: 'jabatan' },
            { data: 'kapal' },
            { 
                data: null, 
                render: function (data, type, row) {
                        return `
                        <a href="/checklist/pdf/${row.uid}" type="button" class="btn btn-icon btn-xs btn-flat-primary download" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        `;
                }
            },
            { 
                data: null, 
                render: function (data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"></button>
                            <div class="dropdown-menu">
                                <a type="button" href="/checklist/edit/${row.uid}" class="dropdown-item resign-btn">Edit</a>
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
                    url: "/checklist/delete/" + id,
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
                        $("#table").DataTable().ajax.reload();
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
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">{{$form->nama}}</h4>
                        <a href="/checklist/add/{{$form->kode}}" class="btn btn-primary btn-sm">Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Nama Kapal</th>
                            <th>PDF</th>
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