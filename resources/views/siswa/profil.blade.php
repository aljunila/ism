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
@endsection
@section('content')
<section class="app-user-view-account">
                    <div class="row">
                        <!-- User Sidebar -->
                        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                            <!-- User Card -->
                            <div class="card">
                                <div class="card-body"> 
                                    <div class="user-avatar-section">
                                        <div class="d-flex align-items-center flex-column">
                                            @if($show->file)
                                            <img class="img-fluid rounded mt-3 mb-2" src="{{$show->file()}}" height="110" width="110" alt="User avatar" />
                                            @else
                                            <img class="img-fluid rounded mt-3 mb-2" src="{{url('/profile/default-profile.png')}}" height="110" width="110" alt="User avatar" />
                                            @endif
                                            {{$show->file}}
                                            <div class="user-info text-center">
                                                <h4>{{$show->nama}}</h4>
                                                <!-- <span class="badge bg-light-secondary">Author</span> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-around my-2 pt-75">
                                        <div class="d-flex align-items-start me-2">
                                            <span class="badge bg-light-primary p-75 rounded">
                                            <i data-feather='award'></i>
                                            </span>
                                            <div class="ms-75">
                                                <strong><span>NIK: {{$show->nik}} </span><br>
                                                <span>NISN: {{$show->nisn}} </span></strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-container">
                                        <ul class="list-unstyled">
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Email:</span>
                                                <span>{{$show->email}}</span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Telephone:</span>
                                                <span>{{$show->telp}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="d-flex align-items-center flex-column">
                                    @if(Session::get('user_group_id')==3)
                                        @if(!empty($user))
                                        <a href="/student/resetpass/{{$user->id}}" class="btn btn-danger btn-sm me-1 align-items-center">Reset Password Akun</a>
                                        @endif
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- User Content -->
                        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                            <!-- Project table -->
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">Data Profil</h4>
                                    <a href="/siswa/edit/{{$show->id}}" class="btn btn-primary btn-sm float-right me-1">Edit Profil</a>
                                </div>
                                
                                <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                <tr>
                                                    <th>Nama Panggilan</td>
                                                    <td>:</td>
                                                    <td>{{ $show->panggilan }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Kenis Kelamin</td>
                                                    <td>:</td>
                                                    <td>{{ $show->jk }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Tempat, Tanggal Lahir</td>
                                                    <td>:</td>
                                                    <td>{{ $show->tmp_lahir }}, {{ $show->tgl_lahir }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Agama</td>
                                                    <td>:</td>
                                                    <td>{{ $show->agama }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Alamat</td>
                                                    <td>:</td>
                                                    <td>{!! $show->alamat !!}</td>
                                                </tr>
                                                <tr>
                                                    <th>Anak ke-</td>
                                                    <td>:</td>
                                                    <td>{{ $show->anak_ke }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Jumlah Saudara</td>
                                                    <td>:</td>
                                                    <td>{{ $show->jml_sodara }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                    </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h5 class="card-title">Data Ayah</h5>
                                </div>
                                <div class="table-responsive">
                                        <table class="table">
                                                <tbody>
                                                <tr>
                                                    <th>Nama Ayah</td>
                                                    <td>:</td>
                                                    <td>{{ $show->ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tahun Lahir Ayah</td>
                                                    <td>:</td>
                                                    <td>{{ $show->yhn_lahir_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pendidikan Ayah</td>
                                                    <td>:</td>
                                                    <td>{{ $show->pendidikan_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pekerjaan Ayah</td>
                                                    <td>:</td>
                                                    <td>{{ $show->pekerjaan_ayah }}
                                                    </td>
                                                </tr>                                     
                                            </tbody>
                                        </table>
                                </div>
                                <hr>
                                <div class="card-header border-bottom">
                                    <h5 class="card-title">Data Ibu</h5>
                                </div>
                                <div class="table-responsive">
                                        <table class="table">
                                                <tbody>
                                                <tr>
                                                    <th>Nama Ibu</td>
                                                    <td>:</td>
                                                    <td>{{ $show->ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tahun Lahir Ibu</td>
                                                    <td>:</td>
                                                    <td>{{ $show->thn_lahir_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pendidikan Ibu</td>
                                                    <td>:</td>
                                                    <td>{{ $show->pendidikan_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pekerjaan Ibu</td>
                                                    <td>:</td>
                                                    <td>{{ $show->pekerjaan_ibu }}
                                                    </td>
                                                </tr>                                     
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                        </div>
                        <!--/ User Content -->
                    </div>
                </section>
                <!-- upgrade your plan Modal --> 
     
@endsection