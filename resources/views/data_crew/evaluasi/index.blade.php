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
        <h4 class="card-title">Crew - Laporan Evaluasi</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-evaluasi">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-evaluasi" class="table table-striped w-100">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Form/Laporan</th>
                    <th width="15%">Kapal</th>
                    <th width="10%">Tanggal</th>
                    <th width="20%">Nama</th>
                    <th width="10%">Jabatan</th>
                    <th width="10%">PDF</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-evaluasi" tabindex="-1" aria-labelledby="modal-evaluasi-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-evaluasi-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama Crew</label>
                    <select id="evaluasi-id_karyawan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal</label>
                    <input type="date" id="evaluasi-date" class="form-control">
                </div>
                 <div class="mb-1">
                    <label class="form-label">Jenis Familiarisasi</label>
                    <select id="evaluasi-id_form" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($form as $f)
                            <option value="{{$f->id}}">{{$f->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-evaluasi">Simpan</button>
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

        const table = $('#table-evaluasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('evaluasi.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'kapal', name: 'kapal' },
                { data: 'date',
                    render: function(data) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                },
                { data: 'nama', name: 'nama' },
                { data: 'jabatan', name: 'jabatan' },
                { 
                    data: null,
                    render: function(data, type, row){
                        if(row.data) {
                        return `<a type="button" href="/data_crew/evaluasi/pdf/${row.uid}" target="_blank" class="btn btn-sm btn-outline-success"
                        >Cetak PDF</a>`;
                        }
                        return `-`;
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        new TomSelect('#evaluasi-id_karyawan', {
            placeholder: 'Karyawan...',
            allowEmptyOption: true,
            maxItems: 1,
            searchField: ['text'],   // bisa diketik
            create: false            // tidak boleh input baru
        });

        const resetForm = () => {
            $('#modal-evaluasi-label').text('Tambah Data');
            $('#evaluasi-id_karyawan').val('');
            $('#evaluasi-id_form').val('');
            $('#evaluasi-date').val('');
            $('#btn-save-evaluasi').data('mode', 'create').data('id', '');
        };

        $('#btn-add-evaluasi').on('click', function () {
            resetForm();
            $('#modal-evaluasi').modal('show');
        });

        $('#btn-save-evaluasi').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                id_karyawan: $('#evaluasi-id_karyawan').val(),
                id_form: $('#evaluasi-id_form').val(),
                date: $('#evaluasi-date').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/evaluasi') }}/' + id : '{{ route('evaluasi.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-evaluasi').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-evaluasi', function () {
            const btn = $(this);
            $('#modal-evaluasi-label').text('Edit Data');
            $('#evaluasi-id_karyawan').val(btn.data('id_karyawan'));
            $('#evaluasi-id_form').val(btn.data('id_form'));
            $('#evaluasi-date').val(btn.data('date'));
            $('#btn-save-evaluasi').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-evaluasi').modal('show');
        });

        $(document).on('click', '.btn-delete-evaluasi', function () {
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
                    url: '{{ url('data_crew/evaluasi') }}/' + id,
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
