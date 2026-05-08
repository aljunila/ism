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
    let table;
    let otpGenerated = false;

    function initSearchSelect(selector) {
        if (typeof TomSelect === 'undefined') {
            return;
        }

        $(selector).each(function () {
            if (this.tomselect) {
                return;
            }

            new TomSelect(this, {
                create: false,
                allowEmptyOption: true,
                sortField: {
                    field: 'text',
                    direction: 'asc'
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

    $(function () {
        initSearchSelect('.js-search-select');
        resetOtpState();

        table = $('#table').DataTable({  
            processing: true,
            searchable: true,
            serverSide: true,
            ajax:{
                url: "/permintaan/baranggudang",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.bagian= $('#bagian').val(),
                    d._token= "{{ csrf_token() }}"
                },
                dataSrc: "data"
            },
            columns: [
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<input type="checkbox" name="check[]" value="${row.id}">`;
                    }
                },
                { data: null, 
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1; 
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'barang' },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return `No  Permintaan : ${row.nomor}<br> Tgl Permintaan : ${row.tanggal}`;
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return `${row.jml_minta} <br>
                        <input type="hidden" name="total[${row.id}]" class="form-control" value="${row.jml_minta}"></td>`;
                    }
                },
                { data: 'stok' },
                { 
                    data: null, 
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, row) {
                        return `<input type="number" name="jumlah[${row.id}]" class="form-control" min="0" value="${row.jml_minta}">
                                <input type="hidden" name="barang[${row.id}]" class="form-control" value="${row.id_barang}">
                                <input type="hidden" name="gudang[${row.id}]" class="form-control" value="${row.idgudang}">`;
                    }
                },
                { 
                    data: null, 
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, row) {
                        return `<select name="ket[${row.id}]" class="form-control">
                                    <option value="Baru">Baru</option>
                                    <option value="Rekondisi">Rekondisi</option>
                                </select>`;
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

    $('#bagian').on('change', function () {
         table.ajax.reload();
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
            url: "{{ route('permintaan.generateOtpKirim') }}",
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
                        window.location.href = "{{ url('/permintaan') }}";
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

    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Data Pengiriman Barang</h4>
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
                    data-store-url="{{ route('permintaan.storekirim') }}" data-update-url="{{ isset($data) ? route('permintaan.update', $data->id) : '' }}">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Pilih Kapal</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="id_kapal" id="id_kapal" class="form-control js-search-select w-100" {{ isset($data) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kapal</option>
                                        @foreach($kapal as $kp)
                                            <option value="{{$kp->id}}" @selected (isset($data) && $kp->id==$data->id_kapal)>{{$kp->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Tanggal</label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ old('tanggal', $data->tanggal ?? '') }}" {{ isset($data) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                 <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Bagian</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="bagian" id="bagian" class="form-control js-search-select w-100" {{ isset($data) ? 'disabled' : '' }}>
                                        <option value="1" @selected (isset($data) && $data->bagian=="1")>DECK</option>
                                        <option value="2" @selected (isset($data) && $data->bagian=="2")>MESIN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="id_penerima">User Penerima</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="id_penerima" id="id_penerima" class="form-control js-search-select w-100" required>
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
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="otp_code">Kode OTP</label>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="otp_code" id="otp_code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="Masukkan OTP dari penerima" disabled required>
                                        <button type="button" class="btn btn-outline-primary" id="btn-generate-otp" disabled>Generate OTP</button>
                                    </div>
                                    <small id="otp-status" class="text-muted">Generate OTP setelah memilih penerima.</small>
                                </div>
                            </div>
                             <div class="mb-1 row" id="form-wrapper">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Daftar Barang Workshop</label>
                                </div>
                                <div class="col-sm-7">
                                    <table id="table" class="table table-bordered table-striped" width="100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Keterangan</th>
                                                <th>Jml Permintaan</th>
                                                <th>Jml Stok</th>
                                                <th>Jml Kirim</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table><br>
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
