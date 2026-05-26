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

    function formatTgl(val) {
        if (!val) return '-';
        const m = String(val).match(/^(\d{4})-(\d{2})-(\d{2})/);
        return m ? `${m[3]}-${m[2]}-${m[1]}` : val;
    }

    function metaChip(icon, text) {
        return `<span class="d-flex align-items-center gap-25" style="font-size:.78rem;color:#6e6b7b;">
            <i data-feather="${icon}" style="width:12px;height:12px;"></i>${escapeHtml(text)}
        </span>`;
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
            const tanggal = formatTgl(row.tanggal);
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
        $('#dashboardPermintaanMeta').html('');
        $('#DashboardPermintaanModal').modal('show');

        $.ajax({
            url: `/dashboard/permintaan/${id}/detail`,
            type: 'GET'
        })
        .done(function (res) {
            const header = res.header || {};
            const items = res.items || [];

            const sep = `<span style="color:#d0cfe8;font-size:.75rem;">|</span>`;
            $('#dashboardPermintaanMeta').html(
                metaChip('hash', header.nomor || '-') + sep +
                metaChip('anchor', header.kapal || '-') + sep +
                metaChip('calendar', formatTgl(header.tanggal) || '-') + sep +
                metaChip('users', header.bagian || '-')
            );
            feather.replace();

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
                                class="btn btn-xs btn-flat-info btn-dashboard-log"
                                data-id="${item.id}"
                                data-nomor="${escapeHtml(header.nomor || '-')}"
                                data-tanggal="${escapeHtml(header.tanggal || '-')}"
                                data-kapal="${escapeHtml(header.kapal || '-')}"
                                data-bagian="${escapeHtml(header.bagian || '-')}"
                                data-peminta="${escapeHtml(header.peminta || '-')}"
                                data-barang="${escapeHtml(item.barang || '-')}"
                                data-jumlah="${escapeHtml(jumlah || '-')}"
                            >
                                <i data-feather="map-pin" style="width:12px;height:12px;"></i> Lacak
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#dashboardPermintaanBody').html(rows);
            feather.replace();
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
        $('#dashDetailTanggal').text(formatTgl($(this).data('tanggal')) || '-');
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

    function showBrowserNotification(title, message) {
        if (!('Notification' in window)) {
            return;
        }

        const notify = function () {
            new Notification(title, {
                body: message,
                icon: "{{ url('/img/trimas.png') }}"
            });
        };

        if (Notification.permission === 'granted') {
            notify();
            return;
        }

        if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function (permission) {
                if (permission === 'granted') {
                    notify();
                }
            });
        }
    }

    $(document).on('click', '#btn-test-push-notification', function () {
        const btn = $(this);
        const originalText = btn.html();
        const target = $('input[name="notification_target"]:checked').val() || 'self';
        const payload = {
            _token: "{{ csrf_token() }}"
        };

        if (target === 'user') {
            const userId = $('#notification-target-user').val();
            if (!userId) {
                Swal.fire('Target belum dipilih', 'Pilih user tujuan notifikasi.', 'warning');
                return;
            }
            payload.id_user = userId;
        }

        if (target === 'role') {
            const roleId = $('#notification-target-role').val();
            if (!roleId) {
                Swal.fire('Target belum dipilih', 'Pilih role tujuan notifikasi.', 'warning');
                return;
            }
            payload.role_id = roleId;
            const idPerusahaan = "{{ Session::get('id_perusahaan') }}";
            const idKapal = "{{ Session::get('id_kapal') }}";
            if (idPerusahaan && idPerusahaan !== '0') {
                payload.id_perusahaan = idPerusahaan;
            }
            if (idKapal && idKapal !== '0') {
                payload.id_kapal = idKapal;
            }
        }

        btn.prop('disabled', true).html('Mengirim...');

        $.ajax({
            url: "{{ route('notifications.test') }}",
            type: 'POST',
            data: payload
        })
        .done(function (res) {
            const notification = res.notification || {};
            const title = notification.judul || 'Test push notification';
            const message = notification.pesan || res.message || 'Notifikasi test berhasil dikirim';

            if (typeof window.refreshNotifications === 'function') {
                window.refreshNotifications();
            }

            showBrowserNotification(title, message);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: `${res.message || 'Notifikasi test berhasil dikirim'} (${res.sent_count || 0} user)`,
                timer: 1600,
                showConfirmButton: false
            });
            $('#DashboardNotificationTestModal').modal('hide');
        })
        .fail(function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: xhr.responseJSON?.message || 'Gagal mengirim test notifikasi'
            });
        })
        .always(function () {
            btn.prop('disabled', false).html(originalText);
        });
    });

    $(document).on('change', 'input[name="notification_target"]', function () {
        const target = $('input[name="notification_target"]:checked').val();
        $('#notification-target-user-wrapper').toggle(target === 'user');
        $('#notification-target-role-wrapper').toggle(target === 'role');
    });
