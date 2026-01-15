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
        <h4 class="card-title">Crew - Familiarisasi Crew</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-familiarisasi">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-familiarisasi" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Form/Laporan</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>PDF</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-familiarisasi" tabindex="-1" aria-labelledby="modal-familiarisasi-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-familiarisasi-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama Crew</label>
                    <select id="familiarisasi-id_karyawan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal</label>
                    <input type="date" id="familiarisasi-date" class="form-control">
                </div>
                 <div class="mb-1">
                    <label class="form-label">Jenis Familiarisasi</label>
                    <select id="familiarisasi-id_form" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($form as $f)
                            <option value="{{$f->id}}">{{$f->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-familiarisasi">Simpan</button>
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

        const table = $('#table-familiarisasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('familiarisasi.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'date', name: 'date' },
                { data: 'nama', name: 'nama' },
                { data: 'jabatan', name: 'jabatan' },
                { 
                    data: null,
                    render: function(data, type, row){
                        if(row.data) {
                        return `<a type="button" href="/data_crew/familiarisasi/pdf/${row.uid}" target="_blank" class="btn btn-sm btn-outline-success"
                        >Cetak PDF</a>`;
                        }
                        return `-`;
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        new TomSelect('#familiarisasi-id_karyawan', {
            placeholder: 'Karyawan...',
            allowEmptyOption: true,
            maxItems: 1,
            searchField: ['text'],   // bisa diketik
            create: false            // tidak boleh input baru
        });

        const resetForm = () => {
            $('#modal-familiarisasi-label').text('Tambah Data');
            $('#familiarisasi-id_karyawan').val('');
            $('#familiarisasi-id_form').val('');
            $('#familiarisasi-date').val('');
            $('#btn-save-familiarisasi').data('mode', 'create').data('id', '');
        };

        $('#btn-add-familiarisasi').on('click', function () {
            resetForm();
            $('#modal-familiarisasi').modal('show');
        });

        $('#btn-save-familiarisasi').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                id_karyawan: $('#familiarisasi-id_karyawan').val(),
                id_form: $('#familiarisasi-id_form').val(),
                date: $('#familiarisasi-date').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/familiarisasi') }}/' + id : '{{ route('familiarisasi.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-familiarisasi').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-familiarisasi', function () {
            const btn = $(this);
            $('#modal-familiarisasi-label').text('Edit Data');
            $('#familiarisasi-id_karyawan').val(btn.data('id_karyawan'));
            $('#familiarisasi-id_form').val(btn.data('id_form'));
            $('#familiarisasi-date').val(btn.data('date'));
            $('#btn-save-familiarisasi').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-familiarisasi').modal('show');
        });

        $(document).on('click', '.btn-delete-familiarisasi', function () {
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
                    url: '{{ url('data_crew/familiarisasi') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Data berhasil dihapus', 'success');
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
