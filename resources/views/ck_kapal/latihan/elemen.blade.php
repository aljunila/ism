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
            url: "/ck_kapal/latihan/databyIdp",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->id}}",
                d.id_perusahaan= "{{ $id_perusahaan}}",
                d.id_kapal= $('#id_kapal').val(),
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
            { data: 'date',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            {
                data: null,
                name: null,
                render: function (data, type, row) {
                    return `${row.kapal} <button type="button"  onclick="openDetail(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Barang">
                    Gambar & Video</button>`;
                }
            },    
            { data: 'pelabuhan', name: 'pelabuhan' },
            { 
                data: null,
                render: function(data, type, row){
                    if(row.file) {
                    return `
                    <a href="{{ asset('checklist') }}/${row.file}" target="_blank" type="button" class="btn btn-icon btn-xs btn-flat-success" title="Buka File">
                            <i data-feather='file'></i>
                        </a>
                    `;
                } else {
                    return ``;
                }
                }
            },
            ],
                drawCallback: function(settings) {
                feather.replace(); 
            }
        });
    });

    $('#id_kapal').on('change', function () {
         table.ajax.reload();
    });

    function openDetail(id) {
        currentId = id;
        $('#DetailModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetail')) {
            DetailTable.ajax.url(`/ck_kapal/latihan/get/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetail').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `/ck_kapal/latihan/get/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: null,
                    render: function (data, type, row) {

                        let file = row.ket;
                        let url = "{{ asset('checklist') }}/" + file;
                        let ext = file.split('.').pop().toLowerCase();

                        let preview = '';

                        // kalau gambar → tampilkan thumbnail
                        if (['jpg','jpeg','png','gif'].includes(ext)) {
                            preview = `<img src="${url}" 
                                            style="width:40px;height:40px;object-fit:cover;border-radius:5px;margin-right:5px;">`;
                        } 
                        // kalau video → kasih icon
                        else if (['mp4','webm','ogg'].includes(ext)) {
                            preview = `🎥`;
                        } 
                        else {
                            preview = `📄`;
                        }
                        return `
                            <div style="display:flex;align-items:center;gap:5px;">
                                
                                <button type="button"
                                    class="btn btn-icon btn-xs btn-flat-primary btn-preview"
                                    data-file="${file}">
                                    ${preview}
                                </button>
                            </div>
                        `;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-icon btn-xs btn-flat-primary btn-preview" data-file="${row.ket}">
                            ${row.ket}
                            </button>
                        `;
                    }
                }
            ]
        });
    }

    $(document).on('click', '.btn-preview', function () {

        let file = $(this).data('file');
        let url = "{{ asset('checklist') }}/" + file;

        let ext = file.split('.').pop().toLowerCase();

        let html = '';

        // gambar
        if (['jpg','jpeg','png','gif'].includes(ext)) {
            html = `<img src="${url}" class="img-fluid">`;
        }

        // video
        else if (['mp4','webm','ogg'].includes(ext)) {
            html = `
                <video controls class="w-100">
                    <source src="${url}" type="video/${ext}">
                </video>
            `;
        }
        else {
            html = `<a href="${url}" target="_blank">Download File</a>`;
        }

        $('#previewContent').html(html);
        $('#filePreviewModal').modal('show');
    });
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="col-12"><h4 class="card-title">{{$form->nama}}</h4></div>
                        @include('kapal')
                        <div class="col-3">
                            <!-- <a href="/checklist/add/{{$form->kode}}" class="btn btn-primary btn-sm">Tambah Data</a> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama Kapal</th>
                            <th>Pembuat Permintaan</th>
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
<div class="modal fade" id="DetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableDetail" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar/Video</th>
                            <th>Judul</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Preview File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center" id="previewContent">
                <!-- isi preview -->
            </div>

        </div>
    </div>
</div>
@endsection