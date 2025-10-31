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
            let id = {{$show->id}};
            let formData = new FormData(this);

            $.ajax({
                url: '/notulen/update/'+id,
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
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{$show->tanggal}}" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Tempat</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="tempat" name="tempat" value="{{$show->tempat}}" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Hal</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="hal" name="hal" value="{{$show->hal}}" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">DPA/Nahkoda</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="id_nahkoda" id="id_nahkoda"  class="form-control karyawan" required>
                                    @foreach($karyawan as $ky)
                                        <option value="{{$ky->id}}" @selected ($ky->id == $show->id_nahkoda)>{{$ky->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                             <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Notulen</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="id_notulen" id="id_notulen"  class="form-control karyawan" required>
                                    @foreach($karyawan as $k)
                                        <option value="{{$k->id}}" @selected ($k->id == $show->id_notulen)>{{$k->nama}}</option>
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
                                    <textarea class="form-control tinymce" id="materi" name="materi">{!! $show->materi !!}</textarea>
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