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
        $('#form_karyawan').on('submit', function(e){
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
                            window.location.href = "{{ url('/data_kapal/trip') }}";
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

        $(document).on('change', '#id_kapal', function() {
            var kapalID = $(this).val();
            if (kapalID) {
                $.ajax({
                    url: '/get-pelabuhan/' + kapalID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#id_pelabuhan').empty().append('<option value="">-Pilih-</option>');           
                        $.each(data, function(key, value) {
                            $('#id_pelabuhan').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                        });
                    }
                });
            } else {
                $('#id_pelabuhan').empty().append('<option value="">Tidak ada data</option>');
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
                    <h4 class="card-title">Tambah Karyawan</h4>
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
                    <form id="form_karyawan"
                    data-store-url="{{ route('trip.store') }}"
                    data-update-url="{{ isset($trip) ? route('trip.update', $trip->id) : '' }}">
                    @csrf
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Kapal</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_kapal" id="id_kapal" required class="form-control"  {{ isset($trip) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kapal</option>
                                    @foreach($kapal as $kp)
                                        <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tanggal</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required value="{{ old('tanggal', $trip->tanggal ?? '') }}"  {{ isset($trip) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Pelabuhan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_pelabuhan" id="id_pelabuhan" required class="form-control"  {{ isset($trip) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kapal</option>
                                            @foreach($pelabuhan as $p)
                                                <option value="{{$p->id}}"  @selected (isset($trip) && $p->id==$trip->id_pelabuhan)>{{$p->nama}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Trip ke</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="trip" id="trip"  class="form-control"  {{ isset($trip) ? 'disabled' : '' }}>
                                        <option value="">Pilih</option>
                                        @for($a=1; $a<=10; $a++)
                                        <option value="{{$a}}" @selected (isset($trip) && $a==$trip->trip)>{{$a}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Jam</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="time" class="form-control" id="jam" name="jam" value="{{ old('jam', $trip->jam ?? '') }}" {{ isset($trip) ? 'disabled' : '' }}>
                                </div>
                            </div>  
                            <hr>
                            @foreach($kendaraan as $kr)
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">{{$kr->kode}}</label>
                                </div>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control" id="{{$kr->id}}" name="gol[{{$kr->id}}]" value="{{ old('gol.'.$kr->id, $trip->data[$kr->id] ?? 0) }}">
                                </div>
                            </div>  
                            @endforeach
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
