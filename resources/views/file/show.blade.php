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
    $('#karyawan').hide();
    $('#kapal').hide();

    table = $("#table").DataTable({
        processing: true,
        serverSide: false, // kalau data sedikit cukup false, kalau ribuan bisa true
        ajax:{
            url: "/file/data",
            type: "POST",
            data: function(d){
                d.kode= $('#kode').val(),
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
            { data: 'type',
                render: function(data) {
                    return data == 'P' ? 'Perusahaan' :
                        data == 'K' ? 'Kapal' :
                        'Karyawan';
                }
             },
            { data: 'nama' },
            { data: 'ket' },
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

$('#tambah_data').click(function(){
    let nama = $('#nama').val();
    let type = $('#type').val();
    let kapal = $('#kapal').val();
    let karyawan = $('#karyawan').val();

     $.ajax({
        url: "{{ url('/file/store') }}",
        type: "POST",
        data: {
            nama: nama,
            type: type,
            kapal: kapal,
            karyawan: karyawan,
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
                $("#nama").val("");
                $('#FormTambah').modal('hide');
                table.ajax.reload();
            
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
})

$(document).on('click', '.edit-btn', function(){
     var id = $(this).attr('data-id');
    $.ajax({
    // url : "{{url('/getnilai')}}?id="+id,
      url: '/file/edit/'+id,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
         console.log(data);
        $('#id_file').val(data.id);
        $('#nama_file').val(data.nama);
        $('#ket_kapal').val(data.ket).trigger('change');
        $('#ket_karyawan').val(data.ket).trigger('change');
        $('#tipe').val(data.type);
        $('#no_file').val(data.no_urut);
        let file = data.type; // atau ambil dari input
        if (file === 'S') {
            $('#file').html('Karyawan');
            $('.karyawan').show();
            $('.kapal').hide();
        } else if (file === 'K') {
            $('#file').html('Kapal');
            $('.kapal').show();
            $('.karyawan').hide();
        } else {
            $('#file').html('Perusahaan');
            $('.karyawan').hide();
            $('.kapal').hide();
        }
        $('#FormEdit').modal('show');
      }
    });
})

$(document).on('click', '#edit_data', function(){
    let nama_file = $('#nama_file').val()
    let id = $('#id_file').val()
    let type = $('#tipe').val();
    let kapal = $('#ket_kapal').val();
    let no = $('#no_file').val();
    let karyawan = $('#ket_karyawan').val();

    if (!nama_file.trim()) {
        Swal.fire({
            icon: "warning",
            title: "Oops...",
            text: "Nama file tidak boleh kosong!"
        });
        return;
    }
     $.ajax({
        url: "/file/update/" + id,
        type: "POST",
        data: {
            nama: nama_file,
            id: id,
            type: type,
            kapal: kapal,
            karyawan: karyawan,
            no: no,
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
                $("#nama").val("");
                $('#FormEdit').modal('hide');
                table.ajax.reload();
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
                url: "/file/delete/" + id,
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

$('#kode').on('change', function(){
    table.ajax.reload();
});

$(document).on('change', '#type', function() {
    let type = $(this).val();
    if(type=='K'){
        $('.kapal').show();
        $('.karyawan').hide();
    } else if(type=='S') {
        $('.kapal').hide();
        $('.karyawan').show();
    } else {
        $('.karyawan').hide();
        $('.kapal').hide();
    }
})
</script>
@endsection
@section('content')
<section id="complex-header-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="col-sm-12"><h4 class="card-title">Daftar File Upload</h4></div>
                        <div class="col-sm-4">
                            <select name="kode" id="kode" class="form-control">
                                <option value="">Semua</option>
                                <option value="P">Perusahaan</option>
                                <option value="K">Kapal</option>
                                <option value="S">Karyawan</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#FormTambah">Tambah Data</button>
                    </div>
                    <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                      <thead>
                        <tr>
                          <th width="5%">No.</th>
                          <th width="25%">File</th>
                          <th width="30%">Nama File</th>
                          <th width="25%">Keterangan</th>
                          <th width="15%">Aksi</th>
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
            <div class="modal-body">
                    <label>File</label>
                    <div class="mb-1">
                        <p id="file"></p>
                    </div>
                    <div class="mb-1">
                        <select name="ket" id="ket_kapal" class="kapal form-control" required>
                            <option value="">-</option>
                            <option value="Perhubungan Laut">Perhubungan Laut</option>
                            <option value="BKI">BKI</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <select name="ket" id="ket_karyawan" class="karyawan form-control" required>
                            <option value="">-</option>
                            <option value="Crew Laut">Crew Laut</option>
                            <option value="Crew Darat">Crew Darat</option>
                        </select>
                    </div>
                    <label>Nama File</label>
                    <div class="mb-1">
                        <input type="text" placeholder="Nama" class="form-control" name="nama" id="nama_file"/>
                    </div>
                    <label>No Urut</label>
                    <div class="mb-1">
                        <input type="number" placeholder="No Urut" class="form-control" name="no" id="no_file"/>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id_file" id="id_file">
                <input type="hidden" placeholder="Nama" class="form-control" name="tipe" id="tipe"/>
                <button type="submit" class="btn btn-primary" id="edit_data">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-start" id="FormTambah" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Tambah Data</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    <label>File</label>
                    <div class="mb-1">
                        <select name="type" id="type" class="form-control" required>
                            <option value="P">Perusahaan</option>
                            <option value="K">Kapal</option>
                            <option value="S">Karyawan</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <select name="ket" id="kapal" class="kapal form-control" required>
                            <option value="">-</option>
                            <option value="Perhubungan Laut">Perhubungan Laut</option>
                            <option value="BKI">BKI</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <select name="ket" id="karyawan" class="karyawan form-control" required>
                            <option value="">-</option>
                            <option value="Crew Laut">Crew Laut</option>
                            <option value="Crew Darat">Crew Darat</option>
                        </select>
                    </div>
                    <label>Nama File </label>
                    <div class="mb-1">
                        <input type="text" placeholder="Nama" class="form-control" name="nama" id="nama" required/>
                    </div>

                     <label>No Urut</label>
                    <div class="mb-1">
                        <input type="number" placeholder="No Urut" class="form-control" name="no" id="no"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="tambah_data">Simpan</button>
                </div>
        </div>
    </div>
</div>
</section>

@endsection