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
    <title>ISM - PT Aman Lintas Samudra</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{url('/img/logo-als.jpg')}}">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet"> -->

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/vendors.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/themes/bordered-layout.css')}}">
    <script src="https://cdn.tiny.cloud/1/kk3dzyiek4uhy82bodtbqgh5f26brsw2xxin668j9rs34va1/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/style.css')}}">
    <!-- END: Custom CSS-->
    @yield('scriptheader')
    <script>
        tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'image code',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | image code',
        automatic_uploads: true,
        images_upload_url: "{{ route('upload.image') }}",
        file_picker_types: 'image',

        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,

        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave(); // supaya textarea ter-update
            });
        },
        images_upload_handler: function (blobInfo, success, failure) {
            let formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            fetch("{{ route('upload.image') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result && result.location) {
                    success(result.location);
                } else {
                    failure('Upload gagal');
                }
            })
            .catch(() => failure('Server error'));
        }
        });
    </script>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a></li>

                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder">{{Session::get('name') }}</span></div>
                        
                        <span class="avatar"><img class="round" src="" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/karyawan/profil"><i class="me-50" data-feather="user"></i> Profile</a>
                        <a class="dropdown-item" href="{{ url('/logout') }}"><i class="me-50" data-feather="power"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto"><a class="navbar-brand" href="../../../html/ltr/vertical-menu-template/index.html"><span class="brand-logo">
                            </span>
                        <h2 class="brand-text">ISM</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0"><i class="d-block d-xl-none text-red toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-red" data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="@if($active=='dashboard') active @endif"><a class="d-flex align-items-center" href="/dashboard"><i data-feather="home"></i><span class="menu-item text-truncate" data-i18n="eCommerce">Dashboard</span></a>
                </li>
                <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">DATA &amp; MASTER</span><i data-feather="more-horizontal"></i>
                </li> 
                <?php
                    $previllage = Session::get('previllage');
                    $id = Session::get('id_karyawan');
                    if(($previllage)==1){
                        $cek = App\Models\Menu::where('status', 'A')
                            ->where('id_parent', 0)
                            ->orderBy('no', 'ASC')->get(); 
                    } else {
                        $cek = DB::table('akses')
                                ->leftjoin('menu', 'menu.id', '=', 'akses.id_menu')
                                ->where('menu.status', 'A')
                                ->where('menu.id_parent', 0)
                                ->where('akses.id_karyawan', $id)
                                ->orderBy('no', 'ASC')->get(); 
                    }
                ?>
                @foreach($cek as $menu)
                    <?php
                        if(($previllage)==1){
                            $getparent = App\Models\Menu::where('status', 'A')
                                        ->where('id_parent', $menu->id)
                                        ->orderBy('no', 'ASC')->get(); 
                        } else {
                            $getparent = DB::table('akses')
                                ->leftjoin('menu', 'menu.id', '=', 'akses.id_menu')
                                ->where('menu.status', 'A')
                                ->where('akses.id_karyawan', $id)
                                ->where('menu.id_parent', $menu->id)
                                ->orderBy('no', 'ASC')->get(); 
                        }
                            $count=count($getparent);
                            if($active==$menu->kode){
                                $class = 'active';
                            } else {
                                $class='';
                            }
                    ?>
                    @if($count>=1)
                        <li class="{{$class}}"><a class="d-flex align-items-center" href="{{ $menu->link }}">{!! $menu->icon !!}<span class="menu-item text-truncate" data-i18n="Profile">{{ $menu->nama }}</span></a>
                            <ul>
                                @foreach($getparent as $child)
                                    <?php if(($previllage)==1){
                                                $getgc = App\Models\Menu::where('status', 'A')
                                                            ->where('id_parent', $child->id)
                                                            ->orderBy('no', 'ASC')->get(); 
                                            } else {
                                                $getgc = DB::table('akses')
                                                    ->leftjoin('menu', 'menu.id', '=', 'akses.id_menu')
                                                    ->where('menu.status', 'A')
                                                    ->where('akses.id_karyawan', $id)
                                                    ->where('menu.id_parent', $child->id)
                                                    ->orderBy('no', 'ASC')->get(); 
                                            }
                                            $countgc=count($getgc);
                                            if($active==$child->kode) {
                                                $c = 'active';
                                            } else {
                                                $c='';
                                            } ?>
                                    @if($countgc>=1)
                                    <li class="{{$c}}"><a class="d-flex align-items-center" href="{{ $child->link }}">{!! $child->icon !!}<span class="menu-item text-truncate" data-i18n="Profile">{{ $child->nama }}</span></a>
                                        <ul>
                                            @foreach($getgc as $gc)
                                                <?php if($active==$gc->kode) {
                                                            $cg = 'active';
                                                        } else {
                                                            $cg='';
                                                        } ?>
                                                <li class="{{$cg}}"><a class="d-flex align-items-center" href="{{ $gc->link }}">{!! $gc->icon !!}<span class="menu-item text-truncate" data-i18n="Profile">{{ $gc->nama }}</span></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @else
                                    <li class="{{$c}}"><a class="d-flex align-items-center" href="{{ $child->link }}">{!! $child->icon !!}<span class="menu-item text-truncate" data-i18n="Profile">{{ $child->nama }}</span></a>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @else 
                    <li class={{$class}}><a class="d-flex align-items-center" href="{{ $menu->link }}">{!! $menu->icon !!}<span class="menu-item text-truncate" data-i18n="Profile">{{ $menu->nama }}</span></a>
                        </li>    
                    @endif
                @endforeach
            </ul>
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

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">2025<a class="ms-25" href="" target="_blank">PT. Aman Lintas Samudra</a></p>
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
    </script>
    
</body>
<!-- END: Body-->

</html>