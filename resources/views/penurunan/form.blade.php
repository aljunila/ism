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
    let otpGenerated = false;

    function initSearchSelect(selector) {
        if (typeof TomSelect === 'undefined') {
            return;
        }

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

    function getAjaxErrorMessage(xhr, fallback) {
        if (xhr.responseJSON?.message) {
            return xhr.responseJSON.message;
        }

        if (xhr.responseJSON?.errors) {
            const firstKey = Object.keys(xhr.responseJSON.errors)[0];
            if (firstKey && xhr.responseJSON.errors[firstKey]?.length) {
                return xhr.responseJSON.errors[firstKey][0];
            }
        }

        return fallback;
    }

    function resetOtpState() {
        otpGenerated = false;
        $('#otp_code').val('').prop('disabled', true);
        $('#otp-status').removeClass('text-success text-danger').addClass('text-muted')
            .text('Generate OTP setelah memilih penerima.');
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
                    url: "/penurunan/deldetail/" + id,
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

                    <div class="col-sm-2">
                        <select name="item[]" id="item" class="form-control select-item">
                            <option value="">Pilih Barang</option>
                        </select>
                    </div>

                    <div class="col-sm-1">
                        <input type="number" class="form-control stok" placeholder="Stok" name="stok[]" readonly>
                    </div>

                    <div class="col-sm-1">
                        <input type="number" class="form-control" placeholder="Jumlah" name="jumlah[]">
                    </div>

                    <div class="col-sm-2">
                        <select name="ket[]" class="form-control">
                            <option value="Rusak">Rusak</option>
                            <option value="Rekondisi">Rekondisi</option>
                        </select>
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

    $(document).on('change', '.select-item', function () {

    let itemId = $(this).val();
    let id_kapal = $('#id_kapal').val();

    let row = $(this).closest('.field-item');

    $.ajax({
        url: '/penurunan/datagudang',
        type: 'POST',
        data: {
            item: itemId,
            id_kapal: id_kapal,
        },
        success: function (res) {
            row.find('.stok').val(res.stok);
            if (res.stok > 0) {
                row.find('input[name="jumlah[]"]').prop('disabled', false);
            } else {
                row.find('input[name="jumlah[]"]').prop('disabled', true);
                alert('Stok tidak tersedia');
            }
        },
        error: function () {
            alert('Gagal mengambil stok');
        }
    });

});

    $(document).on("click", ".hapus", function () {
        $(this).closest(".field-item").remove();
    });

    $('#id_penerima').on('change', function () {
        resetOtpState();
        $('#btn-generate-otp').prop('disabled', !$(this).val());
    });

    $('#btn-generate-otp').on('click', function () {
        const receiverId = $('#id_penerima').val();
        if (!receiverId) {
            Swal.fire('Penerima belum dipilih', 'Pilih user penerima terlebih dahulu.', 'warning');
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('Mengirim...');
        $('#otp-status').removeClass('text-success text-danger').addClass('text-muted').text('Mengirim OTP ke penerima...');

        $.ajax({
            url: "{{ route('penurunan.generateOtpTurun') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id_penerima: receiverId
            },
            success: function(response){
                otpGenerated = true;
                $('#otp_code').prop('disabled', false).focus();
                $('#otp-status').removeClass('text-muted text-danger').addClass('text-success')
                    .text(response.message ?? 'OTP berhasil dikirim ke penerima.');

                if (typeof window.refreshNotifications === 'function') {
                    window.refreshNotifications();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'OTP Terkirim',
                    text: response.message ?? 'OTP berhasil dikirim ke penerima',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr){
                otpGenerated = false;
                $('#otp_code').val('').prop('disabled', true);
                $('#otp-status').removeClass('text-muted text-success').addClass('text-danger')
                    .text(getAjaxErrorMessage(xhr, 'Gagal mengirim OTP'));
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: getAjaxErrorMessage(xhr, 'Gagal mengirim OTP')
                });
            },
            complete: function(){
                btn.prop('disabled', !$('#id_penerima').val()).html(originalText);
            }
        });
    });

    $('#form_permintaan').on('submit', function(e){
        e.preventDefault(); // cegah submit biasa
        let form = $(this);

        if (!$('#id_penerima').val()) {
            Swal.fire('Penerima wajib dipilih', 'Pilih user penerima sebelum menyimpan.', 'warning');
            return;
        }

        const otpCode = ($('#otp_code').val() || '').trim();
        if (!otpGenerated || !otpCode) {
            Swal.fire('OTP wajib diisi', 'Generate OTP lalu masukkan kode OTP dari penerima.', 'warning');
            return;
        }

        if (!/^[0-9]{6}$/.test(otpCode)) {
            Swal.fire('OTP tidak valid', 'Kode OTP harus 6 digit angka.', 'warning');
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
                        window.location.href = "{{ url('/penurunan') }}";
                    });
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: getAjaxErrorMessage(xhr, 'Gagal menyimpan data')
                });
            }
        });
    });

    initSearchSelect('.js-search-select');
    resetOtpState();

    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Penurunan Barang</h4>
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
                    data-store-url="{{ route('penurunan.store') }}" data-update-url="{{ isset($data) ? route('penurunan.update', $data->id) : '' }}">
                    @csrf
                    <div class="row">
                        <div class="col-10">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Pilih Kapal</label>
                                </div>
                                <div class="col-sm-3">
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
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ old('tanggal', $data->tanggal ?? '') }}" {{ isset($data) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                 <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Bagian</label>
                                </div>
                                <div class="col-sm-3">
                                    <select name="bagian" id="bagian" class="js-search-select w-100" {{ isset($data) ? 'disabled' : '' }}>
                                        <option value="1" @selected (isset($data) && $data->bagian==1)>DECK</option>
                                        <option value="2" @selected (isset($data) && $data->bagian==2)>MESIN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="id_penerima">User Penerima</label>
                                </div>
                                <div class="col-sm-3">
                                    <select name="id_penerima" id="id_penerima" class="js-search-select w-100" required>
                                        <option value="">Pilih User Penerima</option>
                                        @foreach($penerima as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->nama ?? $user->username }} ({{ $user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="otp_code">Kode OTP</label>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="otp_code" id="otp_code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="Masukkan OTP dari penerima" disabled required>
                                        <button type="button" class="btn btn-outline-primary" id="btn-generate-otp" disabled>Generate OTP</button>
                                    </div>
                                    <small id="otp-status" class="text-muted">Generate OTP setelah memilih penerima.</small>
                                </div>
                            </div>
                             <div class="mb-1 row" id="form-wrapper">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Daftar Barang Penurunan</label>
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

@endsection
