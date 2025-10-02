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
                            <div class="card card-statistics">
                                <div class="card-header">
                                    <h4 class="card-title">Statistics</h4>
                                    <div class="d-flex align-items-center">
                                        <p class="card-text font-small-2 me-25 mb-0">Updated Today</p>
                                    </div>
                                </div>
                                <div class="card-body statistics-body">
                                    <div class="row">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Statistics Card -->
                    </div>

</section>
@endsection