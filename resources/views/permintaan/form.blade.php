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

    const resetForm = () => {
        $('#modal-barang-label').text('Tambah Data');
        $('#barang-nama').val('');
        $('#barang-kode').val('');
        $('#barang-deskripsi').val('');
        $('#barang-id_kel_barang').val('');
        $('#btn-save-barang').data('mode', 'create').data('id', '');
    };

    $('#btn-add-barang').on('click', function () {     
        resetForm();
        $('#modal-barang').modal('show');
    });

    $('#btn-save-barang').on('click', function () {
        const mode = $(this).data('mode') || 'create';
        const id = $(this).data('id');
        const payload = {
            nama: $('#barang-nama').val(),
            kode: $('#barang-kode').val(),
            deskripsi: $('#barang-deskripsi').val(),
            id_kel_barang: $('#barang-id_kel_barang').val(),
        };
        const ajaxOpts = {
            url: mode === 'edit' ? '{{ url('data_master/barang') }}/' + id : '{{ route('barang.store') }}',
            type: mode === 'edit' ? 'PUT' : 'POST',
            data: payload
        };
        $.ajax(ajaxOpts)
        .done(res => {
            Swal.fire(res.status, res.message, res.status)
                .then(() => {
                    if (res.status === 'success') {
                        $('#modal-barang').modal('hide');
                        table.ajax.reload(null, false);
                    }
                });
        })
        .fail(xhr => {
            Swal.fire(
                'Gagal',
                xhr.responseJSON?.message || 'Error',
                'error'
            );
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
        let bagian = $('#bagian').val();
        $.ajax({
            url: '/data_master/barang/databyKat',
            type: 'POST',
            data: {
                bagian: bagian
            },
            success: function (res) {
                let options = `<option value="">Pilih Kelompok</option>`;
                res.forEach(function (b) {
                    options += `<option value="${b.id}">
                                    ${b.nama} (${b.kode})
                                </option>`;
                });
                let field = `
                <div class="mb-1 row field-item">
                    <div class="col-sm-3"></div>

                    <div class="col-sm-3">
                        <select name="kel[]" class="form-control select-kelompok">
                            ${options}
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <select name="item[]" id="item" class="form-control select-item">
                            <option value="">Pilih Barang</option>
                        </select>
                    </div>

                    <div class="col-sm-2">
                        <input type="text" class="form-control" placeholder="Masukan jumlah" name="jumlah[]">
                    </div>

                    <div class="col-sm-1">
                        <button type="button" class="btn btn-danger btn-sm hapus">Hapus</button>
                    </div>
                </div>`;

                $("#field-container").append(field);

                // init select2 / search select
                initSearchSelect("#field-container .js-search-select:last");
            },
            error: function () {
                alert('Gagal load data barang');
            }
        });
    });

    $(document).on('change', '.select-kelompok', function () {
        let kelompokId = $(this).val();
        let parent = $(this).closest('.field-item');
        let itemSelect = parent.find('.select-item');
        if (!kelompokId) {
            itemSelect.html('<option value="">Pilih Barang</option>');
            return;
        }

        $.ajax({
            url: '/data_master/barang/barangbyKel',
            type: 'GET',
            data: { id_kel_barang: kelompokId },
            success: function (res) {
                let options = '<option value="">Pilih Barang</option>';
                res.forEach(function (b) {
                    options += `<option value="${b.id}">${b.nama} (${b.kode})</option>`;
                });
                itemSelect.html(options);
                initSearchSelect(itemSelect);
            }
        });

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
                    <button class="btn btn-primary btn-sm" id="btn-add-barang">Tambah Data</button>
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
                                        <option value="1" @selected (isset($data) && $data->bagian==1)>DECK</option>
                                        <option value="2" @selected (isset($data) && $data->bagian==2)>MESIN</option>
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
                                                    @if((int) $d->status === (int) ($permintaanStatusId ?? 0)) 
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

<div class="modal fade" id="modal-barang" tabindex="-1" aria-labelledby="modal-barang-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-barang-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Kelompok Barang</label>
                    <select id="barang-id_kel_barang" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($kelompok as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Kode</label>
                    <input type="text" id="barang-kode" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Nama</label>
                    <input type="text" id="barang-nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Satuan</label>
                    <input type="text" id="barang-deskripsi" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection
