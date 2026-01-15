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
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    
@endsection

@section('scriptfooter')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/moment.min.js')}}"></script> -->
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/vendors/js/extensions/polyfill.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/modal-edit-user.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/app-user-view-account.js')}}"></script>
    <script src="{{ url('/vuexy/app-assets/js/scripts/pages/app-user-view.js')}}"></script>
    <script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

    <script>
        let table;
        $(function () {
            table = $('#table').DataTable({
                processing: true,
                ordering: false,
                searchable: false,
                ajax:{
                url: "/kapal/datafile",
                type: "POST",
                data: function(d){
                    d.id_kapal= "{{$show->id}}",
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'nama' },
                { data: 'ket' },
                { 
                    data: null,
                    render: function(data, type, row) {
                       return `
                            ${row.penerbit ?? '-'}
                            ${row.nomor ? `<br>No : ${row.nomor}` : ''}
                        `; }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                       return `
                            ${row.tgl_terbit ? `Terbit : ${row.tgl_terbit} ` : '-'}
                            ${row.tgl_expired ? `<br>Expired : ${row.tgl_expired}` : ''}
                        `; }
                },
                { 
                    data: null,
                    render: function (data, type, row) {

                        // JIKA ADA FILE
                        if (row.file) {
                            return `
                                <a href="{{ asset('file_upload') }}/${row.file}"
                                    class="btn btn-icon rounded-circle btn-xs btn-flat-success"
                                    title="Buka File" target="_blank">
                                    <i data-feather="file"></i>
                                </a>

                                <button type="button"
                                    class="btn btn-icon rounded-circle btn-xs btn-flat-danger delete-btn"
                                    title="Hapus File"
                                    data-id="${row.id_upload}"
                                    data-file="${row.file}">
                                    <i data-feather="trash"></i>
                                </button>
                            `;
                        }

                        // JIKA TIDAK ADA FILE
                        return `
                            <button type="button"
                                class="btn btn-icon rounded-circle btn-xs btn-flat-warning upload-btn"
                                title="Upload File"
                                data-id="${row.id}"
                                data-file="${row.nama}">
                                <i data-feather="upload"></i>
                            </button>
                        `;
                    }
                }

            ],
            drawCallback: function(settings) {
            feather.replace(); // supaya icon feather muncul ulang
            }
            });
        });

        $(document).on('click', '.upload-btn', function(){
            let id = $(this).attr('data-id');
            let file = $(this).attr('data-file');
            let id_kapal = {{$show->id}};
            $('#id_file').val(id);
            $('#id_kapal').val(id_kapal);
            $('#file').html(file);
            $('#FormUpload').modal('show');
        });

        $('#form_file').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let id = $('#id_file').val();
            let formData = new FormData(this);

            $.ajax({
                url: "/kapal/savefile/" + id,
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

         $('#form_docking').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let formData = new FormData(this);
                id= "{{$show->id}}"
            $.ajax({
                url: "/kapal/docking_store/" + id,
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
                            $('#FormDocking').modal('hide');
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

        $(document).on('click', '.delete-btn', function () {
            let id   = $(this).data('id');
            let file = $(this).data('file');

            Swal.fire({
                title: 'Yakin hapus file?',
                text: 'File yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/kapal/delfile/" + id,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            file: file
                        },
                        success: function (res) {
                            Swal.fire('Berhasil!', res.message, 'success');
                            $('#table').DataTable().ajax.reload(null, false);
                        },
                        error: function () {
                            Swal.fire('Error!', 'Gagal menghapus file', 'error');
                        }
                    });
                }
            });
        });

    </script>
