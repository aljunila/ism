@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
  <style>
    .track-wrap { display: grid; grid-template-columns: 1fr 1.15fr; min-height: 460px; }
    .track-left { padding: 1.25rem 1.35rem; border-right: 1px solid #ebe9f1; background: #fafafd; }
    .track-right { padding: 1.25rem 1.35rem; background: #f5f6fa; }
    .track-label { font-size: .8rem; color: #6e6b7b; margin-bottom: .2rem; }
    .track-value { font-size: .98rem; font-weight: 600; color: #3b3a45; margin-bottom: .85rem; }
    .track-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem 1rem; }
    .track-timeline { max-height: 400px; overflow-y: auto; background: #fff; border: 1px solid #ebe9f1; border-radius: .5rem; padding: .75rem; }
    .track-timeline-title { font-size: .82rem; font-weight: 700; color: #6e6b7b; text-transform: uppercase; letter-spacing: .04em; margin-bottom: .75rem; }
    .timeline-item { display: grid; grid-template-columns: 88px 14px 1fr; gap: .6rem; align-items: start; position: relative; padding: .35rem 0 .85rem; }
    .timeline-item::before { content: ''; position: absolute; left: 103px; top: 22px; bottom: -4px; width: 2px; border-left: 2px dashed #e0deea; }
    .timeline-item:last-child::before { display: none; }
    .timeline-time { text-align: right; font-size: .78rem; color: #6e6b7b; line-height: 1.25; }
    .timeline-dot { width: 14px; height: 14px; border-radius: 50%; background: #b7bfd4; margin-top: .15rem; box-shadow: 0 0 0 4px rgba(170,177,198,.18); }
    .timeline-content { font-size: .9rem; color: #3b3a45; line-height: 1.4; }
    .timeline-content .small { color: #8e8aa1; font-size: .8rem; }
    .timeline-item.is-active .timeline-dot { background: #0d6efd; box-shadow: 0 0 0 4px rgba(13,110,253,.2); position: relative; }
    .timeline-item.is-active .timeline-dot::after { content: ''; position: absolute; inset: -7px; border-radius: 50%; border: 2px solid rgba(13,110,253,.3); animation: trackPulse 1.8s ease-out infinite; }
    @keyframes trackPulse { 0% { transform:scale(.85); opacity:.85; } 70% { transform:scale(1.2); opacity:0; } 100% { transform:scale(1.2); opacity:0; } }
    .timeline-empty { text-align: center; color: #8e8aa1; padding: 1.5rem .5rem; font-size: .9rem; }
    @media (max-width: 767px) {
      .track-wrap { grid-template-columns: 1fr; }
      .track-left { border-right: 0; border-bottom: 1px solid #ebe9f1; }
      .track-meta-grid { grid-template-columns: 1fr; }
    }
  </style>
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="permintaan-tab" data-bs-toggle="tab" href="#permintaan" aria-controls="permintaan" role="tab" aria-selected="true"><i data-feather="permintaan"></i>Permintaan</a>
                    </li>
                    @if(Session::get('role_id')==10)
                    <li class="nav-item">
                        <a class="nav-link" id="purchas-tab" data-bs-toggle="tab" href="#purchas" aria-controls="purchas" role="tab" aria-selected="true" data-status="purchasing"><i data-feather="file"></i>Purchasing</a>
                    </li>
                    @elseif(Session::get('role_id')==4)
                    <li class="nav-item">
                        <a class="nav-link" id="kapal-tab" data-bs-toggle="tab" href="#kapal" aria-controls="kapal" role="tab" aria-selected="true"><i data-feather="permintaan"></i>Kapal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" aria-controls="history" role="tab" aria-selected="true"><i data-feather="clock"></i>History</a>
                    </li>
                    @else
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
                    @endif
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
                        @if((Session::get('previllage')==1) or (Session::get('previllage')==3))
                        <a href="/permintaan/form" class="btn btn-primary btn-sm pull-right">Tambah Data</a>
                        @endif
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
                                        <th>Penerima</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="history" aria-labelledby="history-tab" role="tabpanel">
                        <div class="card-body">
                            <table id="table-history" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kapal</th>
                                        <th>Bagian</th>
                                        <th>Jumlah Item</th>
                                        <th>Pembuat Permintaan</th>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-1 px-1 py-50 border-bottom" id="detail-meta-bar" style="background:#f8f8f8;min-height:36px;">
                <span class="d-flex align-items-center gap-25" style="font-size:.78rem;color:#6e6b7b;">
                    <i data-feather="hash" style="width:12px;height:12px;"></i><span id="meta-nomor">-</span>
                </span>
                <span style="color:#d0cfe8;font-size:.75rem;">|</span>
                <span class="d-flex align-items-center gap-25" style="font-size:.78rem;color:#6e6b7b;">
                    <i data-feather="anchor" style="width:12px;height:12px;"></i><span id="meta-kapal">-</span>
                </span>
                <span style="color:#d0cfe8;font-size:.75rem;">|</span>
                <span class="d-flex align-items-center gap-25" style="font-size:.78rem;color:#6e6b7b;">
                    <i data-feather="calendar" style="width:12px;height:12px;"></i><span id="meta-tanggal">-</span>
                </span>
                <span style="color:#d0cfe8;font-size:.75rem;">|</span>
                <span class="d-flex align-items-center gap-25" style="font-size:.78rem;color:#6e6b7b;">
                    <i data-feather="users" style="width:12px;height:12px;"></i><span id="meta-bagian">-</span>
                </span>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0" id="tableDetail" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-1" style="width:40px;">No</th>
                            <th>Barang</th>
                            <th style="width:100px;">Jumlah</th>
                            <th style="width:160px;">Status</th>
                            <th style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="LacakModal" tabindex="-1" style="z-index:1070;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lacak Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="track-wrap">
                    <div class="track-left">
                        <div class="fw-bold mb-1" style="font-size:.95rem;color:#3b3a45;">Detail Permintaan</div>
                        <div class="track-meta-grid">
                            <div>
                                <div class="track-label">Nama Barang</div>
                                <div class="track-value" id="lacakBarang">-</div>
                            </div>
                            <div>
                                <div class="track-label">Jumlah</div>
                                <div class="track-value" id="lacakJumlah">-</div>
                            </div>
                            <div>
                                <div class="track-label">Keterangan</div>
                                <div class="track-value" id="lacakKet">-</div>
                            </div>
                            <div>
                                <div class="track-label">Status Saat Ini</div>
                                <div class="track-value" id="lacakStatus">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="track-right">
                        <div class="track-timeline-title">Riwayat Proses</div>
                        <div class="track-timeline" id="lacakTimeline">
                            <div class="timeline-empty">Pilih barang untuk melihat riwayat.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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

<div class="modal fade" id="vendorCabangModal" tabindex="-1" aria-hidden="true" style="z-index:1080;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Cabang untuk Vendor Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-75">Vendor <strong id="vendor-nama-display"></strong> akan ditambahkan ke cabang:</p>
                <select id="vendor-cabang-select" class="form-control">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->id }}">{{ $c->cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="vendor-cabang-confirm">Tambahkan</button>
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
                                <select name="vendor" id="vendor" class="form-control">
                                    <option value="">Pilih</option>
                                    @foreach($vendor as $v)
                                        <option value="{{$v->id}}">{{$v->nama}}</option>
                                    @endforeach
                                </select>
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
    let vendorSelect = null;

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

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
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
                        const kapal = escapeHtml(row.kapal || '-');
                        const nomor = escapeHtml(row.nomor || '-');
                        return `${kapal} <button type="button"
                            class="btn btn-icon btn-xs btn-flat-primary detail-perm-btn"
                            data-id="${row.id}"
                            data-kapal="${kapal}"
                            data-nomor="${nomor}"
                            data-tanggal="${escapeHtml(row.tanggal || '-')}"
                            data-bagian="${escapeHtml(row.bagian || '-')}"
                            title="Detail Barang">Detail Permintaan</button><br>No : ${nomor}`;
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
                { data: 'penerima', name: 'penerima' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        const tableHistory = $('#table-history').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/permintaan/history",
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
                        const kapal = escapeHtml(row.kapal || '-');
                        const nomor = escapeHtml(row.nomor || '-');
                        return `${kapal} <button type="button"
                            class="btn btn-icon btn-xs btn-flat-primary detail-perm-btn"
                            data-id="${row.id}"
                            data-kapal="${kapal}"
                            data-nomor="${nomor}"
                            data-tanggal="${escapeHtml(row.tanggal || '-')}"
                            data-bagian="${escapeHtml(row.bagian || '-')}"
                            title="Detail Barang">Detail Permintaan</button><br>No : ${nomor}`;
                    }
                },
                { data: 'bagian', name: 'bagian' },
                { data: 'item_count', name: 'item_count', searchable: false },
                { data: 'created', name: 'created' },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const nomor = escapeHtml(row.nomor || '-');
                        return `
                            <button type="button" class="btn btn-sm btn-outline-warning btn-repeat-order" data-id="${row.id}" data-nomor="${nomor}">
                                Repeat Order
                            </button>
                        `;
                    }
                }
            ]
        });

        $('#pembelian').hide();
        $('#zahir').hide();

        document.getElementById('LacakModal').addEventListener('show.bs.modal', function () {
            setTimeout(function () {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) backdrops[backdrops.length - 1].style.zIndex = '1065';
            }, 0);
        });
        document.getElementById('LacakModal').addEventListener('hidden.bs.modal', function () {
            if ($('#DetailModal').hasClass('show')) document.body.classList.add('modal-open');
        });

        const vendorCabangModalEl = document.getElementById('vendorCabangModal');
        vendorCabangModalEl.addEventListener('show.bs.modal', function () {
            // Naikkan backdrop baru di atas prosesModal
            setTimeout(function () {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    backdrops[backdrops.length - 1].style.zIndex = '1075';
                }
            }, 0);
        });
        vendorCabangModalEl.addEventListener('hidden.bs.modal', function () {
            // Pastikan prosesModal tetap bisa di-scroll / tidak kehilangan scroll-lock
            if ($('#prosesModal').hasClass('show')) {
                document.body.classList.add('modal-open');
            }
        });

        vendorSelect = new TomSelect('#vendor', {
            placeholder: 'Pilih atau ketik nama vendor baru...',
            allowEmptyOption: true,
            createFilter: function (input) {
                return input.trim().length >= 2;
            },
            create: function (input, callback) {
                function doStore(nama, idCabang) {
                    const payload = {
                        nama: nama,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    if (idCabang) payload.id_cabang = idCabang;

                    $.ajax({
                        url: '/data_master/vendor/quick-store',
                        type: 'POST',
                        data: payload,
                        success: function (res) {
                            callback({ value: res.id, text: res.nama });
                        },
                        error: function (xhr) {
                            Swal.fire('Gagal', xhr.responseJSON?.message || 'Vendor tidak dapat disimpan', 'error');
                            callback();
                        }
                    });
                }

                $.ajax({
                    url: '/data_master/vendor/quick-store',
                    type: 'POST',
                    data: {
                        nama: input.trim(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        callback({ value: res.id, text: res.nama });
                    },
                    error: function (xhr) {
                        const json = xhr.responseJSON || {};
                        if (xhr.status === 422 && json.requires_cabang) {
                            $('#vendor-nama-display').text(input.trim());
                            $('#vendor-cabang-select').val('');
                            $('#vendorCabangModal').modal('show');

                            $('#vendor-cabang-confirm').off('click').on('click', function () {
                                const idCabang = $('#vendor-cabang-select').val();
                                if (!idCabang) {
                                    Swal.fire('Peringatan', 'Pilih cabang terlebih dahulu', 'warning');
                                    return;
                                }
                                $('#vendorCabangModal').modal('hide');
                                doStore(input.trim(), idCabang);
                            });
                        } else {
                            Swal.fire('Gagal', json.message || 'Vendor tidak dapat disimpan', 'error');
                            callback();
                        }
                    }
                });
            }
        });

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
                            if (tableId === 'table-workshop') {
                                return '';
                            }
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

                if (activePane?.id === 'history') {
                    tableHistory.columns.adjust().ajax.reload();
                    return;
                }

                if (!status) {
                    return;
                }
                const dt = window.initLogTable();
                if (dt) dt.ajax.reload();
            });
        });

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
            table2.ajax.reload();
            tableHistory.ajax.reload();
            Object.keys(logTables).forEach(function (key) {
                logTables[key].ajax.reload();
            });
        });

        $('#tanggal').on('change', function () {
            Object.keys(logTables).forEach(function (key) {
                logTables[key].ajax.reload();
            });
            table.ajax.reload();
            table2.ajax.reload();
            tableHistory.ajax.reload();
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

        $(document).on('click', '.btn-repeat-order', function () {
            const id = $(this).data('id');
            const nomor = $(this).data('nomor') || '-';

            Swal.fire({
                title: 'Repeat order?',
                text: `Buat permintaan baru dari ${nomor}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `/permintaan/repeat/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                })
                .done(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    }).then(() => {
                        if (res.redirect_url) {
                            window.location.href = res.redirect_url;
                            return;
                        }

                        table.ajax.reload(null, false);
                        tableHistory.ajax.reload(null, false);
                    });
                })
                .fail(xhr => {
                    Swal.fire(
                        'Gagal',
                        xhr.responseJSON?.message || 'Repeat order gagal dibuat',
                        'error'
                    );
                });
            });
        });
    });

    function formatTgl(val) {
        if (!val) return '-';
        const m = String(val).match(/^(\d{4})-(\d{2})-(\d{2})/);
        return m ? `${m[3]}-${m[2]}-${m[1]}` : val;
    }

    $(document).on('click', '.detail-perm-btn', function () {
        const btn = $(this);
        openDetail(btn.data('id'), {
            kapal:    btn.data('kapal'),
            nomor:    btn.data('nomor'),
            tanggal:  btn.data('tanggal'),
            bagian:   btn.data('bagian')
        });
    });

    function openDetail(id, meta) {
        currentId = id;
        meta = meta || {};
        $('#meta-nomor').text(meta.nomor   || '-');
        $('#meta-kapal').text(meta.kapal   || '-');
        $('#meta-tanggal').text(formatTgl(meta.tanggal) || '-');
        $('#meta-bagian').text(meta.bagian || '-');
        feather.replace();
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
                { data: null, orderable: false, render: (d, t, r, m) => m.row + 1 },
                { data: 'barang' },
                {
                    data: null,
                    render: (d, t, row) => `${escapeHtml(row.jumlah || '-')} ${escapeHtml(row.satuan || '')}`
                },
                { data: 'status' },
                {
                    data: null,
                    orderable: false,
                    render: function (d, t, row) {
                        return `<button type="button"
                            class="btn btn-xs btn-flat-info lacak-btn"
                            data-id="${row.id}"
                            data-barang="${escapeHtml(row.barang || '-')}"
                            data-jumlah="${escapeHtml(row.jumlah || '-')}"
                            data-satuan="${escapeHtml(row.satuan || '')}"
                            data-ket="${escapeHtml(row.keterangan || '-')}"
                            data-status="${escapeHtml(row.status || '-')}">
                            <i data-feather="map-pin" style="width:12px;height:12px;"></i> Lacak
                        </button>`;
                    }
                }
            ],
            drawCallback: function () { feather.replace(); }
        });
    }

    $(document).on('click', '.lacak-btn', function () {
        const btn = $(this);
        openLacak(
            btn.data('id'),
            btn.data('barang'),
            btn.data('jumlah'),
            btn.data('satuan'),
            btn.data('ket'),
            btn.data('status')
        );
    });

    function openLacak(id, barang, jumlah, satuan, ket, status) {
        $('#lacakBarang').text(barang || '-');
        $('#lacakJumlah').text((`${jumlah} ${satuan}`).trim() || '-');
        $('#lacakKet').text(ket || '-');
        $('#lacakStatus').text(status || '-');
        $('#lacakTimeline').html('<div class="timeline-empty">Memuat riwayat...</div>');
        $('#LacakModal').modal('show');

        $.get(`/permintaan/getlog/${id}`)
            .done(function (res) { renderTrackTimeline(res); })
            .fail(function () {
                $('#lacakTimeline').html('<div class="timeline-empty">Gagal memuat data riwayat.</div>');
            });
    }

    function renderTrackTimeline(rows) {
        const el = document.getElementById('lacakTimeline');
        if (!rows || !rows.length) {
            el.innerHTML = '<div class="timeline-empty">Belum ada riwayat proses.</div>';
            return;
        }
        let html = '';
        rows.forEach(function (row, i) {
            const activeClass = i === 0 ? 'is-active' : '';
            html += `
                <div class="timeline-item ${activeClass}">
                    <div class="timeline-time">${formatTgl(row.tanggal)}</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="fw-semibold">${escapeHtml(row.keterangan || row.status || '-')}</div>
                        <div class="small">Status: ${escapeHtml(row.status || '-')}</div>
                        <div class="small">Oleh: ${escapeHtml(row.created || '-')}</div>
                    </div>
                </div>`;
        });
        el.innerHTML = html;
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
                    if (vendorSelect) vendorSelect.setValue(data.vendor || '');
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
        if (vendorSelect) vendorSelect.setValue('');
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
