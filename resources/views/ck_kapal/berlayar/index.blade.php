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
        <h4 class="card-title">Kapal - Berlayar</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-berlayar">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-berlayar" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Form/Laporan</th>
                    <th>Tanggal</th>
                    <th>Kapal</th>
                    <th>Dari Pelabuhan</th>
                    <th>PDF</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-berlayar" tabindex="-1" aria-labelledby="modal-berlayar-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-berlayar-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nama Kapal</label>
                    <select id="id_kapal" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($kapal as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal</label>
                    <input type="date" id="date" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Dari Pelabuhan</label>
                    <select id="id_pelabuhan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($form as $f)
                            <option value="{{$f->id}}">{{$f->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Jenis berlayar</label>
                    <select id="id_form" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($form as $f)
                            <option value="{{$f->id}}">{{$f->nama}}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="mb-1">
                    <label class="form-label">Upload File</label>
                    <input type="file" name="file" id="file_upload" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-berlayar">Simpan</button>
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

        const table = $('#table-berlayar').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('berlayar.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'date',
                    render: function(data) {
                        if (!data) return '';
                        let parts = data.split(' ')[0].split('-'); 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                },
                { data: 'kapal', name: 'kapal' },
                { data: 'pelabuhan', name: 'pelabuhan' },
                { 
                    data: null,
                    render: function(data, type, row){
                        if(row.file) {
                        return `
                        <a href="{{ asset('checklist') }}/${row.file}" target="_blank" type="button" class="btn btn-icon btn-xs btn-flat-success" title="Buka File">
                                <i data-feather='file'></i>
                            </a>
                        `;
                    } else {
                        return ``;
                    }
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            drawCallback: function(settings) {
            feather.replace(); // supaya icon feather muncul ulang
            }
        });

        let getPelabuhanUrl = "{{ route('getPelabuhan', ':id') }}";

        $(document).on('change', '#id_kapal', function() {
            var kapalID = $(this).val();
            if (kapalID) {
                $.ajax({
                    url: getPelabuhanUrl.replace(':id', kapalID),
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#id_pelabuhan').empty().append('<option value="">-Pilih-</option>');           
                        $.each(data, function(key, value) {
                            $('#id_pelabuhan').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                        });
                    }
                });
            } else {
                $('#id_pelabuhan').empty().append('<option value="">Tidak ada data</option>');
            }
        });

        const resetForm = () => {
            $('#modal-berlayar-label').text('Tambah Data');
            $('#id_kapal').val('');
            $('#id_form').val('');
            $('#id_pelabuhan').val('');
            $('#date').val('');
            $('#btn-save-berlayar').data('mode', 'create').data('id', '');
        };

        $('#btn-add-berlayar').on('click', function () {
            resetForm();
            $('#modal-berlayar').modal('show');
        });

        $('#btn-save-berlayar').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');

            let formData = new FormData();

            formData.append('id_kapal', $('#id_kapal').val());
            formData.append('id_form', $('#id_form').val());
            formData.append('id_pelabuhan', $('#id_pelabuhan').val());
            formData.append('date', $('#date').val());

            // ambil file
            let file = $('#file_upload')[0].files[0];
            if (file) {
                formData.append('file', file);
            }

            const ajaxOpts = {
                url: mode === 'edit'
                    ? '{{ url('ck_kapal/berlayar') }}/' + id
                    : '{{ route('berlayar.store') }}',
                type: mode === 'edit' ? 'POST' : 'POST', // tetap POST
                data: formData,
                processData: false,
                contentType: false
            };

            // kalau edit method PUT
            if (mode === 'edit') {
                formData.append('_method', 'PUT');
            }

            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-berlayar').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-berlayar', function () {
            const btn = $(this);
            $('#modal-berlayar-label').text('Edit Data');
            $('#id_kapal').val(btn.data('id_kapal'));
            $('#id_form').val(btn.data('id_form'));
            $('#id_pelabuhan').val(btn.data('id_pelabuhan'));
            $('#date').val(btn.data('date'));
            $('#btn-save-berlayar').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-berlayar').modal('show');
        });

        $(document).on('click', '.btn-delete-berlayar', function () {
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
                    url: '{{ url('ck_kapal/berlayar') }}/' + id,
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
