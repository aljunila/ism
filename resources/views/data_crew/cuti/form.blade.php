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
        $('#cuti-id_kapal').hide();
        $('#pengganti').hide();

        $(document).on('change', '#cuti-id_m_cuti', function() {
            let id_m_cuti = $(this).val();
            if(id_m_cuti==9){
                $('#cuti-id_kapal').show();
                $('#cuti-id_karyawan').hide();
                $('#pengganti').hide();
            } else {
                $('#cuti-id_kapal').hide();
                $('#cuti-id_karyawan').show();
                $('#pengganti').show();
            }
        })

        document.getElementById('cuti-tgl_selesai').addEventListener('change', hitungHari);
        document.getElementById('cuti-tgl_mulai').addEventListener('change', hitungHari);

        function hitungHari() {
            const tglMulai   = document.getElementById('cuti-tgl_mulai').value;
            const tglSelesai = document.getElementById('cuti-tgl_selesai').value;

            if (!tglMulai || !tglSelesai) {
                document.getElementById('cuti-jml_hari').value = '';
                return;
            }

            let start = new Date(tglMulai);
            let end   = new Date(tglSelesai);

            start.setDate(start.getDate());

            if (end < start) {
                document.getElementById('cuti-jml_hari').value = 0;
                return;
            }

            let totalHari = 0;

            while (start <= end) {
                if (start.getDay() !== 0) { // 0 = Minggu
                    totalHari++;
                }
                start.setDate(start.getDate() + 1);
            }

            document.getElementById('cuti-jml_hari').value = totalHari;
        }

         $('#cuti-id_kapal').on('change', function() {
            var kapalID = $(this).val();
            if (kapalID) {
                $.ajax({
                    url: '/get-karyawan/' + kapalID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('.crew').empty().append('<option value="">-- Pilih Crew --</option>');
                    
                        $.each(data, function(key, value) {
                            $('.crew').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                        });
                    }
                });
            } else {
                $('.crew').empty().append('<option value="">-- Pilih Crew --</option>');
            }
        });

        $("#tambah").click(function () {
            let field = `
            <div class="mb-1 row field-item">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-4">
                    <select name="crew[]" class="form-control crew">
                        <option value="">Pilih Crew</option>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm hapus">Hapus</button>
                </div>
            </div>`;
            $("#field-container").append(field);
        });

        $(document).on("click", ".hapus", function () {
            $(this).closest(".field-item").remove();
        });

        $('#form_cuti').on('submit', function(e){
            e.preventDefault(); // cegah submit biasa
            let form = $(this);
            let formData = new FormData(this);
            let url = form.data('update-url')
                ? form.data('update-url')   // EDIT
                : form.data('store-url'); //ADD
            $.ajax({
                url: url,
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
                            window.location.href = "{{ url('/data_crew/cuti') }}";
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
    </script>
@endsection

@section('content')
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Data Trip</h4>
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
                    <form id="form_cuti"
                    data-store-url="{{ route('cuti.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Jenis Cuti</label>
                                </div>
                                <div class="col-sm-9">
                                   <select name="id_m_cuti" id="cuti-id_m_cuti"  class="form-control">
                                        <option value="">Pilih</option>
                                        @foreach($jeniscuti as $c)
                                            <option value="{{$c->id}}">{{$c->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Pilih Crew/Kapal</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_karyawan" id="cuti-id_karyawan" class="form-control">
                                        <option value="">Pilih Crew</option>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}">{{$k->nama}}</option>
                                        @endforeach
                                    </select>
                                    <select name="id_kapal" id="cuti-id_kapal" class="form-control">
                                        <option value="">Pilih Kapal</option>
                                        @foreach($kapal as $kp)
                                            <option value="{{$kp->id}}">{{$kp->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                             <div class="mb-1 row" id="form-wrapper">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Daftar Crew</label>
                                </div>
                                <div class="col-sm-9">
                                    <table id="table" class="table table-bordered table-striped" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table><br>
                                    <button type="button" class="btn btn-success btn-sm" id="tambah">Tambah</button>
                                </div>
                            </div>
                            <div id="field-container"></div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Tanggal</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" name="tgl_mulai" id="cuti-tgl_mulai">
                                </div>
                                <div class="col-sm-1">-</div>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" name="tgl_selesai" id="cuti-tgl_selesai">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                 <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Total Hari</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="jml_hari" id="cuti-jml_hari">
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Keterangan</label>
                                </div>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="note" id="cuti-note"></textarea>
                                </div>
                            </div>
                            <div id="pengganti">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="first-name">Pengganti</label>
                                </div>
                                <div class="col-sm-9">
                                    <select name="id_pengganti" id="cuti-id_pengganti" class="form-control">
                                        <option value="">Pilih</option>
                                        @foreach($karyawan as $k)
                                            <option value="{{$k->id}}">{{$k->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
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
