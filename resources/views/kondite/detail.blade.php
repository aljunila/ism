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
            url: "/kondite/datadetail",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->kode}}",
                d.id= "{{ $periode->id}}",
                d._token= "{{ csrf_token() }}"
            },
        },
        columns: [
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + 1; 
                }
            },
            { data: 'karyawan' },
            { data: 'jabatan' },
            { 
                data: 'id',
                render: function(data, type, row){
                    return `
                        <button type="button" class="btn btn-icon rounded-circle btn-xs btn-flat-warning form-btn" 
                            title="Edit" data-id="${data}">
                            <i data-feather="edit"></i>
                        </button>
                        ${row.note ? `
                         <a href="/kondite/pdf/${row.uid}" type="button" class="btn btn-icon btn-xs btn-flat-primary download" target="_blank" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        ` : ''}
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
        url: "/kondite/getChecklist",
        type: "POST",
        data: {
            kode: "{{$form->kode}}",
            id: id,
            _token: "{{ csrf_token() }}"
        },
        success: function(respons) {
            console.log("Response:", respons);
            formitem(respons.data);
        }
    });

    $.ajax({
        url: "/kondite/getKondite",
        type: "POST",
        data: {
            kode: "{{$form->kode}}",
            id: id,
            _token: "{{ csrf_token() }}"
        },
        success: function(data) {
            $('#FormIsi').modal('show');
            $('#iddata').val(id);
            $('#note').val(data.note);
            $('#rekomendasi').val(data.rekomendasi).trigger('change');
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
                            value="2" ${data == 2 ? 'checked' : ''}>
                    `;
                }
            },
            {
                data: 'value',
                render: function(data, type, row) {
                    return `
                    <input type="radio" class="form-check-input"
                            name="item[${row.id}]"
                            value="3" ${data == 3 ? 'checked' : ''}>
                    `;
                }
            },
            {
                data: 'value',
                render: function(data, type, row) {
                    return `
                    <input type="radio" class="form-check-input"
                            name="item[${row.id}]"
                            value="4" ${data == 4 ? 'checked' : ''}>
                    `;
                }
            },
        ],
    });
}

$(document).on('submit', '#form_checklist', function(e){
     e.preventDefault();
    console.log("MASUK SUBMIT");

    let formData = new FormData(this);
    console.log("FORMDATA SELESAI");

    $.ajax({
        url: '/kondite/saveform',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: res.message ?? "Data berhasil disimpan",
                showConfirmButton: false,
                timer: 1500
            });
             $('#FormIsi').modal('hide');
            table.ajax.reload();
        },
        error: function(err){
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
                        <div class="col-12">
                            <h4 class="card-title">{{$form->nama}} <br><br></h4>
                            <p>Kapal : {{$periode->get_kapal()->nama}} <br>
                            Periode : {{$periode->bulan}} {{$periode->tahun}}</p>
                        </div>
                </div>
                <div class="card-body">
                <table id="table" class="table table-bordered table-striped" width="100%">
                    <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="50%">Nama Crew</th>
                        <th width="25%">Jabatan</th>
                        <th width="20%">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-start" id="FormEdit" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_edit" enctype="multipart/form-data">
                        @csrf
                <div class="modal-body">
                    <label>Perusahaan </label>
                        

                        <label>Bulan</label>
                        <div class="mb-1">
                            <select class="form-control" name="bulan" id="bulan">
                                <option value="">Pilih</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <label>Tahun</label>
                        <div class="mb-1">
                            <select class="form-control" name="tahun" id="tahun">
                                <option value="">Pilih</option>
                                @for($a=2020; $a<=2040; $a++)
                                    <option value="{{ $a }}" {{ $a == date('Y') ? 'selected' : '' }}>{{ $a }}</option>
                                @endfor
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id">
                    <button type="submit" class="btn btn-primary" id="edit_data">Simpan</button>
                </div>
            </form>
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
                        <table id="tablecheck" class="table table-bordered table-striped" width="100%">
                            <thead>
                            <tr>
                                <td width="60%">Kriteria</td>
                                <td width="10%">Kurang</td>
                                <td width="10%">Sedang</td>
                                <td width="10%">Baik</td>
                                <td width="10%">Sangat Baik</td>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Rekomendasi Penilai</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="rekomendasi" id="rekomendasi" class="form-control">
                                    <option value="1">Dapat dipromosikan </option>
                                    <option value="2">Dapat dipertahankan </option>
                                    <option value="3">Perlu peningkatan kemampuan profesinya  </option>
                                    <option value="4">Perlu peningkatan akhlak dan budi pekertinya </option>
                                    <option value="5">Direkomendasikan turun dari kapal </option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Catatan</label>
                            </div>
                            <div class="col-sm-9">
                                <textarea name="note" id="note" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Penilai I</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="id_penilai_1" id="id_penilai_1" class="form-control">
                                    @foreach($penilai as $p)
                                        <option value="{{$p->id}}">{{$p->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Penilai II</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="id_penilai_2" id="id_penilai_2" class="form-control">
                                    @foreach($penilai as $p)
                                        <option value="{{$p->id}}">{{$p->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Mengetahui</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="id_mengetahui" id="id_mengetahui" class="form-control">
                                    @foreach($penilai as $p)
                                        <option value="{{$p->id}}">{{$p->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>    
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
