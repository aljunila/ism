@extends('main')

@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="col-sm-12"><h4 class="card-title">Data Docking</h4></div>
    </div>
    <div class="table-responsive">
        <div class="col-sm-4">
        <table class="table" width="50%">      
            <tr>
                <td> Nama Kapal</td>
                <td>:</td>
                <td>{{ $show->get_kapal()->nama }}</td>
            </tr>
             <tr>
                <td> Jadwal Docking</td>
                <td>:</td>
                <td>{{ $show->bulan }} - {{ $show->tahun }}</td>
            </tr>
            <tr>
                <td> Tempat</td>
                <td>:</td>
                <td>{{ $show->tempat }}</td>
            </tr>
            <tr>
                <td> Tanggal Docking</td>
                <td>:</td>
                <td>{{ $show->tgl_mulai ? \Carbon\Carbon::parse($show->tgl_mulai)->format('d-m-Y') : '' }} s/d
                    {{ $show->tgl_selesai ? \Carbon\Carbon::parse($show->tgl_selesai)->format('d-m-Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td> Durasi</td>
                <td>:</td>
                <td>{{ $show->durasi }} hari</td>
            </tr>
        </table>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="col-sm-12"><h4 class="card-title">Rincian Biaya Docking</h4></div>
        <div class="col-sm-6"></div>
        <div class="col-sm-2"><button class="btn btn-primary btn-sm" id="btn-add-biaya">Tambah Data</button></div>
    </div>
    <div class="card-body">
        <table id="table-biaya" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Job</th>
                    <th>Sub Job</th>
                    <th>Deskripsi</th>
                    <th>Unit</th>
                    <th>Volume</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="text-end mt-2">
            <strong>
                Grand Total:
                <span id="grand-total">Rp 0</span>
            </strong>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-biaya" tabindex="-1" aria-labelledby="modal-biaya-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-biaya-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Job</label>
                        </div>
                        <div class="col-9">
                            <select id="biaya-job" class="form-control">
                                @foreach ($job as $j)
                                    <option value="{{$j->id}}">{{$j->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Subjob</label>
                        </div>
                        <div class="col-9">
                            <select id="biaya-subjob" class="form-control">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Deskripsi</label>
                        </div>
                        <div class="col-9">
                            <textarea id="biaya-deskripsi" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Unit</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="biaya-unit" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Volume</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="biaya-volume" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Harga</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="biaya-harga" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Total</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="biaya-total" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Keterangan</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="biaya-keterangan" class="form-control">
                            <input type="hidden" id="biaya-docking" class="form-control" value="{{$show->id}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-biaya">Simpan</button>
            </div>
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
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-biaya').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/data_teknik/docking/databiaya",
                type: "POST",
                data: function(d){
                    d.id_docking= "{{$show->id}}",
                    d._token= "{{ csrf_token() }}"
                },
                dataSrc: function(json) {
                    $('#grand-total').html(
                        'Rp ' + Number(json.grand_total || 0).toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2
                        })
                    );
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'job', name: 'job' },
                { data: 'subjob', name: 'subjob' },
                { data: 'deskripsi', name: 'deskripsi' },
                { data: 'unit', name: 'unit' },
                { data: 'volume', name: 'volume' },
                {
                    data: 'harga',
                    name: 'harga',
                    render: function(data, type, row) {
                        if (data == null || data === '') return '';

                            return 'Rp ' +  Number(data).toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2
                        });
                    }
                },
                {
                    data: 'total',
                    name: 'total',
                    render: function(data, type, row) {
                        if (data == null || data === '') return '';

                            return 'Rp ' +  Number(data).toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2
                        });
                    }
                },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const resetForm = () => {
            $('#modal-biaya-label').text('Tambah Data');
            $('#biaya-job').val('');
            $('#biaya-subjob').val('');
            $('#biaya-deskripsi').val('');
            $('#biaya-unit').val('');
            $('#biaya-volume').val('');
            $('#biaya-harga').val('');
            $('#biaya-total').val('');
            $('#biaya-keterangan').val('');
            $('#btn-save-biaya').data('mode', 'create').data('id', '');
        };

        $('#btn-add-biaya').on('click', function () {     
            resetForm();
            $('#modal-biaya').modal('show');
        });

        $('#btn-save-biaya').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const volume = parseVolume($('#biaya-volume').val()); 
            const harga = parseAngka($('#biaya-harga').val()); 
            const total = volume * harga; 

            let formData = new FormData();
            formData.append('volume', volume); 
            formData.append('harga', harga); 
            formData.append('total', total);
            formData.append('id_subjob', $('#biaya-subjob').val());
            formData.append('id_docking', $('#biaya-docking').val());
            formData.append('deskripsi', $('#biaya-deskripsi').val());
            formData.append('job', $('#biaya-job').val());
            formData.append('unit', $('#biaya-unit').val());
            formData.append('keterangan', $('#biaya-keterangan').val());

            const ajaxOpts = {
                url: mode === 'edit'
                    ? '{{ url('data_teknik/docking/biaya') }}/' + id
                    : '{{ route('docking.storebiaya') }}',
                type: mode === 'edit' ? 'POST' : 'POST', 
                data: formData,
                processData: false,
                contentType: false
            };
            if (mode === 'edit') {
                formData.append('_method', 'PUT');
            }

            $.ajax(ajaxOpts)
            .done(res => {
                console.log(res);
                Swal.fire(res.status, res.message, res.status)
                    .then(() => {
                        if (res.status === 'Berhasil') {
                            $('#modal-biaya').modal('hide');
                            table.ajax.reload(null, false);
                        }
                    });
            })
            // .done(() => {
            //     Swal.fire('Sukses', mode === 'edit' ? 'Pelabuhan diperbarui' : 'Pelabuhan ditambahkan', 'success');
            //     $('#modal-biaya').modal('hide');
            //     table.ajax.reload(null, false);
            // })
            // .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
            .fail(xhr => {
                Swal.fire(
                    'Gagal',
                    xhr.responseJSON?.message || 'Error',
                    'error'
                );
            });
        });

        $(document).on('click', '.btn-edit-biaya', function () {
            const btn = $(this);
            $('#biaya-harga').val(
                formatNominal(parseFloat(btn.data('harga')) || 0)
            );

            $('#biaya-total').val(
                formatNominal(parseFloat(btn.data('total')) || 0)
            );
            $('#modal-biaya-label').text('Edit Data');
            $('#biaya-subjob').val(btn.data('subjob')).trigger('change');
            $('#biaya-deskripsi').val(btn.data('deskripsi'));
            $('#biaya-job').val(btn.data('job')).trigger('change');
            $('#biaya-unit').val(btn.data('unit'));
            $('#biaya-volume').val(btn.data('volume'));
            $('#biaya-keterangan').val(btn.data('keterangan'));
            $('#btn-save-biaya').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-biaya').modal('show');
        });

        $(document).on('click', '.btn-delete-biaya', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_teknik/docking/biaya') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'Data biaya berhasil dihapus', 'success');
                        table.ajax.reload(null, false);                    
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });
    });

    $(document).on('change', '#biaya-job', function() {
        var id_job = $(this).val();
        if (id_job) {
            $.ajax({
                url: '/data_master/subjob/dataByJob/' + id_job,
                type: "GET",
                dataType: "json",
                success: function(data) {          
                    $.each(data, function(key, value) {
                        $('#biaya-subjob').append('<option value="'+ value.id +'">'+ value.nama +'</option>');
                    });
                    table.ajax.reload();
                }
            });
        } else {
            $('#biaya-subjob').empty().append('<option value="">Tidak ada data</option>');
            table.ajax.reload();
        }
    });

    function parseVolume(value) {
        if (value === null || value === undefined || value === '') {
            return 0;
        }
        value = String(value).trim();
        value = value.replace(',', '.');

        return parseFloat(value) || 0;
    }
    function parseAngka(value) {
        if (!value) return 0;

        value = String(value).trim();
        value = value.replace(/\./g, '');
        value = value.replace(',', '.');

        return parseFloat(value) || 0;
    }

    function formatNominal(value) {
        return Number(value).toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    function hitungTotal() {
        const volume = parseVolume($('#biaya-volume').val());
        const harga  = parseAngka($('#biaya-harga').val());

        const total = volume * harga;

        $('#biaya-total').val(formatNominal(total));
    }

    $('#biaya-volume').on('input', hitungTotal);
    $('#biaya-harga').on('input', hitungTotal);

</script>
@endsection
