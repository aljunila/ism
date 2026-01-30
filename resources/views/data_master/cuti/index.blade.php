@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - Jenis Cuti</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-cuti">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-cuti" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Cuti</th>
                    <th>Jumlah Hari</th>
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
                <div class="mb-1">
                    <label class="form-label">Nama Cuti</label>
                    <input type="text" id="cuti-nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Total Hari</label>
                    <input type="number" id="cuti-jumlah" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-cuti">Simpan</button>
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

        const table = $('#table-cuti').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('mcuti.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-cuti-label').text('Tambah Data');
            $('#cuti-nama').val('');
            $('#cuti-jumlah').val('');
            $('#btn-save-cuti').data('mode', 'create').data('id', '');
        };

        $('#btn-add-cuti').on('click', function () {
            resetForm();
            $('#modal-cuti').modal('show');
        });

        $('#btn-save-cuti').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#cuti-nama').val(),
                jumlah: $('#cuti-jumlah').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/mcuti') }}/' + id : '{{ route('mcuti.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Pelabuhan diperbarui' : 'Pelabuhan ditambahkan', 'success');
                $('#modal-cuti').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-cuti', function () {
            const btn = $(this);
            $('#modal-cuti-label').text('Edit Data');
            $('#cuti-nama').val(btn.data('nama'));
            $('#cuti-jumlah').val(btn.data('jumlah'));
            $('#btn-save-cuti').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-cuti').modal('show');
        });

        $(document).on('click', '.btn-delete-cuti', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus jenis cuti ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_master/mcuti') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Jenis Cuti berhasil dihapus', 'success');
                        table.ajax.reload(null, false);
                        loadCabang                    
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
