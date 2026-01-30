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
                url: '/data_crew/ganti/savedata/'+id,
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
                            window.location.href = "{{ url('/data_crew/ganti') }}";
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
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-10"><h4 class="card-title">Checklist Familiarisasi</h4></div>
                    <div class="col-sm-2">
                    <a href="/data_crew/ganti/pdfover/{{$show->uid}}" type="button" target="_blank" class="btn btn-sm btn-primary download" title="Cetak PDF">
                        Unduh PDF
                    </a>
                    <!-- <a href="/checklist/item/{{$form->kode}}" class="btn btn-danger btn-sm">Setting Form</a> -->
                    </div>
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
                                        <label class="col-form-label" for="first-name">Kapal</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{$show->get_kapal()->nama}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Dari</label>
                                    </div>
                                    <div class="col-sm-10">
                                       {{$show->get_karyawan()->nama}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Kepada</label>
                                    </div>
                                    <div class="col-sm-10">
                                       {{$show->get_karyawan2()->nama}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Tanggal</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{ \Carbon\Carbon::parse($show->date)->format('d-m-Y') }}
                                    </div>
                                </div>
                                 <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">No Laporan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" name="no" class="form-control" value="{{ $dataItem['no'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Pelabuhan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="pelabuhan" class="form-control">
                                            @foreach($pelabuhan as $p)
                                                <option value="{{$p->nama}}" {{ ($keterangan['pelabuhan'] ?? '') == $p->nama ? 'selected' : '' }}>{{$p->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Jam</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="time" name="jam" class="form-control" value="{{ $show->time ?? '' }}">
                                    </div>
                                </div><hr>
                                <label class="col-form-label" for="first-name">ROB bungker saat ini:</label>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">FO</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="fo" class="form-control" value="{{ $dataItem['fo'] ?? '' }}">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">M/T</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">DO</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="do" class="form-control" value="{{ $dataItem['do'] ?? '' }}">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">M/T</label>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">FW</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="fw" class="form-control" value="{{ $dataItem['fw'] ?? '' }}">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">M/T</label>
                                    </div>
                                </div>
                                <!-- <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Catatan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <textarea name="note" id="note" class="form-control">{!!$show->note!!}</textarea>
                                    </div>
                                </div> -->
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