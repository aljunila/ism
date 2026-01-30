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
        <h4 class="card-title">Crew - Mutation</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-mutasi">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-mutasi" class="table table-striped w-100">
            <thead>
                <tr>
                    <th width="5%" rowspan="2">No.</th>
                    <th width="25%" rowspan="2">Nama</th>
                    <th width="10%" rowspan="2">Jabatan</th>
                    <th width="20%" colspan="2">Mutasi</th>
                    <th width="10%" rowspan="2">Tgl Naik</th>
                    <th width="10%" rowspan="2">Tgl Turun</th>
                    <th width="15%" rowspan="2">Keterangan</th>
                    <th width="5%" rowspan="2">Aksi</th>
                </tr>
                <tr>
                    <th>Dari</th>
                    <th>Ke</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-mutasi" tabindex="-1" aria-labelledby="modal-mutasi-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-mutasi-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Karyawan</label>
                    <select id="mutasi-id_karyawan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Mutasi ke</label>
                    <select id="mutasi-ke_perusahaan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($perusahaan as $p)
                            <option value="{{$p->id}}">{{$p->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Ditempatkan di</label>
                    <select id="mutasi-ke_kapal" class="form-control">
                        <option value="">-Pilih-</option>
                         @foreach($kapal as $kp)
                            <option value="{{$kp->id}}">{{$kp->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal Naik</label>
                    <input type="date" id="mutasi-tgl_naik" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal Turun</label>
                    <input type="date" id="mutasi-tgl_turun" class="form-control">
                </div>
                 <div class="mb-1">
                    <label class="form-label">Keterangan</label>
                    <textarea id="mutasi-keterangan" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-mutasi">Simpan</button>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-mutasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('mutasi.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'jabatan', name: 'jabatan' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `${row.dari_perusahaan} <br> ${row.dari_kapal}`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `${row.ke_perusahaan} <br> ${row.ke_kapal}`;
                    }
                },
                { data: 'tgl_naik', name: 'tgl_naik' },
                { data: 'tgl_turun', name: 'tgl_turun' },
                { data: 'ket', name: 'ket' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        new TomSelect('#mutasi-id_karyawan', {
            placeholder: 'Karyawan...',
            allowEmptyOption: true,
            maxItems: 1,
            searchField: ['text'],   // bisa diketik
            create: false            // tidak boleh input baru
        });

        $('#mutasi-ke_perusahaan').on('click', function () {
        var perusahaanID = $(this).val();
        if (perusahaanID) {
            $.ajax({
                url: '/get-kapal/' + perusahaanID,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#mutasi-ke_kapal').empty().append('<option value="">Semua</option>');           
                    $.each(data, function(key, value) {
                        $('#mutasi-ke_kapal').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                    });
                    table.ajax.reload();
                }
            });
        } else {
            $('#mutasi-ke_kapal').empty().append('<option value="">Tidak ada data</option>');
            table.ajax.reload();
        }
    });

        const resetForm = () => {
            $('#modal-mutasi-label').text('Tambah Data');
            $('#mutasi-id_karyawan').val('');
            $('#mutasi-ke_perusahaan').val('');
            $('#mutasi-ke_kapal').val('');
            $('#mutasi-tgl_naik').val('');
            $('#mutasi-tgl_turun').val('');
            $('#mutasi-keterangan').val('');
            $('#btn-save-mutasi').data('mode', 'create').data('id', '');
        };


        $('#btn-add-mutasi').on('click', function () {
            resetForm();
            $('#modal-mutasi').modal('show');
        });

        $('#btn-save-mutasi').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                id_karyawan: $('#mutasi-id_karyawan').val(),
                ke_perusahaan: $('#mutasi-ke_perusahaan').val(),
                ke_kapal: $('#mutasi-ke_kapal').val(),
                tgl_naik: $('#mutasi-tgl_naik').val(),
                tgl_turun: $('#mutasi-tgl_turun').val(),
                keterangan: $('#mutasi-keterangan').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/mutasi') }}/' + id : '{{ route('mutasi.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-mutasi').modal('hide');
                table.ajax.reload(null, false);
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-mutasi', function () {
            const btn = $(this);
            $('#modal-mutasi-label').text('Edit Data');
            $('#mutasi-id_karyawan').val(btn.data('id_karyawan'));
            $('#mutasi-ke_perusahaan').val(btn.data('ke_perusahaan'));
            $('#mutasi-ke_kapal').val(btn.data('ke_kapal'));
            $('#mutasi-tgl_naik').val(btn.data('tgl_naik'));
            $('#mutasi-tgl_turun').val(btn.data('tgl_turun'));
            $('#mutasi-keterangan').val(btn.data('keterangan'));
            $('#btn-save-mutasi').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-mutasi').modal('show');
        });

        $(document).on('click', '.btn-delete-mutasi', function () {
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
                    url: '{{ url('data_crew/mutasi') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Data berhasil dihapus', 'success');
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
