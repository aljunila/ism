@extends('main')
@section('scriptheader')
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
     <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
    <!-- END: Page CSS-->
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <!-- END: Page Vendor JS-->
    <!-- BEGIN: Page JS-->
    <script src="{{ url('/vuexy/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <!-- END: Page JS-->

   <script>
        $('#form_karyawan').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa

            let formData = new FormData(this);

            $.ajax({
                url: "{{ url('/karyawan/store') }}",
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
                            window.location.href = "{{ url('/karyawan') }}";
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

        $(document).on('change', '#id_perusahaan', function() {
            var perusahaanID = $(this).val();
            if (perusahaanID) {
                $.ajax({
                    url: '/get-kapal/' + perusahaanID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#id_kapal').empty().append('<option value="">Office</option>');           
                        $.each(data, function(key, value) {
                            $('#id_kapal').append('<option value="'+ value.id +'">Kapal '+ value.nama +'</option>');
                        });
                    }
                });
            } else {
                $('#id_kapal').empty().append('<option value="">Tidak ada data</option>');
            }
        });
    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tambah Karyawan</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                    @endif
                    <form id="form_karyawan" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Nama Lengkap</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">NIK</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="nik" name="nik" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Jenis Kelamin</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="jk" id="jk"  class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="P">Pria</option>
                                        <option value="W">Wanita</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tempat Lahir</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="tmp_lahir" name="tmp_lahir">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tanggal Lahir</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Status Perkawinan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="status_kawin" id="status_kawin"  class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="S">Lajang</option>
                                        <option value="M">Menikah</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Agama</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="agama" id="agama"  class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen Protestan">Kristen Protestan</option>
                                        <option value="Kristen Katolik">Kristen Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Golongan Darah</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="gol_darah" id="gol_darah"  class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="O">O</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Pendidikan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="pend" id="pend"  class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="SMA">SMA/Sederajat</option>
                                        <option value="D1">D1</option>
                                        <option value="D2">D2</option>
                                        <option value="D3">D3</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Institusi Pendidikan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="institusi_pend" name="institusi_pend">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Jurusan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="jurusan" name="jurusan">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Sertifikat</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sertifikat" name="sertifikat">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">No Telp</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="telp" name="telp" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Email</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Alamat</label>
                                </div>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Nama Bank</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_bank" name="nama_bank">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Nama Pemegang Rekening</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_rekening" name="nama_rekening">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">No Rekening</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="no_rekening" name="no_rekening">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">NPWP</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="npwp" name="npwp">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Status PTKP</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="status_ptkp" id="status_ptkp"  class="form-control" required>
                                        <option value="">Pilih</option>
                                        @foreach($ptkp as $value)
                                            <option value="{{$value->id}}">{{$value->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">BPJS Kesehatan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="bpjs_kes" name="bpjs_kes">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">BPJS Ketenagakerjaan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="bpjs_tk" name="bpjs_tk">
                                </div>
                            </div>
                            <hr>
                             @if(Session::get('previllage')==1)
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Perusahaan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_perusahaan" id="id_perusahaan" required class="form-control">
                                        <option value="">Pilih Perusahaan</option>
                                    @foreach($perusahaan as $p)
                                        <option value="{{$p->id}}">{{$p->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="id_perusahaan" value="{{Session::get('id_perusahaan')}}">
                            @endif
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Ditempatkan di</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_kapal" id="id_kapal" class="form-control">
                                        <option value="">Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Jabatan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_jabatan" id="id_jabatan"  class="form-control" required>
                                    <option value="">Pilih</option>
                                    @foreach($jabatan as $j)
                                        <option value="{{$j->id}}">{{$j->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tanggal Mulai Gabung</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" required>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Status Karyawan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="status_karyawan" id="status_karyawan"  class="form-control" required>
                                        <option value="">Pilih</option>
                                        <option value="TP">Tetap Permanen</option>
                                        <option value="TC">Tetap Percobaan</option>
                                        <option value="K">Kontrak</option>
                                        <option value="F">Freelance</option>
                                        <option value="M">Magang</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tanda Tangan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="tanda_tangan" name="tanda_tangan">
                                </div>
                            </div>
                            <hr>
                            <!-- <div class="mb-1 row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="first-name">Buat Akun</label>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Username</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Level Akses</label>
                                </div>
                                <div class="col-sm-9">
                                     <select name="id_previllage" id="id_previllage"  class="form-control">
                                    @foreach($previllage as $p)
                                        <option value="{{$p->id}}">{{$p->nama}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div> -->
                        </div>
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary me-1" id="simpan_data">Simpan</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection