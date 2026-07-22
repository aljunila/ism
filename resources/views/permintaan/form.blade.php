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
    let currentTomSelect = null;
    let namaBarangBaru = '';

    function initTomSelect(element) {
        if (!element || element.tomselect) return;
        let ts = new TomSelect(element, {
            create: false,
            persist: false,
            placeholder: 'Pilih Barang',

            load: function(query, callback) {

                // hapus option tambah sebelumnya
                this.removeOption('__new__');

                if (query.length >= 2) {

                    this.addOption({
                        value: '__new__',
                        text: '➕ Tambah Barang "' + query + '"'
                    });

                    namaBarangBaru = query;
                }

                callback();
            },

            onChange: function(value) {

                if (value === '__new__') {

                    currentTomSelect = this;

                    $('#barang-kelompok-select').val('');
                    $('#barang-kode-baru').val('');

                    $('#modalBarang').modal('show');

                    this.clear();
                }

            }

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
        let id_kapal = $('#id_kapal').val();
        $.ajax({
            url: '/data_master/barang/databyKat',
            type: 'POST',
            data: {
                bagian: bagian,
                id_kapal: id_kapal
            },
            success: function (res) {
                let options = `<option value="">Pilih Barang</option>`;
                res.forEach(function (b) {
                    options += `<option value="${b.id}">
                                    ${b.nama} (${b.kode})
                                </option>`;
                });
                let field = `
                <div class="mb-1 row field-item">
                    <div class="col-sm-3"></div>

                    <div class="col-sm-3">
                        <select name="item[]" class="form-control barang">
                            ${options}
                        </select>
                    </div>

                    <div class="col-sm-2">
                        <input type="number" class="form-control" placeholder="Jumlah" name="jumlah[]">
                    </div>

                    <div class="col-sm-2">
                        <select name="ket[]" class="form-control">
                            <option value="Umum">Umum</option>
                            <option value="Segera">Segera</option>
                        </select>
                    </div>

                    <div class="col-sm-1">
                        <button type="button" class="btn btn-danger btn-sm hapus">Hapus</button>
                    </div>
                </div>`;

                $("#field-container").append(field);

                let select = $("#field-container .barang").last()[0];
                initTomSelect(select);
            },
            error: function () {
                alert('Gagal load data barang');
            }
        });
    });
        
    $(document).on("click", ".hapus", function () {
        $(this).closest(".field-item").remove();
    });

    function toggleDetailKeterangan(input) {
        const original = String($(input).data('original') ?? '');
        const current = String($(input).val() ?? '');
        const wrapper = $(input).closest('.jumlah-cell');
        const reasonBlock = wrapper.find('.detail-keterangan-wrapper');
        const reasonInput = wrapper.find('.detail-keterangan');
        const isChanged = current !== '' && current !== original;

        reasonBlock.toggleClass('d-none', !isChanged);
        reasonInput.prop('required', isChanged);

        if (!isChanged) {
            reasonInput.val('');
        }
    }

    $(document).on('input change', '.detail-jumlah', function () {
        toggleDetailKeterangan(this);
    });

    $('#form_permintaan').on('submit', function(e){
        e.preventDefault(); // cegah submit biasa
        let form = $(this);
        let invalidChange = false;

        $('.detail-jumlah').each(function () {
            toggleDetailKeterangan(this);
            const original = String($(this).data('original') ?? '');
            const current = String($(this).val() ?? '');
            const reason = $(this).closest('.jumlah-cell').find('.detail-keterangan').val();

            if (current !== '' && current !== original && !String(reason || '').trim()) {
                invalidChange = true;
            }
        });

        if (invalidChange) {
            Swal.fire({
                icon: 'error',
                title: 'Keterangan wajib diisi',
                text: 'Isi keterangan untuk setiap jumlah permintaan yang bertambah atau berkurang.'
            });
            return;
        }

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
                const errors = xhr.responseJSON?.errors;
                const firstError = errors ? Object.values(errors).flat()[0] : null;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: firstError || xhr.responseJSON?.message || 'Gagal menyimpan data'
                });
            }
        });
    });

    $(document).on('click','#barang-confirm',function(){
        $.ajax({
            url:'/data_master/barang/storeAjax',
            type:'POST',
            data:{
                nama:namaBarangBaru,
                kode:$('#barang-kode').val(),
                id_kelompok:$('#barang-kelompok-select').val(),
                _token:$('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){

                currentTomSelect.addOption({
                    value:res.id,
                    text:res.nama+' ('+res.kode+')'
                });

                currentTomSelect.setValue(res.id);

                $('#modalBarang').modal('hide');

            }
        });
    });

    $(document).on('change', '#bagian', function() {
        var idbagian = $(this).val();
        var id_kapal = $('#id_kapal').val();
        if (idbagian) {
            $.ajax({
                url: '/data_master/kelbarang/get',
                type: "POST",
                dataType: "json",
                data: {
                    idbagian: idbagian,
                    id_kapal: id_kapal
                },
                success: function(data) {
                    $('#barang-kelompok-select').empty().append('<option value="">Semua</option>');           
                    $.each(data, function(key, value) {
                        $('#barang-kelompok-select').append('<option value="'+ value.id +'">'+ value.nama +'('+ value.kode +')</option>');
                    });
                }
            });
        } else {
            $('#barang-kelompok-select').empty().append('<option value="">Tidak ada data</option>');
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
                    <h4 class="card-title">Form Permintaan Barang</h4>
                    <!-- <button class="btn btn-primary btn-sm" id="btn-add-barang">Tambah Data</button> -->
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
                        <div class="col-10">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Pilih Kapal</label>
                                </div>
                                <div class="col-sm-3">
                                    <select name="id_kapal" id="id_kapal" class="form-control" {{ isset($data) ? 'disabled' : '' }}>
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
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ old('tanggal', $data->tanggal ?? '') }}" {{ isset($data) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                 <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Bagian</label>
                                </div>
                                <div class="col-sm-3">
                                    <select name="bagian" id="bagian" class="form-control" {{ isset($data) ? 'disabled' : '' }}>
                                        <option value="1" @selected (isset($data) && $data->bagian==1)>DECK</option>
                                        <option value="2" @selected (isset($data) && $data->bagian==2)>MESIN</option>
                                        <option value="3" @selected (isset($data) && $data->bagian==3)>ELECTRICIANT</option>
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
                                            @php
                                                $canEditDetail = (int) $d->status === (int) ($permintaanStatusId ?? 0);
                                            @endphp
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$d->get_barang()->nama}}</td>
                                                <td>{{$d->get_barang()->deskripsi}}</td>
                                                <td class="jumlah-cell">
                                                    <input
                                                        type="number"
                                                        class="form-control detail-jumlah"
                                                        name="detail_jumlah[{{$d->id}}]"
                                                        value="{{$d->jumlah}}"
                                                        min="1"
                                                        data-original="{{$d->jumlah}}"
                                                    >
                                                    <div class="detail-keterangan-wrapper d-none mt-1">
                                                        <label class="form-label mb-25">Keterangan perubahan jumlah</label>
                                                        <textarea
                                                            name="detail_keterangan[{{$d->id}}]"
                                                            class="form-control detail-keterangan"
                                                            rows="2"
                                                            placeholder="Jelaskan kenapa jumlah bertambah atau berkurang"
                                                        ></textarea>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($canEditDetail)
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
                            <button type="submit" class="btn btn-sm btn-primary me-1" id="simpan_data">Buat Permintaan</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalBarang" tabindex="-1" aria-hidden="true" style="z-index:1080;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Kelompok untuk Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-75">Barang akan ditambahkan ke kelompok:</p>
                <select id="barang-kelompok-select" class="form-control">
                    <option value="">-- Pilih Kelompok --</option>
                    @foreach($kelompok as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
                <div class="mb-1">
                    <label class="form-label">Part Number</label>
                    <input type="text" id="barang-kode" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="barang-confirm">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

@endsection
