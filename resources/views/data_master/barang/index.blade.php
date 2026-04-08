@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - Barang</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-barang">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-barang" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelompok</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-barang" tabindex="-1" aria-labelledby="modal-barang-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-barang-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Kelompok Barang</label>
                    <select id="barang-id_kel_barang" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($kelompok as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Kode</label>
                    <input type="text" id="barang-kode" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Nama</label>
                    <input type="text" id="barang-nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Satuan</label>
                    <input type="text" id="barang-deskripsi" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-barang">Simpan</button>
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

        const table = $('#table-barang').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('barang.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kelompok', name: 'kelompok' },
                { data: 'kode', name: 'kode' },
                { data: 'nama', name: 'nama' },
                { data: 'deskripsi', name: 'deskripsi' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-barang-label').text('Tambah Data');
            $('#barang-nama').val('');
            $('#barang-kode').val('');
            $('#barang-deskripsi').val('');
            $('#barang-id_kel_barang').val('');
            $('#btn-save-barang').data('mode', 'create').data('id', '');
        };

        $('#btn-add-barang').on('click', function () {
            console.log('aaaa');
            
            resetForm();
            $('#modal-barang').modal('show');
        });

        $('#btn-save-barang').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#barang-nama').val(),
                kode: $('#barang-kode').val(),
                deskripsi: $('#barang-deskripsi').val(),
                id_kel_barang: $('#barang-id_kel_barang').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/barang') }}/' + id : '{{ route('barang.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(res => {
                Swal.fire(res.status, res.message, res.status)
                    .then(() => {
                        if (res.status === 'success') {
                            $('#modal-barang').modal('hide');
                            table.ajax.reload(null, false);
                        }
                    });
            })
            // .done(() => {
            //     Swal.fire('Sukses', mode === 'edit' ? 'Pelabuhan diperbarui' : 'Pelabuhan ditambahkan', 'success');
            //     $('#modal-barang').modal('hide');
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

        $(document).on('click', '.btn-edit-barang', function () {
            const btn = $(this);
            $('#modal-barang-label').text('Edit Data');
            $('#barang-nama').val(btn.data('nama'));
            $('#barang-kode').val(btn.data('kode'));
            $('#barang-deskripsi').val(btn.data('deskripsi'));
            $('#barang-id_kel_barang').val(btn.data('id_kel_barang'));
            $('#btn-save-barang').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-barang').modal('show');
        });

        $(document).on('click', '.btn-delete-barang', function () {
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
                    url: '{{ url('data_master/barang') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Data barang berhasil dihapus', 'success');
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
