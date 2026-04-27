@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - DIvisi</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-divisi">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-divisi" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-divisi" tabindex="-1" aria-labelledby="modal-divisi-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-divisi-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama Divisi</label>
                    <input type="text" id="divisi-nama" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-divisi">Simpan</button>
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

        const table = $('#table-divisi').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('divisi.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-divisi-label').text('Tambah Data');
            $('#divisi-nama').val('');
            $('#btn-save-divisi').data('mode', 'create').data('id', '');
        };

        $('#btn-add-divisi').on('click', function () {
            resetForm();
            $('#modal-divisi').modal('show');
        });

        $('#btn-save-divisi').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#divisi-nama').val(),
                id_cabang: $('#divisi-id_cabang').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/divisi') }}/' + id : '{{ route('divisi.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'divisi diperbarui' : 'divisi ditambahkan', 'success');
                $('#modal-divisi').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-divisi', function () {
            const btn = $(this);
            $('#modal-divisi-label').text('Edit Data');
            $('#divisi-nama').val(btn.data('nama'));
            $('#btn-save-divisi').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-divisi').modal('show');
        });

        $(document).on('click', '.btn-delete-divisi', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus divisi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_master/divisi') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'divisi berhasil dihapus', 'success');
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
