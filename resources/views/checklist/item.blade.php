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
     <script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
        <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
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
                url: "/checklist/dataitem",
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
                    { data: 'item' },
                    { 
                        data: 'id',
                        render: function(data, type, row){
                            return `
                                <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                    title="Edit" data-id="${data}">Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                    title="Hapus" data-id="${data}">Hapus
                                </button>
                            `;
                        }
                    }
                ]
            });
            table.on('draw', function () {
                feather.replace();
            });
        });
        $('#btn_save').on('click', function(e){
            e.preventDefault();
            $.ajax({
                url: "{{ url('/form/intruksi') }}",
                method: "POST",
                data:{
                    intruksi: $('#intruksi').val(),
                    kode: "{{ $form->kode}}"
                },
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'Data berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: true
                        })
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

         $('#tambah_data').on('click', function(e){
            e.preventDefault();
            $.ajax({
                url: "{{ url('/checklist/storeitem') }}",
                method: "POST",
                data:{
                    item: $('#item').val(),
                    kode: "{{ $form->kode}}"
                },
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'Data berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: true
                        });
                        tinymce.get("item").setContent("");
                        $('#FormTambah').modal('hide');
                        $('#table').DataTable().ajax.reload();
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

        $(document).on('click', '.edit-btn', function(){
            var id = $(this).attr('data-id');
            $.ajax({
            // url : "{{url('/getnilai')}}?id="+id,
            url: '/checklist/edititem/'+id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                console.log(data);
                $('#id').val(data.id);
                tinymce.get('nama').setContent(data.item);
                $('#FormEdit').modal('show');
            }
            });
        })

        $(document).on('click', '#edit_data', function(){
            let nama = $('#nama').val()
            let id = $('#id').val()
            $.ajax({
                url: "/checklist/updateitem/" + id,
                type: "POST",
                data: {
                    item: nama,
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: response.message ?? "Data berhasil disimpan",
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // reset input
                        $("#nama").val("");
                        $('#FormEdit').modal('hide');
                        $('#table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Tidak dapat menyimpan data"
                    });
                    console.error(xhr.responseText);
                }
            });
        })

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
                        url: "/checklist/deleteitem/" + id,
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
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Setting Form: {{$form->nama}}</h4>
                    <a type="button" class="btn btn-success btn-sm" href="/{{$form->kode}}"><i data-feather='arrow-left'></i>Kembali</a>
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
                    <div class="row">
                        <form id="form_checklist" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-12">
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Intruksi</label>
                                </div>
                                <div class="col-sm-10">
                                    <textarea name="intruksi" id="intruksi" class="form-control tinymce">{!!$form->intruksi!!}</textarea>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary me-1 btn-sm float-right" id="btn_save">Simpan</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@if(($form->kode!='el0503') or ($form->kode!='el0510'))
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#FormTambah">Tambah Item</button>
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
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="table" class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                    <th width="5%">No.</th>
                                    <th width="70%">Item Checklist</th>
                                    <th width="25%">Aksi</th>
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

    <div class="modal fade text-start" id="FormTambah" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Tambah Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <label>Item Checklist </label>
                        <div class="mb-1">
                            <textarea name="item" id="item"  class="tinymce form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="tambah_data">Simpan</button>
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormEdit" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <label>Item Checklist </label>
                        <div class="mb-1">
                            <textarea name="nama" id="nama"  class="tinymce form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="id" name="id">
                        <button type="button" class="btn btn-primary" id="edit_data">Simpan</button>
                    </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection