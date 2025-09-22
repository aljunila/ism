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
    $(function () {
		$('#table').DataTable({
        processing: true,
        searchable: true,
        ajax:{
            url: "/checklist/listGanti",
            type: "POST",
            data: function(d){
                d.kode= "el0307",
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
            { data: 'pelabuhan' },
            { data: 'kapal' },
            { data: 'dari' },
            { data: 'kepada' },
            { 
                data: null, 
                render: function (data, type, row) {
                    return `
                        <a type="button" data-id="${row.id}" class="btn btn-icon btn-xs btn-flat-success form-btn" title="Isi Form">
                                <i data-feather='edit'></i>
                            </a>
                        `;
                }
            },
            { 
                data: null, 
                render: function (data, type, row) {
                    let kode = @json($form->kode);
                        return `
                        <a href="/checklist/nahkodapdf/${row.uid}/${kode}" type="button" class="btn btn-icon btn-xs btn-flat-primary download" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        `;
                }
            }
        ],
         drawCallback: function(settings) {
            feather.replace();
        }
    });
    });

    $(document).on("click", ".form-btn", function() {
        let id = $(this).attr("data-id");
        console.log("ID:", id);

        $.ajax({
            url: "/checklist/getChecklist",
            type: "POST",
            data: {
                kode: "{{$form->kode}}",
                id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function(respons) {
                console.log("Response:", respons);

                formitem(respons.data);
                $('#FormIsi').modal('show');
                $('#iddata').val(id);
            },
            error: function(err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: "Gagal memuat data"
                });
            }
        });
    });

    function formitem(data) {
        $('#tablecheck').DataTable({
            destroy: true, 
            processing: false,
            searchable: false,
            data: data,
            columns: [
                { data: 'item' },
                {
                    data: 'value',
                    render: function(data, type, row) {
                        return `
                        <input type="radio" class="form-check-input"
                               name="item[${row.id}]"
                               value="1" ${data == 1 ? 'checked' : ''}>
                        `;
                    }
                },
                {
                    data: 'value',
                    render: function(data, type, row) {
                        return `
                        <input type="radio" class="form-check-input"
                               name="item[${row.id}]"
                               value="0" ${data == 0 ? 'checked' : ''}>
                        `;
                    }
                },
                {
                    data: 'ket',
                    render: function (data, type, row) {
                        return `
                            <input type="text" class="form-control"
                                name="ket[${row.id}]"
                                value="${data ?? ''}">
                        `;
                    }
                }
            ],
        });
    }

    $('#form_checklist').on('submit', function(e){
        e.preventDefault(); 
        let formData = new FormData(this);

        $.ajax({
            url: '/checklist/save',
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message ?? 'Data berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#FormIsi').modal('hide');
                    });
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menyimpan data'
                });
            }
        });
    });
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">{{$form->nama}}</h4>
                    <a href="/checklist/item/{{$form->kode}}" class="btn btn-danger btn-sm">Setting Form</a>
                </div>
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Pelabuhan</th>
                        <th>Nama Kapal</th>
                        <th>Dari</th>
                        <th>Kepada</th>
                        <th>Isi Form</th>
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

    <div class="modal fade text-start" id="FormIsi" tabindex="-1" aria-labelledby="myModalLabel17" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel17">Isi Form</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_checklist" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <table id="tablecheck" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <td>Uraian</td>
                                <td>Ya</td>
                                <td>Tidak</td>
                                <td>Keterangan</td>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="kode" id="kode" value="{{$form->kode}}">
                        <input type="hidden" name="id" id="iddata">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection