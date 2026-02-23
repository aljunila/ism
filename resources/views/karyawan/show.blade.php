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
    $('#id_kapal').hide();
    $('#id_cabang').hide();
 
  $(function () {
	table = $('#table').DataTable({  
        processing: true,
        searchable: true,
        serverSide: true,
        ajax:{
            url: "/karyawan/data",
            type: "POST",
            data: function(d){
                d.id_perusahaan= $('#id_perusahaan').val(),
                d.kel= $('#kel').val(),
                d.id_kapal= $('#id_kapal').val(),
                d.id_cabang= $('#id_cabang').val(),
                d._token= "{{ csrf_token() }}"
            },
            dataSrc: "data"
        },
        columns: [
            { data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; 
                },
                orderable: false,
                searchable: false
            },
            { 
                data: 'nama',
                render: function(data, type, row) {
                    return `${row.nama}<br>${row.nip}`;
                }
            },
            { data: 'nik' },
            { 
                data: null,
                render: function(data, type, row) {
                    if(row.kapal) { return `${row.kapal}`; }
                    else if(row.cabang) { return `Office ${row.cabang}`; }
                    else { return `Office`; }
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
                                <a type="button" data-id="${row.id}" class="dropdown-item resign-btn">Nonaktif</a>
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

    table2 = $('#table2').DataTable({  
        processing: true,
        searchable: true,
        serverSide: true,
        ajax: '{{ route('karyawan.resign') }}',
        columns: [
            { data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; 
                },
                orderable: false,
                searchable: false
            },
            { 
                data: 'nama',
                render: function(data, type, row) {
                    return `${row.nama}<br>${row.nip}`;
                }
            },
            { data: 'nik' },
            { data: 'tgl_resign',
                    render: function(data) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                },
            { data: 'alasan' },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <a type="button" href="/karyawan/profil/${row.uid}" class="btn btn-success btn-sm">Profil</a>
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
    $('#id').val(id);
    $('#FormResign').modal('show');
  });

    $(document).on('change', '#kel', function() {
        let kel = $(this).val();
        if(kel==1){
            $('#id_kapal').show();
            $('#id_cabang').hide();
            table.ajax.reload();
        } else {
            $('#id_kapal').hide();
            $('#id_cabang').show();
            table.ajax.reload();
        }
    })

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
    
    $('#id_kapal').on('change', function () {
         table.ajax.reload();
    });

    $('#id_cabang').on('change', function () {
         table.ajax.reload();
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

    $('#form_resign').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let formData = new FormData(this);

            $.ajax({
                 url: "{{ url('/karyawan/resign') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'Data berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#FormResign').modal('hide');
                            table.ajax.reload();
                            table2.ajax.reload();
                        });
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menyimpan data'
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
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="aktif-tab" data-bs-toggle="tab" href="#aktif" aria-controls="aktif" role="tab" aria-selected="true"><i data-feather="user"></i>Crew Aktif</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="resign-tab" data-bs-toggle="tab" href="#resign" aria-controls="resign" role="tab" aria-selected="true"><i data-feather="user-x"></i>Crew Resign</a>
                        </li>
                    </ul>
                    <!-- <form action="/karyawan/export" method="POST" enctype="multipart/form-data">
                    @csrf -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="aktif" aria-labelledby="aktif-tab" role="tabpanel">
                            <div class="card-header border-bottom">
                                <div class="col-sm-12"><h4 class="card-title">Daftar Karyawan</h4></div>
                                <div class="col-sm-2">
                                <select name="kel" id="kel" class="form-control">
                                    <option value="0">Pilih</option>
                                    <option value="1">Laut</option>
                                    <option value="2">Darat</option>
                                </select>
                                </div>
                                @include('perusahaan')
                                <div class="col-sm-2">
                                    <select name="id_kapal" id="id_kapal" class="form-control kapal">
                                        <option value="">Pilih Kapal</option>
                                        @foreach($kapal as $k)
                                            <option value="{{$k->id}}">{{$k->nama}}</option>
                                        @endforeach
                                    </select>
                                    <select name="id_cabang" id="id_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach($cabang as $c)
                                        <option value="{{$c->id}}">{{$c->cabang}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                <button type="button" class="btn btn-warning btn-sm" id="download"><i data-feather='download'></i> Unduh Data</button>
                                <a href="/karyawan/add" class="btn btn-primary btn-sm"><i data-feather='file-plus'></i> Tambah Data</a>
                                </div>
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
                        <div class="tab-pane" id="resign" aria-labelledby="resign-tab" role="tabpanel">
                            <div class="card-body">
                                <table id="table2" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>Tgl Nonaktif</th>
                                        <th>Alasan</th>
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
            </div>
        </div>
</section>
<div class="modal fade text-start" id="FormResign" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Nonaktif Crew</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_resign" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Tgl Nonaktif</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="tgl_resign" id="tgl_resign">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Keterangan</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="alasan" id="alasan" placeholder="Contoh: resing, PHK, dll">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-12">
                                <label class="col-form-label" style="color: red;">Karyawan nonaktif tidak akan dapat LogIn ke dalam sistem.</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" class="form-control" name="id" id="id">
                        <button type="submit" class="btn btn-danger" id="simpan_resign">Nonaktifkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection