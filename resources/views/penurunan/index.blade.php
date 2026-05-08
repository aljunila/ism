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
        <h4 class="card-title">Penurunan Barang</h4>
        <a type="button" href="/penurunan/form" class="btn btn-primary btn-sm">Tambah Data</a>
         <!-- <button class="btn btn-primary btn-sm" id="btn-add-turun">Tambah Data</button> -->
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
        <table id="table-turun" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kapal</th>
                    <th>Bagian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="DetailturunModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableDetailturun" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Kondisi</th>
                            <th>Jumlah</th>
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

        const table = $('#table-turun').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/penurunan/data",
                type: "POST",
                data: function(d){
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
                        return `${row.kapal} <button type="button"  onclick="openDetailTurun(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Barang">
                        Detail Penurunan</button><br>
                        No : ${row.nomor}`;
                    }
                },    
                { data: 'bagian', name: 'bagian' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
            
        });

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
        });

        $('#tanggal').on('change', function () {
            table.ajax.reload();
        });

        $(document).on('click', '.btn-delete-turun', function () {
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
                    url: '{{ url('data_crew/turun') }}/' + id,
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

    function openDetailturun(id) {
        currentId = id;
        $('#DetailTurunModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetailTurun')) {
            DetailTable.ajax.url(`/penurunan/get/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetailturun').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `/permintaan/getturun/${id}`,
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
                { data: 'kondisi', },
                { data: 'jumlah', },
            ]
        });
    }
</script>
@endsection
