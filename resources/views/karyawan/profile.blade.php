@extends('main')
@section('scriptheader')
  
   <!-- BEGIN: Vendor CSS-->
   <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/animate/animate.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <!-- END: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
    
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/moment.min.js')}}"></script> 
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/polyfill.min.js')}}"></script>
    <script src="{{ url('/app-assets/vendors/js/tom-select.min.js') }}"></script>
    <!-- END: Page Vendor JS-->

    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/modal-edit-user.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/app-user-view-account.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/app-user-view.js')}}"></script>
    <script>
        $('#form_karyawan').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = $('#id').val();
            let formData = new FormData(this);

            $.ajax({
                url: "/karyawan/update/" + id,
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
                            $('#FormEdit').modal('hide');
                            window.location.reload();
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

        $('#form_ttd').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = $('#id').val();
            let formData = new FormData(this);

            $.ajax({
                url: "/karyawan/updatettd/" + id,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'Tanda tangan berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#FormTtd').modal('hide');
                            window.location.reload();
                        });
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menyimpan tanda tangan'
                    });
                }
            });
        });

        $(document).on('click', '.upload-btn', function(){
            let id = $(this).attr('data-id');
            let file = $(this).attr('data-file');
            $('#id_file').val(id);
            $('#file').html(file);
            $('#FormUpload').modal('show');
        });

        $('#form_file').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = $('#id_file').val();
            let formData = new FormData(this);

            $.ajax({
                url: "/karyawan/savefile/" + id,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message ?? 'File berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#FormUpload').modal('hide');
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

        $('#form_mutasi').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let formData = new FormData(this);

            $.ajax({
                 url: "{{ url('/data_crew/mutasi') }}",
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
                            $('#FormEdit').modal('hide');
                            window.location.reload();
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
        
        $(document).on("click", ".delmutasi-btn", function(){
            let id = $(this).data("id");

            Swal.fire({
                title: "Yakin mau hapus?",
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/data_crew/mutasi/" + id,
                        type: "delete",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res){
                            Swal.fire({
                                icon: "success",
                                title: "Terhapus!",
                                text: "Data berhasil dihapus",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        window.location.reload();
                        },
                        error: function(err){
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "Data gagal dihapus"
                            });
                        }
                    });
                }
            });
        });

        new TomSelect('#id_pengganti', {
            placeholder: 'Karyawan...',
            allowEmptyOption: true,
            maxItems: 1,
            searchField: ['text'],   // bisa diketik
            create: false            // tidak boleh input baru
        });

        $('#form_cuti').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let formData = new FormData(this);

            $.ajax({
                url: "{{ url('/data_crew/cuti') }}",
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
                            $('#FormCuti').modal('hide');
                            table.ajax.reload();
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

        let table;
        $(function () {
            table = $('#table').DataTable({
                processing: true,
                ordering: false,
                searchable: false,
                ajax:{
                    url: "/data_crew/cuti/databyId",
                    type: "POST",
                    data: function(d){
                        d.id= "{{ $show->id }}", 
                        d._token= "{{ csrf_token() }}"
                    },
                },
                columns: [
                    { data: null, 
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1; 
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'jenis' },
                    {
                        data: null,
                        render: row => `
                            <div>
                                <div>${formatTgl(row.tgl_mulai)} s/d ${formatTgl(row.tgl_selesai)}</div>
                            </div>
                        `
                    },
                    { data: 'jml_hari' },
                    { data: 'pengganti' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            if (data == 1) return '<a class="badge badge-light-primary">Pengajuan</a>';
                            if (data == 2) return '<a class="badge badge-light-success">Diterima</a>';
                            if (data == 3) return '<a class="badge badge-light-danger">Ditolak</a>';
                            return '-';
                        }
                    },
                    { data: 'approval' }

                ],
                drawCallback: function(settings) {
                    feather.replace(); // supaya icon feather muncul ulang
                }
            });
        });
    </script>
@endsection
@section('content')
<section class="app-user-view-account">
    <div class="row">
        <div class="col-xl-3 col-lg-5 col-md-5 order-1 order-md-0">
            <div class="card">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            <div class="user-info text-center">
                                <h4>{{$show->nama}}</h4>
                                <a href="/karyawan/pdf/{{$show->uid}}" type ="button" target="_blank" class="btn btn-warning btn-sm me-1" id="pdf-btn"><i data-feather='download-cloud'></i>  Unduh Data</a>
                                <!-- <span class="badge bg-light-secondary">Author</span> -->
                            </div>
                        </div>
                    </div><br>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">NIP:</span>
                                <span>{{$show->nip}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">NIK:</span>
                                <span>{{$show->nik}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Tanda tangan:</span>
                                <button class="btn btn-success btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormTtd">Upload</button>
                            </li>
                            <li class="mb-75">
                                    <img class="img-fluid mt-3 mb-2" src="{{$show->tanda_tangan_url}}" height="125" width="125" alt="User avatar" />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /User Card -->
        </div>
        <div class="col-xl-9 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- User Card -->
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true"><i data-feather="home"></i>Profil Pribadi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="karyawan-tab" data-bs-toggle="tab" href="#karyawan" aria-controls="karyawan" role="tab" aria-selected="true"><i data-feather="user"></i>Profil Karyawan</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" id="dokumen-tab" data-bs-toggle="tab" href="#dokumen" aria-controls="dokumen" role="tab" aria-selected="true"><i data-feather="file"></i>Dokumen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="cuti-tab" data-bs-toggle="tab" href="#cuti" aria-controls="cuti" role="tab" aria-selected="true"><i data-feather="log-out"></i>Cuti</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                            <div class="card-header border-bottom">
                                <h5 class="fw-bolder">Informasi Pribadi</h5>
                                <a type="button" href="/karyawan/edit/{{$show->uid}}" class="btn btn-primary btn-sm float-right me-1">Edit Data Profil</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table" width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="25%"> Jenis Kelamin</td>
                                            <td width="5%">:</td>
                                            <td width="75%">{{ ($show->jk=='P') ? 'Pria' : 'Wanita' }}</td>
                                        </tr>
                                        <tr>
                                            <td> Tempat, Tanggal Lahir</td>
                                            <td>:</td>
                                            <td>{!! ($show->tmp_lahir) ? $show->tmp_lahir : '-' !!}, {!! ($show->tgl_lahir) ? $show->tgl_lahir : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Status Perkawinan</td>
                                            <td>:</td>
                                            <td>{{ ($show->status_kawin=='M') ? 'Menikah' : 'Lajang' }}</td>
                                        </tr>
                                        <tr>
                                            <td> Agama</td>
                                            <td>:</td>
                                            <td>{!! ($show->agama) ? $show->agama : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Golongan Darah</td>
                                            <td>:</td>
                                            <td>{!! ($show->gol_darah) ? $show->gol_darah : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Pendidikan</td>
                                            <td>:</td>
                                            <td>{!! ($show->pend) ? $show->pend : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Institusi Pendidikan</td>
                                            <td>:</td>
                                            <td>{!! ($show->institusi_pend) ? $show->institusi_pend : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Jurusan</td>
                                            <td>:</td>
                                            <td>{!! ($show->jurusan) ? $show->jurusan : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Sertifikat</td>
                                            <td>:</td>
                                            <td>{!! ($show->sertifikat) ? $show->sertifikat : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> No Telp</td>
                                            <td>:</td>
                                            <td>{!! ($show->telp) ? $show->telp : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Email</td>
                                            <td>:</td>
                                            <td>{!! ($show->email) ? $show->email : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Alamat</td>
                                            <td>:</td>
                                            <td>{!! ($show->alamat) ? $show->alamat : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Kontak Darurat</td>
                                            <td>:</td>
                                            <td>{!! ($show->kontak_darurat) ? $show->kontak_darurat : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Nama</td>
                                            <td>:</td>
                                            <td>{!! ($show->nama_kontak) ? $show->nama_kontak : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> No Telp Darurat</td>
                                            <td>:</td>
                                            <td>{!! ($show->telp_kontak) ? $show->telp_kontak : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Nama Bank</td>
                                            <td>:</td>
                                            <td>{!! ($show->nama_bank) ? $show->nama_bank : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Nama Pemilik Rekening</td>
                                            <td>:</td>
                                            <td>{!! ($show->nama_rekening) ? $show->nama_rekening : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> No Rekening</td>
                                            <td>:</td>
                                            <td>{!! ($show->no_rekening) ? $show->no_rekening : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> No Telp</td>
                                            <td>:</td>
                                            <td>{!! ($show->telp) ? $show->telp : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Status PTKP</td>
                                            <td>:</td>
                                            <td>{!! ($show->status_ptkp) ? $show->status_ptkp : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> BPJS Kesehatan</td>
                                            <td>:</td>
                                            <td>{!! ($show->bpjs_kes) ? $show->bpjs_kes : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> BPJS Ketenagakerjaan</td>
                                            <td>:</td>
                                            <td>{!! ($show->bpjs_tk) ? $show->bpjs_tk : '-' !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="karyawan" aria-labelledby="karyawan-tab" role="tabpanel">
                            <div class="card-header border-bottom">
                                <h5 class="fw-bolder">Informasi Karyawan</h5>
                                @if(Session::get('previllage')!=4)
                                <button class="btn btn-primary btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormEdit">Edit Data Karyawan</button>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table" width="100%">
                                    <tbody>
                                        <tr>
                                            <td> Perusahaan</td>
                                            <td>:</td>
                                            <td>{!! ($show->perusahaan) ? $show->perusahaan : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Ditempatkan di</td>
                                            <td>:</td>
                                            <td>{!! ($show->kapal) ? $show->kapal : 'Office' !!}</td>
                                        </tr>
                                        <tr>
                                            <td> Jabatan</td>
                                            <td>:</td>
                                            <td>{!! ($show->id_jabatan) ? $show->jabatan : '-' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Status Karyawan</td>
                                            <td>:</td>
                                            <td>@switch($show->status_karyawan)
                                                    @case('TP')
                                                        Tetap Permanen
                                                        @break

                                                    @case('TC')
                                                        Tetap Percobaan
                                                        @break

                                                    @case('K')
                                                        Kontrak
                                                        @break

                                                    @case('F')
                                                        Freelance
                                                        @break

                                                    @case('M')
                                                        Magang
                                                        @break

                                                    @default
                                                        -
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> Tanggal Mulai Gabung</td>
                                            <td>:</td>
                                            <td>{!! ($show->tgl_mulai) ? $show->tgl_mulai : '-' !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br><br>
                                <div class="card-header border-bottom">
                                    <h5 class="fw-bolder">Data Mutasi</h5>
                                    @if(Session::get('previllage')!=4)
                                    <button class="btn btn-primary btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormMutasi">Mutasi Crew</button>
                                    @endif
                                </div>
                                <table class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr align="center">
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Tgl Mutasi</th>
                                            <th colspan="2">Mutasi</th>
                                            <th rowspan="2">Aksi</th>
                                        </tr>
                                        <tr>
                                            <th>Dari</th>
                                            <th>Ke</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mutasi as $m)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ \Carbon\Carbon::parse($m->tgl_naik)->format('d-m-Y') }}</td>
                                            <td>{{ $m->get_dari_perusahaan()->nama }} <br>
                                                {!! ($m->dari_kapal) ? $m->get_dari_kapal()->nama : '-' !!}</td>
                                            <td>{{ $m->get_ke_perusahaan()->nama }} <br>
                                                {!! ($m->ke_kapal) ? $m->get_ke_kapal()->nama : '-' !!}</td>
                                            <td> <button type="button" class="btn btn-icon rounded-circle btn-xs btn-flat-danger delmutasi-btn" 
                                                title="Hapus Mutasi" data-id="{{$m->id}}">
                                                <i data-feather='trash'></i>
                                            </button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="dokumen" aria-labelledby="dokumen-tab" role="tabpanel">
                            <h5 class="fw-bolder border-bottom pb-50 mb-1">Crew Documents</h5>
                            <div class="table-responsive">
                                <table class="table" width="100%" border="1">
                                    <tr>
                                        <th>Nama Dokumen</th>
                                        <th>No</th>
                                        <th>Penerbit</th>
                                        <th>Tgl Terbit</th>
                                        <th>Tgl Expired</th>
                                        <th>Aksi</th>
                                    </tr>
                                    @foreach($file as $f)
                                    <tr>
                                        <td width="30%">{{$f->nama}}</td>
                                        <td width="15%">{{ ($f->no) ? $f->no : '-' }}</td>
                                        <td width="15%">{{$f->penerbit}}</td>
                                        <td width="10%">{{$f->tgl_terbit}}</td>
                                        <td width="10%">{{$f->tgl_expired}}</td>
                                        <td width="20%">
                                                <button type="button" class="btn btn-icon rounded-circle btn-xs btn-flat-warning upload-btn" 
                                                    title="Upload File" data-id="{{$f->id}}" data-file="{{$f->nama}}">
                                                    <i data-feather='upload'></i>
                                                </button>
                                            @if(!empty($f->file))
                                                <a type="button" href="{{ asset('file_upload/'.$f->file) }}" target="_blank" class="btn btn-icon rounded-circle btn-xs btn-flat-success" 
                                                    title="Buka File" data-id="{{$f->id}}" data-file="{{$f->nama}}">
                                                    <i data-feather='file'></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="cuti" aria-labelledby="cuti-tab" role="tabpanel">
                           @include('karyawan/cuti')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormEdit" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_karyawan" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Perusahaan</label>
                            </div>
                            <div class="col-sm-9">
                                @if(Session::get('previllage')==1)
                                    <select name="id_perusahaan" id="id_perusahaan"  class="form-control" required>
                                        @foreach($perusahaan as $ph)
                                            @if($show->id_perusahaan==$ph->id)
                                                    <option value="{{$ph->id}}" selected>{{$ph->nama}}</option>
                                                @else
                                                    <option value="{{$ph->id}}">{{$ph->nama}}</option>
                                                @endif
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="id_perusahaan" id="id_perusahaan" value="{{$show->id_perusahaan}}">
                                    {{$show->perusahaan}}
                                @endif
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Ditempatkan di</label>
                            </div>
                            <div class="col-sm-9">
                                    <select name="id_kapal" id="id_kapal"  class="form-control">
                                        <option value="">Office</option>
                                        @foreach($kapal as $k)
                                            @if($show->id_kapal==$k->id)
                                                <option value="{{$k->id}}" selected>{{$k->nama}}</option>
                                            @else
                                                <option value="{{$k->id}}">{{$k->nama}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                            </div>
                        </div> -->
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Jabatan</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="id_jabatan" id="id_jabatan"  class="form-control" required>
                                @foreach($jabatan as $j)
                                    @if($show->id_jabatan==$j->id)
                                        <option value="{{$j->id}}" selected>{{$j->nama}}</option>
                                    @else
                                        <option value="{{$j->id}}">{{$j->nama}}</option>
                                    @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Status Karyawan</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="status_karyawan" id="status_karyawan"  class="form-control">
                                    <option value="">Pilih</option>
                                    <option value="TP" @selected ($show->status_karyawan=='TP')>Tetap Permanen</option>
                                    <option value="TC" @selected ($show->status_karyawan=='TC')>Tetap Percobaan</option>
                                    <option value="K" @selected ($show->status_karyawan=='K')>Kontrak</option>
                                    <option value="F" @selected ($show->status_karyawan=='F')>Freelance</option>
                                    <option value="M" @selected ($show->status_karyawan=='M')>Magang</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Tanggal mulai gabung</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control" value="{{$show->tgl_mulai}}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" id="id" value="{{$show->id}}">
                        <button type="submit" class="btn btn-primary" id="edit_data">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormMutasi" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Mutasi Crew</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_mutasi" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Perusahaan</label>
                            </div>
                            <div class="col-sm-9">
                                @if(Session::get('previllage')==1)
                                    <select name="ke_perusahaan" id="ke_perusahaan"  class="form-control" required>
                                        @foreach($perusahaan as $ph)
                                            @if($show->id_perusahaan==$ph->id)
                                                    <option value="{{$ph->id}}" selected>{{$ph->nama}}</option>
                                                @else
                                                    <option value="{{$ph->id}}">{{$ph->nama}}</option>
                                                @endif
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="ke_perusahaan" id="ke_perusahaan" value="{{$show->id_perusahaan}}">
                                    {{$show->perusahaan}}
                                @endif
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Ditempatkan di</label>
                            </div>
                            <div class="col-sm-9">
                                    <select name="ke_kapal" id="ke_kapal"  class="form-control">
                                        <option value="0">Office</option>
                                        @foreach($kapal as $k)
                                            @if($show->id_kapal==$k->id)
                                                <option value="{{$k->id}}" selected>{{$k->nama}}</option>
                                            @else
                                                <option value="{{$k->id}}">{{$k->nama}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Tgl Mutasi</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="tgl_naik" id="tgl_naik">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Keterangan</label>
                            </div>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="ket" id="ket"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id_karyawan" value="{{$show->id}}">
                        <input type="hidden" name="kode" id="kode" value="el0610">
                        <button type="submit" class="btn btn-primary" id="simpan_mutasi">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormTtd" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Upload Tanda Tangan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_ttd" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Upload Image</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" id="tanda_tangan" name="tanda_tangan" required value="{{$show->nama}}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id_karyawan" value="{{$show->id}}">
                        <button type="submit" class="btn btn-primary" id="upload_ttd">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormUpload" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <form id="form_file" enctype="multipart/form-data">
                    @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="file">Upload Dokumen</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        @if($show->kel==1)
                        <label>Tgl Terbit</label>
                        <div class="mb-1">
                            <input type="date" class="form-control" name="tgl_terbit" id="tgl_terbit"/>
                        </div>

                        <label>Tgl Expired</label>
                        <div class="mb-1">
                            <input type="date" class="form-control" name="tgl_expired" id="tgl_expired"/>
                        </div>

                        <label>No Dokumen</label>
                        <div class="mb-1">
                            <input type="text" class="form-control" name="no" id="no"/>
                        </div>

                        <label>Penerbit</label>
                        <div class="mb-1">
                            <input type="text" class="form-control" name="penerbit" id="penerbit"/>
                        </div>
                        @endif
                        <label>Format file: PDF</label>
                        <div class="mb-1">
                            <input type="file" class="form-control" name="file" id="file"/>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id_file" id="id_file">
                    <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{$show->id}}">
                    <button type="submit" class="btn btn-primary" id="save_file">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</section>
@endsection
