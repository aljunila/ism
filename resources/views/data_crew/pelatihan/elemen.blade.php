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
    table = $("#table").DataTable({
        processing: true,
        serverSide: false, // kalau data sedikit cukup false, kalau ribuan bisa true
        ajax:{
            url: "/data_crew/pelatihan/getData",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->kode}}",
                d.id_perusahaan = "{{$id_perusahaan}}",
                d.id_form = "{{$form->id}}",
                d.id_kapal= $('#id_kapal').val(),
                d._token= "{{ csrf_token() }}"
            },
        },
        columns: [
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + 1; // auto numbering
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `${row.karyawan} <br> ${row.jabatan}`;
                }
            },                
            { data: 'nama', name: 'nama' },
            { data: 'tgl_mulai',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { data: 'tgl_selesai',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { data: 'tempat' },
            { data: 'hasil' },
        ],
        drawCallback: function(settings) {
            feather.replace(); // supaya icon feather muncul ulang
        }
    });
});

$(document).on('change', '.kapal', function() {
    var kapalID = $(this).val();
    if (kapalID) {
        $.ajax({
            url: '/get-karyawan/' + kapalID,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('.karyawan').empty().append('<option value="">-- Pilih Karyawan --</option>');
            
                $.each(data, function(key, value) {
                    $('.karyawan').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                });
                table.ajax.reload();
            }
        });
    } else {
        $('.karyawan').empty().append('<option value="">-- Pilih Karyawan --</option>');
         table.ajax.reload();
    }
});

$(document).on('click', '#btn-pdf', function() {
    let id_perusahaan = "{{$id_perusahaan}}";
    let idform = "{{$form->id}}";
    let start = $('#start').val();
    let end = $('#end').val();
    let kode = "{{ $form->kode}}";

    if (!start.trim()) {
        Swal.fire({
            icon: "warning",
            title: "Oops...",
            text: "Silahkan pilih tanggal terlebih dahulu"
        });
        return;
    }

    let url = "{{ url('/data_crew/pelatihan/pdf') }}" + "?id_perusahaan=" + id_perusahaan + "&idform=" + idform + "&start=" + start + "&end=" + end;
    window.open(url, '_blank');
});
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                            <div class="col-12">
                                <h4 class="card-title">{{$form->nama}}</h4>
                            </div>
                                @include('kapal')
                            <div class="col-3">
                                <!-- <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#FormTambah">Tambah Data</button> -->
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#FormDownload">Cetak Laporan</button>
                            </div>
                    </div>
                    <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                      <thead>
                        <tr align="center">
                            <th width="5%" rowspan="2">No.</th>
                            <th width="25%" rowspan="2">Nama Peserta</th>
                            <th width="20%" rowspan="2">Nama Pelatihan</th>
                            <th width="10%" colspan="2">Waktu (Tanggal)</th>
                            <th width="10%" rowspan="2">Tempat</th>
                            <th width="15%" rowspan="2">Hasil</th>
                        </tr>
                        <tr align="center">
                            <th>Mulai</th>
                            <th>Selesai</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
<div class="modal fade text-start" id="FormDownload" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Cetak Laporan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_download" enctype="multipart/form-data">
                    @csrf
                <div class="modal-body">
                    <label>Dari Tanggal </label>
                    <div class="mb-1">
                        <input type="date" name="start_date" id="start" class="form-control">
                    </div>
                    <label>Sampai Tanggal</label>
                    <div class="mb-1">
                        <input type="date" class="form-control" name="end_date" id="end"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button target="_blank" type="button" class="btn btn-success btn-sm float-right" id="btn-pdf">Cetak</button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>

@endsection