@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/app-assets/vendors/css/forms/select/tom-select.css')}}">
  <style>
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

    .permintaan-track-meta {
        margin-bottom: 1rem;
        padding-bottom: .85rem;
        border-bottom: 1px solid #ebe9f1;
    }

    .permintaan-track-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .9rem 1rem;
    }

    .permintaan-track-meta-item {
        min-width: 0;
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
        left: 109.2px;
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

    .timeline-content {
        font-size: .93rem;
        color: #3b3a45;
        line-height: 1.45;
    }

    .timeline-content .small {
        color: #8e8aa1;
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
        0% {
            transform: scale(0.85);
            opacity: 0.85;
        }
        70% {
            transform: scale(1.2);
            opacity: 0;
        }
        100% {
            transform: scale(1.2);
            opacity: 0;
        }
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
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Laporan - Permintaan</h4>
    </div>
    <div class="card-body">
        <table id="table-permintaan" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Permintaan Dari</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="modal fade permintaan-track-modal" id="DetailModal" tabindex="-1">
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

                        <div class="permintaan-track-meta permintaan-track-meta-grid mb-0 pb-0 border-bottom-0">
                            <div class="permintaan-track-meta-item">
                                <div class="permintaan-track-label">Nama Barang</div>
                                <div class="permintaan-track-value" id="detailBarang">-</div>
                            </div>
                            <div class="permintaan-track-meta-item">
                                <div class="permintaan-track-label">Jumlah Barang</div>
                                <div class="permintaan-track-value" id="detailJumlah">-</div>
                            </div>
                            <div class="permintaan-track-meta-item">
                                <div class="permintaan-track-label">Nomor</div>
                                <div class="permintaan-track-value" id="detailNomor">-</div>
                            </div>
                            <div class="permintaan-track-meta-item">
                                <div class="permintaan-track-label">Tanggal Permintaan</div>
                                <div class="permintaan-track-value" id="detailTanggal">-</div>
                            </div>
                            <div class="permintaan-track-meta-item">
                                <div class="permintaan-track-label">Dari Kapal</div>
                                <div class="permintaan-track-value" id="detailKapal">-</div>
                            </div>
                            <div class="permintaan-track-meta-item">
                                <div class="permintaan-track-label">Bagian</div>
                                <div class="permintaan-track-value" id="detailBagian">-</div>
                            </div>
                            <div class="permintaan-track-meta-item">
                                <div class="permintaan-track-label">Peminta</div>
                                <div class="permintaan-track-value" id="detailPeminta">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="permintaan-track-right">
                        <div class="permintaan-track-status">Status: <strong>-</strong></div>
                        <div class="permintaan-track-timeline" id="logTimeline">
                            <div class="timeline-empty">Memuat riwayat rute...</div>
                        </div>
                    </div>
                </div>
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
<script src="{{ url('/app-assets/vendors/js/tom-select.min.js') }}"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#table-permintaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('lappermintaan.data') }}',
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
                        return `${row.kapal} <br>
                        No : ${row.nomor}`;
                    }
                },
                { data: 'status', name: 'status' },
                { data: null,
                    name: null,
                    render: function (data, type, row) {
                        return `<button
                            type="button"
                            class="btn btn-sm btn-outline-primary btn-open-log"
                            data-id="${row.id}"
                            data-barang="${row.barang || '-'}"
                            data-jumlah="${row.jumlah || '-'}"
                            data-satuan="${row.satuan || ''}"
                            data-nomor="${row.nomor || '-'}"
                            data-tanggal="${row.tanggal || '-'}"
                            data-kapal="${row.kapal || '-'}"
                            data-bagian="${row.bagian || '-'}"
                            data-peminta="${row.peminta || '-'}"
                            title="Detail">
                        Detail Permintaan</button>`;
                    } 
                },
            ],
            
        });

        $(document).on('click', '.btn-open-log', function () {
            const detail = {
                id: $(this).data('id'),
                barang: $(this).data('barang'),
                jumlah: $(this).data('jumlah'),
                satuan: $(this).data('satuan'),
                nomor: $(this).data('nomor'),
                tanggal: $(this).data('tanggal'),
                kapal: $(this).data('kapal'),
                bagian: $(this).data('bagian'),
                peminta: $(this).data('peminta')
            };
            openLog(detail);
        });
    });

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    function renderTimeline(rows) {
        const wrapper = document.getElementById('logTimeline');
        if (!wrapper) return;

        if (!rows || !rows.length) {
            wrapper.innerHTML = '<div class="timeline-empty">Belum ada data rute.</div>';
            return;
        }

        let html = '';
        rows.forEach(function (row) {
            const tanggal = escapeHtml(row.tanggal || '-');
            const status = escapeHtml(row.status || '-');
            const created = escapeHtml(row.created || '-');
            const activeClass = html === '' ? 'is-active' : '';

            html += `
                <div class="timeline-item ${activeClass}">
                    <div class="timeline-time">${tanggal}</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div><strong>${status}</strong></div>
                        <div class="small">Diproses oleh: ${created}</div>
                    </div>
                </div>
            `;
        });

        wrapper.innerHTML = html;
    }

    function openLog(detail) {
        if (!detail || !detail.id) {
            return;
        }

        $('#DetailModal').modal('show');

        const jumlahBarang = `${detail.jumlah || '-'} ${detail.satuan || ''}`.trim();
        $('#detailBarang').text(detail.barang || '-');
        $('#detailJumlah').text(jumlahBarang || '-');
        $('#detailNomor').text(detail.nomor || '-');
        $('#detailTanggal').text(detail.tanggal || '-');
        $('#detailKapal').text(detail.kapal || '-');
        $('#detailBagian').text(detail.bagian || '-');
        $('#detailPeminta').text(detail.peminta || '-');
        $('#logTimeline').html('<div class="timeline-empty">Memuat riwayat rute...</div>');

        $.ajax({
            url: `permintaan/getlog/${detail.id}`,
            type: 'GET'
        })
        .done(function (res) {
            renderTimeline(res);
        })
        .fail(function () {
            $('#logTimeline').html('<div class="timeline-empty">Gagal memuat data rute.</div>');
        });
    }
</script>
@endsection
