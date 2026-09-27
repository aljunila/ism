@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <div class="col-sm-12"><h4 class="card-title">Laporan Data Produksi</h4></div>
                <div class="col-sm-3">
                    <select name="id_kapal" id="id_kapal" class="form-control">
                        <option value="">Pilih Kapal</option>
                    @foreach($kapal as $kp)
                        <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
                    @endforeach
                    </select>
                </div>  
                <div class="col-sm-3">
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
                <div class="col-sm-3">
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-warning btn-sm" id="download"><i data-feather='download'></i> Unduh Data</button>
                </div>
            </div>
            <div class="card-body">
                <table id="table-pel" class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kapal</th>
                            <th>Tanggal</th>
                            <th>Pelabuhan</th>
                            <th>Trip</th>
                            <th>Jam</th>
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
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-pel').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/laporan/produksi/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.start_date= $('#start_date').val(),
                    d.end_date= $('#end_date').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kapal', name: 'kapal' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'pelabuhan', name: 'pelabuhan' },
                { data: 'trip', name: 'trip' },
                { data: 'jam', name: 'jam' }
            ]
        });

        $('#id_kapal').on('change', function () {
         table.ajax.reload();
        });

        $('#start_date').on('change', function () {
         table.ajax.reload();
        });

        $('#end_date').on('change', function () {
         table.ajax.reload();
        });

    });

     $(document).on('click', '#download', function() {
        $.ajax({
            url: "/laporan/produksi/export",
            method: "POST",
            xhrFields: { responseType: 'blob' },
            data: {
                id_kapal: $('#id_kapal').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(data){
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = "lap_produksi.xlsx";
                link.click();
            }
        })
    });
</script>
@endsection
