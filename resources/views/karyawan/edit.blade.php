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
            let id = {{$show->id}};
            let uid = "{{$show->uid}}";
            let formData = new FormData(this);

            $.ajax({
                url: '/karyawan/update/'+id,
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
                           window.location.href = "{{ url('/karyawan/profil') }}/"+uid;
                        });
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Username sudah digunakan, silahkan ganti Username Anda'
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
                    <h4 class="card-title">Edit Data Profil Karyawan</h4>
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
                                    <input type="text" class="form-control" id="nama" name="nama" required value="{{$show->nama}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">NIK</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="nik" name="nik" required value="{{$show->nik}}">
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
                                    <input type="text" class="form-control" id="tmp_lahir" name="tmp_lahir" value="{{$show->tmp_lahir}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tanggal Lahir</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{$show->tgl_lahir}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Status Perkawinan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="status_kawin" id="status_kawin"  class="form-control">
                                        <option value="">Pilih</option>
                                        <option value="S" @selected ($show->status_kawin=='S')>Lajang</option>
                                        <option value="M" @selected ($show->status_kawin=='M')>Menikah</option>
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
                                        <option value="Islam" @selected ($show->agama=='Islam')>Islam</option>
                                        <option value="Kristen Protestan" @selected ($show->agama=='Kristen Protestan')>Kristen Protestan</option>
                                        <option value="Kristen Katolik" @selected ($show->agama=='Kristen Katolik')>Kristen Katolik</option>
                                        <option value="Hindu" @selected ($show->agama=='Hindu')>Hindu</option>
                                        <option value="Budha" @selected ($show->agama=='Budha')>Budha</option>
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
                                        <option value="O" @selected ($show->gol_darah=='O')>O</option>
                                        <option value="A" @selected ($show->gol_darah=='A')>A</option>
                                        <option value="B" @selected ($show->gol_darah=='B')>B</option>
                                        <option value="AB" @selected ($show->gol_darah=='AB')>AB</option>
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
                                        <option value="SMA" @selected ($show->pend=='SMA')>SMA/Sederajat</option>
                                        <option value="D1" @selected ($show->pend=='D1')>D1</option>
                                        <option value="D2" @selected ($show->pend=='D2')>D2</option>
                                        <option value="D3" @selected ($show->pend=='D3')>D3</option>
                                        <option value="S1" @selected ($show->pend=='S1')>S1</option>
                                        <option value="S2" @selected ($show->pend=='S2')>S2</option>
                                        <option value="S3" @selected ($show->pend=='S3')>S3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Institusi Pendidikan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="institusi_pend" name="institusi_pend" value="{{$show->institusi_pend}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Jurusan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="jurusan" name="jurusan" value="{{$show->jurusan}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Sertifikat</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sertifikat" name="sertifikat" value="{{$show->sertifikat}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">No Telp</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="telp" name="telp" required value="{{$show->telp}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Email</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" value="{{$show->email}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Alamat</label>
                                </div>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="alamat" name="alamat" required>{!! $show->alamat !!}</textarea>
                                </div>
                            </div>
                            <hr>
                            <label for="col-form-label">Kontak Darurat</label>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Hubungan</label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control" id="kontak_darurat" name="kontak_darurat" >
                                        <option value="Suami/Istri" @selected ($show->kontak_darurat=='Suami/Istri')>Suami/Istri</option>
                                        <option value="Anak" @selected ($show->kontak_darurat=='Anak')>Anak</option>
                                        <option value="Orangtua" @selected ($show->kontak_darurat=='Orangtua')>Orangtua</option>
                                        <option value="Saudara" @selected ($show->kontak_darurat=='Saudara')>Saudara</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Nama</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_kontak" name="nama_kontak" value="{{$show->nama_kontak}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">No Telp</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="telp_kontak" name="telp_kontak" value="{{$show->telp_kontak}}">
                                </div>
                            </div>
                            <hr>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Nama Bank</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_bank" name="nama_bank" value="{{$show->nama_bank}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Nama Pemegang Rekening</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_rekening" name="nama_rekening" value="{{$show->nama_rekening}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">No Rekening</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="no_rekening" name="no_rekening" value="{{$show->no_rekening}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">NPWP</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="npwp" name="npwp" value="{{$show->npwp}}">
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
                                            <option value="{{$value->id}}" @selected ($show->status_ptkp==$value->id)>{{$value->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">BPJS Kesehatan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="bpjs_kes" name="bpjs_kes" value="{{$show->bpjs_kes}}">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">BPJS Ketenagakerjaan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="bpjs_tk" name="bpjs_tk" value="{{$show->bpjs_tk}}">
                                </div>
                            </div>
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