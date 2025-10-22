@extends('main')
@section('scriptheader')
<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/pages/page-profile.css')}}">

<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/pages/dashboard-ecommerce.css')}}">
<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/charts/chart-apex.css')}}">
<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/extensions/ext-component-toastr.css')}}">
    
@endsection

@section('scriptfooter')

@endsection
@section('content')
<section id="dashboard-ecommerce">
                    <div class="row match-height">
                        <!-- Statistics Card -->
                        <div class="col-12">
                            <div class="card card-congratulations">
                                <div class="card-body text-center">
                                    <img src="{{ url('/vuexy/app-assets/images/elements/decore-left.png')}}" class="congratulations-img-left" alt="card-img-left" />
                                    <img src="{{ url('/vuexy/app-assets/images/elements/decore-right.png')}}" class="congratulations-img-right" alt="card-img-right" />
                                    <div class="avatar avatar-xl bg-primary shadow">
                                        <div class="avatar-content">
                                            <i data-feather="award" class="font-large-1"></i>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h1 class="mb-1 text-white">Hallo {{Session::get('name') }}</h1>
                                        <p class="card-text m-auto w-75">
                                            Selamat datang di sistem TFM (Trimas Ferries Management)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(Session::get('previllage')!=4)
                        <div class="col-12">
                            <div class="card card-statistics">
                                <div class="card-header">
                                    <h4 class="card-title">Statistics</h4>
                                    <div class="d-flex align-items-center">
                                        <p class="card-text font-small-2 me-25 mb-0">Updated Today</p>
                                    </div>
                                </div>
                                <div class="card-body statistics-body">
                                    <div class="row">
                                        @if(!empty($perusahaan))
                                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                            <div class="d-flex flex-row">
                                                <div class="avatar bg-light-primary me-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="home" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="my-auto">
                                                    <h4 class="fw-bolder mb-0">{{$perusahaan}}</h4>
                                                    <p class="card-text font-small-3 mb-0">Perusahaan</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if(!empty($kapal))
                                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                            <div class="d-flex flex-row">
                                                <div class="avatar bg-light-info me-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="anchor" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="my-auto">
                                                    <h4 class="fw-bolder mb-0">{{$kapal}}</h4>
                                                    <p class="card-text font-small-3 mb-0">Kapal</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if(!empty($karyawan))
                                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                                            <div class="d-flex flex-row">
                                                <div class="avatar bg-light-danger me-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="user" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="my-auto">
                                                    <h4 class="fw-bolder mb-0">{{$karyawan}}</h4>
                                                    <p class="card-text font-small-3 mb-0">Karyawan</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if(!empty($user))
                                        <div class="col-xl-3 col-sm-6 col-12">
                                            <div class="d-flex flex-row">
                                                <div class="avatar bg-light-success me-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="user-check" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="my-auto">
                                                    <h4 class="fw-bolder mb-0">{{$user}}</h4>
                                                    <p class="card-text font-small-3 mb-0">User Aktif</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        @if(Session::get('id_kapal')!=0)
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p>Frekuensi Akses Prosedur</p>
                                        <table id="tabledetail" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <td>Prosedur</td>
                                                <td>Lihat</td>
                                                <td>Terakhir Lihat</td>
                                                <td>Download</td>
                                                <td>Terakhir Download</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($prosedur as $show)
                                            <tr>
                                                <td>{{$show->kode}}</td>
                                                <td>{{$show->jml_lihat}}x</td>
                                                <td>@if($show->update_lihat) {{ \Carbon\Carbon::parse($show->update_lihat)->addHours(7)->format('d-m-Y H:i') }} @else - @endif</td>
                                                <td>{{$show->jml_download}}x</td>
                                                 <td>@if($show->update_download) {{ \Carbon\Carbon::parse($show->update_download)->addHours(7)->format('d-m-Y H:i') }} @else - @endif</td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        <!--/ Statistics Card -->
                    </div>

</section>
@endsection