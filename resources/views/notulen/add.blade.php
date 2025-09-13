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
        $('#form_aturan').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa

            let formData = new FormData(this);

            $.ajax({
                url: "{{ url('/notulen/store') }}",
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
                            window.location.href = "{{ url('/el0301') }}";
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
                    <h4 class="card-title">Tambah Form</h4>
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
                                    <label class="col-form-label" for="first-name">Tanggal</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Tempat</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="tempat" name="tempat" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Hal</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="hal" name="hal" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">DPA/Nahkoda</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="id_nahkoda" id="id_nahkoda"  class="form-control" required>
                                    @foreach($karyawan as $ky)
                                        <option value="{{$ky->id}}">{{$ky->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                             <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Notulen</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="id_notulen" id="id_notulen"  class="form-control" required>
                                    @foreach($karyawan as $ky)
                                        <option value="{{$ky->id}}">{{$ky->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-1 row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="first-name">Materi Rapat</label>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Isi</label>
                                </div>
                                <div class="col-sm-10">
                                    <textarea class="form-control tinymce" id="materi" name="materi"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 offset-sm-3">
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