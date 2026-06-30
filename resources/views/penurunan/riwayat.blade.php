@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Riwayat Penurunan Barang</h4>
    </div>
    <div class="card-header border-bottom">
        <div class="row g-1 w-100">
            <div class="col-sm-3">
                <select id="filter-kapal" class="form-control">
                    <option value="">Semua Kapal</option>
                    @foreach($kapal as $kp)
                        <option value="{{ $kp->id }}">{{ $kp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select id="filter-bagian" class="form-control">
                    <option value="">Semua Bagian</option>
                    <option value="1">DECK</option>
                    <option value="2">MESIN</option>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="date" id="filter-dari" class="form-control" placeholder="Tanggal Dari">
            </div>
            <div class="col-sm-2">
                <input type="date" id="filter-sampai" class="form-control" placeholder="Tanggal Sampai">
            </div>
            <div class="col-sm-2">
                <button id="btn-filter" class="btn btn-primary">Filter</button>
                <button id="btn-reset" class="btn btn-outline-secondary ms-50">Reset</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="table-riwayat" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nomor</th>
                    <th>Kapal</th>
                    <th>Bagian</th>
                    <th>Penerima</th>
                    <th>Pembuat</th>
                    <th>Jumlah Item</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-detail-riwayat" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang Penurunan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="px-1 py-50 border-bottom bg-light d-flex gap-1 flex-wrap" id="riwayat-meta" style="font-size:.85rem;min-height:34px;"></div>
                <table class="table table-hover mb-0" id="table-detail-riwayat">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">No</th>
                            <th>Nama Barang</th>
                            <th style="width:100px;">Satuan</th>
                            <th style="width:90px;">Jumlah</th>
                            <th style="width:110px;">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-detail-riwayat">
                        <tr><td colspan="5" class="text-center text-muted py-2">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const table = $('#table-riwayat').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('penurunan.riwayat.data') }}',
            type: 'POST',
            data: function (d) {
                d.id_kapal       = $('#filter-kapal').val();
                d.bagian         = $('#filter-bagian').val();
                d.tanggal_dari   = $('#filter-dari').val();
                d.tanggal_sampai = $('#filter-sampai').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {
                data: 'tanggal',
                render: function (data) {
                    if (!data) return '-';
                    const p = data.split(' ')[0].split('-');
                    return p[2] + '-' + p[1] + '-' + p[0];
                }
            },
            { data: 'nomor', name: 'nomor', defaultContent: '-' },
            { data: 'kapal', name: 'kapal' },
            { data: 'bagian_label', name: 'bagian_label' },
            { data: 'penerima', name: 'penerima' },
            { data: 'pembuat', name: 'pembuat' },
            { data: 'jumlah_item', name: 'jumlah_item' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <div class="d-flex gap-50">
                            <button type="button"
                                class="btn btn-sm btn-outline-primary btn-detail-riwayat"
                                data-id="${row.id}"
                                data-kapal="${row.kapal}"
                                data-tanggal="${row.tanggal ?? ''}"
                                data-bagian="${row.bagian_label}"
                                data-nomor="${row.nomor ?? '-'}">
                                Detail
                            </button>
                            <a href="{{ url('/penurunan/pdf') }}/${row.uid}" target="_blank"
                                class="btn btn-sm btn-outline-success">
                                PDF
                            </a>
                        </div>`;
                }
            }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            if (typeof feather !== 'undefined') feather.replace();
        }
    });

    $('#btn-filter').on('click', function () {
        table.ajax.reload();
    });

    $('#btn-reset').on('click', function () {
        $('#filter-kapal').val('');
        $('#filter-bagian').val('');
        $('#filter-dari').val('');
        $('#filter-sampai').val('');
        table.ajax.reload();
    });

    $(document).on('click', '.btn-detail-riwayat', function () {
        const id      = $(this).data('id');
        const kapal   = $(this).data('kapal');
        const tanggal = $(this).data('tanggal');
        const bagian  = $(this).data('bagian');
        const nomor   = $(this).data('nomor');

        const tgl = tanggal ? tanggal.split(' ')[0].split('-').reverse().join('-') : '-';

        $('#riwayat-meta').html(`
            <span style="color:#6e6b7b;"><b>No:</b> ${nomor}</span>
            <span style="color:#d0cfe8;">|</span>
            <span style="color:#6e6b7b;"><b>Kapal:</b> ${kapal}</span>
            <span style="color:#d0cfe8;">|</span>
            <span style="color:#6e6b7b;"><b>Tanggal:</b> ${tgl}</span>
            <span style="color:#d0cfe8;">|</span>
            <span style="color:#6e6b7b;"><b>Bagian:</b> ${bagian}</span>
        `);

        $('#tbody-detail-riwayat').html('<tr><td colspan="5" class="text-center text-muted py-2">Memuat...</td></tr>');
        $('#modal-detail-riwayat').modal('show');

        $.get('{{ url('/penurunan/get') }}/' + id, function (res) {
            if (!res || res.length === 0) {
                $('#tbody-detail-riwayat').html('<tr><td colspan="5" class="text-center text-muted py-2">Tidak ada data.</td></tr>');
                return;
            }
            let rows = '';
            res.forEach(function (item, i) {
                rows += `<tr>
                    <td>${i + 1}</td>
                    <td>${item.barang ?? '-'}</td>
                    <td>${item.satuan ?? '-'}</td>
                    <td>${item.jumlah ?? '-'}</td>
                    <td>${item.kondisi ?? '-'}</td>
                </tr>`;
            });
            $('#tbody-detail-riwayat').html(rows);
        }).fail(function () {
            $('#tbody-detail-riwayat').html('<tr><td colspan="5" class="text-center text-danger py-2">Gagal memuat data.</td></tr>');
        });
    });
});
</script>
@endsection
