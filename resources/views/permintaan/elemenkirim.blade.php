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
        <h4 class="card-title">Pengiriman Barang</h4>
        <a type="button" href="/permintaan/kirim" class="btn btn-primary btn-sm">Tambah Data</a>
         <!-- <button class="btn btn-primary btn-sm" id="btn-add-kirim">Tambah Data</button> -->
    </div>
    <div class="card-header border-bottom">
        <div class="col-sm-3">
            <select name="id_kapal" id="id_kapal" class="form-control">
                <option value="">Pilih Kapal</option>
            @foreach($kapal as $kp)
                <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
            @endforeach
            </select>
        </div>  
        <div class="col-sm-3">
            <input type="date" name="tanggal" id="tanggal" class="form-control">
        </div>
        <div class="col-sm-6"></div>
    </div>
    <div class="card-body">
        <table id="table-kirim" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kapal</th>
                    <th>Bagian</th>
                    <th>Pengirim</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="DetailKirimModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableDetailKirim" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Jml Permintaan</th>
                            <th>Jml Kirim</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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

        const table = $('#table-kirim').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/permintaan/kirimByIdp",
                type: "POST",
                data: function(d){
                    d.kode= "{{ $form->id}}",
                    d.id_perusahaan= "{{ $id_perusahaan}}",
                    d.id_kapal= $('#id_kapal').val(),
                    d.tanggal= $('#tanggal').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal' },
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `${row.kapal} <button type="button"  onclick="openDetailKirim(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Barang">
                        Detail Pengiriman</button><br>
                        No : ${row.nomor}`;
                    }
                },    
                { data: 'bagian', name: 'bagian' },
                { data: 'created', name: 'created' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
            
        });

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
        });

        $('#tanggal').on('change', function () {
            table.ajax.reload();
        });

        $(document).on('click', '.btn-delete-kirim', function () {
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
                    url: '{{ url('data_crew/kirim') }}/' + id,
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

    function openDetailKirim(id) {
        currentId = id;
        $('#DetailKirimModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetailKirim')) {
            DetailTable.ajax.url(`/permintaan/getkirim/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetailKirim').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `/permintaan/getkirim/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                { data: 'barang', },
                { data: 'jml_minta', },
                { data: 'jumlah', },
            ]
        });
    }
</script>
@endsection