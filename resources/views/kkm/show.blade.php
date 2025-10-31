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
            url: "/kkm/data",
            type: "POST",
            data: function(d){
                d.id_perusahaan= $('#id_perusahaan').val(),
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
            { data: 'tanggal',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { data: 'nomer' },
            { data: 'lama' },
            { 
                data: null, 
                render: function (data, type, row) {
                        return `
                        <a href="/kkm/pdf/${row.uid}" type="button" target="_blank" class="btn btn-icon btn-xs btn-flat-primary download" title="Cetak PDF">
                                <i data-feather='printer'></i>
                            </a>
                        `;
                }
            },
            { 
                data: 'id',
                render: function(data, type, row){
                    return `
                        <button type="button" class="btn btn-icon rounded-circle btn-xs btn-flat-warning edit-btn" 
                            title="Edit" data-id="${data}">
                            <i data-feather="edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon rounded-circle btn-xs btn-flat-danger delete-btn" 
                            title="Hapus" data-id="${data}">
                            <i data-feather="trash-2"></i>
                        </button>
                    `;
                }
            }
        ],
        drawCallback: function(settings) {
            feather.replace(); // supaya icon feather muncul ulang
        }
    });
});

   $('#form_checklist').on('submit', function(e){
        e.preventDefault(); 
        let formData = new FormData(this);

        $.ajax({
            url: '/kkm/store',
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
                        $('#FormTambah').modal('hide');
                        $("#table").DataTable().ajax.reload();
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

$(document).on('click', '.edit-btn', function(){
     var id = $(this).attr('data-id');
    $.ajax({
    // url : "{{url('/getnilai')}}?id="+id,
      url: '/kkm/edit/'+id,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
         console.log(data);
        $('#id').val(data.id);
        $('#id_kepada').val(data.id_kepada);
        $('#nomer').val(data.nomer);
        $('#tanggal').val(data.tanggal);
        $('#jam').val(data.jam);
        $('#id_lama').val(data.id_lama);
        $('#id_baru').val(data.id_baru);
        $('#fo').val(data.fo);
        $('#do').val(data.do);
        $('#fw').val(data.fw);
        $('#FormTambah').modal('show');
      }
    });
})


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
                url: "/kkm/delete/" + id,
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

$(document).on('change', '.perusahaan', function() {
    var perusahaanID = $(this).val();
    if (perusahaanID) {
        $.ajax({
            url: '/get-kapal/' + perusahaanID,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('.kapal').empty().append('<option value="">Semua</option>');           
                $.each(data, function(key, value) {
                    $('.kapal').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                });
                table.ajax.reload();
            }
        });
        $.ajax({
            url: '/get-karyawanbyCom/' + perusahaanID,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('.karyawan').empty().append('<option value="">Semua</option>');           
                $.each(data, function(key, value) {
                    $('.karyawan').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                });
            }
        });
    } else {
        $('.kapal').empty().append('<option value="">Tidak ada data</option>');
        $('.karyawan').empty().append('<option value="">Tidak ada data</option>');
        table.ajax.reload();
    }
});

$('#id_kapal').on('change', function () {
        table.ajax.reload();
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
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#FormTambah">Tambah Data</button>
                    </div>
                </div>
                <div class="card-body">
                <table id="table" class="table table-bordered table-striped" width="100%">
                    <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="15%">Tanggal</th>
                        <th width="20%">Nomer</th>
                        <th width="40%">KKM</th>
                        <th width="10%">PDF</th>
                        <th width="10%">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade text-start" id="FormTambah" tabindex="-1" aria-labelledby="myModalLabel17" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel17">Tambah Data</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_checklist" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-12">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Perusahaan</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="idp" id="idp"  class="form-control perusahaan" required>
                                @foreach($perusahaan as $p)
                                    <option value="{{$p->id}}">{{$p->nama}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Kapal</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="idk" id="idk"  class="form-control kapal" required>
                                @foreach($karyawan as $kp)
                                    <option value="{{$kp->id}}">{{$kp->nama}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Nomer</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nomer" id="nomer">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Kepada</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="id_kepada" id="id_kepada"  class="form-control karyawan" required>
                                @foreach($karyawan as $k)
                                    <option value="{{$k->id}}">{{$k->nama}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Tanggal</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="tanggal" id="tanggal">
                            </div>
                        </div><div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">Jam</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="jam" id="jam">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">KKM Lama</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="id_lama" id="id_lama"  class="form-control karyawan" required>
                                @foreach($karyawan as $k)
                                    <option value="{{$k->id}}">{{$k->nama}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">KKM Baru</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="id_baru" id="id_baru"  class="form-control karyawan" required>
                                @foreach($karyawan as $k)
                                    <option value="{{$k->id}}">{{$k->nama}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">FO</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="fo" id="fo">
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label" for="first-name">M/T</label>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">DO</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="do" id="do">
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label" for="first-name">M/T</label>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label" for="first-name">FW</label>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="fw" id="fw">
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label" for="first-name">M/T</label>
                            </div>
                        </div>
                    </div>
                </div>    
                <div class="modal-footer">
                    <input type="hidden" class="form-control" name="id" id="id">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>

@endsection