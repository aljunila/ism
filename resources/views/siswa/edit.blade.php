@extends('main')
@section('scriptheader')
<!-- BEGIN: Vendor CSS-->
	<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/editors/quill/katex.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/editors/quill/monokai-sublime.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/editors/quill/quill.snow.css')}}">
<!-- END: Vendor CSS-->

<!-- BEGIN: Page CSS-->
	<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/form-quill-editor.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/pages/page-blog.css')}}">
    
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<!-- END: Page CSS-->
<script src="https://cdn.tiny.cloud/1/kk3dzyiek4uhy82bodtbqgh5f26brsw2xxin668j9rs34va1/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
@endsection

@section('scriptfooter')
 <!-- BEGIN: Page Vendor JS-->
 <script src="{{ url('/vuexy/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/editors/quill/katex.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/editors/quill/highlight.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
    <!-- END: Page Vendor JS-->
<script src="{{ url('/vuexy/app-assets/js/scripts/pages/page-blog-edit.js')}}"></script>
<!-- BEGIN: Page Vendor JS-->
<script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <!-- END: Page Vendor JS-->
<script src="{{ url('/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/page-account-settings-account.js')}}"></script>
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
  <script>
        $(document).ready(function() {
            $('#province').on('change', function() {
               var provinceID = $(this).val();
               if(provinceID) {
                   $.ajax({
                       url: '/getRegency/'+provinceID,
                       type: "GET",
                       data : {"_token":"{{ csrf_token() }}"},
                       dataType: "json",
                       success:function(data)
                       {
                         if(data){
                            $('#regency').empty();
                            $('#regency').append('<option hidden>Pilih</option>'); 
                            $.each(data, function(key, regency){
                                $('select[name="regency"]').append('<option value="'+ regency.id +'">' + regency.name+ '</option>');
                            });
                        }else{
                            $('#regency').empty();
                        }
                     }
                   });
               }else{
                 $('#regency').empty();
               }
            });

            $('#regency').on('change', function() {
               var regencyID = $(this).val();
            //    alert(regencyID);
               if(regencyID) {
                   $.ajax({
                       url: '/getDistrict/'+regencyID,
                       type: "GET",
                       data : {"_token":"{{ csrf_token() }}"},
                       dataType: "json",
                       success:function(data)
                       {
                         if(data){
                            $('#district').empty();
                            $('#district').append('<option hidden>Pilih</option>'); 
                            $.each(data, function(key, district){
                                $('select[name="district"]').append('<option value="'+ district.id +'">' + district.name+ '</option>');
                            });
                        }else{
                            $('#district').empty();
                        }
                     }
                   });
               }else{
                 $('#district').empty();
               }
            });

            $('#district').on('change', function() {
               var districtID = $(this).val();
            //    alert(districtID);
               if(districtID) {
                   $.ajax({
                       url: '/getVillage/'+districtID,
                       type: "GET",
                       data : {"_token":"{{ csrf_token() }}"},
                       dataType: "json",
                       success:function(data)
                       {
                         if(data){
                            $('#village').empty();
                            $('#village').append('<option hidden>Pilih</option>'); 
                            $.each(data, function(key, village){
                                $('select[name="village"]').append('<option value="'+ village.id +'">' + village.name+ '</option>');
                            });
                        }else{
                            $('#village').empty();
                        }
                     }
                   });
               }else{
                 $('#village').empty();
               }
            });
        });
        </script>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <!-- profile -->
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Data Lengkap Siswa</h4>
            </div>
            @if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif
            <div class="card-body py-2 my-25">
                <form action="{{ url('siswa/update', $show->id) }}" method="POST" enctype="multipart/form-data">
   						 @csrf
                            
                    <div class="row">
                        <div class="col-12 col-sm-12 mb-1">
                            <label for="timeZones" class="form-label">Pas Foto&nbsp;&nbsp;&nbsp;&nbsp;</label><small class="text-muted">Format (JPG, JPEG, PNG) Size (1000Kb)</small>
                            <input type="file" class="form-control" id="file" name="file" placeholder="Foto">
                        </div>
                        @if($show->st_pelajar=='C')
                        <div class="col-12 col-sm-6 mb-1">
                            <label for="timeZones" class="form-label">Gelombang Pendaftaran</label>
                            <select id="daftar_id" name="daftar_id" data-placeholder="Select" class="form-control">
                                <option value="">-- Pilih -- </option>
                                    @foreach ($psb as $psb)
                                        <option value="{{$psb->id}}" @if($psb->id==$show->id_daftar) selected @endif>{{$psb->nama}}</option>
                                    @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mb-1"></div>
                        @endif
                        
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountFirstName">Nama Lengkap</label>
                            <input type="text" class="form-control" id="fullname" name="nama" data-msg="Nama Lengkap" required value="{{$show->nama}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Nama Panggilan</label>
                            <input type="text" class="form-control" id="nickname" name="panggilan" value="{{$show->panggilan}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">NIK</label>
                            <input type="number" class="form-control" id="nik" name="nik" required value="{{$show->nik}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">NISN</label>
                            <input type="number" class="form-control" id="nisn" name="nisn" value="{{$show->nisn}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Tempat Lahir</label>
                            <input type="text" class="form-control" id="pob" name="tmp_lahir" required value="{{$show->tmp_lahir}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Tanggal Lahir</label>
                            <input type="date" class="form-control" placeholder="YYYY-MM-DD" id="fp-default" name="tgl_lahir" required value="{{$show->tgl_lahir}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Agama</label>
                            <input type="text" class="form-control" id="religion" name="religion"value="{{$show->agama}}" />
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{$show->email}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountPhoneNumber">No Telp/Handphone</label>
                            <input type="text" class="form-control" id="telp" name="telp" required value="{{$show->telp}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Jenis Kelamin</label>
                            <select name="jk" id="gender" class="form-control">
                                <option value="">Pilih</option>
                                <option value="L" @if($show->jk=='L') selected @endif)>Laki-laki</option>
                                <option value="P" @if($show->jk=='P') selected @endif>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 mb-1">
                            <label class="form-label" for="accountState">Alamat</label>
                            <textarea name="alamat" class="form-control">{{$show->alamat}}</textarea>
                        </div>
						<div class="col-12 col-sm-6 mb-1">
                            <label for="timeZones" class="form-label">Anak Ke</label>
                            <input type="number" class="form-control" id="child_no" name="anak_ke" value="{{$show->anak_ke}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label for="timeZones" class="form-label">Jumlah Saudara</label>
                            <input type="number" class="form-control" id="number_of_siblings" name="jml_sodara" value="{{$show->jml_sodara}}"/>
                        </div>
                        <hr>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Nama Ayah</label>
                            <input type="text" class="form-control" id="father_name" name="ayah" value="{{$show->ayah}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Nama Ibu</label>
                            <input type="text" class="form-control" id="mother_name" name="ibu" value="{{$show->ibu}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Tahun Lahir Ayah</label>
                            <input type="text" class="form-control" id="thn_lahir_ayah" name="thn_lahir_ayah" value="{{$show->thn_lahir_ayah}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Tahun Lahir Ibu</label>
                            <input type="text" class="form-control" id="thn_lahir_ibu" name="thn_lahir_ibu" value="{{$show->thn_lahir_ibu}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Pendidikan Ayah</label>
                            <input type="text" class="form-control" id="pendidikan_ayah" name="pendidikan_ayah" value="{{$show->pendidikan_ayah}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Pendidikan Ibu</label>
                            <input type="text" class="form-control" id="pendidikan_ibu" name="pendidikan_ibu" value="{{$show->pendidikan_ibu}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Pekaerjaan Ayah</label>
                            <input type="text" class="form-control" id="pekerjaan_ayah" name="pekerjaan_ayah" value="{{$show->pekerjaan_ayah}}"/>
                        </div>
                        <div class="col-12 col-sm-6 mb-1">
                            <label class="form-label" for="accountState">Pekerjaan Ibu</label>
                            <input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu" value="{{$show->pekerjaan_ibu}}" />
                        </div>
                        <div class="col-12">
                        <button type="submit" class="btn btn-primary mt-1 me-1">Simpan Data</button>
                            <a href="/student" class="btn btn-outline-secondary mt-1">Kembali</a>
                        </div>
                    </div>
                </form>
                <!--/ form -->
            </div>
        </div>
        <!--/ profile -->
    </div>
</div>
@endsection
