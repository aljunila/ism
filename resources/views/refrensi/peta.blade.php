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
            url: "/refrensi/data-peta",
            type: "POST",
            data: function(d){
                d.kode= "{{ $refrensi->kode}}",
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
            { data: 'no_bpi' },
            { data: 'tgl_terima',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { data: 'tgl_koreksi',
                render: function(data) {
                    if (!data) return '';
                    let parts = data.split(' ')[0].split('-'); 
                    return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                }
            },
            { data: 'jml' },
            { data: 'no_peta' },
            { data: 'perusahaan',
                render: function(data, type, row){
                   let kapal = '';

                    if (row.kapal === null || row.kapal === '') {
                    kapal = '';
                    } else {
                    kapal = `<br>${row.kapal}`;
                    }

                    return `${data}${kapal}`;
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

$('#form_refrensi').on('submit', function(e){
    e.preventDefault(); // cegah submit biasa

    let formData = new FormData(this);

    $.ajax({
        url: "{{ url('/refrensi/storepeta') }}",
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
                });
                $('#FormTambah').modal('hide');
                table.ajax.reload();
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
      url: '/refrensi/editpeta/'+id,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
         console.log(data);
        $('#id').val(data.id);
        $('#idp').val(data.id_perusahaan).trigger('change');
        setTimeout(() => {
            $('#idk').val(data.id_kapal).trigger('change');
        }, 500);
        $('#tgl_terima').val(data.tgl_terima);
        $('#tgl_koreksi').val(data.tgl_koreksi);
        $('#jml').val(data.jml);
        $('#no_peta').val(data.no_peta);
        $('#no_bpi').val(data.no_bpi);
        setTimeout(() => {
            $('#id_pj').val(data.id_pj).trigger('change');
        }, 500);
        $('#FormEdit').modal('show');
      }
    });
})

$('#form_edit').on('submit', function(e){
    e.preventDefault(); 
    let id = $('#id').val()
    let formData = new FormData(this);
    
    $.ajax({
        url: "/refrensi/updatepeta/" + id,
        type: "POST",
        data: formData,
         processData: false,
        contentType: false,
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
                url: "/refrensi/delpeta/" + id,
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
    let id_perusahaan = $('#id_perusahaan').val();
    let kode = "{{ $refrensi->kode}}";

    if (!id_perusahaan.trim()) {
        Swal.fire({
            icon: "warning",
            title: "Oops...",
            text: "Silahkan pilih perusahaan terlebih dahulu"
        });
        return;
    }
    
    let url = "{{ url('/refrensi/pdfpeta') }}" + "?kode=" + kode + "&id_perusahaan=" + id_perusahaan;
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
                                <h4 class="card-title">{{$refrensi->nama}}</h4>
                            </div>
                            @if($refrensi->kode=='el0101')
                                @include('perusahaan')
                            @else
                                @include('filter')
                            @endif
                            <div class="col-3">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#FormTambah">Tambah Data</button>
                                <button target="_blank" type="button" class="btn btn-success btn-sm float-right" id="btn-pdf">Cetak PDF</button>
                                <a href="/checklist/item/{{$refrensi->kode}}" class="btn btn-danger btn-sm">Instruksi</a>
                            </div>
                    </div>
                    <div class="card-body">
                    <table id="table" class="table table-bordered table-striped" width="100%">
                      <thead>
                        <tr>
                          <th width="5%">No.</th>
                          <th width="20%">No BPI</th>
                          <th width="10%">Tgl terima</th>
                          <th width="10%">Tgl koreksi</th>
                          <th width="10%">Jml titik yg dikoreksi</th>
                          <th width="10%">No peta</th>
                          <th width="25%">Berkas Milik</th>
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
                        <select name="idp" id="idp" class="form-control perusahaan" required>
                            <option value="">Pilih</option>
                            @foreach($perusahaan as $p)
                                <option value="{{$p->id}}">{{$p->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label>Kapal </label>
                    <div class="mb-1">
                        <select name="idk" id="idk" class="form-control kapal" required>
                            <option value="">Pilih</option>
                            @foreach($kapal as $k)
                                <option value="{{$k->id}}">{{$k->nama}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label>No BPI</label>
                    <div class="mb-1">
                        <input type="text" class="form-control" name="no_bpi" id="no_bpi"/>
                    </div>

                    <label>Tanggal Terima</label>
                    <div class="mb-1">
                        <input type="date" class="form-control" name="tgl_terima" id="tgl_terima"/>
                    </div>

                    <label>Tanggal Koreksi</label>
                    <div class="mb-1">
                        <input type="date" class="form-control" name="tgl_koreksi" id="tgl_koreksi"/>
                    </div>

                    <label>Jumlah titik yg dikoreksi </label>
                    <div class="mb-1">
                        <input type="number" class="form-control" name="jml" id="jml"/>
                    </div>

                    <label>No peta yg dikoerksi</label>
                    <div class="mb-1">
                        <input type="number" class="form-control" name="no_peta" id="no_peta"/>
                    </div>

                    <label>Nahkoda </label>
                    <div class="mb-1">
                        <select name="id_pj" id="id_pj" class="form-control karyawan" required>
                            @foreach($karyawan as $ky)
                                <option value="{{$ky->id}}">{{$ky->nama}}</option>
                            @endforeach
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
                        <select name="idp" class="form-control perusahaan" required>
                            <option value="">Pilih</option>
                            @foreach($perusahaan as $p)
                                <option value="{{$p->id}}">{{$p->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label>Kapal </label>
                    <div class="mb-1">
                        <select name="idk" class="form-control kapal" required>
                            <option value="">Pilih</option>
                            @foreach($kapal as $k)
                                <option value="{{$k->id}}">{{$k->nama}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label>No BPI</label>
                    <div class="mb-1">
                        <input type="text" class="form-control" name="no_bpi"/>
                    </div>

                    <label>Tanggal Terima</label>
                    <div class="mb-1">
                        <input type="date" class="form-control" name="tgl_terima"/>
                    </div>

                    <label>Tanggal Koreksi</label>
                    <div class="mb-1">
                        <input type="date" class="form-control" name="tgl_koreksi" />
                    </div>

                    <label>Jumlah titik yg dikoreksi </label>
                    <div class="mb-1">
                        <input type="number" class="form-control" name="jml" />
                    </div>

                    <label>No peta yg dikoerksi</label>
                    <div class="mb-1">
                        <input type="number" class="form-control" name="no_peta"/>
                    </div>

                    <label>Tanggung Jawab </label>
                    <div class="mb-1">
                        <select name="id_pj"  class="form-control karyawan" required>
                            @foreach($karyawan as $ky)
                                <option value="{{$ky->id}}">{{$ky->nama}}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="kode" id="kode" value="{{$refrensi->kode}}">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>

@endsection