@extends('main')
@section('scriptheader')
  
   <!-- BEGIN: Vendor CSS-->
   <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/animate/animate.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <!-- END: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/form-validation.css')}}">
    
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/moment.min.js')}}"></script> -->
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/polyfill.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/modal-edit-user.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/app-user-view-account.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/app-user-view.js')}}"></script>

    <script>
        $(document).on('click', '.upload-btn', function(){
            let id = $(this).attr('data-id');
            let file = $(this).attr('data-file');
            let id_perusahaan = {{$show->id}};
            $('#id_file').val(id);
            $('#id_perusahaan').val(id_perusahaan);
            $('#file').html(file);
            $('#FormUpload').modal('show');
        });

        $('#form_file').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = $('#id_file').val();
            let formData = new FormData(this);

            $.ajax({
                url: "/perusahaan/savefile/" + id,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'File berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#FormUpload').modal('hide');
                            window.location.reload();
                        });
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menyimpan file'
                    });
                }
            });
        });
    </script>
@endsection
@section('content')
<section class="app-user-view-account">
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-3 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            <img class="img-fluid mt-3 mb-2" src="{{$show->logo_url}}" height="125" width="125" alt="User avatar" />
                            <div class="user-info text-center">
                                <h4>{{$show->nama}}</h4>
                                <span class="badge bg-light-secondary">{{$show->kode}}</span><br><br>
                                <a href="/perusahaan/pdf/{{$show->uid}}" type ="button" target="_blank" class="btn btn-warning btn-sm me-1" id="pdf-btn"><i data-feather='download-cloud'></i>  Unduh Data</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                            
            <!-- /User Card -->
        </div>
        <!--/ User Sidebar -->

        <!-- User Content -->
        <div class="col-xl-9 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- Project table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="fw-bolder">Informasi Umum</h5>
                    <a href="/perusahaan/edit/{{$show->uid}}" class="btn btn-primary btn-sm float-right me-1">Edit Data</a>
                </div>
                <div class="table-responsive">
                    <table class="table" width="100%">
                        <tbody>
                            <tr>
                                <td> NPWP</td>
                                <td>:</td>
                                <td>{{ $show->npwp }}</td>
                            </tr>
                            <tr>
                                <td> NIB</td>
                                <td>:</td>
                                <td>{{ $show->nib }}</td>
                            </tr>
                            <tr>
                                <td> No Telp</td>
                                <td>:</td>
                                <td>{{ $show->telp }}</td>
                            </tr>
                            <tr>
                                <td> Email</td>
                                <td>:</td>
                                <td>{{ $show->email }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>:</td>
                                <td>{!! $show->alamat !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bolder border-bottom pb-50 mb-1">Berkas Penunjang</h5>
                        <div class="table-responsive">
                            <table class="table" width="100%">
                                @foreach($file as $f)
                                <tr>
                                    <td width="70%">{{$f->nama}}</td>
                                    <td width="5%">:</td>
                                    <td width="25%">
                                            <button type="button" class="btn btn-icon rounded-circle btn-xs btn-flat-warning upload-btn" 
                                                title="Upload File" data-id="{{$f->id}}" data-file="{{$f->nama}}">
                                                <i data-feather='upload'></i>
                                            </button>
                                        @if(!empty($f->file))
                                            <a type="button" href="{{ asset('file_upload/'.$f->file) }}" target="_blank" class="btn btn-icon rounded-circle btn-xs btn-flat-success" 
                                                title="Buka File" data-id="{{$f->id}}" data-file="{{$f->nama}}">
                                                <i data-feather='file'></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                </div>
            </div>
            <!-- /Project table -->
        </div>

        <!--/ User Content -->
    </div>

    <div class="modal fade text-start" id="FormUpload" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <form id="form_file" enctype="multipart/form-data">
                    @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="file">Edit Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <label>Format file: PDF</label>
                        <div class="mb-1">
                            <input type="file" class="form-control" name="file" id="file"/>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id_file" id="id_file">
                    <input type="hidden" name="id_perusahaan" id="id_perusahaan">
                    <button type="submit" class="btn btn-primary" id="save_file">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</section>
@endsection