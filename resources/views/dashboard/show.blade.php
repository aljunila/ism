@extends('main')
@section('scriptheader')
<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/pages/page-profile.css')}}">

<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/pages/dashboard-ecommerce.css')}}">
<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/charts/chart-apex.css')}}">
<link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/css/plugins/extensions/ext-component-toastr.css')}}">
    
@endsection

@section('scriptfooter')
<script>
    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    function renderLogTimeline(rows) {
        const wrapper = document.getElementById('dashboardLogTimeline');
        if (!wrapper) return;

        if (!rows || !rows.length) {
            wrapper.innerHTML = '<div class="timeline-empty">Belum ada data rute.</div>';
            return;
        }

        let html = '';
        rows.forEach(function (row, idx) {
            const tanggal = escapeHtml(row.tanggal || '-');
            const title = escapeHtml(row.keterangan || row.status || '-');
            const status = escapeHtml(row.status || '-');
            const created = escapeHtml(row.created || '-');
            const activeClass = idx === 0 ? 'is-active' : '';

            html += `
                <div class="timeline-item ${activeClass}">
                    <div class="timeline-time">${tanggal}</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div><strong>${title}</strong></div>
                        <div class="small">Status: ${status}</div>
                        <div class="small">Diproses oleh: ${created}</div>
                    </div>
                </div>
            `;
        });

        wrapper.innerHTML = html;
    }

    $(document).on('click', '.btn-dashboard-detail', function () {
        const id = $(this).data('id');
        if (!id) return;

        $('#dashboardPermintaanBody').html('<tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>');
        $('#dashboardPermintaanMeta').text('-');
        $('#DashboardPermintaanModal').modal('show');

        $.ajax({
            url: `/dashboard/permintaan/${id}/detail`,
            type: 'GET'
        })
        .done(function (res) {
            const header = res.header || {};
            const items = res.items || [];

            $('#dashboardPermintaanMeta').text(
                `${header.nomor || '-'} | ${header.tanggal || '-'} | ${header.kapal || '-'} | ${header.bagian || '-'}`
            );

            if (!items.length) {
                $('#dashboardPermintaanBody').html('<tr><td colspan="5" class="text-center text-muted">Tidak ada barang.</td></tr>');
                return;
            }

            let rows = '';
            items.forEach(function (item, idx) {
                const jumlah = `${item.jumlah || '-'} ${item.satuan || ''}`.trim();
                rows += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${escapeHtml(item.barang || '-')}</td>
                        <td>${escapeHtml(jumlah || '-')}</td>
                        <td>${escapeHtml(item.status || '-')}</td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary btn-dashboard-log"
                                data-id="${item.id}"
                                data-nomor="${escapeHtml(header.nomor || '-')}"
                                data-tanggal="${escapeHtml(header.tanggal || '-')}"
                                data-kapal="${escapeHtml(header.kapal || '-')}"
                                data-bagian="${escapeHtml(header.bagian || '-')}"
                                data-peminta="${escapeHtml(header.peminta || '-')}"
                                data-barang="${escapeHtml(item.barang || '-')}"
                                data-jumlah="${escapeHtml(jumlah || '-')}"
                            >
                                Lihat Log
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#dashboardPermintaanBody').html(rows);
        })
        .fail(function () {
            $('#dashboardPermintaanBody').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat detail permintaan.</td></tr>');
        });
    });

    $(document).on('click', '.btn-dashboard-log', function () {
        const idDetail = $(this).data('id');
        if (!idDetail) return;

        $('#DashboardPermintaanModal').modal('hide');
        $('#DashboardLogModal').modal('show');

        $('#dashDetailBarang').text($(this).data('barang') || '-');
        $('#dashDetailJumlah').text($(this).data('jumlah') || '-');
        $('#dashDetailNomor').text($(this).data('nomor') || '-');
        $('#dashDetailTanggal').text($(this).data('tanggal') || '-');
        $('#dashDetailKapal').text($(this).data('kapal') || '-');
        $('#dashDetailBagian').text($(this).data('bagian') || '-');
        $('#dashDetailPeminta').text($(this).data('peminta') || '-');
        $('#dashboardLogTimeline').html('<div class="timeline-empty">Memuat riwayat rute...</div>');

        $.ajax({
            url: `/dashboard/permintaan/log/${idDetail}`,
            type: 'GET'
        })
        .done(function (res) {
            renderLogTimeline(res);
        })
        .fail(function () {
            $('#dashboardLogTimeline').html('<div class="timeline-empty">Gagal memuat data rute.</div>');
        });
    });
</script>
@endsection
@section('content')
<style>
    .dash-hero {
        background: linear-gradient(135deg, #0d6efd, #3a8df7);
        border-radius: 16px;
        color: #fff;
        padding: 28px;
        box-shadow: 0 10px 25px rgba(0, 60, 136, 0.2);
    }
    .stat-card {
        border: 1px solid #e6ebf1;
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: 0 12px 18px -12px rgba(13, 110, 253, 0.35);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 26px -14px rgba(13,110,253,0.55);
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(13,110,253,0.12);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
    }
    .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #1f2d3d;
    }
    .stat-label {
        margin: 0;
        color: #6c7a8a;
        font-size: 13px;
    }
    .table-minimal thead {
        background: #e9f2ff;
        color: #0d3a6e;
    }
    .table-minimal td, .table-minimal th {
        vertical-align: middle;
    }
    .text-blue {
        color: #0d6efd !important;
    }
    .permintaan-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: .9rem;
    }
    .permintaan-item {
        border: 1px solid #e8edf5;
        border-radius: 12px;
        padding: .95rem 1rem;
        background: #fff;
        box-shadow: 0 10px 18px -14px rgba(13, 110, 253, 0.55);
    }
    .permintaan-item-title {
        font-weight: 700;
        color: #273246;
        margin-bottom: .4rem;
    }
    .permintaan-item-meta {
        font-size: .85rem;
        color: #6e6b7b;
        margin-bottom: .65rem;
        line-height: 1.45;
    }
    .permintaan-item-meta span {
        display: block;
    }
    .permintaan-pagination {
        margin-top: 1rem;
        display: flex;
        justify-content: center;
    }
    .permintaan-pagination .pagination {
        margin-bottom: 0;
    }
    .permintaan-track-modal .modal-dialog {
        max-width: 1100px;
    }
    .permintaan-track-wrap {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        min-height: 520px;
    }
    .permintaan-track-left {
        padding: 1.25rem 1.35rem 1.15rem;
        border-right: 1px solid #ebe9f1;
        background: #fafafd;
    }
    .permintaan-track-title {
        margin-bottom: 1rem;
        font-weight: 700;
    }
    .permintaan-track-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .9rem 1rem;
    }
    .permintaan-track-label {
        font-size: .82rem;
        color: #6e6b7b;
        margin-bottom: .2rem;
    }
    .permintaan-track-value {
        font-size: 1.02rem;
        font-weight: 600;
        color: #3b3a45;
    }
    .permintaan-track-right {
        padding: 1.25rem 1.35rem 1.15rem;
        background: #f5f6fa;
    }
    .permintaan-track-status {
        font-size: 1.35rem;
        margin-bottom: 1rem;
        color: #6e6b7b;
    }
    .permintaan-track-status strong {
        color: #3b3a45;
    }
    .permintaan-track-timeline {
        max-height: 420px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: .6rem;
        padding: .75rem;
    }
    .timeline-item {
        display: grid;
        grid-template-columns: 92px 16px 1fr;
        gap: .75rem;
        align-items: start;
        position: relative;
        padding: .45rem 0 .9rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 108.4px;
        top: 24px;
        bottom: -4px;
        width: 2px;
        border-left: 2px dashed #e0deea;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-time {
        text-align: right;
        font-size: .83rem;
        color: #6e6b7b;
        line-height: 1.2;
    }
    .timeline-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #b7bfd4;
        margin-top: .2rem;
        box-shadow: 0 0 0 4px rgba(170, 177, 198, 0.18);
        position: relative;
    }
    .timeline-item.is-active .timeline-dot {
        background: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
    }
    .timeline-item.is-active .timeline-dot::after {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 50%;
        border: 2px solid rgba(13, 110, 253, 0.32);
        animation: timelinePulse 1.8s ease-out infinite;
    }
    @keyframes timelinePulse {
        0% { transform: scale(0.85); opacity: 0.85; }
        70% { transform: scale(1.2); opacity: 0; }
        100% { transform: scale(1.2); opacity: 0; }
    }
    .timeline-content {
        font-size: .93rem;
        color: #3b3a45;
        line-height: 1.45;
    }
    .timeline-content .small {
        color: #8e8aa1;
    }
    .timeline-empty {
        text-align: center;
        color: #8e8aa1;
        padding: 1.2rem .5rem;
    }
    @media (max-width: 991px) {
        .permintaan-track-wrap {
            grid-template-columns: 1fr;
        }
        .permintaan-track-left {
            border-right: 0;
            border-bottom: 1px solid #ebe9f1;
        }
        .permintaan-track-meta-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<section id="dashboard-ecommerce">
    <div style="display:flex;justify-content:space-between;margin-bottom:1rem;padding-left:1rem;padding-right:1rem;">
        <div>
            <h2>Selamat Datang Kembali, {{ Session::get('name') }}</h2>
            <p class="mb-0 text-white-75">{{ $com->nama ?? '-' }}</p>
        </div>

        <div class="d-flex align-items-center mt-3 mt-md-0">
            <div class="stat-icon me-2">
                <i data-feather="award"></i>
            </div>
            <div class="text-white-75 small">Akses aktif • {{ date('d M Y') }}</div>
        </div>
    </div>

    @if(Session::get('previllage')!=4)
        <div class="col-lg-12" style="display:flex;justify-content:space-between;margin-bottom:1rem;padding-left:1rem;padding-right:1rem;">
            <div class="row">
                @if($count_doc)
                <div class="alert alert-primary col-lg-5" role="alert">
                    <h4 class="alert-heading">Dokumen Kapal</h4>
                    <div class="alert-body">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#large">Anda memiliki {{ $count_doc }} dokumen yang akan segera expired.</a>
                    </div>
                </div>
                <div class="col-lg-1"></div>
                @endif
                @if($count_dockru)
                <div class="alert alert-success col-lg-5" role="alert">
                    <h4 class="alert-heading">Dokumen Kru</h4>
                    <div class="alert-body">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#krumodal">Anda memiliki {{ $count_dockru }} dokumen yang akan segera expired.</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
       
        <div class="row g-2">
            @if(!empty($perusahaan))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center bg-white">
                    <div class="stat-icon me-2">
                        <i data-feather="home"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$perusahaan}}</h4>
                        <p class="fw-bolder">Perusahaan</p>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($kapal))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center bg-white">
                    <div class="stat-icon me-2">
                        <i data-feather="anchor"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$kapal}}</h4>
                        <p class="fw-bolder">Kapal</p>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($karyawan))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center bg-white">
                    <div class="stat-icon me-2">
                        <i data-feather="user"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$karyawan}}</h4>
                        <p class="fw-bolder">Karyawan</p>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($user))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card h-100 d-flex align-items-center bg-white">
                    <div class="stat-icon me-2">
                        <i data-feather="user-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-xl">{{$user}}</h4>
                        <p class="fw-bolder">User Aktif</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @else
        @if(Session::get('id_kapal')!=0)
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="mb-0 text-blue">Frekuensi Akses Prosedur</h5>
                            <span class="text-muted small">Live</span>
                        </div>
                        <table id="tabledetail" class="table table-minimal table-striped">
                            <thead>
                            <tr>
                                <th>Prosedur</th>
                                <th>Lihat</th>
                                <th>Terakhir Lihat</th>
                                <th>Download</th>
                                <th>Terakhir Download</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($prosedur as $show)
                            <tr>
                                <td>{{$show->kode}}</td>
                                <td>{{$show->jml_lihat}}x</td>
                                <td>@if($show->update_lihat) {{ \Carbon\Carbon::parse($show->update_lihat)->addHours(7)->format('d-m-Y H:i') }} @else - @endif</td>
                                <td>{{$show->jml_download}}x</td>
                                <td>@if($show->update_download) {{ \Carbon\Carbon::parse($show->update_download)->addHours(7)->format('d-m-Y H:i') }} @else - @endif</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    @if(!empty($permintaan_dashboard) && count($permintaan_dashboard))
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-blue">Detail Permintaan Barang</h5>
                    <span class="text-muted small">Cek Permintaan Barang</span>
                </div>
                <div class="card-body">
                    <div class="permintaan-list">
                        @foreach($permintaan_dashboard as $permintaan)
                        <div class="permintaan-item">
                            <div class="permintaan-item-title">{{ $permintaan->nomor }}</div>
                            <div class="permintaan-item-meta">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span>Tanggal: {{ $permintaan->tanggal }}</span>
                                        <span>Peminta: {{ $permintaan->peminta ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <span>Kapal: {{ $permintaan->kapal ?? '-' }}</span>
                                        <span>Bagian: {{ $permintaan->bagian ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary btn-dashboard-detail"
                                data-id="{{ $permintaan->id }}"
                            >
                                Detail Barang
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @if(method_exists($permintaan_dashboard, 'hasPages') && $permintaan_dashboard->hasPages())
                    <div class="permintaan-pagination">
                        {{ $permintaan_dashboard->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

<div class="modal fade" id="DashboardPermintaanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Detail Barang Permintaan</h5>
                    <small class="text-muted" id="dashboardPermintaanMeta">-</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="dashboardPermintaanBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade permintaan-track-modal" id="DashboardLogModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lacak Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="permintaan-track-wrap">
                    <div class="permintaan-track-left">
                        <h5 class="permintaan-track-title">Detail Permintaan</h5>
                        <div class="permintaan-track-meta-grid">
                            <div>
                                <div class="permintaan-track-label">Nama Barang</div>
                                <div class="permintaan-track-value" id="dashDetailBarang">-</div>
                            </div>
                            <div>
                                <div class="permintaan-track-label">Jumlah Barang</div>
                                <div class="permintaan-track-value" id="dashDetailJumlah">-</div>
                            </div>
                            <div>
                                <div class="permintaan-track-label">Nomor</div>
                                <div class="permintaan-track-value" id="dashDetailNomor">-</div>
                            </div>
                            <div>
                                <div class="permintaan-track-label">Tanggal Permintaan</div>
                                <div class="permintaan-track-value" id="dashDetailTanggal">-</div>
                            </div>
                            <div>
                                <div class="permintaan-track-label">Dari Kapal</div>
                                <div class="permintaan-track-value" id="dashDetailKapal">-</div>
                            </div>
                            <div>
                                <div class="permintaan-track-label">Bagian</div>
                                <div class="permintaan-track-value" id="dashDetailBagian">-</div>
                            </div>
                            <div>
                                <div class="permintaan-track-label">Peminta</div>
                                <div class="permintaan-track-value" id="dashDetailPeminta">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="permintaan-track-right">
                        <div class="permintaan-track-status">Status: <strong>-</strong></div>
                        <div class="permintaan-track-timeline" id="dashboardLogTimeline">
                            <div class="timeline-empty">Memuat riwayat rute...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-size-lg d-inline-block">
    <div class="modal fade text-start" id="large" tabindex="-1" aria-labelledby="myModalLabel17" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel17">List Dokumen Kapal</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                            <table class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th width="35%">Kapal</th>
                                        <th width="45%">Nama Document</th>
                                        <th width="20%">Tgl Expired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($document as $d)
                                        @php
                                            $kapal = $d->get_kapal();
                                            $pemilik = $kapal ? $kapal->get_pemilik() : null;
                                            $file = $d->get_file();
                                        @endphp
                                        <tr>
                                            <td>{{ $pemilik->nama ?? '-' }}<br>
                                                {{ $kapal->nama ?? '-' }}</td>
                                            <td><a type="button" href="{{ asset('file_upload/'.$d->file) }}" target="_blank" 
                                                    title="Buka File" data-id="{{$d->id}}" data-file="{{$d->nama}}">{{ $file->nama ?? ($d->nama ?? 'File') }}</a></td>
                                            <td>{{ \Carbon\Carbon::parse($d->tgl_expired)->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Accept</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-size-lg d-inline-block">
    <div class="modal fade text-start" id="krumodal" tabindex="-1" aria-labelledby="myModalLabel17" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel17">List Dokumen Kru</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                            <table class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th width="35%">Kapal</th>
                                        <th width="45%">Nama Document</th>
                                        <th width="20%">Tgl Expired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doc_kru as $kru)
                                        @php
                                            $karyawan = $kru->get_karyawan();
                                            $kapalKru = $karyawan ? $karyawan->get_kapal() : null;
                                            $file = $kru->get_file();
                                        @endphp
                                        <tr>
                                            <td>{{ $kapalKru->nama ?? '-' }}<br>
                                                {{ $karyawan->nama ?? '-' }}</td>
                                            <td><a type="button" href="{{ asset('file_upload/'.$kru->file) }}" target="_blank" 
                                                    title="Buka File" data-id="{{$kru->id}}" data-file="{{$kru->nama}}">{{ $file->nama ?? ($kru->nama ?? 'File') }}</a></td>
                                            <td>{{ \Carbon\Carbon::parse($kru->tgl_expired)->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Accept</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