@endsection
@section('content')
<section class="app-user-view-account">
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            <div class="user-info text-center">
                                <h4>{{$show->nama}}</h4>
                                <a href="/kapal/pdf/{{$show->uid}}" type ="button" target="_blank" class="btn btn-warning btn-sm me-1" id="pdf-btn"><i data-feather='download-cloud'></i>  Unduh Data</a>
                            </div>
                        </div>
                    </div><br>
                    <h5 class="fw-bolder border-bottom pb-50 mb-1">Informasi Umum</h5>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Lokasi Cabang:</span>
                                <span>{{$show->get_cabang()->cabang}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Kelas:</span>
                                <span>{{$show->gol}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Call Sign:</span>
                                <span>{{$show->call_sign}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Nama Pendaftaran:</span>
                                <span>{{$show->pendaftaran}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Grosse Akte Nomor:</span>
                                <span>{{$show->no_akte}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">No SIUP:</span>
                                <span>{{$show->no_siup}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Dikeluarkan di:</span>
                                <span>{{$show->dikeluarkan_di}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Pemilik:</span>
                                <span>{{$show->get_pemilik()->nama}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Gal/Thn Buat:</span>
                                <span>{{$show->galangan}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Konstruksi:</span>
                                <span>{{$show->konstruksi}}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">Type:</span>
                                <span>{{$show->type}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="fw-bolder">Riwayat Docking</h5>
                    <button class="btn btn-primary btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormDocking">Tambah Data</button>
                </div><br>
                <div class="card-body">
                    <div class="info-container">
                        <table class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th>Tgl Mulai</th>
                                    <th>Tgl Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($docking as $d)
                                    <tr>
                                        <td>{{$d->tgl_mulai}}</td>
                                        <td>{{$d->tgl_selesai}}</td>
                                        <td><a type="button" href="{{ asset('file_docking/'.$d->file) }}" target="_blank" class="btn btn-icon rounded-circle btn-xs btn-flat-success" 
                                                title="Buka File" data-id="{{$d->id}}" data-file="{{$d->nama}}">
                                                <i data-feather='file'></i>
                                            </a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /User Card -->
        </div>
        <!--/ User Sidebar -->

        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- Project table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="fw-bolder">Informasi Detail</h5>
                    <a href="/kapal/edit/{{$show->uid}}" class="btn btn-primary btn-sm float-right me-1">Edit Data</a>
                </div>
                <div class="table-responsive">
                    <table class="table" width="100%">
                        <tr>
                            <td width="50%">
                                <table class="table">
                                <tbody>
                                <tr>
                                    <th colspan="3">Ukuran Pokok</th>
                                </tr>
                                <tr>
                                    <td> Panjang kapal seluruhnya (LOA)</td>
                                    <td>:</td>
                                    <td>{{ $show->loa }} meter</td>
                                </tr>
                                <tr>
                                    <td> Panjang antara garis tegak (LBP)</td>
                                    <td>:</td>
                                    <td>{{ $show->lbp }} meter</td>
                                </tr>
                                <tr>
                                    <td> Dalam Kapal</td>
                                    <td>:</td>
                                    <td>{{ $show->dalam }} meter</td>
                                </tr>
                                <tr>
                                    <td> Lebar Kapal</td>
                                    <td>:</td>
                                    <td>{{ $show->lebar }} meter</td>
                                </tr>
                                <tr>
                                    <th colspan="3">Draft Kapal</th>
                                </tr>
                                <tr>
                                    <td>Sarat musim panas (Summer Draft)</td>
                                    <td>:</td>
                                    <td>{{ $show->summer_draft }} meter</td>
                                </tr>
                                <tr>
                                    <td>Sarat musim dingin (Winter Draft)</td>
                                    <td>:</td>
                                    <td>{{ $show->winter_draft }} meter</td>
                                </tr>
                                <tr>
                                    <td>Draft pada air tawar</td>
                                    <td>:</td> 
                                    <td>{{ $show->draft_air_tawar }} meter</td>
                                </tr>
                                <tr>
                                    <td>Draft pada air tawar</td>
                                    <td>:</td> 
                                    <td>{{ $show->draft_air_tawar }} meter</td>
                                </tr>
                                <tr>
                                    <td>Sarat Tropik (Tropical Draft)</td>
                                    <td>:</td> 
                                    <td>{{ $show->tropical_draft }} meter</td>
                                </tr>
                                <tr>
                                    <td>Isi Kotor</td>
                                    <td>:</td> 
                                    <td>{{ $show->isi_kotor }}</td>
                                </tr>
                                <tr>
                                    <td>Bobot Mati</td>
                                    <td>:</td> 
                                    <td>{{ $show->bobot_mati }}</td>
                                </tr>
                                <tr>
                                    <td>NT</td>
                                    <td>:</td> 
                                    <td>{{ $show->nt }}</td>
                                </tr>
                                </tbody>
                            </table>
                            </td>
                            <td width="50%">
                                <table class="table">
                                <tbody>
                                    <tr>
                                        <th colspan="3">Mesin Induk</th>
                                    </tr>
                                    <tr>
                                        <td>Merek</td>
                                        <td>:</td>
                                        <td>{{ $show->merk_mesin_induk }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tahun</td>
                                        <td>:</td>
                                        <td>{{ $show->tahun_mesin_induk }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nomor</td>
                                        <td>:</td>
                                        <td>{{ $show->nomor_mesin_induk }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3">Mesin Bantu</th>
                                    </tr>
                                    <tr>
                                        <td>Merek</td>
                                        <td>:</td>
                                        <td>{{ $show->merk_mesin_bantu }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tahun</td>
                                        <td>:</td>
                                        <td>{{ $show->tahun_mesin_bantu }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nomor</td>
                                        <td>:</td>
                                        <td>{{ $show->nomor_mesin_bantu }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3">Kecepatan/Speed</th>
                                    </tr>
                                    <tr>
                                        <td>Maksimum</td>
                                        <td>:</td>
                                        <td>{{ $show->max_speed }} knot</td>
                                    </tr>
                                    <tr>
                                        <td>Normal</td>
                                        <td>:</td>
                                        <td>{{ $show->normal_speed }} knot</td>
                                    </tr>
                                    <tr>
                                        <td>Minimum</td>
                                        <td>:</td>
                                        <td>{{ $show->min_speed }} knot</td>
                                    </tr>
                                    <tr>
                                        <td>Bahan bakar</td>
                                        <td>:</td>
                                        <td>{{ $show->bahan_bakar }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kebutuhan /hari</td>
                                        <td>:</td>
                                        <td>{{ $show->jml_butuh }} ton</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bolder border-bottom pb-50 mb-1">Ship Documents</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" id="table">
                                <thead>
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Penerbit</th>
                                    <th>Keterangan</th>
                                    <th>Tgl Terbit/Expired</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                
                            </table>
                        </div>
                </div>
            </div>
            <!-- /Project table -->
        </div>

        <!--/ User Content -->
    </div>

    <div class="modal fade text-start" id="FormUpload" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <form id="form_file" enctype="multipart/form-data">
                    @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Upload Document</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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

                        <label>Keterangan</label>
                        <div class="mb-1">
                            <input type="text" class="form-control" name="penerbit" id="penerbit"/>
                        </div>
                        
                        <label>Format file: PDF</label>
                        <div class="mb-1">
                            <input type="file" class="form-control" name="file" id="file"/>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id_file" id="id_file">
                    <input type="hidden" name="id_kapal" id="id_kapal">
                    <button type="submit" class="btn btn-primary" id="save_file">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormDocking" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Data Docking</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_docking" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Tgl Mulai</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Tgl Selesai</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="tgl_selesai" id="tgl_selesai">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Upload File : PDF</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="file" id="file_docking">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="edit_data">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection