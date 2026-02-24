<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TFM - Trimas Ferries Management</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{url('/img/trimas.png')}}">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet"> -->

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/vendors.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/themes/bordered-layout.css')}}">
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/style.css')}}">
    <!-- END: Custom CSS-->
    <style>
        .floating-gear-btn {
            position: fixed;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 52px;
            height: 52px;
            border: 0;
            border-radius: 99px 0px 0px 99px;
            background: #0d6efd;
            color: #fff;
            z-index: 1205;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 18px rgba(115, 103, 240, 0.35);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .floating-gear-btn:hover {
            transform: translateY(-50%) scale(1.04);
            box-shadow: 0 10px 22px rgba(115, 103, 240, 0.45);
        }

        .floating-gear-btn i {
            width: 22px;
            height: 22px;
        }

        .floating-gear-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(34, 41, 47, 0.35);
            z-index: 1206;
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
        }

        .floating-gear-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: 340px;
            max-width: 90vw;
            height: 100vh;
            background: #fff;
            z-index: 1207;
            transform: translateX(100%);
            transition: transform .25s ease;
            box-shadow: -10px 0 28px rgba(34, 41, 47, 0.2);
            display: flex;
            flex-direction: column;
        }

        .floating-gear-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #ebe9f1;
        }

        .floating-gear-drawer-body {
            padding: 1rem 1.25rem;
            overflow-y: auto;
        }

        .layout-picker {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .layout-card {
            position: relative;
            display: block;
            margin: 0;
            cursor: pointer;
        }

        .layout-card-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .layout-card-ui {
            height: 110px;
            border: 1px solid #ebe9f1;
            border-radius: 0.75rem;
            background: #f8f8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .layout-card-ui i {
            width: 36px;
            height: 36px;
            color: #6e6b7b;
        }

        .layout-card:hover .layout-card-ui {
            border-color: #c7c4d1;
        }

        .layout-card-input:checked + .layout-card-ui {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.08);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
        }

        .layout-card-input:checked + .layout-card-ui i {
            color: #0d6efd;
        }

        .floating-gear-drawer-close {
            border: 0;
            background: transparent;
            color: #6e6b7b;
            font-size: 1.5rem;
            line-height: 1;
            padding: 0;
        }

        body.gear-drawer-open .floating-gear-backdrop {
            opacity: 1;
            visibility: visible;
        }

        body.gear-drawer-open .floating-gear-drawer {
            transform: translateX(0);
        }

        /* --- Section title --- */
        .layout-fieldset{
            border: 0;
            padding: 0;
            margin: 0;
            min-inline-size: 0;
        }
        .layout-legend{
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: .75rem;
            opacity: .8;
            margin-bottom: .5rem;
        }

        /* --- Layout cards --- */
        .layout-picker{
            display: grid;
            grid-template-columns: 1fr;
            gap: .6rem;
        }

        .layout-option{
            display: grid;
            grid-template-columns: 44px 1fr;
            gap: .75rem;
            align-items: center;

            padding: .75rem .85rem;
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,.10);
            background: rgba(255,255,255,.6);

            cursor: pointer;
            user-select: none;
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
        }

        .layout-option:hover{
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(0,0,0,.08);
            border-color: rgba(0,0,0,.16);
        }

        /* icon box */
        .layout-icon{
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(0,0,0,.08);
            background: rgba(0,0,0,.03);
        }

        /* text */
        .layout-title{
            display: block;
            font-weight: 700;
            line-height: 1.1;
        }
        .layout-desc{
            display: block;
            font-size: .85rem;
            opacity: .75;
            margin-top: .2rem;
        }

        /* --- Radio input accessible but hidden --- */
        .layout-input{
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            clip-path: inset(50%);
            white-space: nowrap;
        }

        /* --- Selected state (checked) --- */
        .layout-input:checked + .layout-icon{
            border-color: #0d6efd;
            background: rgba(13,110,253,.14);
            box-shadow: inset 0 0 0 1px rgba(13,110,253,.35);
        }
        .layout-input:checked ~ .layout-text .layout-title::after{
            content: " Aktif";
            display: inline-flex;
            align-items: center;
            margin-left: .4rem;
            padding: .12rem .38rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .68rem;
            letter-spacing: .02em;
            color: #0d6efd;
            background: rgba(13,110,253,.12);
            opacity: 1;
        }

        /* Card highlight when selected */
        .layout-input:checked{
        /* no-op, but kept for clarity */
        }
        .layout-option:has(.layout-input:checked){
            border-color: rgba(13,110,253,.38);
            background: linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(240,247,255,.95) 100%);
            /* box-shadow: 0 12px 24px rgba(13,110,253,.16); */
            transform: translateY(-1px);
        }

        /* --- Keyboard focus --- */
        .layout-input:focus-visible + .layout-icon{
            outline: 3px solid rgba(59,130,246,.45);
            outline-offset: 2px;
        }
        .layout-option:has(.layout-input:focus-visible){
            border-color: rgba(59,130,246,.45);
        }

        /* --- Optional: responsive for wider drawer --- */
        @media (min-width: 420px){
            .layout-picker{
                grid-template-columns: 1fr 1fr;
            }
        }

        .zahir-navbar-main {
            display: none;
            align-items: center;
            flex: 1;
            min-width: 0;
            gap: 1rem;
        }

        .zahir-brand {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            flex: 0 0 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.98rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            /* background: #ffffff; */
            text-decoration: none;
        }

        .zahir-menu-scroll {
            min-width: 0;
            flex: 1;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: thin;
        }

        .zahir-top-menu {
            list-style: none;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin: 0;
            padding: 0;
            white-space: nowrap;
        }

        .zahir-top-menu-link {
            display: inline-flex;
            align-items: center;
            height: 42px;
            padding: 0 .95rem;
            border-radius: 9px;
            color: rgba(255, 255, 255, 0.86);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.015em;
            transition: background-color .2s ease, color .2s ease;
        }

        .zahir-top-menu-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.13);
        }

        .zahir-top-menu-item.active .zahir-top-menu-link {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
        }

        body.layout-zahir .main-menu,
        body.layout-zahir .bookmark-wrapper,
        body.layout-zahir #navbar-company-wrapper,
        body.layout-zahir .header-navbar-shadow {
            display: none !important;
        }

        body.layout-zahir .zahir-navbar-main {
            display: flex;
        }

        body.layout-zahir .header-navbar {
            background: linear-gradient(90deg, #0d6efd 0%, #0b5ed7 100%);
            border-radius: 0;
            box-shadow: 0 8px 18px rgba(13, 110, 253, 0.28);
            width: 100%;
            max-width: 100%;
            left: 0;
            right: 0;
            top: 0;
            margin: 0;
            z-index: 1100;
        }

        body.layout-zahir .header-navbar.container-xxl,
        body.layout-zahir .header-navbar.floating-nav {
            width: 100vw !important;
            max-width: 100vw !important;
            left: 0 !important;
            right: 0 !important;
            margin: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            border-radius: 0 !important;
            transform: none !important;
        }

        body.layout-zahir .header-navbar .navbar-container {
            gap: .85rem;
            width: 100%;
            max-width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        body.layout-zahir #navbar-right-section > li > a,
        body.layout-zahir #navbar-right-section > li > a .ficon {
            color: #ffffff;
        }

        body.layout-zahir #navbar-right-section .user-name {
            color: #ffffff;
        }

        body.layout-zahir .app-content,
        body.layout-zahir .footer {
            margin-left: 0 !important;
        }

        body.layout-zahir {
            overflow-x: hidden;
        }

        .zahir-child-drawer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            top: 72px;
            z-index: 1090;
            display: none;
            overflow-y: auto;
            padding: 1rem;
            background: rgba(244, 247, 252, 0.97);
        }

        body.layout-zahir.zahir-child-open .zahir-child-drawer {
            display: block;
        }

        .zahir-child-panel {
            width: 100%;
        }

        .zahir-child-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: 1rem;
        }

        .zahir-child-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #3d3b63;
            text-align: center;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .zahir-child-action {
            border: 0;
            background: #ffffff;
            color: #3d3b63;
            min-height: 36px;
            border-radius: 10px;
            padding: 0 .7rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            box-shadow: 0 3px 10px rgba(15, 31, 73, 0.08);
            transition: background-color .2s ease;
        }

        .zahir-child-action:hover {
            background: #f3f5fb;
        }

        .zahir-child-action[hidden] {
            visibility: hidden;
            display: inline-flex;
        }

        .zahir-child-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }

        .zahir-child-card {
            --zahir-card-bg: #25c4a7;
            width: 100%;
            border: 0;
            border-radius: 16px;
            min-height: 210px;
            padding: 1.3rem 1.1rem;
            color: #ffffff;
            background: linear-gradient(145deg, var(--zahir-card-bg), var(--zahir-card-bg));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 14px 28px rgba(18, 31, 67, 0.15);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .zahir-child-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 30px rgba(18, 31, 67, 0.22);
            filter: saturate(1.04);
            color: #ffffff;
        }

        .zahir-child-card-icon {
            width: 84px;
            height: 84px;
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .zahir-child-card-icon svg,
        .zahir-child-card-icon i {
            width: 84px;
            height: 84px;
            stroke-width: 1.6;
            color: #ffffff;
        }

        .zahir-child-card-title {
            margin: 0;
            font-size: 1.8rem;
            line-height: 1.28;
            font-weight: 600;
            text-align: center;
            letter-spacing: 0.01em;
            color: #ffffff !important;
        }

        .zahir-child-card-sub {
            margin-top: .55rem;
            font-size: .82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: .86;
        }

        @media (min-width: 576px) {
            .zahir-child-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 992px) {
            .zahir-child-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1400px) {
            .zahir-child-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            body.layout-zahir .header-navbar.container-xxl,
            body.layout-zahir .header-navbar.floating-nav {
                width: 100% !important;
                max-width: 100% !important;
            }

            .zahir-top-menu-link {
                height: 38px;
                padding: 0 .75rem;
                font-size: 0.88rem;
            }

            .zahir-child-drawer {
                padding: .8rem;
            }

            .zahir-child-card {
                min-height: 170px;
                border-radius: 14px;
            }

            .zahir-child-card-icon,
            .zahir-child-card-icon svg,
            .zahir-child-card-icon i {
                width: 68px;
                height: 68px;
            }

            .zahir-child-card-title {
                font-size: 1.45rem;
            }
        }
    </style>
    @yield('scriptheader')
    
    <script>
    tinymce.init({
        selector: '.tinymce',
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        },
        license_key: 'gpl',
        plugins: 'image code link lists',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',

        automatic_uploads: true,
        file_picker_types: 'image',
        relative_urls: false,
        remove_script_host: false,
        images_upload_handler: (blobInfo, progress) => {
            return new Promise((resolve, reject) => {
                let formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                fetch('{{ route("upload.image") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.location) {
                        resolve(data.location); 
                    } else {
                        reject('Upload gagal');
                    }
                })
                .catch(() => {
                    reject('Error upload server');
                });
            });
        }
    });

    function formatTgl(d) {
        return d ? d.substring(0,10).split('-').reverse().join('-') : '-';
    }
    </script>
</head>
<!-- END: Head-->

    <!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center" id="default-menu-toggle">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
            </div>
            <div id="navbar-company-wrapper">
                <select name="" id="perusahaan" class="form-select">
                    <option value="">Pilih Perusahaan</option>
                </select>
            </div>
            <div id="zahir-navbar-main" class="zahir-navbar-main" aria-hidden="true">
                <a href="{{ url('/') }}" class="zahir-brand" aria-label="TFM Home">TFM</a>
                <div class="zahir-menu-scroll">
                    <ul id="zahir-top-menu" class="zahir-top-menu" aria-label="Parent menus"></ul>
                </div>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto" id="navbar-right-section">
                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a></li>
                @php
                    use App\Models\ResetPassword;
                    use App\Models\Perusahaan;
                    use App\Models\Role;
                    use Illuminate\Support\Facades\DB;
                    $previllage = Session::get('previllage');
                    $id_perusahaan = Session::get('id_perusahaan');
                    $id_kapal = Session::get('id_kapal');

                    $cek = Perusahaan::where('id', $id_perusahaan)->first();
                    $activeRoleId = Session::get('active_role_id', Session::get('role_id'));
                    $pre = Role::find($activeRoleId);
                    if ($previllage == 1) {
                        $get = DB::table('reset_password')
                                ->leftJoin('user', 'user.id', '=', 'reset_password.id_user')
                                ->leftJoin('karyawan', 'karyawan.id', '=', 'user.id_karyawan')
                                ->where('reset_password.status', 'N')
                                ->select('reset_password.id', 'user.nama', 'karyawan.uid', 'karyawan.nip')
                                ->get();
                    } elseif ($previllage == 2) {
                        $get = DB::table('reset_password')
                                ->leftJoin('user', 'user.id', '=', 'reset_password.id_user')
                                ->leftJoin('karyawan', 'karyawan.id', '=', 'user.id_karyawan')
                                ->where('karyawan.id_perusahaan', $id_perusahaan)->where('reset_password.status', 'N')
                                ->select('reset_password.id', 'user.nama', 'karyawan.uid', 'karyawan.nip')
                                ->get();
                    } else{
                        $get = DB::table('reset_password')
                                ->leftJoin('user', 'user.id', '=', 'reset_password.id_user')
                                ->leftJoin('karyawan', 'karyawan.id', '=', 'user.id_karyawan')
                                ->where('karyawan.id_kapal', $id_kapal)->where('reset_password.status', 'N')
                                ->select('reset_password.id', 'user.nama', 'karyawan.uid', 'karyawan.nip')
                                ->get();
                    }
                    $count = count($get);
                @endphp
                @if($previllage!=4)
                <li class="nav-item dropdown dropdown-notification me-25"><a class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#ResetPassword" ><i class="ficon" data-feather='key'></i>@if($count>0)<span class="badge rounded-pill bg-danger badge-up">{{$count}}</span>@endif</a>
                </li>
                @endif
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder">{{Session::get('name') }}<br> {{$pre->nama ?? '-'}} </span></div>
                        
                        <span class="avatar"><img src="{{url('/img/user.png')}}" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        @php
                            use App\Models\Karyawan;
                            $id =  Session::get('id_karyawan');
                            $data = Karyawan::where('id',$id)->first();
                        @endphp
                        @if($data)
                        <a class="dropdown-item" href="/karyawan/profil/{{$data->uid}}"><i class="me-50" data-feather="user"></i> Profile</a>
                        @endif
                        <a class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#ChangePassword" ><i data-feather='key'></i> Ubah Password</a>
                        <a class="dropdown-item" href="{{ url('/logout') }}"><i class="me-50" data-feather="power"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- END: Header-->

    <section id="zahir-child-drawer" class="zahir-child-drawer" aria-hidden="true">
        <div class="zahir-child-panel">
            <div class="zahir-child-toolbar">
                <button id="zahir-child-back" class="zahir-child-action" type="button" hidden>
                    <i data-feather="arrow-left"></i>
                    <span>Kembali</span>
                </button>
                <h5 class="zahir-child-title">&nbsp;</h5>
                <button id="zahir-child-close" class="zahir-child-action" type="button">
                    <i data-feather="x"></i>
                    <span>Tutup</span>
                </button>
            </div>
            <div id="zahir-child-grid" class="zahir-child-grid"></div>
        </div>
    </section>

    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto"><a class="navbar-brand" href="{{ url('/') }}"><span class="brand-logo">
                            </span>
                        <h2 class="brand-text">TFM</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0"><i class="d-block d-xl-none text-red toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-red" data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
            <div class="main-menu-content">
            @php
                use App\Models\Menu;
                $previllage = Session::get('previllage');
                $roleId = Session::get('active_role_id', $previllage);
                $role = \App\Models\Role::find($roleId);
                $isSuper = $role && (int)($role->is_superadmin ?? 0) === 1;

                // fungsi recursive
                function getMenu($parentId = 0, $roleId, $isSuper) {
                    if ($isSuper) {
                        return Menu::where('status', 'A')
                                    ->where('id_parent', $parentId)
                                    ->where('menu_user', 'N')
                                    ->orderBy('no', 'ASC')
                                    ->get();
                    }
                    return DB::table('role_menu')
                        ->leftJoin('menu', 'menu.id', '=', 'role_menu.menu_id')
                        ->where('role_menu.role_id', $roleId)
                        ->where('menu.status', 'A')
                        ->where('menu.id_parent', $parentId)
                        ->orderBy('menu.no', 'ASC')
                        ->select('menu.*')
                        ->get();
                }

                // Tambah parent ACL khusus superadmin
                $aclParent = null;
                $aclChildren = [];
                if ($isSuper) {
                    $aclParent = (object) [
                        'id' => 9999,
                        'id_parent' => 0,
                        'kode' => 'acl',
                        'nama' => 'ACL',
                        'link' => '#',
                        'icon' => "<i data-feather='shield'></i>",
                    ];

                    $aclChildren = [
                        (object) [
                            'id' => 99991,
                            'id_parent' => 9999,
                            'kode' => '/data_master/menu',
                            'nama' => 'Menu',
                            'link' => url('/data_master/menu'),
                            'icon' => "<i data-feather='circle'></i>",
                        ],
                        (object) [
                            'id' => 99992,
                            'id_parent' => 9999,
                            'kode' => '/acl/roles',
                            'nama' => 'Role',
                            'link' => url('/acl/roles'),
                            'icon' => "<i data-feather='circle'></i>",
                        ],
                        (object) [
                            'id' => 99993,
                            'id_parent' => 9999,
                            'kode' => '/acl/users',
                            'nama' => 'User Management',
                            'link' => url('/acl/users'),
                            'icon' => "<i data-feather='circle'></i>",
                        ],
                        (object) [
                            'id' => 99994,
                            'id_parent' => 9999,
                            'kode' => '/acl/cabang',
                            'nama' => 'Cabang',
                            'link' => url('/acl/cabang'),
                            'icon' => "<i data-feather='circle'></i>",
                        ],
                    ];
                }

                $menus = getMenu(0, $roleId, $isSuper);
                if ($aclParent) {
                    $menus->push($aclParent);
                }
            @endphp
            <ul class="navigation navigation-main" data-menu="menu-navigation">
                @foreach($menus as $menu)
                    @php
                        $children = $menu->id == 9999 ? collect($aclChildren) : getMenu($menu->id, $roleId, $isSuper);
                        $hasChild = count($children) > 0;
                        $isActive = ($active == $menu->kode) ? 'active' : '';
                    @endphp

                    <li class="{{ $isActive }}">
                        <a href="{{ $hasChild ? '#' : $menu->link }}">
                            {!! $menu->icon !!} <span>{{ $menu->nama }}</span>
                        </a>

                        @if($hasChild)
                            <ul>
                                @foreach($children as $child)
                                    @php
                                         $subChildren = getMenu($child->id, $roleId, $isSuper);
                                        $subHasChild = count($subChildren) > 0;
                                        $isChildActive = ($active == $child->kode) ? 'active' : '';
                                    @endphp

                                    <li class="{{ $isChildActive }}">
                                        <a href="{{ $subHasChild ? '#' : $child->link }}">
                                            {!! $child->icon !!} <span>{{ $child->nama }}</span>
                                        </a>

                                        @if($subHasChild)
                                            <ul>
                                                @foreach($subChildren as $gc)
                                                    <li class="{{ $active == $gc->kode ? 'active' : '' }}">
                                                        <a href="{{ $gc->link }}">
                                                            {!! $gc->icon !!} <span>{{ $gc->nama }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
            </div>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                @include('alert')
                @yield('content')
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    @if(session()->has('username'))
        <button id="floating-gear-btn" class="floating-gear-btn" type="button" aria-label="Open quick drawer">
            <i data-feather="settings"></i>
        </button>
        <div id="floating-gear-backdrop" class="floating-gear-backdrop"></div>
        <aside id="floating-gear-drawer" class="floating-gear-drawer" aria-hidden="true">
            <div class="floating-gear-drawer-header">
                <h5 class="mb-0">Pengaturan</h5>
                <button id="floating-gear-close" class="floating-gear-drawer-close" type="button" aria-label="Close drawer">&times;</button>
            </div>
            <div class="floating-gear-drawer-body">
                <fieldset class="layout-fieldset" aria-label="Pilih layout">
                    <legend class="layout-legend">Layout</legend>

                    <div class="layout-picker" role="radiogroup">
                    <!-- Default -->
                    <label class="layout-option" for="layout-default">
                        <input
                            class="layout-input"
                            type="radio"
                            name="layout_view"
                            id="layout-default"
                            value="default"
                            checked
                        />
                        <span class="layout-icon" aria-hidden="true">
                        <i data-feather="layout"></i>
                        </span>
                        <span class="layout-text">
                        <span class="layout-title">Layout 1</span>
                        {{-- <span class="layout-desc">Tampilan standar, paling ringkas.</span> --}}
                        </span>
                    </label>

                    <!-- Zahir -->
                    <label class="layout-option" for="layout-zahir">
                        <input
                            class="layout-input"
                            type="radio"
                            name="layout_view"
                            id="layout-zahir"
                            value="zahir"
                        />
                        <span class="layout-icon" aria-hidden="true">
                        <i data-feather="sliders"></i>
                        </span>
                        <span class="layout-text">
                        <span class="layout-title">Layout 2</span>
                        {{-- <span class="layout-desc">Kontrol lebih lengkap, terasa modern.</span> --}}
                        </span>
                    </label>
                    </div>
                </fieldset>
            </div>
        </aside>
    @endif

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <div class="modal fade text-start" id="ChangePassword" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Ubah Password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_password" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Password Lama</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Password Baru</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="change-pass">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="ResetPassword" tabindex="-1" aria-labelledby="myModalLabel17" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel17">Reset Password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    @csrf
                    <div class="modal-body">
                        <table id="tablecheck" class="table">
                            <thead>
                            @foreach($get as $value)
                            <tr>
                                <td><a href="/karyawan/profil/{{$value->uid}}">{{$value->nama}}</a><br>{{$value->nip}}</td>
                                <td><button class="btn btn-sm btn-danger" id="reset" data-id="{{$value->id}}">Reset Password</button></td>
                            </tr>
                            @endforeach
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">2025<a class="ms-25" href="https://trimas-ferry.co.id" target="_blank">{{$cek->nama}}</a></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->


   <!-- BEGIN: Vendor JS-->
    <script src="{{ url('/app-assets/vendors/js/vendors.min.js')}}"></script>
     <script src="{{ url('/app-assets/js/sweetalert2.js')}}"></script>
    <!-- BEGIN Vendor JS-->


    <!-- BEGIN: Theme JS-->
    <script src="{{ url('/app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{ url('/app-assets/js/core/app.js')}}"></script>
    <script src="{{ url('/app-assets/vendors/js/tom-select.min.js')}}"></script>
    <!-- END: Theme JS-->
    
    @yield('scriptfooter')
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })

        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });

        $('#change-pass').on('click', function(e){
            e.preventDefault(); // cegah submit biasa
            let id_user = $('#id_user').val();
            console.log($('#old_password').val());            

            $.ajax({
                url: "/login/password",
                method: "POST",
                data: {
                    'old_password': $('#old_password').val(),
                    'new_password': $('#new_password').val()
                },
                success: function(response){
                    if(response.success){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ url('/logout') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server'
                    });
                }
            });
        });

         $(document).on("click", "#reset", function (e) {
            let id = $(this).attr('data-id');

            $.ajax({
                url: "/login/reset/" + id,
                method: "GET",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'Password berhasil direset',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#ResetPassword').modal('hide');
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

        (function () {
            const gearBtn = document.getElementById('floating-gear-btn');
            const drawer = document.getElementById('floating-gear-drawer');
            const closeBtn = document.getElementById('floating-gear-close');
            const backdrop = document.getElementById('floating-gear-backdrop');

            var SETTINGS_KEY = 'settings';
            var DEFAULT_LAYOUT = 'default';
            var ZAHIR_LAYOUT = 'zahir';
            var zahirTopMenu = document.getElementById('zahir-top-menu');
            var zahirChildDrawer = document.getElementById('zahir-child-drawer');
            var zahirChildGrid = document.getElementById('zahir-child-grid');
            var zahirChildTitle = document.getElementById('zahir-child-title');
            var zahirChildBack = document.getElementById('zahir-child-back');
            var zahirChildClose = document.getElementById('zahir-child-close');
            var zahirMenuTree = [];
            var zahirStack = [];
            var zahirCardColors = ['#53b856', '#39c8af', '#36aec6', '#145f86', '#efa83a', '#eb5145', '#e05fa2', '#8f79d8'];

            function loadSettings() {
                try {
                    var raw = localStorage.getItem(SETTINGS_KEY);
                    return raw ? JSON.parse(raw) : {};
                } catch (e) {
                    return {};
                }
            }

            function saveSettings(partial) {
                var current = loadSettings();
                for (var key in partial) {
                    if (Object.prototype.hasOwnProperty.call(partial, key)) {
                        current[key] = partial[key];
                    }
                }
                try {
                    localStorage.setItem(SETTINGS_KEY, JSON.stringify(current));
                } catch (e) {
                    // ignore quota / storage errors
                }
            }

            function normalizeLayout(layoutValue) {
                return layoutValue === DEFAULT_LAYOUT ? DEFAULT_LAYOUT : ZAHIR_LAYOUT;
            }

            function getDirectChildByTagName(parent, tagName) {
                if (!parent) {
                    return null;
                }

                for (var i = 0; i < parent.children.length; i++) {
                    if (parent.children[i].tagName === tagName) {
                        return parent.children[i];
                    }
                }
                return null;
            }

            function getDirectChildrenByTagName(parent, tagName) {
                var result = [];
                if (!parent) {
                    return result;
                }

                for (var i = 0; i < parent.children.length; i++) {
                    if (parent.children[i].tagName === tagName) {
                        result.push(parent.children[i]);
                    }
                }
                return result;
            }

            function getFirstNavigableLink(nodes) {
                if (!nodes || !nodes.length) {
                    return '';
                }

                for (var i = 0; i < nodes.length; i++) {
                    if (nodes[i].link && nodes[i].link !== '#') {
                        return nodes[i].link;
                    }
                    var nested = getFirstNavigableLink(nodes[i].children);
                    if (nested) {
                        return nested;
                    }
                }

                return '';
            }

            function parseMenuNodeFromListItem(listItem) {
                var directAnchor = getDirectChildByTagName(listItem, 'A');
                if (!directAnchor) {
                    return null;
                }

                var labelElement = directAnchor.querySelector('span');
                var label = labelElement ? labelElement.textContent.trim() : directAnchor.textContent.trim();
                if (!label) {
                    return null;
                }

                var href = directAnchor.getAttribute('href') || '#';
                var iconNode = directAnchor.querySelector('i[data-feather], svg');
                var iconHtml = iconNode ? iconNode.outerHTML : '';

                var children = [];
                var childList = getDirectChildByTagName(listItem, 'UL');
                if (childList) {
                    var childItems = getDirectChildrenByTagName(childList, 'LI');
                    for (var i = 0; i < childItems.length; i++) {
                        var parsedChild = parseMenuNodeFromListItem(childItems[i]);
                        if (parsedChild) {
                            children.push(parsedChild);
                        }
                    }
                }

                var active = listItem.classList.contains('active');
                if (!active) {
                    for (var j = 0; j < children.length; j++) {
                        if (children[j].active) {
                            active = true;
                            break;
                        }
                    }
                }

                return {
                    label: label,
                    link: href,
                    iconHtml: iconHtml,
                    children: children,
                    active: active
                };
            }

            function buildZahirMenuTree() {
                zahirMenuTree = [];
                var parentItems = document.querySelectorAll('.navigation-main > li');
                if (!parentItems || !parentItems.length) {
                    return;
                }

                for (var i = 0; i < parentItems.length; i++) {
                    var parsed = parseMenuNodeFromListItem(parentItems[i]);
                    if (parsed) {
                        zahirMenuTree.push(parsed);
                    }
                }
            }

            function closeZahirChildDrawer() {
                document.body.classList.remove('zahir-child-open');
                if (zahirChildDrawer) {
                    zahirChildDrawer.setAttribute('aria-hidden', 'true');
                }
                zahirStack = [];
            }

            function syncZahirChildDrawerOffset() {
                if (!zahirChildDrawer) {
                    return;
                }

                var navBar = document.querySelector('.header-navbar');
                if (!navBar) {
                    return;
                }

                var navRect = navBar.getBoundingClientRect();
                var topOffset = Math.max(navRect.bottom, 0);
                zahirChildDrawer.style.top = topOffset + 'px';
            }

            function renderZahirChildCards(nodes) {
                if (!zahirChildGrid) {
                    return;
                }

                zahirChildGrid.innerHTML = '';
                var fragment = document.createDocumentFragment();

                for (var i = 0; i < nodes.length; i++) {
                    var node = nodes[i];
                    var hasChildren = node.children && node.children.length > 0;
                    var isNavigable = node.link && node.link !== '#';
                    var card = document.createElement(hasChildren ? 'button' : 'a');
                    card.className = 'zahir-child-card';
                    card.setAttribute('data-node-index', String(i));
                    card.style.setProperty('--zahir-card-bg', zahirCardColors[i % zahirCardColors.length]);

                    if (hasChildren) {
                        card.setAttribute('type', 'button');
                    } else {
                        card.setAttribute('href', isNavigable ? node.link : 'javascript:void(0)');
                    }

                    var iconWrap = document.createElement('span');
                    iconWrap.className = 'zahir-child-card-icon';
                    iconWrap.innerHTML = node.iconHtml || '<i data-feather="grid"></i>';

                    var title = document.createElement('h6');
                    title.className = 'zahir-child-card-title';
                    title.textContent = node.label;

                    card.appendChild(iconWrap);
                    card.appendChild(title);

                    if (hasChildren) {
                        var sub = document.createElement('span');
                        sub.className = 'zahir-child-card-sub';
                        sub.textContent = 'Buka menu';
                        card.appendChild(sub);
                    }

                    fragment.appendChild(card);
                }

                zahirChildGrid.appendChild(fragment);

                if (window.feather) {
                    feather.replace();
                }
            }

            function showZahirChildLevel() {
                if (!zahirChildDrawer || !zahirStack.length) {
                    return;
                }

                var currentLevel = zahirStack[zahirStack.length - 1];
                if (zahirChildTitle) {
                    zahirChildTitle.textContent = currentLevel.title;
                }
                if (zahirChildBack) {
                    zahirChildBack.hidden = zahirStack.length <= 1;
                }

                renderZahirChildCards(currentLevel.nodes);
                syncZahirChildDrawerOffset();
                document.body.classList.add('zahir-child-open');
                zahirChildDrawer.setAttribute('aria-hidden', 'false');
            }

            function openZahirChildDrawer(title, nodes) {
                zahirStack = [{ title: title, nodes: nodes }];
                showZahirChildLevel();
            }

            function renderZahirTopMenu() {
                if (!zahirTopMenu) {
                    return;
                }

                zahirTopMenu.innerHTML = '';
                var fragment = document.createDocumentFragment();

                for (var i = 0; i < zahirMenuTree.length; i++) {
                    var node = zahirMenuTree[i];
                    var hasChildren = node.children && node.children.length > 0;
                    var fallbackHref = getFirstNavigableLink(node.children);
                    var href = node.link && node.link !== '#' ? node.link : (fallbackHref || 'javascript:void(0)');

                    var li = document.createElement('li');
                    li.className = node.active ? 'zahir-top-menu-item active' : 'zahir-top-menu-item';

                    var a = document.createElement('a');
                    a.className = 'zahir-top-menu-link';
                    a.setAttribute('href', href);
                    a.setAttribute('data-parent-index', String(i));
                    if (hasChildren) {
                        a.setAttribute('data-has-children', '1');
                    }
                    a.textContent = node.label;

                    li.appendChild(a);
                    fragment.appendChild(li);
                }

                zahirTopMenu.appendChild(fragment);
            }

            function setActiveZahirTopMenu(index) {
                if (!zahirTopMenu) {
                    return;
                }

                var items = zahirTopMenu.querySelectorAll('.zahir-top-menu-item');
                for (var i = 0; i < items.length; i++) {
                    if (i === index) {
                        items[i].classList.add('active');
                    } else {
                        items[i].classList.remove('active');
                    }
                }
            }

            function applyLayout(layoutValue) {
                var layout = normalizeLayout(layoutValue);
                var zahirMain = document.getElementById('zahir-navbar-main');

                document.body.classList.remove('layout-default', 'layout-zahir');
                document.body.classList.add('layout-' + layout);

                if (zahirMain) {
                    zahirMain.setAttribute('aria-hidden', layout === ZAHIR_LAYOUT ? 'false' : 'true');
                }

                if (layout === ZAHIR_LAYOUT) {
                    buildZahirMenuTree();
                    renderZahirTopMenu();
                    syncZahirChildDrawerOffset();
                    document.body.classList.remove('menu-open', 'menu-hide');
                } else {
                    closeZahirChildDrawer();
                }
            }

            // Drawer open/close
            const openDrawer = function () {
                document.body.classList.add('gear-drawer-open');
                drawer.setAttribute('aria-hidden', 'false');
            };

            const closeDrawer = function () {
                document.body.classList.remove('gear-drawer-open');
                drawer.setAttribute('aria-hidden', 'true');
            };

            if (gearBtn && drawer && closeBtn && backdrop) {
                gearBtn.addEventListener('click', openDrawer);
                closeBtn.addEventListener('click', closeDrawer);
                backdrop.addEventListener('click', closeDrawer);

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeZahirChildDrawer();
                        closeDrawer();
                    }
                });
            }

            if (zahirTopMenu) {
                zahirTopMenu.addEventListener('click', function (event) {
                    var link = event.target.closest('a[data-parent-index]');
                    if (!link) {
                        return;
                    }

                    var parentIndex = parseInt(link.getAttribute('data-parent-index'), 10);
                    if (isNaN(parentIndex) || !zahirMenuTree[parentIndex]) {
                        return;
                    }

                    var selected = zahirMenuTree[parentIndex];
                    setActiveZahirTopMenu(parentIndex);
                    if (selected.children && selected.children.length) {
                        event.preventDefault();
                        openZahirChildDrawer(selected.label, selected.children);
                        return;
                    }

                    var href = link.getAttribute('href') || '';
                    if (!href || href === '#') {
                        event.preventDefault();
                    }
                });
            }

            if (zahirChildGrid) {
                zahirChildGrid.addEventListener('click', function (event) {
                    var card = event.target.closest('.zahir-child-card');
                    if (!card || !zahirStack.length) {
                        return;
                    }

                    var nodeIndex = parseInt(card.getAttribute('data-node-index'), 10);
                    var currentLevel = zahirStack[zahirStack.length - 1];
                    if (isNaN(nodeIndex) || !currentLevel.nodes[nodeIndex]) {
                        return;
                    }

                    var selected = currentLevel.nodes[nodeIndex];
                    if (selected.children && selected.children.length) {
                        event.preventDefault();
                        zahirStack.push({ title: selected.label, nodes: selected.children });
                        showZahirChildLevel();
                        return;
                    }

                    if (card.tagName === 'BUTTON') {
                        var targetLink = selected.link && selected.link !== '#' ? selected.link : '';
                        if (targetLink) {
                            window.location.href = targetLink;
                        } else {
                            event.preventDefault();
                        }
                    }
                });
            }

            if (zahirChildBack) {
                zahirChildBack.addEventListener('click', function () {
                    if (zahirStack.length > 1) {
                        zahirStack.pop();
                        showZahirChildLevel();
                    } else {
                        closeZahirChildDrawer();
                    }
                });
            }

            if (zahirChildClose) {
                zahirChildClose.addEventListener('click', function () {
                    closeZahirChildDrawer();
                });
            }

            if (zahirChildDrawer) {
                zahirChildDrawer.addEventListener('click', function (event) {
                    if (event.target === zahirChildDrawer) {
                        closeZahirChildDrawer();
                    }
                });
            }

            window.addEventListener('resize', syncZahirChildDrawerOffset);

            // Layout setting: load awal dari localStorage.settings.layout_view
            var settings = loadSettings();
            var activeLayout = normalizeLayout(settings.layout_view);
            if (settings.layout_view !== activeLayout) {
                saveSettings({ layout_view: activeLayout });
            }
            applyLayout(activeLayout);

            var layoutRadios = document.querySelectorAll('input[name="layout_view"]');
            if (layoutRadios && layoutRadios.length) {
                for (var i = 0; i < layoutRadios.length; i++) {
                    if (layoutRadios[i].value === activeLayout) {
                        layoutRadios[i].checked = true;
                        break;
                    }
                }

                for (var j = 0; j < layoutRadios.length; j++) {
                    layoutRadios[j].addEventListener('change', function () {
                        if (this.checked) {
                            var nextLayout = normalizeLayout(this.value);
                            saveSettings({ layout_view: nextLayout });
                            applyLayout(nextLayout);
                        }
                    });
                }
            }
        })();
    </script>
    
</body>
<!-- END: Body-->

</html>
