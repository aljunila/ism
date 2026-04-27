@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Gudang</h4>
        <!-- <button class="btn btn-primary btn-sm" id="btn-add-gudang">Tambah Data</button> -->
    </div>
    <div class="card-body">
        <div class="card-header border-bottom">
            <div class="col-sm-2">
                <select name="jenis" id="jenis" class="form-control">
                    <option value="0">Pilih</option>
                    <option value="1">Gudang Kapal</option>
                    <option value="2">Gudang Cabang</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="id_kapal" id="id_kapal" class="form-control kapal">
                    <option value="">Pilih Kapal</option>
                    @foreach($kapal as $k)
                        <option value="{{$k->id}}">{{$k->nama}}</option>
                    @endforeach
                </select>
                <select name="id_cabang" id="id_cabang" class="form-control">
                    <option value="">Pilih Cabang</option>
                    @foreach($cabang as $c)
                    <option value="{{$c->id}}">{{$c->cabang}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
            <button type="button" class="btn btn-warning btn-sm" id="download"><i data-feather='download'></i> Unduh Data</button>
            <a href="/karyawan/add" class="btn btn-primary btn-sm"><i data-feather='file-plus'></i> Tambah Data</a>
            </div>
        </div>
        <table id="table-gudang" class="table table-striped w-100">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Kelompok</th>
                    <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Kode Barang</th>
                    <th colspan="2">Kondisi</th>
                    <th rowspan="2">Total</th>
                    <th rowspan="2">Aksi</th>
                </tr>
                 <tr>
                    <th>Baik</th>
                    <th>Habis/Rusak</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-gudang" tabindex="-1" aria-labelledby="modal-gudang-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-gudang-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Barang</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="gudang-barang" disabled class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Jumlah</label>
                        </div>
                        <div class="col-9">
                            <input type="number" id="gudang-jumlah" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Kondisi</label>
                        </div>
                        <div class="col-4">
                            <input type="number" id="gudang-baik" class="form-control" placeholder="Baik">
                        </div>
                        <div class="col-4">
                            <input type="number" id="gudang-habis" class="form-control" placeholder="Rusak/Habis">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Keterangan</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="gudang-keterangan" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-gudang">Simpan</button>
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
        $('#id_kapal').hide();
        $('#id_cabang').hide();

        $(document).on('change', '#jenis', function() {
            let jenis = $(this).val();
            if(jenis==1){
                $('#id_kapal').show();
                $('#id_cabang').hide();
                table.ajax.reload();
            } else {
                $('#id_kapal').hide();
                $('#id_cabang').show();
                table.ajax.reload();
            }
        })

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
            $('#id_cabang').val('');
        });

        $('#id_cabang').on('change', function () {
            table.ajax.reload();
            $('#id_kapal').val('');
        });

        const table = $('#table-gudang').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/gudang/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.id_cabang= $('#id_cabang').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: null, 
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1; 
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'kelompok', name: 'kelompok' },
                { data: 'barang', name: 'barang' },
                { data: 'kode', name: 'kode' },
                { data: 'baik', name: 'baik' },
                { data: 'habis', name: 'habis' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.btn-edit-gudang', function () {
            const btn = $(this);
            $('#modal-gudang-label').text('Edit Data');
            $('#gudang-barang').val(btn.data('barang'));
            $('#gudang-kode').val(btn.data('kode'));
            $('#gudang-jumlah').val(btn.data('jumlah'));
            $('#gudang-baik').val(btn.data('baik'));
            $('#gudang-habis').val(btn.data('habis'));
            $('#gudang-keterangan').val(btn.data('keterangan'));
            $('#btn-save-gudang').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-gudang').modal('show');
        });

        $('#btn-save-gudang').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                jumlah: $('#gudang-jumlah').val(),
                baik: $('#gudang-baik').val(),
                habis: $('#gudang-habis').val(),
                keterangan: $('#gudang-keterangan').val(),
            };
            const ajaxOpts = {
                url: '{{ url('gudang') }}/' + id,
                type: 'PUT',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'data diperbarui' : 'data ditambahkan', 'success');
                $('#modal-gudang').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });
    });
</script>
@endsection
