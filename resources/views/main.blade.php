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
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
            </div>
            <div>
                <select name="" id="perusahaan" class="form-select">
                    <option value="">Pilih Perusahaan</option>
                </select>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
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
    </script>
    
</body>
<!-- END: Body-->

</html>
