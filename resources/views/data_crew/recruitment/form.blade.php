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
                url: '/data_crew/recruitment/savedata/'+id,
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
                            window.location.href = "{{ url('/data_crew/recruitment') }}";
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
                    <div class="col-sm-10"><h4 class="card-title">Hasil Interview</h4></div>
                    <div class="col-sm-2">
                    <a href="/data_crew/recruitment/pdf/{{$show->uid}}" type="button" target="_blank" class="btn btn-sm btn-primary download" title="Cetak PDF">
                        Unduh PDF
                    </a>
                    <a href="/checklist/item/{{$form->kode}}" class="btn btn-danger btn-sm">Setting Form</a>
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
                                        <label class="col-form-label" for="first-name">Perusahaan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{$show->get_perusahaan()->nama}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Nama</label>
                                    </div>
                                    <div class="col-sm-10">
                                       {{$show->nama}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Jabatan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{$show->get_jabatan()->nama}}
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Tgl Periksa</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="tgl_periksa" name="tgl_periksa" value="{{$show->tgl_periksa}}"required>
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
                                                <td>Uraian</td>
                                                <td>Ya</td>
                                                <td>Tidak</td>
                                                <td>Keterangan</td>
                                            </tr>
                                            @foreach($item as $ck)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{!!$ck->item!!}</td>
                                                <td><input type="radio" class="form-check-input" name="item[{{$ck->id}}]" value="1" {{ ($dataItem[$ck->id]['value'] ?? null) == 1 ? 'checked' : '' }}></td>
                                                <td><input type="radio" class="form-check-input" name="item[{{$ck->id}}]" value="0" {{ ($dataItem[$ck->id]['value'] ?? null) == 0 ? 'checked' : '' }}></td>
                                                <td><input type="text" class="form-control" name="ket[{{$ck->id}}]" value="{{ $dataItem[$ck->id]['ket'] ?? '' }}"></td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Catatan</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <textarea name="note" id="note" class="form-control">{!!$show->note!!}</textarea>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Yang Menyetujui</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="id_menyetujui" id="id_menyetujui"  class="form-control karyawan" required>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}" @selected($k->id==$show->id_menyetujui)>{{$k->nama}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Hasil</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="status" id="status"  class="form-control karyawan" required>
                                            <option value="A" @selected($show->status=='A')>Diterima</option>
                                            <option value="D" @selected($show->status=='D')>Ditolak</option>
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