</script>
@endsection
@section('content')
<style>
    /* ── Design tokens ── */
    #dashboard-ecommerce {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* ── Hero ── */
    .dash-hero-card {
        background: linear-gradient(135deg, #0c63e4 0%, #1877f2 45%, #3395ff 100%);
        border-radius: 18px;
        color: #fff;
        padding: 28px 32px;
        box-shadow: 0 8px 30px rgba(13, 110, 253, 0.32);
        position: relative;
        overflow: hidden;
        z-index: 0;
    }
    .dash-hero-card .h-blob {
        position: absolute;
        border-radius: 50%;
        z-index: -1;
        pointer-events: none;
    }
    .dash-hero-card .h-blob-1 { top:-55px; right:-35px; width:200px; height:200px; background:rgba(255,255,255,0.08); }
    .dash-hero-card .h-blob-2 { bottom:-70px; right:70px; width:260px; height:260px; background:rgba(255,255,255,0.05); }
    .dash-hero-card .h-blob-3 { top:10px; left:38%; width:130px; height:130px; background:rgba(255,255,255,0.04); }
    .dash-hero-name {
        font-size: 1.55rem;
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.2;
    }
    .dash-hero-company {
        font-size: .88rem;
        opacity: .78;
        margin-top: .35rem;
    }
    .dash-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: .38rem;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.26);
        border-radius: 999px;
        padding: .28rem .85rem;
        font-size: .76rem;
        font-weight: 600;
        letter-spacing: .03em;
        backdrop-filter: blur(6px);
    }
    .dash-hero-date {
        font-size: .74rem;
        opacity: .62;
        text-align: right;
    }

    /* ── Expiry notice cards ── */
    .dash-expiry-card {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: 1rem 1.15rem;
        border-radius: 14px;
        cursor: pointer;
        text-decoration: none !important;
        transition: transform .17s, box-shadow .17s;
        color: inherit !important;
        user-select: none;
    }
    .dash-expiry-card:hover { transform: translateY(-2px); }
    .dash-expiry-card.ec-blue  { background:#eaf1ff; box-shadow:0 2px 12px rgba(13,110,253,0.1); }
    .dash-expiry-card.ec-green { background:#e7f9ef; box-shadow:0 2px 12px rgba(40,199,111,0.1); }
    .dash-expiry-card.ec-blue:hover  { box-shadow:0 6px 22px rgba(13,110,253,0.18); }
    .dash-expiry-card.ec-green:hover { box-shadow:0 6px 22px rgba(40,199,111,0.18); }
    .dash-expiry-icon {
        width: 50px; height: 50px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .ec-blue  .dash-expiry-icon { background:rgba(13,110,253,0.13); color:#0d6efd; }
    .ec-green .dash-expiry-icon { background:rgba(40,199,111,0.13); color:#28c76f; }
    .dash-expiry-body { flex: 1; min-width: 0; }
    .dash-expiry-eyebrow {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: .15rem;
        opacity: .65;
    }
    .ec-blue  .dash-expiry-eyebrow { color:#0d6efd; }
    .ec-green .dash-expiry-eyebrow { color:#28c76f; }
    .dash-expiry-title { font-size: .9rem; font-weight: 700; color:#1a2332; line-height:1.25; }
    .dash-expiry-sub   { font-size: .76rem; color:#6e7a8a; margin-top:.1rem; }
    .dash-expiry-count {
        flex-shrink: 0;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    .ec-blue  .dash-expiry-count { color:#0d6efd; }
    .ec-green .dash-expiry-count { color:#28c76f; }

    /* ── Stat cards ── */
    .stat-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 2px 14px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: .9rem;
        transition: transform .17s, box-shadow .17s;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 6px 24px rgba(0,0,0,0.1); }
    .stat-card::after {
        content:'';
        position:absolute;
        bottom:-22px; right:-22px;
        width:95px; height:95px;
        border-radius:50%;
        opacity:.06;
    }
    .sc-blue::after   { background:#0d6efd; }
    .sc-teal::after   { background:#00b8d4; }
    .sc-green::after  { background:#28c76f; }
    .sc-purple::after { background:#7367f0; }
    .stat-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .si-blue   { background:rgba(13,110,253,0.1);  color:#0d6efd; }
    .si-teal   { background:rgba(0,184,212,0.1);   color:#00b8d4; }
    .si-green  { background:rgba(40,199,111,0.1);  color:#28c76f; }
    .si-purple { background:rgba(115,103,240,0.1); color:#7367f0; }
    .stat-text { min-width: 0; }
    .stat-card-label {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .09em;
        color: #a3adb8;
        font-weight: 600;
        margin-bottom: .22rem;
    }
    .stat-card-value {
        font-size: 1rem;
        font-weight: 700;
        color: #1a2332;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }

    /* ── Section card ── */
    .dash-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 14px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dash-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .95rem 1.35rem;
        border-bottom: 1px solid #edf0f5;
        background: #fbfcff;
    }
    .dash-card-body { padding: 1.25rem 1.35rem; }

    /* ── Section title ── */
    .dash-section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .88rem;
        font-weight: 700;
        color: #1a2332;
    }
    .dash-title-bar {
        display: inline-block;
        width: 3px; height: 15px;
        background: #0d6efd;
        border-radius: 2px;
        flex-shrink: 0;
    }

    /* ── OTP section ── */
    .otp-section-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 14px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .otp-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .9rem 1.35rem;
        border-bottom: 1px solid #e5eeff;
        background: linear-gradient(90deg, #f2f7ff, #f8faff);
    }
    .otp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(255px, 1fr));
        gap: .875rem;
        padding: 1.1rem 1.35rem;
    }
    .otp-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        border: 1px solid #dce8ff;
        border-left: 3px solid #0d6efd;
        border-radius: 10px;
        padding: .8rem 1rem;
        background: #fff;
        transition: box-shadow .17s, transform .17s;
    }
    .otp-item:hover { box-shadow:0 4px 14px rgba(13,110,253,0.11); transform:translateY(-1px); }
    .otp-icon-wrap {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: rgba(13,110,253,0.08);
        display: flex; align-items: center; justify-content: center;
        color: #0d6efd;
        flex-shrink: 0;
    }
    .otp-code {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: .14em;
        color: #1a2332;
        line-height: 1;
    }
    .otp-sender {
        font-size: .74rem;
        color: #6e7a8a;
        margin-top: .2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 130px;
    }
    .otp-expire { margin-left:auto; text-align:right; flex-shrink:0; }
    .otp-expire-label {
        font-size: .63rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #a3adb8;
        line-height: 1;
    }
    .otp-expire-val {
        font-size: .76rem;
        font-weight: 700;
        color: #c94000;
        margin-top: .15rem;
        white-space: nowrap;
    }

    /* ── Table ── */
    .text-blue { color: #0d6efd !important; }
    .table-minimal thead th {
        background: #f0f5ff;
        color: #1a3a6e;
        font-size: .73rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        font-weight: 700;
        border-bottom: 2px solid #dae4ff;
        padding: .7rem 1rem;
        white-space: nowrap;
    }
    .table-minimal tbody td {
        vertical-align: middle;
        padding: .6rem 1rem;
        font-size: .86rem;
        border-bottom: 1px solid #f0f2f5;
        color: #2d3a4a;
    }
    .table-minimal tbody tr:last-child td { border-bottom: none; }
    .table-minimal tbody tr:hover td { background: #f6f9ff; }
    .live-dot {
        display: inline-block;
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #28c76f;
        margin-right: .3rem;
        vertical-align: middle;
        animation: livePulse 2s ease-in-out infinite;
    }
    @keyframes livePulse {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:.45; transform:scale(.7); }
    }

    /* ── Permintaan cards ── */
    .permintaan-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(275px, 1fr));
        gap: 1rem;
    }
    .permintaan-item {
        display: flex;
        flex-direction: column;
        gap: .7rem;
        border: 1px solid #e8edf5;
        border-top: 3px solid #0d6efd;
        border-radius: 14px;
        padding: 1.1rem 1.15rem 1rem;
        background: #fff;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        transition: transform .17s, box-shadow .17s;
    }
    .permintaan-item:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(13,110,253,0.12); }
    .permintaan-item-nomor {
        font-weight: 700;
        font-size: .92rem;
        color: #1a2332;
        line-height: 1.3;
    }
    .permintaan-item-date {
        display: inline-flex;
        align-items: center;
        gap: .22rem;
        font-size: .7rem;
        font-weight: 600;
        color: #0d6efd;
        background: rgba(13,110,253,0.08);
        border-radius: 6px;
        padding: .18rem .5rem;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .permintaan-divider {
        border: none;
        border-top: 1px solid #edf0f5;
        margin: 0;
    }
    .permintaan-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .55rem .75rem;
    }
    .permintaan-meta-field { display:flex; flex-direction:column; gap:.05rem; min-width:0; }
    .permintaan-meta-label {
        font-size: .63rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #a3adb8;
        font-weight: 600;
    }
    .permintaan-meta-value {
        font-size: .8rem;
        font-weight: 600;
        color: #2d3a4a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .permintaan-pagination {
        margin-top: 1.25rem;
        display: flex;
        justify-content: center;
    }
    .permintaan-pagination .pagination { margin-bottom:0; }

    /* ── Timeline (modal internals — layout only) ── */
    .permintaan-track-wrap {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        min-height: 520px;
    }
    .permintaan-track-left {
        padding: 1.5rem;
        border-right: 1px solid #edf0f5;
        background: #fafafd;
    }
    .permintaan-track-title {
        font-size: .95rem;
        font-weight: 700;
        color: #1a2332;
        margin-bottom: 1rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #edf0f5;
    }
    .permintaan-track-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem 1.25rem;
    }
    .permintaan-track-label {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #a3adb8;
        font-weight: 600;
        margin-bottom: .2rem;
    }
    .permintaan-track-value { font-size:.9rem; font-weight:600; color:#2d3a4a; }
    .permintaan-track-right { padding:1.5rem; background:#f6f7fb; }
    .permintaan-track-status {
        font-size: .82rem;
        font-weight: 600;
        color: #6e7a8a;
        margin-bottom: 1rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #edf0f5;
    }
    .permintaan-track-status strong { color:#1a2332; }
    .permintaan-track-timeline {
        max-height: 420px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 10px;
        padding: .8rem 1rem;
    }
    .timeline-item {
        display: grid;
        grid-template-columns: 86px 14px 1fr;
        gap: .75rem;
        align-items: start;
        position: relative;
        padding: .4rem 0 .85rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 101px;
        top: 21px;
        bottom: -4px;
        border-left: 2px dashed #dde2ee;
    }
    .timeline-item:last-child::before { display:none; }
    .timeline-time {
        text-align: right;
        font-size: .74rem;
        color: #6e7a8a;
        line-height: 1.35;
        padding-top: .08rem;
    }
    .timeline-dot {
        width: 14px; height: 14px;
        border-radius: 50%;
        background: #c8cfe0;
        margin-top: .12rem;
        box-shadow: 0 0 0 3px rgba(200,207,224,0.22);
        position: relative;
    }
    .timeline-item.is-active .timeline-dot {
        background: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13,110,253,0.2);
    }
    .timeline-item.is-active .timeline-dot::after {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 50%;
        border: 2px solid rgba(13,110,253,0.3);
        animation: timelinePulse 1.8s ease-out infinite;
    }
    @keyframes timelinePulse {
        0%   { transform:scale(.85); opacity:.85; }
        70%  { transform:scale(1.2); opacity:0; }
        100% { transform:scale(1.2); opacity:0; }
    }
    .timeline-content { font-size:.86rem; color:#2d3a4a; line-height:1.5; }
    .timeline-content .small { color:#6e7a8a; font-size:.76rem; }
    .timeline-empty { text-align:center; color:#a3adb8; padding:1.5rem .5rem; font-size:.86rem; }

    /* ── Responsive ── */
    @media (max-width: 991px) {
        .permintaan-track-wrap { grid-template-columns:1fr; }
        .permintaan-track-left { border-right:0; border-bottom:1px solid #edf0f5; }
        .permintaan-track-meta-grid { grid-template-columns:1fr 1fr; }
        .dash-hero-card { padding:20px 22px; }
        .dash-hero-name { font-size:1.3rem; }
    }
    @media (max-width: 575px) {
        .otp-grid { grid-template-columns:1fr; }
        .permintaan-list { grid-template-columns:1fr; }
        .permintaan-track-meta-grid { grid-template-columns:1fr; }
        .dash-hero-right { display:none; }
        .dash-expiry-count { display:none; }
    }
</style>

<section id="dashboard-ecommerce">

    {{-- ── Hero ── --}}
    <div class="dash-hero-card">
        <span class="h-blob h-blob-1"></span>
        <span class="h-blob h-blob-2"></span>
        <span class="h-blob h-blob-3"></span>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
            <div>
                <div class="dash-hero-name">Selamat Datang Kembali, {{ Session::get('name') }}</div>
                <div class="dash-hero-company">{{ $com->nama ?? '-' }}</div>
            </div>
            <div class="dash-hero-right d-flex flex-column align-items-end gap-50">
                <div class="dash-hero-pill">
                    <i data-feather="award" style="width:13px;height:13px;"></i>
                    Akses Aktif
                </div>
                <div class="dash-hero-date">{{ date('l, d F Y') }}</div>
            </div>
        </div>
    </div>

    {{-- ── OTP Pengiriman ── --}}
    @if(!empty($pending_kirim_otps) && count($pending_kirim_otps))
    <div class="otp-section-card">
        <div class="otp-section-header">
            <span class="dash-section-title">
                <span class="dash-title-bar"></span>
                <i data-feather="send" style="width:14px;height:14px;"></i>
                OTP Pengiriman Barang
            </span>
            <span class="badge bg-primary rounded-pill">{{ count($pending_kirim_otps) }}</span>
        </div>
        <div class="otp-grid">
            @foreach($pending_kirim_otps as $otp)
            <div class="otp-item">
                <div class="otp-icon-wrap">
                    <i data-feather="key" style="width:17px;height:17px;"></i>
                </div>
                <div style="min-width:0;">
                    <div class="otp-code">{{ $otp->otp_code }}</div>
                    <div class="otp-sender">{{ $otp->pengirim_nama ?? $otp->pengirim_username ?? '-' }}</div>
                </div>
                <div class="otp-expire">
                    <div class="otp-expire-label">Expired</div>
                    <div class="otp-expire-val">{{ \Carbon\Carbon::parse($otp->expires_at)->format('d-m-Y H:i') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── OTP Penurunan ── --}}
    @if(!empty($pending_turun_otps) && count($pending_turun_otps))
    <div class="otp-section-card">
        <div class="otp-section-header">
            <span class="dash-section-title">
                <span class="dash-title-bar"></span>
                <i data-feather="arrow-down-circle" style="width:14px;height:14px;"></i>
                OTP Penurunan Barang
            </span>
            <span class="badge bg-primary rounded-pill">{{ count($pending_turun_otps) }}</span>
        </div>
        <div class="otp-grid">
            @foreach($pending_turun_otps as $otp)
            <div class="otp-item">
                <div class="otp-icon-wrap">
                    <i data-feather="key" style="width:17px;height:17px;"></i>
                </div>
                <div style="min-width:0;">
                    <div class="otp-code">{{ $otp->otp_code }}</div>
                    <div class="otp-sender">{{ $otp->pengirim_nama ?? $otp->pengirim_username ?? '-' }}</div>
                </div>
                <div class="otp-expire">
                    <div class="otp-expire-label">Expired</div>
                    <div class="otp-expire-val">{{ \Carbon\Carbon::parse($otp->expires_at)->format('d-m-Y H:i') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(Session::get('previllage') != 4)

        {{-- ── Expiry alerts ── --}}
        @if($count_doc || $count_dockru)
        <div class="row g-2">
            @if($count_doc)
            <div class="col-md-6 col-12">
                <a class="dash-expiry-card ec-blue d-flex w-100"
                   data-bs-toggle="modal" data-bs-target="#large">
                    <div class="dash-expiry-icon">
                        <i data-feather="file-text" style="width:22px;height:22px;"></i>
                    </div>
                    <div class="dash-expiry-body">
                        <div class="dash-expiry-eyebrow">Perhatian</div>
                        <div class="dash-expiry-title">Dokumen Kapal</div>
                        <div class="dash-expiry-sub">Akan segera expired. Klik untuk melihat.</div>
                    </div>
                    <div class="dash-expiry-count">{{ $count_doc }}</div>
                </a>
            </div>
            @endif
            @if($count_dockru)
            <div class="col-md-6 col-12">
                <a class="dash-expiry-card ec-green d-flex w-100"
                   data-bs-toggle="modal" data-bs-target="#krumodal">
                    <div class="dash-expiry-icon">
                        <i data-feather="users" style="width:22px;height:22px;"></i>
                    </div>
                    <div class="dash-expiry-body">
                        <div class="dash-expiry-eyebrow">Perhatian</div>
                        <div class="dash-expiry-title">Dokumen Kru</div>
                        <div class="dash-expiry-sub">Akan segera expired. Klik untuk melihat.</div>
                    </div>
                    <div class="dash-expiry-count">{{ $count_dockru }}</div>
                </a>
            </div>
            @endif
        </div>
        @endif

        {{-- ── Stat cards ── --}}
        <div class="row g-2">
            @if(!empty($perusahaan))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card sc-blue">
                    <div class="stat-icon si-blue">
                        <i data-feather="home" style="width:20px;height:20px;"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-card-label">Perusahaan</div>
                        <div class="stat-card-value" title="{{ $perusahaan }}">{{ $perusahaan }}</div>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($kapal))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card sc-teal">
                    <div class="stat-icon si-teal">
                        <i data-feather="anchor" style="width:20px;height:20px;"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-card-label">Kapal</div>
                        <div class="stat-card-value" title="{{ $kapal }}">{{ $kapal }}</div>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($karyawan))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card sc-green">
                    <div class="stat-icon si-green">
                        <i data-feather="users" style="width:20px;height:20px;"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-card-label">Karyawan</div>
                        <div class="stat-card-value">{{ $karyawan }}</div>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($user))
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="stat-card sc-purple">
                    <div class="stat-icon si-purple">
                        <i data-feather="user-check" style="width:20px;height:20px;"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-card-label">User Aktif</div>
                        <div class="stat-card-value">{{ $user }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

    @else

        {{-- ── Prosedur table (privilege 4) ── --}}
        @if(Session::get('id_kapal') != 0)
        <div class="dash-card">
            <div class="dash-card-header">
                <span class="dash-section-title">
                    <span class="dash-title-bar"></span>
                    <i data-feather="book-open" style="width:14px;height:14px;"></i>
                    Frekuensi Akses Prosedur
                </span>
                <span class="badge rounded-pill"
                      style="background:#e7f9ef;color:#1a7a48;font-size:.7rem;font-weight:600;">
                    <span class="live-dot"></span>Live
                </span>
            </div>
            <div class="dash-card-body p-0">
                <div class="table-responsive">
                    <table id="tabledetail" class="table table-minimal mb-0">
                        <thead>
                        <tr>
                            <th style="padding-left:1.35rem;">Prosedur</th>
                            <th>Lihat</th>
                            <th>Terakhir Lihat</th>
                            <th>Download</th>
                            <th>Terakhir Download</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($prosedur as $show)
                        <tr>
                            <td style="padding-left:1.35rem;">{{ $show->kode }}</td>
                            <td>
                                <span class="badge rounded-pill"
                                      style="background:#eef2ff;color:#3d5a99;font-weight:700;">
                                    {{ $show->jml_lihat }}×
                                </span>
                            </td>
                            <td>
                                @if($show->update_lihat)
                                    {{ \Carbon\Carbon::parse($show->update_lihat)->addHours(7)->format('d-m-Y H:i') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill"
                                      style="background:#eef2ff;color:#3d5a99;font-weight:700;">
                                    {{ $show->jml_download }}×
                                </span>
                            </td>
                            <td>
                                @if($show->update_download)
                                    {{ \Carbon\Carbon::parse($show->update_download)->addHours(7)->format('d-m-Y H:i') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    @endif

    {{-- ── Permintaan Barang ── --}}
    @if(!empty($permintaan_dashboard) && count($permintaan_dashboard))
    <div class="dash-card">
        <div class="dash-card-header">
            <span class="dash-section-title">
                <span class="dash-title-bar"></span>
                <i data-feather="package" style="width:14px;height:14px;"></i>
                Detail Permintaan Barang
            </span>
            <span class="badge rounded-pill"
                  style="background:#eef2ff;color:#0d6efd;font-size:.7rem;font-weight:600;">
                {{ count($permintaan_dashboard) }} permintaan
            </span>
        </div>
        <div class="dash-card-body">
            <div class="permintaan-list">
                @foreach($permintaan_dashboard as $permintaan)
                <div class="permintaan-item">
                    <div class="d-flex align-items-start justify-content-between gap-75">
                        <div class="permintaan-item-nomor">{{ $permintaan->nomor }}</div>
                        <span class="permintaan-item-date">
                            <i data-feather="calendar" style="width:10px;height:10px;"></i>
                            {{ $permintaan->tanggal }}
                        </span>
                    </div>
                    <hr class="permintaan-divider">
                    <div class="permintaan-meta-grid">
                        <div class="permintaan-meta-field">
                            <span class="permintaan-meta-label">Peminta</span>
                            <span class="permintaan-meta-value" title="{{ $permintaan->peminta ?? '-' }}">{{ $permintaan->peminta ?? '-' }}</span>
                        </div>
                        <div class="permintaan-meta-field">
                            <span class="permintaan-meta-label">Kapal</span>
                            <span class="permintaan-meta-value" title="{{ $permintaan->kapal ?? '-' }}">{{ $permintaan->kapal ?? '-' }}</span>
                        </div>
                        <div class="permintaan-meta-field" style="grid-column:1/-1;">
                            <span class="permintaan-meta-label">Bagian</span>
                            <span class="permintaan-meta-value" title="{{ $permintaan->bagian ?? '-' }}">{{ $permintaan->bagian ?? '-' }}</span>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary btn-dashboard-detail align-self-start"
                        data-id="{{ $permintaan->id }}"
                        style="border-radius:8px;"
                    >
                        <i data-feather="list" style="width:13px;height:13px;" class="me-25"></i>
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
    @endif

</section>

<div class="modal fade" id="DashboardPermintaanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Detail Barang Permintaan</h5>
                    <div id="dashboardPermintaanMeta" class="d-flex flex-wrap align-items-center gap-1 mt-25"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-1" style="width:40px;">No</th>
                            <th>Barang</th>
                            <th style="width:110px;">Jumlah</th>
                            <th style="width:160px;">Status</th>
                            <th style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody id="dashboardPermintaanBody"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade permintaan-track-modal" id="DashboardLogModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lacak Barang</h5>
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
                                <div class="permintaan-track-label">Jumlah</div>
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
                        <div class="permintaan-track-status" style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6e7a8a;margin-bottom:.85rem;">Riwayat Proses</div>
                        <div class="permintaan-track-timeline" id="dashboardLogTimeline">
                            <div class="timeline-empty">Memuat riwayat rute...</div>
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

<div class="modal fade" id="DashboardNotificationTestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Push Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Target Notifikasi</label>
                <div class="d-flex flex-column gap-50 mb-1">
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="notification_target" value="self" checked>
                        <span class="form-check-label">Diri sendiri</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="notification_target" value="user">
                        <span class="form-check-label">User tertentu</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="notification_target" value="role" {{ empty($notification_roles) || count($notification_roles) === 0 ? 'disabled' : '' }}>
                        <span class="form-check-label">Role tertentu</span>
                    </label>
                </div>
                <div id="notification-target-user-wrapper" style="display:none;">
                    <label class="form-label">Pilih User</label>
                    <select id="notification-target-user" class="form-control">
                        <option value="">-Pilih User-</option>
                        @foreach($notification_users as $user)
                            <option value="{{ $user->id }}">{{ $user->nama ?? $user->username }} ({{ $user->username }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="notification-target-role-wrapper" style="display:none;">
                    <label class="form-label">Pilih Role</label>
                    <select id="notification-target-role" class="form-control">
                        <option value="">-Pilih Role-</option>
                        @foreach($notification_roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nama }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Role akan dikirim ke semua user aktif pada role tersebut sesuai konteks perusahaan/kapal aktif.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-test-push-notification">
                    <i data-feather="send" class="me-25"></i>
                    Kirim Test
                </button>
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
                                        <tr>
                                            <td>{{ $kru->kapal ?? '-' }}<br>
                                                {{ $kru->karyawan ?? '-' }}</td>
                                            <td><a type="button" href="{{ asset('file_upload/'.$kru->file) }}" target="_blank" 
                                                    title="Buka File" data-id="{{$kru->id}}" data-file="{{$kru->file}}">{{ $kru->filename ?? ($kru->filename ?? 'File') }}</a></td>
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
