@extends('main')
@section('scriptheader')
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
     <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
    <!-- END: Page CSS-->
    <style>
        .trip-form-wrap {
            max-width: 1080px;
            margin: 0 auto;
        }

        .trip-main-fields .form-group-row {
            align-items: center;
            margin-bottom: .85rem;
        }

        .trip-main-fields .field-label {
            font-weight: 600;
            color: #5e5873;
        }

        .trip-divider {
            margin: 1rem 0 .9rem;
        }

        .trip-kendaraan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: .7rem .9rem;
        }

        .trip-kendaraan-item {
            padding: .7rem .8rem;
            border: 1px solid #ebe9f1;
            border-radius: .5rem;
            background: #fbfbfd;
        }

        .trip-kendaraan-item label {
            font-weight: 600;
            margin-bottom: .5rem;
            color: #5e5873;
        }

        .trip-number-group .btn {
            min-width: 36px;
            padding: .38rem .5rem;
        }
    </style>
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
            })
            .done(res => {
                Swal.fire(res.status, res.message, res.status)
                    .then(() => {
                        if (res.status === 'success') {
                            window.location.href = "{{ url('/data_kapal/trip') }}";
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

        let getPelabuhanUrl = "{{ route('getPelabuhan', ':id') }}";

        $(document).on('change', '#id_kapal', function() {
            var kapalID = $(this).val();
            if (kapalID) {
                $.ajax({
                    url: getPelabuhanUrl.replace(':id', kapalID),
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

        $(document).on('click', '.btn-number-step', function() {
            const targetId = $(this).data('target');
            const step = Number($(this).data('step')) || 1;
            const input = document.getElementById(targetId);
            if (!input) return;

            const current = Number(input.value) || 0;
            const nextValue = Math.max(0, current + step);
            input.value = nextValue;
            $(input).trigger('change');
        });
    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row trip-form-wrap">
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
                    <form id="form_karyawan"
                    data-store-url="{{ route('trip.store') }}"
                    data-update-url="{{ isset($trip) ? route('trip.update', $trip->id) : '' }}">
                    @csrf
                    <div class="trip-main-fields">
                            <div class="form-group-row row">
                                <div class="col-sm-3 col-md-2">
                                    <label class="col-form-label field-label">Kapal</label>
                                </div>
                                <div class="col-sm-9 col-md-10">
                                    <select name="id_kapal" id="id_kapal" required class="form-control"  {{ isset($trip) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kapal</option>
                                    @foreach($kapal as $kp)
                                        <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group-row row">
                                <div class="col-sm-3 col-md-2">
                                    <label class="col-form-label field-label">Tanggal</label>
                                </div>
                                <div class="col-sm-9 col-md-10">
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required value="{{ old('tanggal', $trip->tanggal ?? '') }}">
                                </div>
                            </div>
                            <div class="form-group-row row">
                                <div class="col-sm-3 col-md-2">
                                    <label class="col-form-label field-label">Pelabuhan</label>
                                </div>
                                <div class="col-sm-9 col-md-10">
                                    <select name="id_pelabuhan" id="id_pelabuhan" required class="form-control"  {{ isset($trip) ? 'disabled' : '' }}>
                                        <option value="">Pilih Pelabuhan</option>
                                            @foreach($pelabuhan as $p)
                                                <option value="{{$p->id}}"  @selected (isset($trip) && $p->id==$trip->id_pelabuhan)>{{$p->nama}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group-row row">
                                <div class="col-sm-3 col-md-2">
                                    <label class="col-form-label field-label">Trip ke</label>
                                </div>
                                <div class="col-sm-9 col-md-10">
                                    <select name="trip" id="trip"  class="form-control">
                                        <option value="">Pilih</option>
                                        @for($a=1; $a<=10; $a++)
                                        <option value="{{$a}}" @selected (isset($trip) && $a==$trip->trip)>{{$a}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-group-row row">
                                <div class="col-sm-3 col-md-2">
                                    <label class="col-form-label field-label">Jam</label>
                                </div>
                                <div class="col-sm-9 col-md-10">
                                    <input type="time" class="form-control" id="jam" name="jam" value="{{ old('jam', $trip->jam ?? '') }}" {{ isset($trip) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <hr class="trip-divider">

                            <div class="trip-kendaraan-grid">
                            @foreach($kendaraan as $kr)
                            <div class="trip-kendaraan-item">
                                <label class="col-form-label">{{$kr->kode}}</label>
                                <div class="input-group trip-number-group">
                                    <button type="button" class="btn btn-outline-secondary btn-number-step" data-target="{{$kr->id}}" data-step="-1">-</button>
                                    <input type="number" min="0" class="form-control text-center" id="{{$kr->id}}" name="gol[{{$kr->id}}]" value="{{ $gol[$kr->id]['jumlah'] ?? '' }}">
                                    <button type="button" class="btn btn-outline-primary btn-number-step" data-target="{{$kr->id}}" data-step="1">+</button>
                                </div>
                            </div>
                            @endforeach
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-1" id="simpan_data">Simpan</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
