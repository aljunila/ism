@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - Kendaraan (Penumpang)</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-kend">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-kend" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-kend" tabindex="-1" aria-labelledby="modal-kend-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-kend-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama Kendaraan</label>
                    <input type="text" id="kend-nama" class="form-control">
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Kode</label>
                    <input type="text" id="kend-kode" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-kend">Simpan</button>
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

        const table = $('#table-kend').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('kendaraan.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'kode', name: 'kode' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-kend-label').text('Tambah Cabang');
            $('#kend-nama').val('');
            $('#kend-kode').val('');
            $('#btn-save-kend').data('mode', 'create').data('id', '');
        };

        $('#btn-add-kend').on('click', function () {
            resetForm();
            $('#modal-kend').modal('show');
        });

        $('#btn-save-kend').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#kend-nama').val(),
                kode: $('#kend-kode').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/kendaraan') }}/' + id : '{{ route('kendaraan.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Gol Kendaraan diperbarui' : 'Gol Kendaraan ditambahkan', 'success');
                $('#modal-kend').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-kend', function () {
            const btn = $(this);
            $('#modal-kend-label').text('Edit Data');
            $('#kend-nama').val(btn.data('nama'));
            $('#kend-kode').val(btn.data('kode'));
            $('#btn-save-kend').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-kend').modal('show');
        });

        $(document).on('click', '.btn-delete-kend', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Golongan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_master/kendaraan') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Golongan berhasil dihapus', 'success');
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
