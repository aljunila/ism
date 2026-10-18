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
                <div class="col-sm-2 mb-1">
                    <select name="id_kapal" id="id_kapal" class="form-control">
                        <option value="">Pilih Kapal</option>
                    @foreach($kapal as $kp)
                        <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
                    @endforeach
                    </select>
                </div>  
                <div class="col-sm-2 mb-1">
                    <select name="filter" id="filter" class="form-control">
                        <option value="">Pilih Filter</option>
                        <option value="1">Tanggal</option>
                        <option value="2">Bulan</option>
                        <option value="3">Tahun</option>
                    </select>
                </div>

                <div id="tanggal" class="col-sm-4 mb-1" style="display:none;">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="date" name="start_date" id="start_date"
                                class="form-control" placeholder="Dari Tanggal">
                        </div>
                        <div class="col-sm-6">
                            <input type="date" name="end_date" id="end_date"
                                class="form-control" placeholder="Sampai Tanggal">
                        </div>
                    </div>
                </div>

                <div id="bulan" class="col-sm-4 mb-1" style="display:none;">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="month" name="start_month" id="start_month" class="form-control" placeholder="Dari Bulan">
                        </div>
                        <div class="col-sm-6">
                            <input type="month" name="end_month" id="end_month" class="form-control" placeholder="Sampai Bulan">
                        </div>
                    </div>
                </div>

                <div id="tahun" class="col-sm-4 mb-1" style="display:none;">
                    <div class="row">
                        <div class="col-sm-6">
                            <select id="start_year" name="start_year" class="form-control">
                                <option value="">Dari Tahun</option>
                                @for ($a = 2020; $a <= 2036; $a++)
                                    <option value="{{ $a }}">{{ $a }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <select id="end_year" name="end_year" class="form-control">
                                <option value="">Sampai Tahun</option>
                                @for ($a = 2020; $a <= 2036; $a++)
                                    <option value="{{ $a }}">{{ $a }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
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
    $('#tanggal').hide();
    $('#bulan').hide();
    $('#tahun').hide();

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
                    d.start_month= $('#start_month').val(),
                    d.end_month= $('#end_month').val(),
                    d.start_year= $('#start_year').val(),
                    d.end_year= $('#end_year').val(),
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
        
        $('#start_month').on('change', function () {
            table.ajax.reload();
        });

        $('#end_month').on('change', function () {
            table.ajax.reload();
        });

        $('#start_year').on('change', function () {
            table.ajax.reload();
        });

        $('#end_year').on('change', function () {
            table.ajax.reload();
        });

    });

     $(document).ready(function () {
        function toggleFilter() {
            $('#tanggal, #bulan, #tahun').hide();

            if ($('#filter').val() == '1') {
                $('#tanggal').show();
            } else if ($('#filter').val() == '2') {
                $('#bulan').show();
            } else if ($('#filter').val() == '3') {
                $('#tahun').show();
            }
        }

        $('#filter').on('change', toggleFilter);

        toggleFilter();
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
                start_month: $('#start_month').val(),
                end_month: $('#end_month').val(),
                start_year: $('#start_year').val(),
                end_year: $('#end_year').val(),
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
