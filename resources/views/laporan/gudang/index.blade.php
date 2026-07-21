@extends('main')
@section('scriptheader')
  <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Vendor CSS-->
@endsection

@section('scriptfooter')
<!-- jQuery -->
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ url('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('/assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->
<script>
    let table;

    $(function () {
		table = $('#table').DataTable({
        processing: true,
        searchable: true,
        ajax:{
            url: "/laporan/gudang/data",
            type: "POST",
            data: function(d){
                d.id_perusahaan= $('#id_perusahaan').val(),
                d._token= "{{ csrf_token() }}"
            },
        },
        columns: [
            { data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + 1; // auto numbering
                },
                orderable: false,
                searchable: false
            },
            { data: 'nama' },
            { data: 'call_sign' },
            { data: 'cabang' },
            { 
                data: null,
                render: function(data, type, row){
                    return `<a href="/laporan/gudang/pdf/${row.uid}" target="_blank" class="btn btn-sm btn-outline-success">
                            Cetak PDF</a>
                            <a target="_blank" class="btn btn-sm btn-outline-primary" data-id="${row.id}" id="btn-pdf">
                            Download Excel</a>`;
                }
            },
        ],
         drawCallback: function(settings) {
            feather.replace(); 
        }
    });
    });

    $(document).on('change', '#id_perusahaan', function() {
        var perusahaanID = $(this).val();
        if (perusahaanID) {
            $.ajax({
                url: '/get-kapal/' + perusahaanID,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#id_kapal').empty().append('<option value="">Semua</option>');           
                    $.each(data, function(key, value) {
                        $('#id_kapal').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                    });
                    table.ajax.reload();
                }
            });
        } else {
            $('#id_kapal').empty().append('<option value="">Tidak ada data</option>');
            table.ajax.reload();
        }
    });

    $(document).on('click', '#btn-pdf', function() {
        let id = $(this).data('id');
        console.log(id);
        
        $.ajax({
            url: "/laporan/gudang/export",
            method: "POST",
            xhrFields: { responseType: 'blob' },
            data: {
                id: id,
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                bagian: $('#bagian').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(data){
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = "lap_gudang.xlsx";
                link.click();
            }
        })
    });
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="col-12"><h4 class="card-title">Laporan Gudang Kapal</h4></div>
                        @include('perusahaan')
                        <div class="col-sm-3">
                            <select name="bagian" id="bagian" class="form-control" >
                                <option value="0">Semua</option>
                                <option value="1">DECK</option>
                                <option value="2">MESIN</option>
                                <option value="3">ELECTRICIANT</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="date" name="start_date" id="start_date" class="form-control" placeholder="start date">
                        </div>
                        <div class="col-sm-2">
                            <input type="date" name="end_date" id="end_date" class="form-control" placeholder="end date">
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                            <th>No.</th>
                            <th>Nama Kapal</th>
                            <th>Call Sign</th>
                            <th>Cabang</th>
                            <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection