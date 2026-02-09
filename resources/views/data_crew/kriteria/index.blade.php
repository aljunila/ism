@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Crew - Kriteria Kru {{$perusahaan->nama}}</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-kriteria">Tambah Data</button>
        <button class="btn btn-warning btn-sm" id="btn-download-kriteria">Cetak PDF</button>
    </div>
    <div class="card-body">
        <table id="table-kriteria" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jabatan</th>
                    <th>Jenis Kriteria</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-kriteria" tabindex="-1" aria-labelledby="modal-kriteria-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-kriteria-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label"> Jabatan</label>
                    <select id="kriteria-id_jabatan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($jabatan as $p)
                            <option value="{{$p->id}}">{{$p->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Kriteria</label>
                    <select id="kriteria-kriteria" class="form-control">
                        <option value="">-Pilih-</option>
                        <option value="1">Ijazah Pelaut</option>
                        <option value="2">Ijazah Tambahan</option>
                        <option value="3">Pengalaman (tahun)</option>
                        <option value="4">Umur (minimal)</option>
                        <option value="5">Kemampuan Bahasa</option>
                        <option value="6">Lain-lain</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="kriteria-des" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-kriteria">Simpan</button>
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

        const table = $('#table-kriteria').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('kriteria.data') }}',
                type: "POST",
                data: function(d){
                    d.id_perusahaan= "{{$perusahaan->id}}",
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jabatan', name: 'jabatan' },
                {
                    data: 'kriteria',
                    name: 'kriteria',
                    render: function (data, type, row) {
                        if (data == 1) return 'Ijazah Pelaut';
                        if (data == 2) return 'Ijazah Tambahan';
                        if (data == 3) return 'Pengalaman (tahun)';
                        if (data == 4) return 'Umur (minimal)';
                        if (data == 5) return 'Kemampuan Bahasa';
                        if (data == 6) return 'Lain-lain';
                        return '-';
                    }
                },
                { data: 'des', name: 'des' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-kriteria-label').text('Tambah Data');
            $('#kriteria-des').val('');
            $('#kriteria-kriteria').val('');
            $('#kriteria-id_jabatan').val('');
            $('#btn-save-kriteria').data('mode', 'create').data('id', '');
        };

        $('#btn-add-kriteria').on('click', function () {
            resetForm();
            $('#modal-kriteria').modal('show');
        });

        $('#btn-save-kriteria').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                kriteria: $('#kriteria-kriteria').val(),
                id_jabatan: $('#kriteria-id_jabatan').val(),
                id_perusahaan: "{{$perusahaan->id}}",
                des: $('#kriteria-des').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/kriteria') }}/' + id : '{{ route('kriteria.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Kriteria diperbarui' : 'Kriteria ditambahkan', 'success');
                $('#modal-kriteria').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-kriteria', function () {
            const btn = $(this);
            $('#modal-kriteria-label').text('Edit Data');
            $('#kriteria-kriteria').val(btn.data('kriteria'));
            $('#kriteria-des').val(btn.data('des'));
            $('#kriteria-id_jabatan').val(btn.data('id_jabatan')).trigger('change');
            $('#kriteria-id_perusahaan').val(btn.data('id_perusahaan'));
            $('#btn-save-kriteria').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-kriteria').modal('show');
        });

        $(document).on('click', '.btn-delete-kriteria', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Kriteria ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_crew/kriteria') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Kriteria berhasil dihapus', 'success');
                        table.ajax.reload(null, false);
                        loadCabang                    
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });

        $(document).on('click', '#btn-download-kriteria', function() {
            let id_perusahaan = "{{$perusahaan->id}}";
            let idform = "{{$form->id}}";

            let url = "{{ url('/data_crew/kriteria/pdf') }}" + "?id_perusahaan=" + id_perusahaan + "&idform=" + idform;
            console.log(url);
            window.open(url, '_blank');
        });
    });
</script>
@endsection
