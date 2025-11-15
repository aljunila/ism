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
    const previllage = "{{ Session::get('previllage') }}";
    let table;

    $(function () {
		table = $('#table').DataTable({
        processing: true,
        searchable: true,
        ajax:{
            url: "/review/data",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->kode}}",
                d.id_perusahaan= $('#id_perusahaan').val(),
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
            { data: 'kapal' },
            { data: 'no_review' },
            { data: 'tgl_review',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { data: 'tgl_diterima',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { 
                data: null, 
                render: function (data, type, row) {
                        return `
                        <a href="/review/pdf/${row.uid}" type="button" class="btn btn-icon btn-xs btn-flat-primary download" target="_blank" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        `;
                }
            },
            { 
                data: null, 
                render: function (data, type, row) {
                    if (previllage != 3) {
                        btnDPA = `
                            <a type="button" 
                                class="dropdown-item dpa-btn"
                                title="Tanggapan DPA" 
                                data-id="${row.id}">
                                Tanggapan DPA
                            </a>`;
                    }
                    return `
                        <div class="btn-group">
                            <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather='edit-3'></i></button>
                            <div class="dropdown-menu">

                                <a type="button" href="/review/edit/${row.uid}" class="dropdown-item">Edit</a>
                                ${btnDPA}
                                <a type="button" data-id="${row.id}" class="dropdown-item delete-btn">Hapus</a>
                            </div>
                        </div>
                    `;
                }
            }
        ],
         drawCallback: function(settings) {
            feather.replace(); // supaya icon feather muncul ulang
        }
    });
    });

    $(document).on("click", ".delete-btn", function(){
        let id = $(this).data("id");

        Swal.fire({
            title: "Yakin mau hapus?",
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/review/delete/" + id,
                    type: "post",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res){
                        Swal.fire({
                            icon: "success",
                            title: "Terhapus!",
                            text: "Data berhasil dihapus",
                            timer: 2000,
                            showConfirmButton: false
                        });
                        table.ajax.reload();
                    },
                    error: function(err){
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: "Data gagal dihapus"
                        });
                    }
                });
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

    $('#id_kapal').on('change', function () {
         table.ajax.reload();
    });

    $(document).on('click', '.dpa-btn', function(){
        var id = $(this).attr('data-id');
        $.ajax({
        // url : "{{url('/getnilai')}}?id="+id,
        url: '/review/get/'+id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            console.log(data);
            $('#id').val(data.id);
            $('#no_review').html(data.no_review);
            $('#tgl_review').html(data.tgl_review);
            $('#hasil').html(data.hasil);
            $('#tgl_diterima').val(data.tgl_diterima);
            tinymce.get('ket').setContent(data.ket);
            $('#FormDPA').modal('show');
        }
        });
    })

    $(document).on('click', '#edit_data', function(){
        let id = $('#id').val()
        let tgl_diterima = $('#tgl_diterima').val()
        let ket = $('#ket').val()
        if (!ket.trim()) {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Silahkan isi tanggapan terlebih dahulu"
            });
            return;
        }
        $.ajax({
            url: "/review/updatedpa/" + id,
            type: "POST",
            data: {
                tgl_diterima: tgl_diterima,
                ket: ket,
                id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message ?? "Data berhasil disimpan",
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // reset input
                    $("#ket").val("");
                    $("#tgl_review").val("");
                    $('#FormDPA').modal('hide');
                    $('#table').DataTable().ajax.reload();
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Tidak dapat menyimpan data"
                });
                console.error(xhr.responseText);
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
                    <div class="col-12"><h4 class="card-title">{{$form->nama}}</h4></div>
                    @include('filter')
                    <div class="col-3">
                            <a href="/review/add/{{$form->kode}}" class="btn btn-primary btn-sm">Tambah Data</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                        <th>No.</th>
                        <th>Nama Kapal</th>
                        <th>No Review</th>
                        <th>Tanggal Review</th>
                        <th>Tanggal Diterima</th>
                        <th>PDF</th>
                        <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormDPA" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Tanggapan DPA</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-1">
                        <div class="col-sm-3"><label>No Review :</label></div>
                        <div class="col-sm-1"><label> :</label></div>
                        <div class="col-sm-8"><p id="no_review"></p></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-sm-3"><label>Tgl Review :</label></div>
                        <div class="col-sm-1"><label> :</label></div>
                        <div class="col-sm-8"><p id="tgl_review"></p></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-sm-3"><label>Hasil review nahkoda </label></div>
                        <div class="col-sm-1"><label> :</label></div>
                        <div class="col-sm-8"><p id="hasil"></p></div>
                    </div>
                    <hr>
                    <label>Tanggal Diterima</label>
                    <div class="mb-1">
                        <input type="date" name="tgl_diterima" id="tgl_diterima" class="form-control">
                    </div>
                    <label>Tanggapan</label>
                    <div class="mb-1">
                        <textarea name="ket" id="ket" class="tinymce form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id">
                    <button type="submit" class="btn btn-primary" id="edit_data">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection