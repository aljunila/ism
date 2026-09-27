@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="col-sm-12"><h4 class="card-title">Docking</h4></div>
        <div class="col-sm-4">
        <select name="id_kapal" id="id_kapal" class="form-control">
            <option value="">Pilih</option>
            @foreach ($kapal as $k)
                <option value="{{$k->id}}">{{$k->nama}}</option>
            @endforeach
        </select>
        </div><div class="col-sm-6"></div>
        <div class="col-sm-2"><button class="btn btn-primary btn-sm" id="btn-add-docking">Tambah Data</button></div>
    </div>
    <div class="card-body">
        <table id="table-docking" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kapal</th>
                    <th>Jadwal</th>
                    <th>Tempat</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Selesai</th>
                    <th>Durasi</th>
                    <th>File</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-docking" tabindex="-1" aria-labelledby="modal-docking-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-docking-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Kapal</label>
                        </div>
                        <div class="col-9">
                            <select id="docking-id_kapal" class="form-control">
                                <option value="">-Pilih-</option>
                                @foreach($kapal as $k)
                                    <option value="{{$k->id}}">{{$k->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Jadwal</label>
                        </div>
                        <div class="col-5">
                            <select id="docking-bulan" class="form-control">
                                <option value="">Pilih</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <select id="docking-tahun" class="form-control">
                                <option value="">-Pilih-</option>
                                @for($a=2020; $a<=2036; $a++)
                                    <option value="{{$a}}">{{$a}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Tempat</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="docking-tempat" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Tanggal Docking</label>
                        </div>
                        <div class="col-4">
                            <input type="date" id="docking-tgl_mulai" class="form-control" placeholder="Tgl mulai">
                        </div>
                        <div class="col-1">s/d</div>
                        <div class="col-4">
                            <input type="date" id="docking-tgl_selesai" class="form-control" placeholder="Tgl selesai">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Durasi</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="docking-durasi" class="form-control">
                        </div>
                    </div>
                </div>
                 <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">File Hasil Docking</label>
                        </div>
                        <div class="col-9">
                            <input type="file" id="docking-file" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-docking">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptfooter')
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#id_kapal').on('change', function () {
         table.ajax.reload();
        });

        const table = $('#table-docking').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/data_teknik/docking/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d._token= "{{ csrf_token() }}"
                },
                dataSrc: "data"
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        render: function (data, type, row) {
                            return `${row.kapal}`;
                        }
                },
                { 
                        data: null, 
                        render: function (data, type, row) {
                            return `${row.bulan} - ${row.tahun}`;
                        }
                },
                { data: 'tempat', name: 'tempat' },
                { data: 'tgl_mulai',
                    render: function(data) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                },
                { data: 'tgl_selesai',
                    render: function(data) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                },
                { data: 'durasi', name: 'durasi' },
                { 
                    data: null,
                    render: function(data, type, row){
                        if(row.file) {
                        return `
                        <a href="{{ asset('file_docking') }}/${row.file}" target="_blank" type="button" class="btn btn-icon btn-xs btn-flat-success" title="Buka File">
                                <i data-feather='file'></i>
                            </a>
                        `;
                    } else {
                        return ``;
                    }
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-docking-label').text('Tambah Data');
            $('#docking-tempat').val('');
            $('#docking-bulan').val('');
            $('#docking-tahun').val('');
            $('#docking-id_kapal').val('');
            $('#docking-tgl_mulai').val('');
            $('#docking-tgl_selesai').val('');
            $('#docking-durasi').val('');
            $('#docking-file').val('');
            $('#btn-save-docking').data('mode', 'create').data('id', '');
        };

        $('#btn-add-docking').on('click', function () {     
            resetForm();
            $('#modal-docking').modal('show');
        });

        $('#btn-save-docking').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            let formData = new FormData();

            formData.append('bulan', $('#docking-bulan').val());
            formData.append('tahun', $('#docking-tahun').val());
            formData.append('tempat', $('#docking-tempat').val());
            formData.append('id_kapal', $('#docking-id_kapal').val());
            formData.append('tgl_mulai', $('#docking-tgl_mulai').val());
            formData.append('tgl_selesai', $('#docking-tgl_selesai').val());
            formData.append('durasi', $('#docking-durasi').val());

            let file = $('#docking-file')[0].files[0];
            if (file) {
                formData.append('file', file);
            }

            const ajaxOpts = {
                url: mode === 'edit'
                    ? '{{ url('data_teknik/docking') }}/' + id
                    : '{{ route('docking.store') }}',
                type: mode === 'edit' ? 'POST' : 'POST', 
                data: formData,
                processData: false,
                contentType: false
            };
            if (mode === 'edit') {
                formData.append('_method', 'PUT');
            }

            $.ajax(ajaxOpts)
            .done(res => {
                Swal.fire(res.status, res.message, res.status)
                    .then(() => {
                        if (res.status === 'success') {
                            $('#modal-docking').modal('hide');
                            table.ajax.reload(null, false);
                        }
                    });
            })
            // .done(() => {
            //     Swal.fire('Sukses', mode === 'edit' ? 'Pelabuhan diperbarui' : 'Pelabuhan ditambahkan', 'success');
            //     $('#modal-docking').modal('hide');
            //     table.ajax.reload(null, false);
            // })
            // .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
            .fail(xhr => {
                Swal.fire(
                    'Gagal',
                    xhr.responseJSON?.message || 'Error',
                    'error'
                );
            });
        });

        $(document).on('click', '.btn-edit-docking', function () {
            const btn = $(this);
            $('#modal-docking-label').text('Edit Data');
            $('#docking-bulan').val(btn.data('bulan'));
            $('#docking-tahun').val(btn.data('tahun'));
            $('#docking-tempat').val(btn.data('tempat'));
            $('#docking-id_kapal').val(btn.data('id_kapal'));
            $('#docking-tgl_mulai').val(btn.data('tgl_mulai'));
            $('#docking-tgl_selesai').val(btn.data('tgl_selesai'));
            $('#docking-durasi').val(btn.data('durasi'));
            $('#btn-save-docking').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-docking').modal('show');
        });

        $(document).on('click', '.btn-delete-docking', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_teknik/docking') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Data docking berhasil dihapus', 'success');
                        table.ajax.reload(null, false);                    
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });

        document.getElementById('docking-tgl_selesai').addEventListener('change', hitungHari);
        document.getElementById('docking-tgl_mulai').addEventListener('change', hitungHari);

        function hitungHari() {
            const tglMulai   = document.getElementById('docking-tgl_mulai').value;
            const tglSelesai = document.getElementById('docking-tgl_selesai').value;

            if (!tglMulai || !tglSelesai) {
                document.getElementById('docking-durasi').value = '';
                return;
            }

            let start = new Date(tglMulai);
            let end   = new Date(tglSelesai);

            if (end < start) {
                document.getElementById('docking-durasi').value = 0;
                return;
            }

            let totalHari = 0;

            while (start <= end) {
                totalHari++;
                start.setDate(start.getDate() + 1);
            }

            document.getElementById('docking-durasi').value = totalHari;
        }
    });
</script>
@endsection
