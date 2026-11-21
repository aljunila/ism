@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="col-sm-12"><h4 class="card-title">Permintaan BBM</h4></div>
        <div class="col-sm-3">
            <select name="id_kapal" id="id_kapal" class="form-control">
                <option value="">Pilih Kapal</option>
                @foreach ($kapal as $k)
                    <option value="{{$k->id}}">{{$k->nama}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3"><input type="date" id="tanggal" class="form-control"></div>
        <div class="col-sm-2"><button class="btn btn-primary btn-sm" id="btn-add-bbm">Tambah Data</button></div>
    </div>
    <div class="card-body">
        <table id="table-bbm" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kapal</th>
                    <th>Tanggal</th>
                    <th>Lintasan</th>
                    <th>Jumlah</th>
                    <th>PDF</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-bbm" tabindex="-1" aria-labelledby="modal-bbm-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-bbm-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Kapal</label>
                        </div>
                        <div class="col-8">
                            <select id="bbm-id_kapal" class="form-control">
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
                        <div class="col-4">
                            <label class="form-label">Tanggal</label>
                        </div>
                        <div class="col-4">
                            <input type="date" id="bbm-tanggal" class="form-control">
                        </div>
                        <div class="col-4">
                            <input type="time" id="bbm-jam" class="form-control">
                        </div>
                    </div>
                </div>
                 <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Lintasan</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="bbm-lintasan" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Jarak Lintasan (Mil)</label>
                        </div>
                        <div class="col-8">
                            <input type="number" id="bbm-jarak" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Jumlah (Liter)</label>
                        </div>
                        <div class="col-8">
                            <input type="number" id="bbm-jumlah" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-bbm">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-proses" tabindex="-1" aria-labelledby="modal-proses-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-proses-label">Proses Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Tanggal</label>
                        </div>
                        <div class="col-8">
                            <input type="date" id="proses-tanggal" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Jumlah (Liter)</label>
                        </div>
                        <div class="col-8">
                            <input type="number" id="proses-jumlah" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-proses">Simpan</button>
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

        $('#tanggal').on('change', function () {
         table.ajax.reload();
        });

        const table = $('#table-bbm').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/data_logistik/bbm/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.tanggal= $('#tanggal').val(),
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
                { data: 'tanggal',
                    render: function (data, type, row) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return `${parts[2]}-${parts[1]}-${parts[0]} ${row.jam}`; 

                    }
                },
                { 
                    data: 'lintasan', 
                    name : 'lintasan',
                    render: function (data, type, row) {
                        return `${row.lintasan} (${row.jarak} Mil)`;
                    }
                },
                { data: 'jumlah', name: 'jumlah' },
                { 
                    data: 'uid', 
                    name : 'uid',
                    render: function (data, type, row) {
                        return `<a type="button" href="/data_logistik/bbm/pdf/${row.uid}" target="_blank" class="btn btn-sm btn-outline-success">PDF Permintaan</a>`;
                    }
                },
                { 
                    data: 'status', 
                    name : 'status',
                    render: function (data, type, row) {
                        if (data == 1) return 'Diajukan';
                        if (data == 2) return 'Telah ditambahkan';
                        return '-';
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-bbm-label').text('Tambah Data');
            $('#bbm-id_kapal').val('');
            $('#bbm-tanggal').val('');
            $('#bbm-jam').val('');
            $('#bbm-lintasan').val('');
            $('#bbm-jarak').val('');
            $('#bbm-jumlah').val('');
            $('#btn-save-bbm').data('mode', 'create').data('id', '');
        };

        $('#btn-add-bbm').on('click', function () {     
            resetForm();
            $('#modal-bbm').modal('show');
        });

        $('#btn-save-bbm').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            let formData = new FormData();

            formData.append('tanggal', $('#bbm-tanggal').val());
            formData.append('jam', $('#bbm-jam').val());
            formData.append('lintasan', $('#bbm-lintasan').val());
            formData.append('id_kapal', $('#bbm-id_kapal').val());
            formData.append('jarak', $('#bbm-jarak').val());
            formData.append('jumlah', $('#bbm-jumlah').val());
            const ajaxOpts = {
                url: mode === 'edit'
                    ? '{{ url('data_logistik/bbm') }}/' + id
                    : '{{ route('reqbbm.store') }}',
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
                            $('#modal-bbm').modal('hide');
                            table.ajax.reload(null, false);
                        }
                    });
            })
            // .done(() => {
            //     Swal.fire('Sukses', mode === 'edit' ? 'Pelabuhan diperbarui' : 'Pelabuhan ditambahkan', 'success');
            //     $('#modal-bbm').modal('hide');
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

        $('#btn-save-proses').on('click', function () {
            const mode = $(this).data('mode') || 'proses';
            const id = $(this).data('id');
            let formData = new FormData();

            formData.append('tanggal', $('#proses-tanggal').val());
            formData.append('jumlah', $('#proses-jumlah').val());
            const ajaxOpts = {
                url: '{{ url('data_logistik/bbm/proses') }}/' + id,
                type: 'POST', 
                data: formData,
                processData: false,
                contentType: false
            };
            $.ajax(ajaxOpts)
            .done(res => {
                Swal.fire(res.status, res.message, res.status)
                    .then(() => {
                        if (res.status === 'success') {
                            $('#modal-proses').modal('hide');
                            table.ajax.reload(null, false);
                        }
                    });
            })
            .fail(xhr => {
                Swal.fire(
                    'Gagal',
                    xhr.responseJSON?.message || 'Error',
                    'error'
                );
            });
        });

        $(document).on('click', '.btn-edit-bbm', function () {
            const btn = $(this);
            $('#modal-bbm-label').text('Edit Data');
            $('#bbm-tanggal').val(btn.data('tanggal'));
            $('#bbm-jam').val(btn.data('jam'));
            $('#bbm-id_kapal').val(btn.data('id_kapal'));
            $('#bbm-lintasan').val(btn.data('lintasan'));
            $('#bbm-jarak').val(btn.data('jarak'));
            $('#bbm-jumlah').val(btn.data('jumlah'));
            $('#btn-save-bbm').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-bbm').modal('show');
        });

         $(document).on('click', '.btn-proses-bbm', function () {
            const btn = $(this);
            $('#modal-proses-label').text('Proses Data');
            $('#proses-jumlah').val(btn.data('jumlah'));
            $('#btn-save-proses').data('id', btn.data('id'));
            $('#modal-proses').modal('show');
        });

        $(document).on('click', '.btn-delete-bbm', function () {
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
                    url: '{{ url('data_logistik/bbm') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Data bbm berhasil dihapus', 'success');
                        table.ajax.reload(null, false);                    
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });
    });
</script>
@endsection
