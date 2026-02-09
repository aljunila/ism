@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Kondite - {{$periode->get_kapal()->nama}} ({{$periode->bulan}} - {{$periode->tahun}})</h4>
        <!-- <a type="button" href="/data_crew/familiarisasi/form" class="btn btn-primary btn-sm">Tambah Data</a> -->
         <button class="btn btn-primary btn-sm" id="btn-add-kondite">Tambah Data</button>
    </div>
    <div class="card-body">
        <table id="table-kondite" class="table table-striped w-100">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Nama Crew</th>
                    <th width="25%">Jabatan</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-kondite" tabindex="-1" aria-labelledby="modal-kondite-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-kondite-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_checklist" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label" id="kondite-karyawan"></label> -
                    <label class="form-label" id="kondite-jabatan"></label>
                </div>
                 <div class="mb-1">
                    <label class="form-label">Penilaian</label>
                   <table class="table table-bordered table-striped" border="1">
                        <tr>
                            <td>Kriteria</td>
                            <td>Nilai</td>
                        </tr>
                            @foreach($item as $c)
                            <tr>
                                <td>{!!$c->item!!}</td>
                                <td><select name="item[{{$c->id}}]" class="form-control">
                                    <option value="1"> Sangat baik</option>
                                    <option value="2">Baik</option>
                                    <option value="3">Sedang</option>
                                    <option value="4">Kurang</option>
                                </select></td>
                            </tr>
                            @endforeach
                    </table>
                </div>
                <div class="mb-1">
                    <label class="form-label">Rekomendasi</label>
                   <select id="kondite-rekomendasi" name="rekomendasi" class="form-control">
                        <option value="1">Dapat dipromosikan </option>
                        <option value="2">Dapat dipertahankan </option>
                        <option value="3">Perlu peningkatan kemampuan profesinya  </option>
                        <option value="4">Perlu peningkatan akhlak dan budi pekertinya </option>
                        <option value="5">Direkomendasikan turun dari kapal </option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Catatan</label>
                    <textarea id="kondite-note" name="note" class="form-control"></textarea>
                </div>
                <div class="mb-1">
                    <label class="form-label">Penilai 1</label>
                    <select id="kondite-id_penilai_1" name="id_penilai_1" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($penilai as $k)
                            <option value="{{$k->id}}">{{$k->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Penilai 2</label>
                    <select id="kondite-id_penilai_2" name="id_penilai_2" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($penilai as $p)
                            <option value="{{$p->id}}">{{$p->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Mengatahui</label>
                    <select id="kondite-id_mengetahui" name="id_mengetahui" class="form-control">
                        <option value="">-Pilih-</option>
                        @foreach($penilai as $m)
                            <option value="{{$m->id}}">{{$m->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="kondite-id" name="id">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scriptfooter')
<script src="{{ url('/assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('/app-assets/vendors/js/tom-select.min.js') }}"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-kondite').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/data_crew/kondite/datakondite",
                type: "POST",
                data: function(d){
                    d.id= "{{ $periode->id}}",
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'karyawan', name: 'karyawan' },
                { data: 'jabatan', name: 'jabatan' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            
        });

        $(document).on('submit', '#form_checklist', function(e){
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: '/data_crew/kondite/savedata',
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
                    $('#modal-kondite').modal('hide');
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

        $(document).on('click', '.btn-edit-kondite', function () {
            const btn = $(this);
            let data = $(this).data('data');

            if (typeof data === 'string') {
                data = JSON.parse(data);
            }
            
            console.log(data);
            $.each(data.item, function (itemId, value) {
                $(`select[name="item[${itemId}]"]`)
                    .val(value)
                    .trigger('change');
            });
            $('#modal-kondite-label').text('Edit Data');
            $('#kondite-karyawan').text(btn.data('karyawan'));
            $('#kondite-jabatan').text(btn.data('jabatan'));
            $('#kondite-rekomendasi').val(btn.data('rekomendasi'));
            $('#kondite-note').val(btn.data('note'));
            $('#kondite-id').val(btn.data('id'));
            $('#kondite-id_penilai_1').val(btn.data('id_penilai_1')).trigger('change');
            $('#kondite-id_penilai_2').val(btn.data('id_penilai_2')).trigger('change');
            $('#kondite-id_mengetahui').val(btn.data('id_mengetahui')).trigger('change');
            $('#btn-save-kondite').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-kondite').modal('show');
        });

    });
</script>
@endsection
