@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Crew - Cuti</h4>
        <a type="button" href="/data_crew/cuti/form" class="btn btn-primary btn-sm">Tambah Data</a>
         <!-- <button class="btn btn-primary btn-sm" id="btn-add-cuti">Tambah Data</button> -->
    </div>
    <div class="card-body">
        <table id="table-cuti" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jenis Cuti</th>
                    <th>Tanggal Cuti</th>
                    <th>Jml Hari</th>
                    <th>Pengganti</th>
                    <th>Status</th>
                    <th>Diproses Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-cuti" tabindex="-1" aria-labelledby="modal-cuti-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-cuti-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1 row">
                    <div class="col-sm-3">
                        <label class="col-form-label" for="first-name">Jenis Cuti</label>
                    </div>
                    <div class="col-sm-9">
                        <select name="cuti-id_m_cuti" id="cuti-id_m_cuti"  class="form-control">
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
                        <select name="cuti-id_karyawan" id="cuti-id_karyawan" class="form-control">
                            <option value="">Pilih</option>
                            @foreach($karyawan as $k)
                                <option value="{{$k->id}}">{{$k->nama}}</option>
                            @endforeach
                        </select>
                        <select name="cuti-id_kapal" id="cuti-id_kapal" class="form-control">
                            <option value="">Pilih</option>
                            @foreach($kapal as $kp)
                                <option value="{{$kp->id}}">{{$kp->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-1 row">
                    <div class="col-sm-3">
                        <label class="col-form-label" for="first-name">Tgl Cuti</label>
                    </div>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" name="cuti-tgl_mulai" id="cuti-tgl_mulai">
                    </div>
                    <div class="col-sm-1">-</div>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" name="cuti-tgl_selesai" id="cuti-tgl_selesai">
                    </div>
                </div>
                <div class="mb-1 row">
                    <div class="col-sm-3">
                        <label class="col-form-label" for="first-name">Total Hari</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="cuti-jml_hari" id="cuti-jml_hari">
                    </div>
                </div>
                <div class="mb-1 row">
                    <div class="col-sm-3">
                        <label class="col-form-label" for="first-name">Keterangan</label>
                    </div>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="cuti-note" id="cuti-note"></textarea>
                    </div>
                </div>
                <div class="mb-1 row">
                    <div class="col-sm-3">
                        <label class="col-form-label" for="first-name">Pengganti</label>
                    </div>
                    <div class="col-sm-9">
                        <select name="cuti-id_pengganti" id="cuti-id_pengganti" class="form-control">
                            <option value="">Pilih</option>
                            @foreach($karyawan as $k)
                                <option value="{{$k->id}}">{{$k->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="btn-save-cuti">Setujui</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="DetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Crew Cuti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableDetail" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
<script src="{{ url('/app-assets/vendors/js/tom-select.min.js') }}"></script>
<script>
    $(function () {
        $('#cuti-id_kapal').hide();
        $('#cuti-id_karyawan').hide();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-cuti').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('cuti.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },     
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        if(row.data) {
                        return `${row.kapal} <button type="button"  onclick="openDetail(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Crew">
                        Detail Crew</button>`;
                        } else {
                        return `${row.karyawan}`;
                        }
                    }
                },            
                { data: 'jenis', name: 'jenis' },
                {
                    data: null,
                    render: row => `
                        <div>
                            <div>${formatTgl(row.tgl_mulai)} s/d ${formatTgl(row.tgl_selesai)}</div>
                        </div>
                    `
                },
                { data: 'jml_hari', name: 'jml_hari' },
                { data: 'pengganti', name: 'pengganti' },
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
                { data: 'approval' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        // new TomSelect('#cuti-id_karyawan', {
        //     placeholder: 'Karyawan...',
        //     allowEmptyOption: true,
        //     maxItems: 1,
        //     searchField: ['text'],   // bisa diketik
        //     create: false            // tidak boleh input baru
        // });

         $(document).on('change', '#cuti-id_m_cuti', function() {
            let id_m_cuti = $(this).val();
            if(id_m_cuti==9){
                $('#cuti-id_kapal').show();
                $('#cuti-id_karyawan').hide();
            } else {
                $('#cuti-id_kapal').hide();
                $('#cuti-id_karyawan').show();
            }
        })

        $('#btn-add-cuti').on('click', function () {
            resetForm();
            $('#modal-cuti').modal('show');
        });

        $('#btn-save-cuti').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                id_karyawan: $('#cuti-id_karyawan').val(),
                id_m_cuti: $('#cuti-id_m_cuti').val(),
                tgl_mulai: $('#cuti-tgl_mulai').val(),
                tgl_selesai: $('#cuti-tgl_selesai').val(),
                jml_hari: $('#cuti-jml_hari').val(),
                note: $('#cuti-note').val(),
                id_pengganti: $('#cuti-id_pengganti').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/cuti') }}/' + id : '{{ route('cuti.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(res => Swal.fire(res.status, res.message, res.status)
                .then(() => res.status === 'success' && (
                    $('#modal-cuti').modal('hide'),
                    table.ajax.reload(null, false)
                ))
            )
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Error', 'error'));
        });

        $(document).on('click', '.btn-edit-cuti', function () {
            const btn = $(this);
            $('#modal-cuti-label').text('Edit Data');
            $('#cuti-id_karyawan').val(btn.data('id_karyawan')).trigger('change').prop('disabled', true);
            $('#cuti-id_kapal').val(btn.data('id_kapal')).trigger('change').prop('disabled', true);
            $('#cuti-id_m_cuti').val(btn.data('id_m_cuti')).trigger('change').prop('disabled', true);
            $('#cuti-tgl_mulai').val(btn.data('tgl_mulai'));
            $('#cuti-tgl_selesai').val(btn.data('tgl_selesai'));
            $('#cuti-jml_hari').val(btn.data('jml_hari'));
            $('#cuti-note').val(btn.data('note')).prop('disabled', true);
            $('#cuti-id_pengganti').val(btn.data('id_pengganti')).trigger('change');
            $('#btn-save-cuti').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-cuti').modal('show');
        });

        $(document).on('click', '.btn-delete-cuti', function () {
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
                    url: '{{ url('data_crew/cuti') }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                })
                .done(res => {
                    Swal.fire(res.status, res.message, res.status)
                        .then(() => {
                            if (res.status === 'success') {
                                $('#modal-cuti').modal('hide');
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
        });

        $(document).on('click', '.btn-reject-cuti', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Apakah pengajuan cuti ditolak?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_crew/cuti/reject') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Tertolak', 'Pengajuan cuti berhasil ditolak', 'success');
                        table.ajax.reload(null, false);                  
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });
    });

     function openDetail(id) {
        currentId = id;
        $('#DetailModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetail')) {
            DetailTable.ajax.url(`cuti/get/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetail').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `cuti/get/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: 'nama',
                },
                {
                    data: 'jabatan',
                }
            ]
        });
    }
</script>
@endsection
