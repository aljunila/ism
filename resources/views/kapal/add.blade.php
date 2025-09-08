@extends('main')
@section('scriptheader')
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
     <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
    <!-- END: Page CSS-->
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
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
        $('#form_kapal').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa

            let formData = new FormData(this);

            $.ajax({
                url: "{{ url('/kapal/store') }}",
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
                            window.location.href = "{{ url('/kapal') }}";
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
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Input Data Kapal</h4>
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
                    <form id="form_kapal" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Nama Kapal</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="nama" name="nama" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Pendaftaran</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="pendaftaran" name="pendaftaran" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Grosse Akte Nomor</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="no_akte" name="no_akte" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">No SIUP</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="no_siup" name="no_siup" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Dikeluarkan di</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="dikeluarkan_di" name="dikeluarkan_di" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Tanda Selar</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="selar" name="selar" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Pemilik Kapal</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="pemilik" class="form-control">
                                            @foreach ($perusahaan as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Call Sign</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="call_sign" name="call_sign" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Nama Galangan/Tahun Pembuatan</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="galangan" name="galangan" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Konstruksi</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="konstruksi" name="konstruksi" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Type Kapal</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="type" name="type" required>
                                    </div>
                                </div>
                                 <hr>
                                <div class="mb-1 row">
                                    <div class="col-sm-12">
                                        <label class="col-form-label" for="first-name">Kecepatan</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Maksimum</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="max_speed" name="max_speed"> 
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">knot</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Nomor</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="normal_speed" name="normal_speed"> 
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">knot</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Minimum</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="min_speed" name="min_speed"> 
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">knot</label>
                                    </div>
                                </div><hr>
                                <div class="mb-1 row">
                                    <div class="col-sm-12">
                                        <label class="col-form-label" for="first-name">Bahan Bakar</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Jenis</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="bahan_bakar" name="bahan_bakar"> 
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Jumlah kebutuhan /hari</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="jml_butuh" name="jml_butuh"> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-12">
                                        <label class="col-form-label" for="first-name">Ukuran Pokok</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Panjang kapal seluruhnya (LOA)</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="loa" name="loa">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Panjang antara garis tegak (LBP) </label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="lbp" name="lbp">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Lebar Kapal</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="lebar" name="lebar">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Dalam (h)</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="dalam" name="dalam">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div><hr>
                                 <div class="mb-1 row">
                                    <div class="col-sm-12">
                                        <label class="col-form-label" for="first-name">Draft Kapal</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Summer Draft</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="summer_draft" name="summer_draft">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Winter Draft</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="winter_draft" name="winter_draft">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Draft air tawar</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="draft_air_tawar" name="draft_air_tawar">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Tropical Draft</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="tropical_draft" name="tropical_draft">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">meter</label>
                                    </div>
                                </div><hr>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Isi Kotor</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="isi_kotor" name="isi_kotor"> 
                                    </div>
                                        <label class="col-form-label" for="first-name">ton</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Bobot Mati</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="bobot_mati" name="bobot_mati"> 
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">NT</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="nt" name="nt"> 
                                    </div>
                                </div>
                                <hr>
                                <div class="mb-1 row">
                                    <div class="col-sm-12">
                                        <label class="col-form-label" for="first-name">Mesin Induk</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Merk</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="merk_mesin_induk" name="merk_mesin_induk"> 
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Tahun</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="tahun_mesin_induk" name="tahun_mesin_induk"> 
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Nomor</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="no_mesin_induk" name="no_mesin_induk"> 
                                    </div>
                                </div>
                                 <hr>
                                <div class="mb-1 row">
                                    <div class="col-sm-12">
                                        <label class="col-form-label" for="first-name">Mesin Bantu</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Merk</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="merk_mesin_bantu" name="merk_mesin_bantu"> 
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Tahun</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="tahun_mesin_bantu" name="tahun_mesin_bantu"> 
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Nomor</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="no_mesin_bantu" name="no_mesin_bantu"> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 offset-sm-3">
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