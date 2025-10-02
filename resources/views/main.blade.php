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
    <link rel="shortcut icon" type="image/x-icon" href="{{url('/img/trimas.png')}}">
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
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/style.css')}}">
    <!-- END: Custom CSS-->
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
                        <a class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#ChangePassword" ><i data-feather='key'></i> Ubah Password</a>
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
            @php
                use App\Models\Menu;
                use Illuminate\Support\Facades\DB;
                $previllage = Session::get('previllage');
                $id = Session::get('id_karyawan');

                // fungsi recursive
                function getMenu($parentId = 0, $previllage, $id) {
                    if ($previllage == 1) {
                        return Menu::where('status', 'A')
                                    ->where('id_parent', $parentId)
                                    ->where('menu.menu_user', 'N')
                                    ->orderBy('no', 'ASC')
                                    ->get();
                    } else if ($previllage == 4) {
                        return Menu::where('status', 'A')
                                    ->where('id_parent', $parentId)
                                    ->where('menu.menu_user', 'Y')
                                    ->orderBy('no', 'ASC')
                                    ->get();
                    } else {
                        return DB::table('akses')
                            ->leftJoin('menu', 'menu.id', '=', 'akses.id_menu')
                            ->where('menu.status', 'A')
                            ->where('menu.menu_user', 'N')
                            ->where('menu.id_parent', $parentId)
                            ->where('akses.id_karyawan', $id)
                            ->orderBy('no', 'ASC')
                            ->get();
                    }
                }

                $menus = getMenu(0, $previllage, $id);
            @endphp
            <ul class="navigation navigation-main" data-menu="menu-navigation">
                @foreach($menus as $menu)
                    @php
                        $children = getMenu($menu->id, $previllage, $id);
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
                                        $subChildren = getMenu($child->id, $previllage, $id);
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

       $(document).ready(function () {
            $.ajax({
                url: "getMenu",
                method: "GET",
                success: function (menus) {
                    let html = renderMenu(menus);
                    $("#sidebarMenu").html(html);
                    setActiveMenu();
                }
            });

            $("#sidebarMenu").html(renderMenu(menus));
    setActiveMenu();

    // 🔹 Fungsi render recursive
    function renderMenu(menus, level = 0) {
        let html = "";
        menus.forEach(menu => {
            let hasChildren = menu.children && menu.children.length > 0;
            let baseClass = "flex items-center py-2 px-3 rounded-md transition duration-150 ease-in-out cursor-pointer";
            let inactiveClass = "text-gray-700 hover:bg-gray-100";

            if (hasChildren) {
                html += `
                    <li>
                        <a class="${baseClass} ${inactiveClass} parent-menu">
                            ${menu.icon ?? ""}<span class="ml-2">${menu.nama}</span>
                            <span class="ml-auto">▸</span>
                        </a>
                        <ul class="ml-4 hidden border-l border-gray-200 space-y-1 pl-2">
                            ${renderMenu(menu.children, level + 1)}
                        </ul>
                    </li>
                `;
            } else {
                html += `
                    <li>
                        <a href="${menu.link}" 
                           class="${baseClass} ${inactiveClass}" 
                           data-link="${menu.link}">
                            ${menu.icon ?? ""}<span class="ml-2">${menu.nama}</span>
                        </a>
                    </li>
                `;
            }
        });
        return html;
    }

    // 🔹 Set menu aktif
    function setActiveMenu() {
        let current = window.location.pathname;

        $("#sidebarMenu a[data-link]").each(function () {
            if ($(this).attr("href") === current) {
                $(this).addClass("bg-indigo-600 text-white font-semibold");
                $(this).parents("ul").removeClass("hidden").addClass("block");
                $(this).parents("li").children(".parent-menu")
                       .addClass("text-indigo-700 font-bold")
                       .find("span:last").text("▾");
            }
        });
    }

    // 🔹 Toggle submenu kalau parent diklik
    $(document).on("click", ".parent-menu", function (e) {
        e.preventDefault();
        let submenu = $(this).next("ul");
        submenu.toggleClass("hidden block");
        $(this).find("span:last").text(submenu.hasClass("hidden") ? "▸" : "▾");
    });
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
    </script>
    
</body>
<!-- END: Body-->

</html>