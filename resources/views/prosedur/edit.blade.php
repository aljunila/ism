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
        $('#form_prosedur').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = {{$show->id}};
            let formData = new FormData(this);

            $.ajax({
                url: '/prosedur/update/'+id,
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
                            window.location.href = "{{ url('/prosedur') }}";
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
                    <form id="form_prosedur" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            @if(Session::get('previllage')==1)
                             <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Perusahaan</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="id_perusahaan" id="id_perusahaan"  class="form-control" required>
                                        <option value="">Pilih</option>
                                    @foreach($perusahaan as $ph)
                                        <option value="{{$ph->id}}" @selected ($ph->id == $show->id_perusahaan)>{{$ph->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="id_perusahaan" value="{{Session::get('id_perusahaan')}}">
                            @endif
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Kode Prosedur</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="kode" name="kode" value="{{$show->kode}}" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Judul</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="judul" name="judul" value="{{$show->judul}}" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">No Dokumen</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="no_dokumen" name="no_dokumen" value="{{$show->no_dokumen}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Edisi</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="edisi" name="edisi" value="{{$show->edisi}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Tanggal Terbit</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="tgl_terbit" name="tgl_terbit" required value="{{$show->tgl_terbit}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Status Manual</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="status_manual" name="status_manual" value="{{$show->status_manual}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Disiapkan Oleh</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="prepered_by" id="prepered_by"  class="form-control" required>
                                    @foreach($karyawan as $k)
                                        @if($k->id==$show->prepered_by)
                                            <option value="{{$k->id}}" selected>{{$k->nama}}</option>
                                        @else
                                            <option value="{{$k->id}}">{{$k->nama}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Diberlakukan Oleh</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="enforced_by" id="enforced_by"  class="form-control" required>
                                    @foreach($karyawan as $ky)
                                        @if($ky->id==$show->enforced_by)
                                            <option value="{{$ky->id}}" selected>{{$ky->nama}}</option>
                                        @else
                                            <option value="{{$ky->id}}">{{$ky->nama}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-1 row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="first-name">Pembuatan Prosedur</label>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Cover (Hal Judul)</label>
                                </div>
                                <div class="col-sm-10">
                                    <textarea class="form-control tinymce" id="cover" name="cover">{!! $show->cover !!}</textarea>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-2">
                                    <label class="col-form-label" for="first-name">Isi</label>
                                </div>
                                <div class="col-sm-10">
                                    <textarea class="form-control tinymce" id="isi" name="isi">{!! $show->isi !!}</textarea>
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