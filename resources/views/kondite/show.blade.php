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
            url: "/kondite/data",
            type: "POST",
            data: function(d){
                d.kode= "{{ $form->kode}}",
                d.id_perusahaan= $('#id_perusahaan').val(),
                d.id_kapal= $('#id_kapal').val(),
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
            { data: 'kapal' },
            { 
                render: function (data, type, row, meta) {
                    const namaBulan = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                     return `${namaBulan[parseInt(row.bulan) - 1]} ${row.tahun}`;
                }
            },
            { 
                data: 'id',
                render: function(data, type, row){
                    return `
                         <a type="button" href="/kondite/detail/${row.uid}" class="btn btn-icon rounded-circle btn-xs btn-flat-success" >
                            <i data-feather='log-in'></i>
                        </a>
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

$('#form_refrensi').on('submit', function(e){
    e.preventDefault(); // cegah submit biasa

    let formData = new FormData(this);

    $.ajax({
        url: "{{ url('/kondite/store') }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response){
            if (response.error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: response.message ?? "Periode tersebut telah dibuat",
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message ?? "Data berhasil disimpan",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
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
      url: '/kondite/edit/'+id,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
         console.log(data);
        $('#id').val(data.id);
        $('#id_kp').val(data.id_kapal);
        $('#idp').val(data.id_perusahaan).trigger('change').prop('disabled', true);
        $('#idk').val(data.id_kapal).trigger('change').prop('disabled', true);
        $('#bulan').val(data.bulan).trigger('change');
        $('#tahun').val(data.tahun).trigger('change');
        $('#FormEdit').modal('show');
      }
    });
})

$('#form_edit').on('submit', function(e){
    e.preventDefault(); 
    let id = $('#id').val()
    let formData = new FormData(this);
    
    $.ajax({
        url: "/kondite/update/" + id,
        type: "POST",
        data: formData,
         processData: false,
        contentType: false,
        success: function(response) {
            if (response.error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: response.message ?? "Periode tersebut telah dibuat",
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message ?? "Data berhasil disimpan",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            
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
                url: "/kondite/delete/" + id,
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
    } else {
        $('.kapal').empty().append('<option value="">Tidak ada data</option>');
        table.ajax.reload();
    }
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
                                @include('filter')
                            <div class="col-3">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#FormTambah">Tambah Data</button>
                                <a href="/checklist/item/{{$form->kode}}" class="btn btn-danger btn-sm">Setting Form</a>
                            </div>
                    </div>
                    <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                      <thead>
                        <tr>
                          <th width="5%">No.</th>
                          <th width="50%">Nama Kapal</th>
                          <th width="25%">Periode</th>
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
                    <div class="mb-1">
                        <select name="id_perusahaan" id="idp" class="form-control">
                            <option value="">Pilih</option>
                            @foreach($perusahaan as $p)
                                <option value="{{$p->id}}">{{$p->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label>Kapal </label>
                    <div class="mb-1">
                        <select name="idk" id="idk" class="form-control">
                            <option value="">Pilih</option>
                            @foreach($kapal as $k)
                                <option value="{{$k->id}}">{{$k->nama}}</option>
                            @endforeach
                        </select><input type="hidden" name="id_kapal" id="id_kp">
                    </div>

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

<div class="modal fade text-start" id="FormTambah" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Tambah Data</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_refrensi" enctype="multipart/form-data">
                    @csrf
                <div class="modal-body">
                    <label>Perusahaan </label>
                    <div class="mb-1">
                        <select name="id_perusahaan" class="form-control perusahaan" required>
                            <option value="">Pilih</option>
                            @foreach($perusahaan as $p)
                                <option value="{{$p->id}}">{{$p->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label>Kapal </label>
                    <div class="mb-1">
                        <select name="id_kapal" class="form-control kapal" required>
                            <option value="">Pilih</option>
                            @foreach($kapal as $k)
                                <option value="{{$k->id}}">{{$k->nama}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label>Bulan</label>
                    <div class="mb-1">
                        <select class="form-control" name="bulan">
                            <option value="">Pilih</option>
                            <option value="01" {{ date('m') == "01" ? 'selected' : '' }}>Januari</option>
                            <option value="02" {{ date('m') == "02" ? 'selected' : '' }}>Februari</option>
                            <option value="03" {{ date('m') == "03" ? 'selected' : '' }}>Maret</option>
                            <option value="04" {{ date('m') == "04" ? 'selected' : '' }}>April</option>
                            <option value="05" {{ date('m') == "05" ? 'selected' : '' }}>Mei</option>
                            <option value="06" {{ date('m') == "06" ? 'selected' : '' }}>Juni</option>
                            <option value="07" {{ date('m') == "07" ? 'selected' : '' }}>Juli</option>
                            <option value="08" {{ date('m') == "08" ? 'selected' : '' }}>Agustus</option>
                            <option value="09" {{ date('m') == "09" ? 'selected' : '' }}>September</option>
                            <option value="10" {{ date('m') == "10" ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ date('m') == "11" ? 'selected' : '' }}>November</option>
                            <option value="12" {{ date('m') == "12" ? 'selected' : '' }}>Desember</option>
                        </select>
                    </div>

                    <label>Tahun</label>
                    <div class="mb-1">
                        <select class="form-control" name="tahun">
                            <option value="">Pilih</option>
                            @for($a=2020; $a<=2040; $a++)
                                <option value="{{ $a }}" {{ $a == date('Y') ? 'selected' : '' }}>{{ $a }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="kode" id="kode" value="{{$form->kode}}">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>

@endsection
