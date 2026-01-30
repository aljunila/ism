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
        <h4 class="card-title">Crew - Training Notes</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-pelatihan">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-pelatihan" class="table table-striped w-100">
            <thead>
                <tr>
                    <th width="5%" rowspan="2">No.</th>
                    <th width="25%" rowspan="2">Nama Peserta</th>
                    <th width="20%" rowspan="2">Nama Pelatihan</th>
                    <th width="10%" colspan="2">Waktu (Tanggal)</th>
                    <th width="10%" rowspan="2">Tempat</th>
                    <th width="15%" rowspan="2">Hasil</th>
                    <th width="5%" rowspan="2">Aksi</th>
                </tr>
                <tr>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-pelatihan" tabindex="-1" aria-labelledby="modal-pelatihan-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-pelatihan-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Karyawan</label>
                    <select id="pelatihan-id_karyawan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Nama Pelatihan</label>
                    <input type="text" id="pelatihan-nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" id="pelatihan-tgl_mulai" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" id="pelatihan-tgl_selesai" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Tempat</label>
                    <input type="text" id="pelatihan-tempat" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Hasil</label>
                    <textarea id="pelatihan-hasil" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-pelatihan">Simpan</button>
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

        const table = $('#table-pelatihan').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('pelatihan.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `${row.karyawan} <br> ${row.jabatan}`;
                    }
                },                
                { data: 'nama', name: 'nama' },
                { data: 'tgl_mulai', name: 'tgl_mulai' },
                { data: 'tgl_selesai', name: 'tgl_selesai' },
                { data: 'tempat', name: 'tempat' },
                { data: 'hasil', name: 'hasil' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        new TomSelect('#pelatihan-id_karyawan', {
            placeholder: 'Karyawan...',
            allowEmptyOption: true,
            maxItems: 1,
            searchField: ['text'],   // bisa diketik
            create: false            // tidak boleh input baru
        });

        const resetForm = () => {
            $('#modal-pelatihan-label').text('Tambah Data');
            $('#pelatihan-id_karyawan').val('');
            $('#pelatihan-nama').val('');
            $('#pelatihan-tempat').val('');
            $('#pelatihan-tgl_mulai').val('');
            $('#pelatihan-tgl_selesai').val('');
            $('#pelatihan-hasil').val('');
            $('#btn-save-pelatihan').data('mode', 'create').data('id', '');
        };


        $('#btn-add-pelatihan').on('click', function () {
            resetForm();
            $('#modal-pelatihan').modal('show');
        });

        $('#btn-save-pelatihan').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                id_karyawan: $('#pelatihan-id_karyawan').val(),
                nama: $('#pelatihan-nama').val(),
                tempat: $('#pelatihan-tempat').val(),
                tgl_mulai: $('#pelatihan-tgl_mulai').val(),
                tgl_selesai: $('#pelatihan-tgl_selesai').val(),
                hasil: $('#pelatihan-hasil').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/pelatihan') }}/' + id : '{{ route('pelatihan.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-pelatihan').modal('hide');
                table.ajax.reload(null, false);
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-pelatihan', function () {
            const btn = $(this);
            $('#modal-pelatihan-label').text('Edit Data');
            $('#pelatihan-id_karyawan').val(btn.data('id_karyawan'));
            $('#pelatihan-nama').val(btn.data('nama'));
            $('#pelatihan-tempat').val(btn.data('tempat'));
            $('#pelatihan-tgl_mulai').val(btn.data('tgl_mulai'));
            $('#pelatihan-tgl_selesai').val(btn.data('tgl_selesai'));
            $('#pelatihan-hasil').val(btn.data('hasil'));
            $('#btn-save-pelatihan').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-pelatihan').modal('show');
        });

        $(document).on('click', '.btn-delete-pelatihan', function () {
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
                    url: '{{ url('data_crew/pelatihan') }}/' + id,
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
