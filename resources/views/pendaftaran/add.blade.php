@extends('main')
@section('scriptheader')

<!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/themes/semi-dark-layout.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/assets/css/style.css')}}">
    <!-- END: Custom CSS-->
 
<!-- END: Page CSS-->
<script src="https://cdn.tiny.cloud/1/kk3dzyiek4uhy82bodtbqgh5f26brsw2xxin668j9rs34va1/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
@endsection

@section('scriptfooter')
    <!-- BEGIN: Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ url('/vuexy/app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/core/app.js')}}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ url('/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <!-- END: Page JS-->

    <script>
    tinymce.init({
      selector: 'textarea',
      plugins: '',
      toolbar: 'a11ycheck addcomment showcomments casechange checklist code export formatpainter pageembed permanentpen table',
      toolbar_mode: 'floating',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
    });
  </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
                    <div class="row">
                        <div class="col-md-10 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Pendaftaran</h4>
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
									<form action="{{ url('pendaftaran/create') }}" method="POST" enctype="multipart/form-data">
   						 			@csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Nama Pendaftaran</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="name" name="name" required>
                                                    </div>
                                                </div>
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Periode</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <select class="form-control" id="periode_id" name="periode_id" required>
                                                            <option value="">Pilih</option>
                                                            @foreach ($periode as $p)
                                                                <option value="{{$p->id}}">{{$p->nama}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Sertai Biaya</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" class="form-check-input" id="fee" name="fee" value="Y" required>
                                                            <label class="form-check-label" for="inlineRadio1">Ya</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" class="form-check-input" id="fee" name="fee" value="N" required>
                                                            <label class="form-check-label" for="inlineRadio1">Tidak</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Tanggal Mulai</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                    <input type="date" name="start_date" id="start_date" class="form-control flatpickr-basic" placeholder="YYYY-MM-DD" required/>
                                                    </div>
                                                </div>
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Tanggal Berakhir</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="date" class="form-control flatpickr-basic" placeholder="YYYY-MM-DD" name="end_date" required/>
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