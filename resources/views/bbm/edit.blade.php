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
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Page CSS-->
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <!-- END: Page Vendor JS-->
    <!-- BEGIN: Page JS-->
    <script src="{{ url('/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <!-- END: Page JS-->

    <script>
        $('#form_checklist').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = {{$show->id}};
            let formData = new FormData(this);

            $.ajax({
                url: '/bbm/update/'+id,
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
                            window.location.href = "/{{$form->kode}}";
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

                $.ajax({
                    url: '/get-karyawanbyCom/' + perusahaanID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('.karyawan').empty().append('<option value="">Semua</option>');           
                        $.each(data, function(key, value) {
                            $('.karyawan').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                        });
                        table.ajax.reload();
                    }
                });
            } else {
                $('#id_kapal').empty().append('<option value="">Tidak ada data</option>');
                $('.karyawan').empty().append('<option value="">Tidak ada data</option>');
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
                    <h4 class="card-title">Input Form: {{$form->nama}}</h4>
                    <a href="/checklist/item/{{$form->kode}}" class="btn btn-danger btn-sm">Setting Form</a>
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
                    <form id="form_checklist" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Intruksi</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {!!$form->intruksi !!}
                                    </div>
                                </div>
                                 <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Perusahaan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_perusahaan" id="id_perusahaan" class="form-control" required>
                                        @foreach($perusahaan as $p)
                                            <option value="{{$p->id}}">{{$p->nama}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Nama Kapal</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_kapal" id="id_kapal"  class="form-control" required>
                                        @foreach($kapal as $kp)
                                            <option value="{{$kp->id}}" @selected ($kp->id == $show->id_kapal)>{{$kp->nama}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Nama Karyawan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_karyawan" id="id_karyawan"  class="form-control karyawan" required>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}" @selected ($k->id == $show->id_karyawan)>{{$k->nama}}</option>
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
                                        <label class="col-form-label" for="first-name">Tanggal</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{$show->tanggal}}" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Waktu</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="waktu" name="waktu" value="{{$show->waktu}}" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">No Pelayaran</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="no_pelayaran" name="no_pelayaran" value="{{$show->no_pelayaran}}" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Pelabuhan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="pelabuhan" name="pelabuhan" value="{{$show->pelabuhan}}" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Fuel Oil (FO) </label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="fo" name="fo" value="{{$show->fo}}" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Marine Diesel Oil (MDO) </label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="mdo" name="mdo" value="{{$show->mdo}}" required>
                                    </div>
                                </div>
                                
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Materi</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <table class="table table-bordered table-striped" border="1">
                                            <tr>
                                                <td>No</td>
                                                <td>Materi</td>
                                                <td>Ya</td>
                                                <td>Tidak</td>
                                            </tr>
                                            @foreach($item as $ck)
                                            @php
                                                $detail = $child[$ck->checklist_item_id] ?? [];
                                            @endphp
                                            <tr>
                                                <td></td>
                                                <td colspan="3">{!!$ck->item!!} <input type="hidden" class="form-control" name="item[{{$ck->checklist_item_id}}]" value="0"></td>
                                            </tr>
                                             @foreach($detail as $c)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{!!$c->item!!}</td>
                                                <td><input type="radio" class="form-check-input" name="item[{{$c->checklist_item_id}}]" value="1" @checked ($c->value == 1)></td>
                                                <td><input type="radio" class="form-check-input" name="item[{{$c->checklist_item_id}}]" value="0" @checked ($c->value == 0)></td>
                                            </tr>
                                            @endforeach
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Mengetahui</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_nahkoda" id="id_nahkoda"  class="form-control karyawan" required>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}" @selected ($k->id==$show->id_nahkoda)>{{$k->nama}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Diperiksa Oleh</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_kkm" id="id_kkm" class="form-control karyawan" required>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}" @selected ($k->id==$show->id_kkm)>{{$k->nama}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Dilaksanakan Oleh</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_jaga" id="id_jaga"  class="form-control karyawan" required>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}" @selected ($k->id==$show->id_jaga)>{{$k->nama}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <div class="col-sm-12 offset-sm-3">
                            <input type="hidden" name="kode" value="{{$form->kode}}">
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