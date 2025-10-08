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
                                <div id="search">
                                    <h4 class="card-text mb-2">Silahkan masukkan NIK Anda</h4></center>
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="login-password">NIK</label>
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="number" class="form-control form-control-merge" name="nik" id="nik"/>
                                            <span class="input-group-text cursor-pointer"><i data-feather="search"></i></span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary w-100" tabindex="4" id="cari">Cari</button>
                                </div>
                                <div id="data">
                                    <div id="keterangan"></div>
                                    <div class="mb-1 row">
                                        <div class="col-sm-4">
                                            <p>Nama </p>
                                        </div>
                                        <div class="col-sm-1">
                                            :
                                        </div>
                                        <div class="col-sm-7">
                                            <p id="nama"></p>
                                        </div>
                                    </div>
                                    <div class="mb-1 row">
                                        <div class="col-sm-4">
                                            <p>Perusahaan </p>
                                        </div>
                                        <div class="col-sm-1">
                                            :
                                        </div>
                                        <div class="col-sm-7">
                                            <p id="perusahaan"></p>
                                        </div>
                                    </div>
                                    <div id="usernameContainer">
                                        <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
                                            <strong>Silahkan isi password untuk membuat akun</strong><br>
                                        </div>
                                        <div class="mb-1">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="login-password">Password</label>
                                            </div>
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="password" class="form-control form-control-merge" name="password1" id="password1" required placeholder="Masukkan password"/>
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                <input type="hidden" name="id" id="id">
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="login-password">Konfirmasi Password</label>
                                            </div>
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="password" class="form-control form-control-merge" name="password2" id="password2" required placeholder="Masukkan ulang password"/>
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary w-100" tabindex="4" id="save" type="button">Simpan</button>
                                    </div>
                                    <a href="/login" class="btn btn-primary w-100" tabindex="4" id="login" type="button"><span>Kembali ke Login</span></a>
                                </div>
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
    <script src="{{ url('/app-assets/js/sweetalert2.js')}}"></script>
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

        $(function() {
            $('#search').show();
            $('#data').hide();
        });

        $('#cari').on('click', function(e) {
                cariDataNik();
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
                method: 'POST',
                data: { nik: nik,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    // console.log(res);
                    if (!res.data || Object.keys(res.data).length === 0) {
                        Swal.fire({
                            icon: "warning",
                            title: "Data tidak ditemukan",
                            text: "Pastikan NIK Anda terdaftar sebagai karyawan",
                            confirmButtonText: "OK"
                        });
                        return; // hentikan eksekusi lanjut
                    }
                    $('#search').hide();
                    $('#data').show();
                    $('#nama').html(res.data.nama);
                    $('#id').val(res.data.id);
                    $('#perusahaan').html(res.data.perusahaan);

                    let username = res.data.username;
                    if (username === null || username === "") {
                        $('#keterangan').html(`
                        <div class="alert alert-danger text-center alert-dismissible fade show" role="alert">
                            <strong>Anda belum memiliki akun!</strong><br>
                        </div>
                    `);                
                        $('#usernameContainer').show();
                        $('#login').hide();
                        feather.replace();
                    } else {
                         $('#keterangan').html(`
                        <div class="alert alert-success text-center alert-dismissible fade show" role="alert">
                            <strong>Anda telah memiliki akun!<br>Silahkan LogIn menggunakan NIK Anda</strong><br>
                        </div>
                    `);      
                        $('#usernameContainer').hide();
                        $('#login').show();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: "warning",
                        title: "Data tidak ditemukan",
                        text: "Pastikan NIK Anda terdaftar sebagai karyawan.",
                        confirmButtonText: "OK"
                    });
                }
            });
        }

        $(document).on("click", "#save", function(){
            let password1 = $('#password1').val();
            let password2 = $('#password2').val();
            let id = $('#id').val();
            if (password1!=password2) {
                Swal.fire({
                    icon: "warning",
                    title: "Oops...",
                    text: "Password tidak sesuai"
                });
                return;
            }
            $.ajax({
                url: "/login/storeuser",
                type: "POST",
                data: {
                    password1: password1,
                    password2: password2,
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: response.message ?? "Akun berhasil dibuat. Silahkan login menggunakan NIK sebagai username dan password yang telah Anda buat",
                            showConfirmButton: true,
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {  
                                window.location.href = "/login";
                            }
                        });
                    
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Tidak dapat menyimpan data"
                    });
                    console.error(xhr.responseText);
                }
            });
        });
    </script>
</body>
<!-- END: Body-->

</html>