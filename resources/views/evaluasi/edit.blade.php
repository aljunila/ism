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
            let id = {{$show->checklist_id}};
            let formData = new FormData(this);

            $.ajax({
                url: '/evaluasi/update/'+id,
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
                    <a href="/checklist/parentitem/{{$form->kode}}" class="btn btn-danger btn-sm">Setting Form</a>
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
                                        <label class="col-form-label" for="first-name">Nama Kapal</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{$show->kapal}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Nama Crew</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{$show->karyawan}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Tanggal Pelatihan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{$show->date}}
                                        <!-- <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{$show->tanggal}}" required> -->
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Tanggal Penilaian</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{$show->tanggal}}" required>
                                    </div>
                                </div>
                                @if($form->kode == 'el0605')
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Naik di/paa</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="ket" name="ket" value="{{$show->ket}}" required>
                                    </div>
                                </div>
                                @endif
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Materi</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <table class="table table-bordered table-striped" border="1">
                                            <tr>
                                                <td>No</td>
                                                <td>Materi</td>
                                                <td>Tingkatan</td>
                                            </tr>
                                            @if($show->id)
                                                @foreach($item as $ck)
                                                @php
                                                    $detail = $child[$ck->checklist_item_id] ?? [];
                                                @endphp
                                                <tr>
                                                    <td></td>
                                                    <td colspan="3">{!!$ck->item!!}
                                                        <input type="hidden" class="form-control" name="item[{{$ck->checklist_item_id}}]" value="0"></td>
                                                    
                                                </tr>
                                                @foreach($detail as $c)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{!!$c->item!!}</td>
                                                    <td><select name="item[{{$c->checklist_item_id}}]" class="form-control">
                                                        <option value="">Pilih</option>
                                                        <option value="1" @selected ($c->value == 1)>1. Sangat Tidak memuaskan</option>
                                                        <option value="2" @selected ($c->value == 2)>2. Tidak Memuaskan</option>
                                                        <option value="3" @selected ($c->value == 3)>3. Cukup Memuaskan</option>
                                                        <option value="4" @selected ($c->value == 4)>4. Sangat Memuaskan</option>
                                                    </select></td>
                                                </tr>
                                                @endforeach
                                                @endforeach
                                            @else
                                                @foreach($item as $ck)
                                                @php
                                                    $detail = $child[$ck->id] ?? [];
                                                @endphp
                                                <tr>
                                                    <td></td>
                                                    <td colspan="3">{!!$ck->item!!}
                                                        <input type="hidden" class="form-control" name="item[{{$ck->id}}]" value="0"></td>
                                                    
                                                </tr>
                                                @foreach($detail as $c)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{!!$c->item!!}</td>
                                                    <td><select name="item[{{$c->id}}]" class="form-control">
                                                        <option value="">Pilih</option>
                                                        <option value="1">1. Sangat Tidak memuaskan</option>
                                                        <option value="2">2. Tidak Memuaskan</option>
                                                        <option value="3">3. Cukup Memuaskan</option>
                                                        <option value="4">4. Sangat Memuaskan</option>
                                                    </select></td>
                                                </tr>
                                                @endforeach
                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                 <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Tanggapan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <textarea name="note" id="note" class="form-control">{{$show->note}}</textarea>
                                    </div>
                                </div>
                                @if($form->kode == 'el0604')
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Instruktur</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_instruktur" id="id_instruktur"  class="form-control karyawan" required>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}" @selected ($k->id==$show->id_instruktur)>{{$k->nama}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
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