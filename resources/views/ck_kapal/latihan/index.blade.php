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
         <button class="btn btn-primary btn-sm" id="btn-add-latihan">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-latihan" class="table table-striped w-100">
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

<div class="modal fade" id="modal-latihan" tabindex="-1" aria-labelledby="modal-latihan-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-latihan-label">Tambah Data</h5>
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
                    <label class="form-label">Jenis latihan</label>
                    <select id="id_form" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($form as $f)
                            <option value="{{$f->id}}">{{$f->nama}}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="mb-1">
                    <label class="form-label">Upload File</label>
                    <input type="file" name="file[]" id="file_upload" class="form-control" multiple>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-latihan">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="DetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableDetail" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar/Video</th>
                            <th>Judul</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Preview File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center" id="previewContent">
                <!-- isi preview -->
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

        const table = $('#table-latihan').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('latihan.data') }}',
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
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `${row.kapal} <button type="button"  onclick="openDetail(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Barang">
                        Gambar & Video</button>`;
                    }
                },    
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
            $('#modal-latihan-label').text('Tambah Data');
            $('#id_kapal').val('');
            $('#id_form').val('');
            $('#id_pelabuhan').val('');
            $('#date').val('');
            $('#btn-save-latihan').data('mode', 'create').data('id', '');
        };

        $('#btn-add-latihan').on('click', function () {
            resetForm();
            $('#modal-latihan').modal('show');
        });

        $('#btn-save-latihan').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');

            let formData = new FormData();

            formData.append('id_kapal', $('#id_kapal').val());
            formData.append('id_form', $('#id_form').val());
            formData.append('id_pelabuhan', $('#id_pelabuhan').val());
            formData.append('date', $('#date').val());

            let files = $('#file_upload')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('file[]', files[i]);
            }

            const ajaxOpts = {
                url: mode === 'edit'
                    ? '{{ url('ck_kapal/latihan') }}/' + id
                    : '{{ route('latihan.store') }}',
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
                $('#modal-latihan').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-latihan', function () {
            const btn = $(this);
            $('#modal-latihan-label').text('Edit Data');
            $('#id_kapal').val(btn.data('id_kapal'));
            $('#id_form').val(btn.data('id_form'));
            $('#id_pelabuhan').val(btn.data('id_pelabuhan'));
            $('#date').val(btn.data('date'));
            $('#btn-save-latihan').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-latihan').modal('show');
        });

        $(document).on('click', '.btn-delete-latihan', function () {
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
                    url: '{{ url('ck_kapal/latihan') }}/' + id,
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

     function openDetail(id) {
        currentId = id;
        $('#DetailModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetail')) {
            DetailTable.ajax.url(`latihan/get/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetail').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `latihan/get/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: null,
                    render: function (data, type, row) {

                        let file = row.ket;
                        let url = "{{ asset('checklist') }}/" + file;
                        let ext = file.split('.').pop().toLowerCase();

                        let preview = '';

                        // kalau gambar → tampilkan thumbnail
                        if (['jpg','jpeg','png','gif'].includes(ext)) {
                            preview = `<img src="${url}" 
                                            style="width:40px;height:40px;object-fit:cover;border-radius:5px;margin-right:5px;">`;
                        } 
                        // kalau video → kasih icon
                        else if (['mp4','webm','ogg'].includes(ext)) {
                            preview = `🎥`;
                        } 
                        else {
                            preview = `📄`;
                        }
                        return `
                            <div style="display:flex;align-items:center;gap:5px;">
                                
                                <button type="button"
                                    class="btn btn-icon btn-xs btn-flat-primary btn-preview"
                                    data-file="${file}">
                                    ${preview}
                                </button>
                            </div>
                        `;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-icon btn-xs btn-flat-primary btn-preview" data-file="${row.ket}">
                            ${row.ket}
                            </button>
                        `;
                    }
                }
            ]
        });
    }

    $(document).on('click', '.btn-preview', function () {

        let file = $(this).data('file');
        let url = "{{ asset('checklist') }}/" + file;

        let ext = file.split('.').pop().toLowerCase();

        let html = '';

        // gambar
        if (['jpg','jpeg','png','gif'].includes(ext)) {
            html = `<img src="${url}" class="img-fluid">`;
        }

        // video
        else if (['mp4','webm','ogg'].includes(ext)) {
            html = `
                <video controls class="w-100">
                    <source src="${url}" type="video/${ext}">
                </video>
            `;
        }

        // selain itu (pdf/dll)
        else {
            html = `<a href="${url}" target="_blank">Download File</a>`;
        }

        $('#previewContent').html(html);
        $('#filePreviewModal').modal('show');
    });
</script>
@endsection
