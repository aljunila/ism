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
        <h4 class="card-title">Crew - Penggantian Crew</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-ganti">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-ganti" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Form/Laporan</th>
                    <th>Tanggal</th>
                    <th>Dari</th>
                    <th>Kepada</th>
                    <th>PDF</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-ganti" tabindex="-1" aria-labelledby="modal-ganti-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-ganti-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Dari</label>
                    <select id="ganti-id_karyawan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Kepada</label>
                    <select id="ganti-id_karyawan2" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($karyawan as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal</label>
                    <input type="date" id="ganti-date" class="form-control">
                </div>
                 <div class="mb-1">
                    <label class="form-label">Jenis Penggantian</label>
                    <select id="ganti-kode" class="form-control">
                        <option value="">-Pilih-</option>
                         @foreach($form as $form)
                            <option value="{{$form->kel}}">Ganti {{$form->kel}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-ganti">Simpan</button>
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

        const table = $('#table-ganti').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('ganti.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'date', name: 'date' },
                { data: 'dari', name: 'dari' },
                { data: 'kepada', name: 'kepada' },
                { 
                    data: null,
                    render: function(data, type, row){
                        if(row.data) {
                        return `<a type="button" href="/data_crew/ganti/pdf/${row.uid}" target="_blank" class="btn btn-sm btn-outline-success"
                        >Cetak PDF</a>`;
                        }
                        return `-`;
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        new TomSelect('#ganti-id_karyawan', {
            placeholder: 'Karyawan...',
            allowEmptyOption: true,
            maxItems: 1,
            searchField: ['text'],   // bisa diketik
            create: false            // tidak boleh input baru
        });

         new TomSelect('#ganti-id_karyawan2', {
            placeholder: 'Karyawan...',
            allowEmptyOption: true,
            maxItems: 1,
            searchField: ['text'],   // bisa diketik
            create: false            // tidak boleh input baru
        });

        const resetForm = () => {
            $('#modal-ganti-label').text('Tambah Data');
            $('#ganti-id_karyawan').val('');
            $('#ganti-kode').val('');
            $('#ganti-date').val('');
            $('#ganti-id_karyawan2').val('');
            $('#btn-save-ganti').data('mode', 'create').data('id', '');
        };

        $('#btn-add-ganti').on('click', function () {
            resetForm();
            $('#modal-ganti').modal('show');
        });

        $('#btn-save-ganti').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                id_karyawan: $('#ganti-id_karyawan').val(),
                id_karyawan2: $('#ganti-id_karyawan2').val(),
                kode: $('#ganti-kode').val(),
                date: $('#ganti-date').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/ganti') }}/' + id : '{{ route('ganti.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-ganti').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-ganti', function () {
            const btn = $(this);
            $('#modal-ganti-label').text('Edit Data');
            $('#ganti-id_karyawan').val(btn.data('id_karyawan'));
            $('#ganti-id_karyawan2').val(btn.data('id_karyawan2'));
            $('#ganti-kode').val(btn.data('kode'));
            $('#ganti-date').val(btn.data('date'));
            $('#btn-save-ganti').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-ganti').modal('show');
        });

        $(document).on('click', '.btn-delete-ganti', function () {
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
                    url: '{{ url('data_crew/ganti') }}/' + id,
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
