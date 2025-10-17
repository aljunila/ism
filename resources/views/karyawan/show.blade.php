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
        serverSide: false,
        ajax:{
            url: "/karyawan/data",
            type: "POST",
            data: function(d){
                d.id_perusahaan= $('#id_perusahaan').val(),
                d.id_kapal= $('#id_kapal').val(),
                d._token= "{{ csrf_token() }}"
            },
        },
        columns: [
            { data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; 
                },
                orderable: false,
                searchable: false
            },
            { data: 'nama' },
            { data: 'nik' },
            { 
                data: 'kapal',
                render: function(data, type, row) {
                    return data ? data : 'Office';
                }
            },
            { data: 'jabatan' },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather='edit-3'></i></button>
                            <div class="dropdown-menu">
                                <a href="/karyawan/profil/${row.uid}" class="dropdown-item">Profil</a>
                                <a type="button" data-id="${row.id}" class="dropdown-item resign-btn">Resign</a>
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
     
    $('#id_kapal').on('change', function () {
         table.ajax.reload();
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
                url: "/karyawan/delete/" + id,
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

  $(document).on("click", ".resign-btn", function(){
    let id = $(this).data("id");

    Swal.fire({
        title: "Apa benar resign?",
        text: "Karyawan yang sudah resign tidak dapat login kembali",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "rgba(202, 221, 32, 1)",
        cancelButtonColor: "#e76006ff",
        confirmButtonText: "Benar!",
        cancelButtonText: "Tidak"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/karyawan/resign/" + id,
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res){
                    Swal.fire({
                        icon: "success",
                        title: "Diarsipkan!",
                        text: "Data telah diarsipkan",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    table.ajax.reload();
                },
                error: function(err){
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: "Data memproses"
                    });
                }
            });
        }
    });
  });

    $(document).on('change', '#id_perusahaan', function() {
        var perusahaanID = $(this).val();
        if (perusahaanID) {
            $.ajax({
                url: '/get-kapal/' + perusahaanID,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#id_kapal').empty().append('<option value="">Semua</option>');           
                    $.each(data, function(key, value) {
                        $('#id_kapal').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                    });
                    table.ajax.reload();
                }
            });
        } else {
            $('#id_kapal').empty().append('<option value="">Tidak ada data</option>');
            table.ajax.reload();
        }
    });

    $(document).on('click', '#download', function() {
        $.ajax({
            url: "/karyawan/export",
            method: "POST",
            xhrFields: { responseType: 'blob' },
            data: {
                id_perusahaan: $('#id_perusahaan').val(),
                id_kapal: $('#id_kapal').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(data){
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = "data_karyawan.xlsx";
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
                    <!-- <form action="/karyawan/export" method="POST" enctype="multipart/form-data">
                    @csrf -->
                    <div class="card-header border-bottom">
                        <div class="col-sm-12"><h4 class="card-title">Daftar Karyawan</h4></div>
                        @include('filter')
                        <button type="button" class="btn btn-warning btn-sm" id="download"><i data-feather='download'></i> Unduh Data</button>
                        <a href="/karyawan/add" class="btn btn-primary btn-sm"><i data-feather='file-plus'></i> Tambah Data</a>
                    </div>
                    <!-- </form> -->
                    <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Nama</th>
                          <th>NIK</th>
                          <th>Penempatan</th>
                          <th>Jabatan</th>
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