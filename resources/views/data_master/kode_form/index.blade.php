@extends('main')
@section('scriptheader')
  <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Vendor CSS-->
@endsection

@section('content')
<div class="table-responsive">
    <div class="card">
        <div class="card-header">
            <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;">
                <h3><b>Master Kode Form</b></h3>

                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambah_master_kode">Tambah Data</button>
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Form</th>
                        <th>Nama form</th>
                        <th>Instruksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade text-start" id="tambah_master_kode" tabindex="-1" aria-labelledby="modal-tambah_master_kode" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-tambah_master_kode">Tambah Master</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <label class="form-label" for="kode_form">Kode Form</label>
                        <input type="text" class="form-control" name="kode_form" id="kode_form" placeholder="Contoh: EL-01-01">
                    </div>
                    <div class="col-md-6 mb-1">
                        <label class="form-label" for="keterangan_kode">Keterangan (otomatis)</label>
                        <input type="text" class="form-control" id="keterangan_kode" readonly>
                    </div>
                    <div class="col-md-12 mb-1">
                        <label class="form-label" for="nama_form">Nama Form</label>
                        <input type="text" class="form-control" name="nama_form" id="nama_form" placeholder="Masukkan nama form">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="instruksi">Instruksi</label>
                        <textarea class="form-control tinymce" id="instruksi" name="instruksi"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="tambah_data">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptfooter')
<!-- jQuery -->
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ url('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->

<script>
    $(function () {
        const formatKode = (val) => (val || '').replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
        const decodeHtml = (html) => $('<textarea/>').html(html || '').text();

        $('#kode_form').on('input', function () {
            $('#keterangan_kode').val(formatKode($(this).val()));
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('kode_form.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'nama', name: 'nama' },
                {
                    data: 'intruksi',
                    name: 'intruksi',
                    render: function (data) {
                        const text = $('<div>').html(data || '').text();
                        return text.length > 80 ? text.substring(0, 80) + '…' : text;
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#kode_form, #nama_form, #keterangan_kode').val('');
            tinymce.get('instruksi')?.setContent('');
            $('#tambah_data').data('mode', 'create').data('id', '');
        };

        $('#tambah_master_kode').on('hidden.bs.modal', resetForm);

        $('#tambah_data').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const instruksi = tinymce.get('instruksi')?.getContent() || '';

            const payload = {
                kode: $('#kode_form').val(),
                nama: $('#nama_form').val(),
                ket: $('#keterangan_kode').val(),
                intruksi: instruksi
            };

            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url("data_master/kode_form") }}/' + id : '{{ route('kode_form.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload,
                success: function () {
                    Swal.fire('Sukses', 'Data berhasil disimpan', 'success');
                    $('#tambah_master_kode').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            };

            $.ajax(ajaxOpts);
        });

        $(document).on('click', '.btn-edit', function () {
            const btn = $(this);
            $('#kode_form').val(btn.data('kode'));
            $('#keterangan_kode').val(btn.data('ket'));
            $('#nama_form').val(btn.data('nama'));
            const intruksiHtml = decodeHtml(btn.data('intruksi'));
            tinymce.get('instruksi')?.setContent(intruksiHtml || '');
            $('#tambah_data').data('mode', 'edit').data('id', btn.data('id'));
            $('#tambah_master_kode').modal('show');
        });

        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus data ini?',
                text: 'Tindakan ini tidak dapat dibatalkan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url("data_master/kode_form") }}/' + id,
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
