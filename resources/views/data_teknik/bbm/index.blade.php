@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="col-sm-12"><h4 class="card-title">Pemakaian BBM</h4></div>
        <div class="col-sm-3">
            <select name="id_kapal" id="id_kapal" class="form-control">
                <option value="">Pilih Kapal</option>
                @foreach ($kapal as $k)
                    <option value="{{$k->id}}">{{$k->nama}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
            <select name="jenis" id="jenis" class="form-control">
                <option value="">Pilih Jenis</option>\
                    <option value="1">Pemakaian</option>
                    <option value="2">Penambahan</option>
                    <option value="3">Penyesuaian</option>
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
                    <th>Status</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Saldo Akhir</th>
                    <th>Jam Operasional</th>
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
                            <label class="form-label">Status</label>
                        </div>
                        <div class="col-8">
                            <select id="bbm-status" class="form-control">
                                <option value="">Pilih</option>
                                <option value="1">In Operation</option>
                                <option value="2">Anchorage</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Tanggal</label>
                        </div>
                        <div class="col-8">
                            <input type="date" id="bbm-tanggal" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Jenis</label>
                        </div>
                        <div class="col-8">
                            <select id="bbm-id_jenis" class="form-control">
                                <option value="">Pilih</option>
                                <option value="1">Pemakaian</option>
                                <option value="2">Penambahan</option>
                                <option value="3">Penyesuaian</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="jenis_pakai">
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Pemakaian (Liter)</label>
                        </div>
                        <div class="col-4">
                             <input type="number" id="bbm-me" class="form-control" placeholder="ME">
                        </div>
                        <div class="col-4">
                             <input type="number" id="bbm-ae" class="form-control" placeholder="AE">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Jam Operational</label>
                        </div>
                        <div class="col-8">
                            <input type="number" id="bbm-jam" class="form-control">
                        </div>
                    </div>
                </div>
                </div>
                <div class="mb-1" id="jumlah">
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
        $('#jenis_pakai').hide();
        $('#jumlah').hide();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#id_kapal').on('change', function () {
         table.ajax.reload();
        });

        $('#jenis').on('change', function () {
         table.ajax.reload();
        });

        $('#tanggal').on('change', function () {
         table.ajax.reload();
        });

        const table = $('#table-bbm').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/data_teknik/bbm/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.jenis= $('#jenis').val(),
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
                    render: function(data) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                },
                { 
                    data: 'status', 
                    name : 'status',
                    render: function (data, type, row) {
                        if (data == 1) return 'In Operation';
                        if (data == 2) return 'Anchorage';
                        return '-';
                    }
                },
                { 
                    data: 'id_jenis', 
                    name : 'id_jenis',
                    render: function (data, type, row) {
                        if (data == 1) return 'Pemakaian'
                        if (data == 2) return 'Penambahan';
                        if (data == 3) return 'Penyesuaian';
                        return '-';
                    }
                },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'saldo_akhir', name: 'saldo_akhir' },
                { data: 'jam', name: 'jam' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-bbm-label').text('Tambah Data');
            $('#bbm-id_kapal').val('');
            $('#bbm-status').val('');
            $('#bbm-tanggal').val('');
            $('#bbm-id_jenis').val('');
            $('#bbm-me').val('');
            $('#bbm-ae').val('');
            $('#bbm-jumlah').val('');
            $('#bbm-jam').val('');
            $('#btn-save-bbm').data('mode', 'create').data('id', '');
        };

        $('#btn-add-bbm').on('click', function () {     
            resetForm();
            $('#modal-bbm').modal('show');
            $('#jenis_pakai').hide();
            $('#jumlah').hide();
        });

        $('#btn-save-bbm').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            let formData = new FormData();

            formData.append('status', $('#bbm-status').val());
            formData.append('tanggal', $('#bbm-tanggal').val());
            formData.append('id_jenis', $('#bbm-id_jenis').val());
            formData.append('id_kapal', $('#bbm-id_kapal').val());
            formData.append('me', $('#bbm-me').val());
            formData.append('ae', $('#bbm-ae').val());
            formData.append('jumlah', $('#bbm-jumlah').val());
            formData.append('jam', $('#bbm-jam').val());
            const ajaxOpts = {
                url: mode === 'edit'
                    ? '{{ url('data_teknik/bbm') }}/' + id
                    : '{{ route('bbm.store') }}',
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

        $(document).on('click', '.btn-edit-bbm', function () {
            const btn = $(this);
            $('#modal-bbm-label').text('Edit Data');
            $('#bbm-status').val(btn.data('status'));
            $('#bbm-tanggal').val(btn.data('tanggal'));
            $('#bbm-id_jenis').val(btn.data('id_jenis'));
            $('#bbm-id_kapal').val(btn.data('id_kapal'));
            $('#bbm-me').val(btn.data('me'));
            $('#bbm-ae').val(btn.data('ae'));
            $('#bbm-jumlah').val(btn.data('jumlah'));
            $('#bbm-jam').val(btn.data('jam'));
            $('#btn-save-bbm').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-bbm').modal('show');
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
                    url: '{{ url('data_teknik/bbm') }}/' + id,
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

    $(document).on('change', '#bbm-id_jenis', function() {
        let jenis = $(this).val();
        if(jenis==1){
            $('#jenis_pakai').show();
            $('#jumlah').hide();
        } else {
            $('#jenis_pakai').hide();
            $('#jumlah').show();
        }
    })
</script>
@endsection
