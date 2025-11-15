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
                url: "/checklist/GetPersonil",
                type: "POST",
                data: function(d){
                    d.id= "{{$show->id}}",
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'nama' },
                { 
                    data: 'id',
                    render: function(data, type, row){
                        return `
                            <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                title="Hapus" data-id="${data}"><i data-feather="trash-2"></i>
                            </button>
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
                        url: "/checklist/deletepersonil/" + id,
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

        $('#form_aturan').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = {{$show->id}};
            let formData = new FormData(this);

            $.ajax({
                url: '/checklist/panasupdate/'+id,
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

        $("#tambah").click(function () {
            let field = `
            <div class="mb-1 row field-item">
                <div class="col-sm-2">
                </div>
                <div class="col-sm-4">
                    <input type="text" name="personil[]" placeholder="Nama Personil" class="form-control">
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
    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tambah Prosedur</h4>
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
                    <form id="form_aturan" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Perusahaan</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="id_perusahaan" id="id_perusahaan" class="form-control" required>
                                    @foreach($perusahaan as $p)
                                        <option value="{{$p->id}}" @selected ($p->id==$show->id_perusahaan)>{{$p->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Kapal</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="id_kapal" id="id_kapal"  class="form-control" required>
                                    @foreach($kapal as $kp)
                                        <option value="{{$kp->id}}"  @selected ($kp->id==$show->id_kapal)>{{$kp->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Tanggal</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{$show->date}}" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Pelabuhan</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="pelabuhan" name="pelabuhan" value="{{$show->ket}}" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Jenis</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="jenis" name="jenis" value="{{$show->note}}" required>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-1 row" id="form-wrapper">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Nama Personil Kerja</label>
                                </div>
                                <div class="col-sm-10">
                                    <table id="table" class="table table-bordered table-striped" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Nama Personil</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table><br>
                                    <button type="button" class="btn btn-success btn-sm" id="tambah">Tambah</button>
                                </div>
                            </div>
                            <div id="field-container"></div>
                            </hr>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Materi</label>
                                </div>
                                <div class="col-sm-10">
                                    <table class="table table-bordered table-striped" border="1">
                                        <tr>
                                            <td>No</td>
                                            <td>Materi</td>
                                            <td>Ya</td>
                                            <td>Tidak</td>
                                        </tr>
                                        @foreach($item as $ck)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{!!$ck->get_item()->item!!}</td>
                                            <td><input type="radio" class="form-check-input" name="item[{{$ck->checklist_item_id}}]" value="1" @checked ($ck->value == 1)></td>
                                            <td><input type="radio" class="form-check-input" name="item[{{$ck->checklist_item_id}}]" value="0" @checked ($ck->value == 0)></td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 offset-sm-3">
                            <input type="hidden" class="form-control" id="kode" name="kode" value="{{$form->kode}}">
                            <button type="submit" class="btn btn-primary me-1" id="simpan_data">Simpan</button>
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