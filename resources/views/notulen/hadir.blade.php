@extends('main')
@section('scriptheader')
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
     <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Page CSS-->
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <!-- END: Page Vendor JS-->
    <!-- BEGIN: Page JS-->
    <script src="{{ url('/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <!-- END: Page JS-->

    <script>
        $(function () {
            $('#table').DataTable({
            processing: true,
            searchable: true,
            ajax:{
                url: "/hadir/KaryawanHadir",
                type: "POST",
                data: function(d){
                    d.kode= "{{ $form->kode}}",
                    d.id= "{{$show->id}}",
                    d._token= "{{ csrf_token() }}"
                },
            },
                columns: [
                    { data: 'karyawan' },
                    { data: 'jabatan' },
                    { 
                        data: 'id',
                        render: function(data, type, row){
                            return `
                                <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                    title="Hapus" data-id="${data}">Hapus
                                </button>
                            `;
                        }
                    }
                ]
            });
        });

        $('#form_checklist').on('submit', function(e){
            e.preventDefault();
            let id = {{$show->id}};
            let formData = new FormData(this);

            $.ajax({
                url: '/hadir/update/'+id,
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
                            window.location.href = "/{{$form->kode}}";
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
                        url: "/hadir/deletedetail/" + id,
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

        $('#id_kapal').on('change', function() {
            var kapalID = $(this).val();
            if (kapalID) {
                $.ajax({
                    url: '/get-karyawan/' + kapalID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('.karyawan').empty().append('<option value="">-- Pilih Karyawan --</option>');
                    
                        $.each(data, function(key, value) {
                            $('.karyawan').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                        });
                    }
                });
            } else {
                $('.karyawan').empty().append('<option value="">-- Pilih Karyawan --</option>');
            }
        });

        $("#tambah").click(function () {
            let field = `
            <div class="mb-1 row field-item">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-7">
                    <select name="id_karyawan[]" class="form-control karyawan" required>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm hapus">Hapus</button>
                </div>
            </div>`;
            $("#field-container").append(field);
        });

        $(document).on("click", ".hapus", function () {
            $(this).closest(".field-item").remove();
        });
    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Hadir</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                    @endif
                    <form id="form_checklist" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Agenda</label>
                                    </div>
                                    <div class="col-sm-9">
                                        {!!$form->nama !!}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Tanggal</label>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ \Carbon\Carbon::parse($show->tanggal)->format('d-m-Y') }}
                                    </div>
                                </div>
                                 <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Tempat</label>
                                    </div>
                                    <div class="col-sm-9">
                                        {!!$show->tempat !!}
                                    </div>
                                </div>
                                <hr>
                                <div class="mb-1 row" id="form-wrapper">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Daftar Karyawan</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <table id="table" class="table table-bordered table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Jabatan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table><br>
                                        <button type="button" class="btn btn-success btn-sm" id="tambah">Tambah</button>
                                    </div>
                                </div>

                                <!-- Container untuk field dinamis -->
                                <div id="field-container">
                                    <div class="mb-1 row field-item">
                                        <div class="col-sm-3">
                                        </div>
                                        <div class="col-sm-7">
                                            <select name="id_karyawan[]" class="form-control karyawan" required>
                                                @foreach($karyawan as $k)
                                                    <option value="{{$k->id}}">{{$k->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-danger btn-sm hapus">Hapus</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <div class="col-sm-12 offset-sm-3">
                            <input type="hidden" name="kode" value="{{$form->kode}}">
                            <button type="submit" class="btn btn-primary me-1">Simpan</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection