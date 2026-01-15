@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Master - Biaya Penumpang</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-biaya">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-biaya" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pelabuhan</th>
                    <th>Kelas</th>
                    <th>Golongan</th>
                    <th>Nominal (RP)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-biaya" tabindex="-1" aria-labelledby="modal-biaya-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-biaya-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label"> Pelabuhan</label>
                    <select id="biaya-id_pelabuhan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($pelabuhan as $p)
                            <option value="{{$p->id}}">{{$p->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Kelas</label>
                    <select id="biaya-kelas" class="form-control">
                        <option value="">-Pilih-</option>
                        <option value="Reguler">Reguler</option>
                        <option value="Express">Express</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Golongan</label>
                    <select id="biaya-id_kendaraan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($kendaraan as $k)
                            <option value="{{$k->id}}">{{$k->kode}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Nominal/Biaya</label>
                    <input type="number" id="biaya-nominal" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-biaya">Simpan</button>
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

        const table = $('#table-biaya').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('biaya.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'pelabuhan', name: 'pelabuhan' },
                { data: 'kelas', name: 'kelas' },
                { data: 'kendaraan', name: 'kendaraan' },
                { data: 'nominal', name: 'nominal' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-biaya-label').text('Tambah Data');
            $('#biaya-nominal').val('');
            $('#biaya-kelas').val('');
            $('#biaya-id_pelabuhan').val('');
            $('#biaya-id_kendaraan').val('');
            $('#btn-save-biaya').data('mode', 'create').data('id', '');
        };

        $('#btn-add-biaya').on('click', function () {
            resetForm();
            $('#modal-biaya').modal('show');
        });

        $('#btn-save-biaya').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                kelas: $('#biaya-kelas').val(),
                id_pelabuhan: $('#biaya-id_pelabuhan').val(),
                id_kendaraan: $('#biaya-id_kendaraan').val(),
                nominal: $('#biaya-nominal').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_master/biaya') }}/' + id : '{{ route('biaya.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Biaya diperbarui' : 'Biaya ditambahkan', 'success');
                $('#modal-biaya').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-biaya', function () {
            const btn = $(this);
            $('#modal-biaya-label').text('Edit Data');
            $('#biaya-kelas').val(btn.data('kelas'));
            $('#biaya-nominal').val(btn.data('nominal'));
            $('#biaya-id_pelabuhan').val(btn.data('id_pelabuhan'));
            $('#biaya-id_kendaraan').val(btn.data('id_kendaraan'));
            $('#btn-save-biaya').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-biaya').modal('show');
        });

        $(document).on('click', '.btn-delete-biaya', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Biaya ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_master/biaya') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Biaya berhasil dihapus', 'success');
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
