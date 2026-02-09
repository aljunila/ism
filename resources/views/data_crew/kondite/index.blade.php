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
        <h4 class="card-title">Crew - Kondite</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-kondite">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-kondite" class="table table-striped w-100">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Perusahaan</th>
                    <th width="25%">Kapal</th>
                    <th width="25%">Periode</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-kondite" tabindex="-1" aria-labelledby="modal-kondite-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-kondite-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Kapal</label>
                    <select id="kondite-id_kapal" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($kapal as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Tanggal</label>
                    <select class="form-control" name="bulan" id="kondite-bulan">
                            <option value="">Pilih</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                </div>
                 <div class="mb-1">
                    <label class="form-label">Tahun</label>
                    <select class="form-control" name="tahun" id="kondite-tahun">
                        <option value="">Pilih</option>
                        @for($a=2020; $a<=2040; $a++)
                            <option value="{{ $a }}">{{ $a }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-kondite">Simpan</button>
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

        const table = $('#table-kondite').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('kondite.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'perusahaan', name: 'perusahaan' },
                { data: 'kapal', name: 'kapal' },
                { 
                    render: function (data, type, row, meta) {
                        const namaBulan = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                            return `${namaBulan[parseInt(row.bulan) - 1]} ${row.tahun}`;
                    }
                },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        const resetForm = () => {
            $('#modal-kondite-label').text('Tambah Data');
            $('#kondite-id_kapal').val('');
            $('#kondite-bulan').val('');
            $('#kondite-tahun').val('');
            $('#btn-save-kondite').data('mode', 'create').data('id', '');
        };

        $('#btn-add-kondite').on('click', function () {
            resetForm();
            $('#modal-kondite').modal('show');
        });

        $('#btn-save-kondite').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                id_kapal: $('#kondite-id_kapal').val(),
                bulan: $('#kondite-bulan').val(),
                tahun: $('#kondite-tahun').val(),
            };
            const ajaxOpts = {
                url: mode === 'edit' ? '{{ url('data_crew/kondite') }}/' + id : '{{ route('kondite.store') }}',
                type: mode === 'edit' ? 'PUT' : 'POST',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'Data diperbarui' : 'Data ditambahkan', 'success');
                $('#modal-kondite').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        $(document).on('click', '.btn-edit-kondite', function () {
            const btn = $(this);
            $('#modal-kondite-label').text('Edit Data');
            $('#kondite-id_kapal').val(btn.data('id_kapal'));
            $('#kondite-bulan').val(btn.data('bulan'));
            $('#kondite-tahun').val(btn.data('tahun'));
            $('#btn-save-kondite').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-kondite').modal('show');
        });

        $(document).on('click', '.btn-delete-kondite', function () {
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
                    url: '{{ url('data_crew/kondite') }}/' + id,
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
