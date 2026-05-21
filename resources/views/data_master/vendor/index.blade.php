@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - Vendor</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-vendor">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-vendor" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Cabang</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Telp</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-vendor" tabindex="-1" aria-labelledby="modal-vendor-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-vendor-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama vendor</label>
                    <input type="text" id="vendor-nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Alamat</label>
                    <input type="text" id="vendor-alamat" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">No Telp</label>
                    <input type="text" id="vendor-telp" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Cabang</label>
                    <select id="vendor-id_cabang" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($cabang as $c)
                            <option value="{{$c->id}}">{{$c->cabang}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-vendor">Simpan</button>
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

        const table = $('#table-vendor').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('vendor.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'cabang', name: 'cabang' },
                { data: 'nama', name: 'nama' },
                { data: 'alamat', name: 'alamat' },
                { data: 'telp', name: 'telp' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-vendor-label').text('Tambah Data');
            $('#vendor-nama').val('');
            $('#vendor-alamat').val('');
            $('#vendor-telp').val('');
            $('#vendor-id_cabang').val('');
            $('#btn-save-vendor').data('mode', 'create').data('id', '');
        };

        $('#btn-add-vendor').on('click', function () {
            resetForm();
            $('#modal-vendor').modal('show');
        });

        $('#btn-save-vendor').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#vendor-nama').val(),
                alamat: $('#vendor-alamat').val(),
                telp: $('#vendor-telp').val(),
                id_cabang: $('#vendor-id_cabang').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/vendor') }}/' + id : '{{ route('vendor.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'vendor diperbarui' : 'vendor ditambahkan', 'success');
                $('#modal-vendor').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-vendor', function () {
            const btn = $(this);
            $('#modal-vendor-label').text('Edit Data');
            $('#vendor-nama').val(btn.data('nama'));
            $('#vendor-alamat').val(btn.data('alamat'));
            $('#vendor-telp').val(btn.data('telp'));
            $('#vendor-id_cabang').val(btn.data('id_cabang'));
            $('#btn-save-vendor').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-vendor').modal('show');
        });

        $(document).on('click', '.btn-delete-vendor', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus vendor ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_master/vendor') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Vendor berhasil dihapus', 'success');
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
