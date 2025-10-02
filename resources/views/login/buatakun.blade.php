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
    <title>TFM - Trimas Ferries Management</title>
    <link rel="apple-touch-icon" href="{{ url('/vuexy/app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('/img/trimas.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/themes/semi-dark-layout.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/pages/authentication.css')}}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/assets/css/style.css')}}">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-basic px-2">
                    <div class="auth-inner my-2">
                        <!-- Login basic -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="index.html" class="brand-logo">
                                    <img src="{{ url('/img/trimas.png')}}" alt="" width="50%">
                                </a>
                                <p class="card-text mb-2">Silahkan masukkan NIK Anda</p></center>

                                <form class="auth-login-form mt-2" action="{{ route('actionlogin') }}" method="POST">
                                @csrf
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="login-password">NIK</label>
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="number" class="form-control form-control-merge" name="nik" id="nik"/>
                                            <span class="input-group-text cursor-pointer"><i data-feather="search"></i></span>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="login-password">Password</label>
                                            <a href="auth-forgot-password-basic.html">
                                                <small>Lupa Password?</small>
                                            </a>
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" name="password" required tabindex="2"/>
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                    </div>
                                    <p class="text-center mt-2">
                                        <a href="/buatakun"><span>Belum punya akun?</span></a>
                                    </p>
                                    <button class="btn btn-primary w-100" tabindex="4" type="submit">Log in</button>
                                </form>
                            </div>
                        </div>
                        <!-- /Login basic -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ url('/vuexy/app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/core/app.js')}}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/auth-login.js')}}"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })

$('#nik').on('keydown', function(e) {
    if (e.key === "Enter" || e.keyCode === 13) {
        e.preventDefault(); // biar form nggak auto-submit (kalau ada)
        cariDataNik();
    }
});

function cariDataNik() {
    let nik = $('#nik').val();

    if (!nik) {
        Swal.fire({
            icon: "warning",
            title: "NIK belum diisi",
            text: "Silakan masukkan NIK terlebih dahulu"
        });
        return;
    }

    $.ajax({
        url: '/carinik', 
        method: 'GET',
        data: { nik: nik
        },
        success: function(res) {
            console.log(res);
            $('#daftar').show();
        },
        error: function(xhr) {
            Swal.fire({
                icon: "error",
                title: "Tidak ditemukan",
                text: xhr.responseJSON?.message || "Data NIK tidak ada"
            });
        }
    });
}

    </script>
</body>
<!-- END: Body-->

</html>