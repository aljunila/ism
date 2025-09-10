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
        $('#form_karyawan').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = $('#id').val();
            let formData = new FormData(this);

            $.ajax({
                url: "/karyawan/update/" + id,
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
                            $('#FormEdit').modal('hide');
                            window.location.reload();
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

        $('#form_ttd').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = $('#id').val();
            let formData = new FormData(this);

            $.ajax({
                url: "/karyawan/updatettd/" + id,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'Tanda tangan berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#FormTtd').modal('hide');
                            window.location.reload();
                        });
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menyimpan tanda tangan'
                    });
                }
            });
        });

         
    </script>
@endsection
@section('content')
<section class="app-user-view-account">
    <div class="row">
        <div class="col-xl-3 col-lg-5 col-md-5 order-1 order-md-0">
            <div class="card">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            <div class="user-info text-center">
                                <h4>{{$show->nama}}</h4>
                                <!-- <span class="badge bg-light-secondary">Author</span> -->
                            </div>
                        </div>
                    </div><br>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">NIK:</span>
                                <span>{{$show->nik}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Tanda tangan:</span>
                                <button class="btn btn-success btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormTtd">Upload</button>
                            </li>
                            <li class="mb-75">
                                    <img class="img-fluid mt-3 mb-2" src="{{$show->tanda_tangan_url}}" height="125" width="125" alt="User avatar" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /User Card -->
        </div>
        <div class="col-xl-9 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- User Card -->
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true"><i data-feather="home"></i>Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="akses-tab" data-bs-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false"><i data-feather="tool"></i>Akses</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                            <div class="card-header border-bottom">
                                <h5 class="fw-bolder">Informasi Pribadi</h5>
                                <button class="btn btn-primary btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormEdit">Edit Data</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table" width="100%">
                                    <tbody>
                                        <tr>
                                            <td> Perusahaan</td>
                                            <td>:</td>
                                            <td>{!! ($show->perusahaan) ? $show->perusahaan : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Kapal</td>
                                            <td>:</td>
                                            <td>{!! ($show->kapal) ? $show->kapal : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Jabatan</td>
                                            <td>:</td>
                                            <td>{!! ($show->jabatan) ? $show->jabatan : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Username</td>
                                            <td>:</td>
                                            <td>{!! ($show->username) ? $show->username : '-' !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="profile" aria-labelledby="akses-tab" role="tabpanel">
                            <div class="card-header border-bottom">
                                <h5 class="fw-bolder">Informasi Akses</h5>
                                <button class="btn btn-warning btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormPassword">Ubah Password</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table" width="100%">
                                    <tbody>
                                        <tr>
                                            <th width="30%"> Level Akses</th>
                                            <td width="5%">:</td>
                                            <td width="65%">{!! ($show->previllage) ? $show->previllage : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <th> Level Menu</th>
                                            <td>:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                            <table width="50%">
                                                @foreach($menu as $m)
                                                <tr>
                                                    @if($m->get_menu()->id_parent==0)
                                                        <td colspan="2">{!!$m->get_menu()->icon!!} {{$m->get_menu()->nama}}</td>
                                                    @else
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td>{!!$m->get_menu()->icon!!}   {{$m->get_menu()->nama}}</td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormEdit" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_karyawan" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Nama Lengkap</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nama" name="nama" required value="{{$show->nama}}">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">NIK</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nik" name="nik" required value="{{$show->nik}}">
                            </div>
                        </div>
                        <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Jabatan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_jabatan" id="id_jabatan"  class="form-control" required>
                                    @foreach($jabatan as $j)
                                        @if($show->id_jabatan==$j->id)
                                            <option value="{{$j->id}}" selected>{{$j->nama}}</option>
                                        @else
                                            <option value="{{$j->id}}">{{$j->nama}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Perusahaan</label>
                            </div>
                            <div class="col-sm-9">
                                    <select name="id_perusahaan" id="id_perusahaan"  class="form-control" required>
                                        @foreach($perusahaan as $ph)
                                            @if($show->id_perusahaan==$ph->id)
                                                    <option value="{{$ph->id}}" selected>{{$ph->nama}}</option>
                                                @else
                                                    <option value="{{$ph->id}}">{{$ph->nama}}</option>
                                                @endif
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Ditempatkan di kapal</label>
                            </div>
                            <div class="col-sm-9">
                                    <select name="id_kapal" id="id_kapal"  class="form-control" required>
                                        <option value="">Tidak</option>
                                        @foreach($kapal as $k)
                                            @if($show->id_kapal==$k->id)
                                                <option value="{{$k->id}}" selected>{{$k->nama}}</option>
                                            @else
                                                <option value="{{$k->id}}">{{$k->nama}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <!-- <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Level Akses</label>
                            </div>
                            <div class="col-sm-9">
                                    <select name="id_previllage" id="id_previllage"  class="form-control" required>
                                        @foreach($previllage as $p)
                                            @if($show->id_previllage==$p->id)
                                                    <option value="{{$p->id}}" selected>{{$p->nama}}</option>
                                                @else
                                                    <option value="{{$p->id}}">{{$p->nama}}</option>
                                                @endif
                                        @endforeach
                                    </select>
                            </div>
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" id="id" value="{{$show->id}}">
                        <button type="submit" class="btn btn-primary" id="edit_data">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormTtd" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Upload Tanda Tangan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_ttd" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Upload Image</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" id="tanda_tangan" name="tanda_tangan" required value="{{$show->nama}}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{$show->id}}">
                        <button type="submit" class="btn btn-primary" id="upload_ttd">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection