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
<style>
    .dash-hero {
        background: linear-gradient(135deg, #0d6efd, #3a8df7);
        border-radius: 16px;
        color: #fff;
        padding: 28px;
        box-shadow: 0 10px 25px rgba(0, 60, 136, 0.2);
    }
    .stat-card {
        border: 1px solid #e6ebf1;
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: 0 12px 18px -12px rgba(13, 110, 253, 0.35);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 26px -14px rgba(13,110,253,0.55);
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(13,110,253,0.12);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
    }
    .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #1f2d3d;
    }
    .stat-label {
        margin: 0;
        color: #6c7a8a;
        font-size: 13px;
    }
    .table-minimal thead {
        background: #e9f2ff;
        color: #0d3a6e;
    }
    .table-minimal td, .table-minimal th {
        vertical-align: middle;
    }
    .text-blue {
        color: #0d6efd !important;
    }
</style>
<section id="dashboard-ecommerce">
    <div style="display:flex;justify-content:space-between;margin-bottom:1rem;padding-left:1rem;padding-right:1rem;">
        <div>
            <h2>Selamat Datang Kembali, {{ Session::get('name') }}</h2>
            <p class="mb-0 text-white-75">{{$com->nama}}</p>
        </div>

        <div class="d-flex align-items-center mt-3 mt-md-0">
            <div class="stat-icon me-2">
                <i data-feather="award"></i>
            </div>
            <div class="text-white-75 small">Akses aktif • {{ date('d M Y') }}</div>
        </div>
    </div>

    @if(Session::get('previllage')!=4)
        <div class="row g-2">
            @if(!empty($perusahaan))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center">
                    <div class="stat-icon me-2">
                        <i data-feather="home"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$perusahaan}}</h4>
                        <p class="fw-bolder">Perusahaan</p>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($kapal))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center">
                    <div class="stat-icon me-2">
                        <i data-feather="anchor"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$kapal}}</h4>
                        <p class="fw-bolder">Kapal</p>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($karyawan))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center">
                    <div class="stat-icon me-2">
                        <i data-feather="user"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$karyawan}}</h4>
                        <p class="fw-bolder">Karyawan</p>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($user))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center">
                    <div class="stat-icon me-2">
                        <i data-feather="user-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$user}}</h4>
                        <p class="fw-bolder">User Aktif</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @else
        @if(Session::get('id_kapal')!=0)
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="mb-0 text-blue">Frekuensi Akses Prosedur</h5>
                            <span class="text-muted small">Live</span>
                        </div>
                        <table id="tabledetail" class="table table-minimal table-striped">
                            <thead>
                            <tr>
                                <th>Prosedur</th>
                                <th>Lihat</th>
                                <th>Terakhir Lihat</th>
                                <th>Download</th>
                                <th>Terakhir Download</th>
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
</section>
@endsection
