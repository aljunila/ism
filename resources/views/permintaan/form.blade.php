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
    <!-- END: Page CSS-->
@endsection

@section('scriptfooter')
    <script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

    <script>
    function initSearchSelect(selector) {
        $(selector).each(function () {
            if (this.tomselect) return;
            new TomSelect(this, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });
    }

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
                    url: "/permintaan/deldetail/" + id,
                    type: "delete",
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
                    window.location.reload();
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

    $("#tambah").click(function () {
        let field = `
        <div class="mb-1 row field-item">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-4">
                <select name="item[]" class="js-search-select w-100">
                    <option value="">Pilih Barang</option>
                    @foreach($barang as $b)
                        <option value="{{$b->id}}">{{$b->nama}}</option>
                    @endforeach
                </select>
            </div>
                <div class="col-sm-3">
                <input type="text" class="form-control" placeholder="Masukan jumlah" name="jumlah[]">
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-danger btn-sm hapus">Hapus</button>
            </div>
        </div>`;
        $("#field-container").append(field);
        initSearchSelect("#field-container .js-search-select:last");
    });

    $(document).on("click", ".hapus", function () {
        $(this).closest(".field-item").remove();
    });

    $('#form_permintaan').on('submit', function(e){
        e.preventDefault(); // cegah submit biasa
        let form = $(this);
        let formData = new FormData(this);
        let url = form.data('update-url')
            ? form.data('update-url')   // EDIT
            : form.data('store-url'); //ADD
        $.ajax({
            url: url,
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
                        window.location.href = "{{ url('/permintaan') }}";
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

    initSearchSelect('.js-search-select');

    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Data Trip</h4>
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
                    <form id="form_permintaan"
                    data-store-url="{{ route('permintaan.store') }}" data-update-url="{{ isset($data) ? route('permintaan.update', $data->id) : '' }}">
                    @csrf
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Pilih Kapal</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_kapal" id="id_kapal" class="js-search-select w-100" {{ isset($data) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kapal</option>
                                        @foreach($kapal as $kp)
                                            <option value="{{$kp->id}}" @selected (isset($data) && $kp->id==$data->id_kapal)>{{$kp->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tanggal</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ old('tanggal', $data->tanggal ?? '') }}" {{ isset($data) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                 <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Bagian</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="bagian" id="bagian" class="js-search-select w-100" {{ isset($data) ? 'disabled' : '' }}>
                                        <option value="DECK" @selected (isset($data) && $data->bagian=="DECK")>DECK</option>
                                        <option value="MESIN" @selected (isset($data) && $data->bagian=="MESIN")>MESIN</option>
                                    </select>
                                </div>
                            </div>
                             <div class="mb-1 row" id="form-wrapper">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Daftar Barang Permintaan</label>
                                </div>
                                <div class="col-sm-9">
                                    @if (isset($data))
                                    <table id="table" class="table table-bordered table-striped" width="100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Satuan</th>
                                                <th>Jumlah</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detail as $d)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$d->get_barang()->nama}}</td>
                                                <td>{{$d->get_barang()->deskripsi}}</td>
                                                <td>{{$d->jumlah}}</td>
                                                <td>
                                                    @if($d->status==1) 
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{$d->id}}">Hapus</button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table><br>
                                    @endif
                                    <button type="button" class="btn btn-success btn-sm" id="tambah">Tambah</button>
                                </div>
                            </div>
                            <div id="field-container"></div>
                        </div>
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary me-1" id="simpan_data">Simpan</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
