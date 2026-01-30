@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Crew - Recruitment</h4>
        <button class="btn btn-primary btn-sm" id="btn-add-recruitment">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-recruitment" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Perusahaan</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th>PDF</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-recruitment" tabindex="-1" aria-labelledby="modal-recruitment-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-recruitment-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label"> Perusahaan</label>
                    <select id="recruitment-id_perusahaan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($perusahaan as $p)
                            <option value="{{$p->id}}">{{$p->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Nama</label>
                    <input type="text" id="recruitment-nama" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Alamat</label>
                    <textarea id="recruitment-alamat" class="form-control"></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label">No Telp</label>
                    <input type="text" id="recruitment-telp" class="form-control">
                </div>
                 <div class="mb-1">
                    <label class="form-label">Bagian/Jabatan</label>
                    <select id="recruitment-id_jabatan" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($jabatan as $j)
                            <option value="{{$j->id}}">{{$j->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-recruitment">Simpan</button>
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

        const table = $('#table-recruitment').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('recruitment.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'perusahaan', name: 'perusahaan' },
                { data: 'nama', name: 'nama' },
                { data: 'jabatan', name: 'jabatan' },
                { data: 'note', name: 'note' },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, row) {
                        if (data === 'N') return 'Baru';
                        if (data === 'A') return 'Diterima';
                        if (data === 'D') return 'Ditolak';
                        return '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row){
                        if(row.data) {
                        return `<a type="button" href="/data_crew/recruitment/pdf/${row.uid}" target="_blank" class="btn btn-sm btn-outline-success"
                        >Cetak PDF</a>`;
                        }
                        return `-`;
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        const resetForm = () => {
            $('#modal-recruitment-label').text('Tambah Data');
            $('#recruitment-id_jabatan').val('');
            $('#recruitment-nama').val('');
            $('#recruitment-id_perusahaan').val('');
            $('#recruitment-alamat').val('');
            $('#recruitment-telp').val('');
            $('#btn-save-recruitment').data('mode', 'create').data('id', '');
        };

        $('#btn-add-recruitment').on('click', function () {
            resetForm();
            $('#modal-recruitment').modal('show');
        });

        $('#btn-save-recruitment').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                kelas: $('#recruitment-kelas').val(),
                id_perusahaan: $('#recruitment-id_perusahaan').val(),
                id_jabatan: $('#recruitment-id_jabatan').val(),
                nama: $('#recruitment-nama').val(),
                alamat: $('#recruitment-alamat').val(),
                telp: $('#recruitment-telp').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/recruitment') }}/' + id : '{{ route('recruitment.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-recruitment').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-recruitment', function () {
            const btn = $(this);
            $('#modal-recruitment-label').text('Edit Data');
            $('#recruitment-nama').val(btn.data('nama'));
            $('#recruitment-alamat').val(btn.data('alamat'));
            $('#recruitment-telp').val(btn.data('telp'));
            $('#recruitment-id_perusahaan').val(btn.data('id_perusahaan'));
            $('#recruitment-id_jabatan').val(btn.data('id_jabatan'));
            $('#btn-save-recruitment').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-recruitment').modal('show');
        });

        $(document).on('click', '.btn-delete-recruitment', function () {
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
                    url: '{{ url('data_crew/recruitment') }}/' + id,
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
