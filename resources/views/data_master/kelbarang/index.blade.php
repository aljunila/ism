@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - Kelompok Barang</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-kel">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-kel" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelompok</th>
                    <th>Nama</th>
                    <th>Kode/Seri</th>
                    <th>Keterangan/Merk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-kel" tabindex="-1" aria-labelledby="modal-kel-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-kel-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Kategori</label>
                    <select id="kel-kategori" class="form-control">
                        <option value="1">Deck</option>
                        <option value="2">Mesin</option>
                        <option value="3">KElistrikan</option>
                        <option value="4">Alat Kebersihan</option>
                    </select>
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama</label>
                    <input type="text" id="kel-nama" class="form-control">
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Kode/Seri</label>
                    <input type="text" id="kel-kode" class="form-control">
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Keterangan/Merk</label>
                    <input type="text" id="kel-ket" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-kel">Simpan</button>
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

        const table = $('#table-kel').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('kelbarang.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { 
                    data: 'kategori',
                    render: function(data, type, row) {
                        if(data==1){
                            return `Deck`;
                        } else if(data==2){
                            return `Mesin`;
                        } else if(data==3){
                            return `Kelistrikan`;
                        } else {
                            return `Alat Kebersihan`;
                        }
                    }
                },
                { data: 'nama', name: 'nama' },
                { data: 'kode', name: 'kode' },
                { data: 'ket', name: 'ket' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-kel-label').text('Tambah Data');
            $('#kel-nama').val('');
            $('#kel-kode').val('');
            $('#kel-ket').val('');
            $('#kel-kategori').val('');
            $('#btn-save-kel').data('mode', 'create').data('id', '');
        };

        $('#btn-add-kel').on('click', function () {
            resetForm();
            $('#modal-kel').modal('show');
        });

        $('#btn-save-kel').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                nama: $('#kel-nama').val(),
                kode: $('#kel-kode').val(),
                ket: $('#kel-ket').val(),
                kategori: $('#kel-kategori').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/kelbarang') }}/' + id : '{{ route('kelbarang.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'data diperbarui' : 'data ditambahkan', 'success');
                $('#modal-kel').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-kel', function () {
            const btn = $(this);
            $('#modal-kel-label').text('Edit Data');
            $('#kel-nama').val(btn.data('nama'));
            $('#kel-kode').val(btn.data('kode'));
            $('#kel-ket').val(btn.data('ket'));
            $('#kel-kategori').val(btn.data('kategori'));
            $('#btn-save-kel').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-kel').modal('show');
        });

        $(document).on('click', '.btn-delete-kel', function () {
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
                    url: '{{ url('data_master/kelbarang') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'data berhasil dihapus', 'success');
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
