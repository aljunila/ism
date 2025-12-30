@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - Pelabuhan</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-pel">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-pel" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Cabang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-pel" tabindex="-1" aria-labelledby="modal-pel-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-pel-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama Pelabuhan</label>
                    <input type="text" id="pel-nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Cabang</label>
                    <select id="pel-id_cabang" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($cabang as $c)
                            <option value="{{$c->id}}">{{$c->cabang}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-pel">Simpan</button>
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

        const table = $('#table-pel').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('pelabuhan.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'cabang', name: 'cabang' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-pel-label').text('Tambah Data');
            $('#pel-nama').val('');
            $('#pel-id_cabang').val('');
            $('#btn-save-pel').data('mode', 'create').data('id', '');
        };

        $('#btn-add-pel').on('click', function () {
            resetForm();
            $('#modal-pel').modal('show');
        });

        $('#btn-save-pel').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#pel-nama').val(),
                id_cabang: $('#pel-id_cabang').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/pelabuhan') }}/' + id : '{{ route('pelabuhan.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Pelabuhan diperbarui' : 'Pelabuhan ditambahkan', 'success');
                $('#modal-pel').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-pel', function () {
            const btn = $(this);
            $('#modal-pel-label').text('Edit Data');
            $('#pel-nama').val(btn.data('nama'));
            $('#pel-id_cabang').val(btn.data('id_cabang'));
            $('#btn-save-pel').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-pel').modal('show');
        });

        $(document).on('click', '.btn-delete-pel', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Pelabuhan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_master/pelabuhan') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Pelabuhan berhasil dihapus', 'success');
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
