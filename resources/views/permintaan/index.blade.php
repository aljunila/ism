@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="permintaan-tab" data-bs-toggle="tab" href="#permintaan" aria-controls="permintaan" role="tab" aria-selected="true"><i data-feather="permintaan"></i>Permintaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="logistik-tab" data-bs-toggle="tab" href="#logistik" aria-controls="logistik" role="tab" aria-selected="true" data-status="logistik"><i data-feather="permintaan"></i>Logistik</a>
                   </li>
                        <li class="nav-item">
                        <a class="nav-link" id="purchas-tab" data-bs-toggle="tab" href="#purchas" aria-controls="purchas" role="tab" aria-selected="true" data-status="purchasing"><i data-feather="file"></i>Purchasing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="po-tab" data-bs-toggle="tab" href="#po" aria-controls="po" role="tab" aria-selected="true" data-status="po"><i data-feather="log-out"></i>P.O.</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="workshop-tab" data-bs-toggle="tab" href="#workshop" aria-controls="workshop" role="tab" aria-selected="true" data-status="workshop"><i data-feather="permintaan"></i>Workshop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="kapal-tab" data-bs-toggle="tab" href="#kapal" aria-controls="kapal" role="tab" aria-selected="true"><i data-feather="permintaan"></i>Kapal</a>
                    </li>
                </ul>
                <div class="card-header border-bottom">
                    <div class="col-sm-3">
                        <select name="id_kapal" id="id_kapal" class="form-control">
                            <option value="">Pilih Kapal</option>
                        @foreach($kapal as $kp)
                            <option value="{{$kp->id}}" @selected (isset($trip) && $kp->id==$trip->id_kapal)>{{$kp->nama}}</option>
                        @endforeach
                        </select>
                    </div>  
                    <div class="col-sm-3">
                        <input type="date" name="tanggal" id="tanggal" class="form-control">
                    </div>
                    <div class="col-sm-6"></div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="permintaan" aria-labelledby="permintaan-tab" role="tabpanel">
                        <div class="card-body">
                        <a href="/permintaan/form" class="btn btn-primary btn-sm pull-right">Tambah Data</a>
                            <table id="table-permintaan" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kapal</th>
                                        <th>Bagian</th>
                                        <th>Pembuat Permintaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="logistik" aria-labelledby="logistik-tab" role="tabpanel" data-status="logistik">
                           @include('permintaan/logistik', ['tableId' => 'table-logistik'])
                    </div>
                    <div class="tab-pane" id="purchas" aria-labelledby="purchas-tab" role="tabpanel" data-status="purchasing">
                            @include('permintaan/logistik', ['tableId' => 'table-purchas'])
                    </div>
                    <div class="tab-pane" id="po" aria-labelledby="po-tab" role="tabpanel" data-status="po">
                            @include('permintaan/logistik', ['tableId' => 'table-po'])
                    </div>
                    <div class="tab-pane" id="workshop" aria-labelledby="workshop-tab" role="tabpanel" data-status="workshop">
                            <br><a href="/permintaan/kirim" class="btn btn-primary btn-sm pull-right">Kirim Barang</a>
                            @include('permintaan/logistik', ['tableId' => 'table-workshop'])
                    </div>
                    <div class="tab-pane" id="kapal" aria-labelledby="kapal-tab" role="tabpanel">
                        <div class="card-body">
                            <table id="table-kirim" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kapal</th>
                                        <th>Bagian</th>
                                        <th>Pengirim</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="DetailKirimModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableDetailKirim" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Jml Permintaan</th>
                            <th>Jml Kirim</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-start" id="prosesModal" tabindex="-1" aria-labelledby="prosesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="proses_modal_title">Proses Permintaan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_proses" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label">Status</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="sedia" id="sedia" class="form-control">
                                <option value="4">Ada (Workshop)</option>
                                <option value="0">Tidak ada</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-1 row" id="pembelian">
                        <div class="col-sm-3">
                            <label class="col-form-label">Pembelian</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="status" id="status" class="form-control"></select>
                        </div>
                    </div>
                    <div class="mb-1 row" id="zahir">
                        <div class="col-sm-3">
                            <label class="col-form-label">Masukkan kode P.O.</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="kode_po" id="kode_po">
                        </div>
                    </div>
                    <div id="finance_block" style="display:none;">
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label">Vendor / Toko</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="vendor" id="vendor" placeholder="Nama vendor/toko">
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label">Jumlah</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="jumlah" id="jumlah" >
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <div class="col-sm-3">
                                <label class="col-form-label">Nominal</label>
                            </div>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="amount" id="amount" placeholder="0" inputmode="numeric">
                            </div>
                            <div class="col-sm-4">
                                <select name="id_currency" id="id_currency" class="form-control">
                                    <option value="">Mata Uang</option>
                                    @if(!empty($currencies))
                                        @foreach($currencies as $cur)
                                            <option value="{{ $cur->id }}" @selected($cur->code === 'IDR')>{{ $cur->code }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-1 row" id="shipping_mode_row" style="display:none;">
                        <div class="col-sm-3">
                            <label class="col-form-label">Mode Kirim</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="shipping_mode" id="shipping_mode" class="form-control">
                                <option value="">-Pilih-</option>
                                <option value="transit">Transit</option>
                                <option value="direct_workshop">Direct ke workshop</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-1 row" id="shipping_point_row" style="display:none;">
                        <div class="col-sm-3">
                            <label class="col-form-label">Transit Pada</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="shipping_point" id="shipping_point" placeholder="Nama lokasi transit">
                        </div>
                    </div>
                    <div class="alert alert-light-primary mb-1 py-50 px-75" id="process_hint" style="display:none;"></div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label">Tgl</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" name="tanggal" id="tanggal_proses">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label">Upload File</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="file" name="img" id="img" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="current_status" id="current_status">
                    <input type="hidden" id="current_flow_stage">
                    <input type="hidden" id="id_kapal_proses">
                    <input type="hidden" name="id" id="proses_id">
                    <button type="submit" class="btn btn-primary" id="simpan_cuti">Simpan</button>
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
<script>
    let logTables = {};

    function setDefaultCurrencyIdr() {
        const idrOption = $('#id_currency option').filter(function () {
            return ($(this).text() || '').trim() === 'IDR';
        }).first();

        if (idrOption.length) {
            $('#id_currency').val(idrOption.val());
        } else {
            $('#id_currency').val('');
        }
    }

    function maskRupiah(value) {
        const numberString = (value || '').replace(/\D/g, '');
        if (!numberString) return '';
        return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updateProcessFormUi() {
        const stage = $('#current_status').val();
        const flowStage = $('#current_flow_stage').val();
        const target = $('#sedia').val();
        const routeValue = $('#status').val();
        const shippingMode = $('#shipping_mode').val();
        let hint = '';

        $('#process_hint').hide().text('');

        if (stage === 'logistik') {
            const showPembelian = flowStage !== 'gudang' && target === '0';
            $('#pembelian').toggle(showPembelian);
            $('#zahir').toggle(showPembelian && (routeValue || '').startsWith('3|'));
            $('#finance_block').hide();
            $('#shipping_mode_row').hide();
            $('#shipping_point_row').hide();

            if (flowStage === 'gudang') {
                hint = 'Barang sudah di gudang. Langkah berikutnya hanya naik kapal.';
            } else if (target === '4') {
                hint = 'Log akan disimpan sebagai "Barang tersedia di workshop".';
            } else if (target === '5') {
                hint = 'Barang akan dipindahkan ke antrean gudang logistik.';
            } else if (target === '6') {
                hint = 'Barang akan diselesaikan dan dinyatakan naik kapal.';
            } else if (target === '7') {
                hint = 'Barang dikirim ke Cabang';
            } else if (target === '0') {
                if ((routeValue || '').startsWith('2|')) {
                    hint = 'Barang akan dipindahkan ke antrean purchasing.';
                } else if ((routeValue || '').startsWith('3|')) {
                    hint = 'Barang akan dipindahkan ke antrean PO.';
                }
            }
        }

        if (stage === 'purchasing') {
            const isDone = target === '4';
            $('#pembelian').hide();
            $('#zahir').hide();
            $('#finance_block').show();
            $('#shipping_mode_row').toggle(isDone);
            $('#shipping_point_row').toggle(isDone && shippingMode === 'transit');

            if (!isDone) {
                $('#shipping_mode').val('');
                $('#shipping_point').val('');
                $('#shipping_point_row').hide();
                hint = 'Log akan disimpan sebagai "Barang sedang dibeli".';
            } else if (shippingMode === 'transit') {
                hint = 'Log akan disimpan sebagai "Transit pada ...".';
            } else if (shippingMode === 'direct_workshop') {
                hint = 'Log akan disimpan sebagai "Direct langsung ke workshop".';
            } else {
                hint = 'Saat selesai, pilih mode kirim untuk melanjutkan ke workshop.';
            }
        }

        if (stage === 'po') {
            $('#pembelian').hide();
            $('#zahir').show();
            $('#finance_block').show();
            $('#shipping_mode_row').hide();
            $('#shipping_point_row').hide();
            $('#shipping_mode').val('');
            $('#shipping_point').val('');
            hint = target === '4'
                ? 'Log akan disimpan sebagai "PO sudah selesai, dikembalikan ke logistik".'
                : 'Log akan disimpan sebagai "Barang sedang di PO".';
        }

        if (hint) {
            $('#process_hint').text(hint).show();
        }
    }

    $(document).on('input', '#amount', function () {
        this.value = maskRupiah(this.value);
    });

    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        const table = $('#table-permintaan').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/permintaan/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.tanggal= $('#tanggal').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal' },
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `${row.kapal} <button type="button"  onclick="openDetail(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Barang">
                        Detail Permintaan</button><br>
                        No : ${row.nomor}`;
                    }
                },    
                { data: 'bagian', name: 'bagian' },
                { data: 'created', name: 'created' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const table2 = $('#table-kirim').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/permintaan/datakirim",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.tanggal= $('#tanggal').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal' },
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `${row.kapal} <button type="button"  onclick="openDetailKirim(${row.id})" class="btn btn-icon btn-xs btn-flat-primary" title="Detail Barang">
                        Detail Pengiriman</button><br>
                        No : ${row.nomor}`;
                    }
                },    
                { data: 'bagian', name: 'bagian' },
                { data: 'created', name: 'created' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        $('#pembelian').hide();
        $('#zahir').hide();

        $(document).on('change', '#sedia', updateProcessFormUi);
        $(document).on('change', '#status', updateProcessFormUi);
        $(document).on('change', '#shipping_mode', function () {
            if ($(this).val() !== 'transit') {
                $('#shipping_point').val('');
            }
            updateProcessFormUi();
        });

        window.initLogTable = function () {
            let activePane = document.querySelector('.tab-pane.active');
            let tableId = activePane.querySelector('table').id;

            if (logTables[tableId]) {
                return logTables[tableId];
            }

            logTables[tableId] = $('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax:{
                    url: "/permintaan/datalog",
                    type: "POST",
                    data: function(d){
                        let activePane = document.querySelector('.tab-pane.active');
                        d.status   = activePane?.getAttribute('data-status');
                        d.id_kapal = $('#id_kapal').val();
                        d.tanggal  = $('#tanggal').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'barang', name: 'barang' },
                    {
                        data: null,
                        name: null,
                        render: function (data, type, row) {
                            return `${row.jumlah} ${row.satuan}`;
                        }
                    },    
                    { data: 'tanggal', name: 'tanggal' },
                    {
                        data: null,
                        name: null,
                        render: function (data, type, row) {
                            let html = `${row.kapal} <br>No : ${row.nomor}`;
                            if (row.flow_view) {
                                html += `<br>Flow : ${row.flow_view}`;
                            }
                            if (row.cabang!="-") {
                                html += `<br>Pembelian di ${row.cabang}`;
                            }
                            if (row.kode_po) {
                                html += `<br>Kode PO : ${row.kode_po}`;
                            }
                            return html;
                        }
                    }, 
                    { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-outline-primary proses-btn" data-id="${row.id}" data-id_kapal="${row.id_kapal}" data-kode_po="${row.kode_po}" data-flow-stage="${row.flow_stage || ''}">
                                    Proses
                                </button>
                            `;
                        }
                    }
                ]
            });

            return logTables[tableId];
        };

        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function () {
                let activePane = document.querySelector('.tab-pane.active');
                let status = activePane?.getAttribute('data-status');

                if (!status) {
                    return;
                }
                const dt = window.initLogTable();
                if (dt) dt.ajax.reload();
            });
        });

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
            Object.keys(logTables).forEach(function (key) {
                logTables[key].ajax.reload();
            });
        });

        $('#tanggal').on('change', function () {
            Object.keys(logTables).forEach(function (key) {
                logTables[key].ajax.reload();
            });
            table.ajax.reload();
        });

        $(document).on('click', '.btn-delete-permintaan', function () {
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
                    url: '{{ url('permintaan/destroy') }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                })
                .done(res => {
                    Swal.fire(res.status, res.message, res.status)
                        .then(() => {
                            if (res.status === 'success') {
                                $('#modal-cuti').modal('hide');
                                table.ajax.reload(null, false);
                            }
                        });
                })
                .fail(xhr => {
                    Swal.fire(
                        'Gagal',
                        xhr.responseJSON?.message || 'Error',
                        'error'
                    );
                });
            });
        });
    });

    function openDetail(id) {
        currentId = id;
        $('#DetailModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetail')) {
            DetailTable.ajax.url(`permintaan/get/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetail').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `permintaan/get/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                { data: 'barang', },
                { data: 'jumlah', },
                { data: 'status', },
            ]
        });
    }

    $(document).on('click', '.proses-btn', function () {
        let id = $(this).data('id');
        let idkapal = $(this).data('id_kapal');
        let flowStage = $(this).data('flow-stage') || '';
        let pane = $(this).closest('.tab-pane');
        let stage = pane.data('status');
        let kode_po = $(this).data('kode_po');

        if((flowStage=='purchasing') || (flowStage=='po')) {
            $.ajax({
                url: "/permintaan/dataPurchas/" + id,
                type: "POST",
                dataType: "JSON",
                data: {
                    flowStage: flowStage,
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                     console.log(data);
                    $('#vendor').val(data.vendor);
                    $('#jumlah').val(data.jumlah);
                    $('#amount').val(data.amount);
                },
            });
        }
        
        $('#proses_id').val(id);
        $('#current_status').val(stage);
        $('#form_proses #kode_po').val(kode_po);
        $('#current_flow_stage').val(flowStage);
        $('#id_kapal_proses').val(idkapal || '');
        configureProcessForm(stage, idkapal, flowStage);
        $('#tanggal_proses').val(new Date().toISOString().slice(0, 10));
        $('#prosesModal').modal('show');
    });

    function openDetailKirim(id) {
        currentId = id;
        $('#DetailKirimModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableDetailKirim')) {
            DetailTable.ajax.url(`permintaan/getkirim/${id}`).load();
            return;
        }

        DetailTable = $('#tableDetailKirim').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `permintaan/getkirim/${id}`,
                dataSrc: function (json) {
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                { data: 'barang', },
                { data: 'jml_minta', },
                { data: 'jumlah', },
            ]
        });
    }

    function loadStatusBarang(idkapal) {
        return $.get(`/permintaan/getcabang/${idkapal}`, function(res){
            let html = '';
            res.forEach(function(item){
                html += `<option value="2|${item.id}">Cabang ${item.cabang}</option>`;
            });
            html += `<option value="3|0">Purchasing Order</option>`;
            $('#status').html(html);
            updateProcessFormUi();
        });
    }

    function configureProcessForm(stage, idkapal, flowStage) {
        const titleMap = {
            logistik: 'Proses Logistik',
            purchasing: 'Proses Purchasing',
            po: 'Proses P.O.'
        };
        $('#proses_modal_title').text(titleMap[stage] || 'Proses Permintaan');

        $('#sedia').val('4');
        $('#status').empty();
        // $('#kode_po').val('');
        $('#vendor').val('');
        $('#jumlah').val('');
        $('#amount').val('');
        setDefaultCurrencyIdr();
        $('#shipping_mode').val('');
        $('#shipping_point').val('');
        $('#pembelian').hide();
        $('#zahir').hide();
        $('#finance_block').hide();
        $('#shipping_mode_row').hide();
        $('#shipping_point_row').hide();
        $('#process_hint').hide().text('');

        if (stage === 'logistik') {
            if (flowStage === 'gudang') {
                $('#sedia').html(`
                    <option value="6">Naik kapal (Selesai)</option>
                `);
                updateProcessFormUi();
                return;
            }

            $('#sedia').html(`
                <option value="4">Ada (Workshop)</option>
                <option value="0">Tidak ada</option>
                <option value="5">Barang masuk gudang</option>
                <option value="6">Naik kapal (Selesai)</option>
            `);
            loadStatusBarang(idkapal);
            updateProcessFormUi();
            return;
        }

        $('#sedia').html(`
            <option value="1">Sedang Proses</option>
            <option value="7">Kirim ke Cabang</option>
            <option value="4">Selesai</option>
        `);

        if (stage === 'purchasing') {
            updateProcessFormUi();
            return;
        }

        if (stage === 'po') {
            updateProcessFormUi();
            return;
        }

        updateProcessFormUi();
    }

    $('#form_proses').on('submit', function(e){
        e.preventDefault();
        if ($('#amount').length) {
            $('#amount').val(($('#amount').val() || '').replace(/\./g, ''));
        }
        let formData = new FormData(this);
        $.ajax({
            url: "/permintaan/proses",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                $('#prosesModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message
                });
                const dt = window.initLogTable();
                if (dt) dt.ajax.reload();
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message ?? 'Terjadi kesalahan'
                });
            }
        });
    });
</script>
@endsection
