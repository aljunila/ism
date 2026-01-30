@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Data Trip Kapal</h4>
        <a href="/data_kapal/trip/form" class="btn btn-primary btn-sm">Tambah Data</a>
    </div>
    <div class="card-body">
        <table id="table-pel" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kapal</th>
                    <th>Tanggal</th>
                    <th>Pelabuhan</th>
                    <th>Trip</th>
                    <th>Jam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="kendaraanModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Kendaraan & Biaya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableKendaraan" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gol Kendaraan</th>
                            <th>Jumlah</th>
                            <th>Nominal</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Grand Total</th>
                            <th id="grandTotal">Rp 0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-success"
                        id="btnDownloadExcel">
                    Download Excel
                </button>
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Tutup
                </button>
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

        const table = $('#table-pel').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('trip.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kapal', name: 'kapal' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'pelabuhan', name: 'pelabuhan' },
                { data: 'trip', name: 'trip' },
                { data: 'jam', name: 'jam' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });


        $(document).on('click', '.btn-delete-pel', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus data trip ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '{{ url('data_kapal/trip') }}/' + id,
                    type: 'DELETE',
                    success: function () {
                        Swal.fire('Terhapus', 'data trip berhasil dihapus', 'success');
                        table.ajax.reload(null, false);
                        loadCabang                    
                    },
                    error: function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        });
    });

    let kendaraanTable;

    function openKendaraanModal(tripId) {
        currentTripId = tripId;
        $('#kendaraanModal').modal('show');

        if ($.fn.DataTable.isDataTable('#tableKendaraan')) {
            kendaraanTable.ajax.url(`trip/${tripId}/amount`).load();
            return;
        }

        kendaraanTable = $('#tableKendaraan').DataTable({
            processing: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: `trip/${tripId}/amount`,
                dataSrc: function (json) {
                    let grandTotal = 0;
                    json.forEach(row => grandTotal += row.total);
                    $('#grandTotal').html(formatRupiah(grandTotal));
                    return json;
                }
            },
            columns: [
                {
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: 'nama',
                },
                {
                    data: 'jumlah',
                    className: 'text-end'
                },
                {
                    data: 'nominal',
                    className: 'text-end',
                    render: data => formatRupiah(data)
                },
                {
                    data: 'total',
                    className: 'text-end',
                    render: data => formatRupiah(data)
                }
            ]
        });
    }

    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    $('#btnDownloadExcel').on('click', function () {
        if (!currentTripId) return;

        window.location.href = "{{ route('trip.excel', ':id') }}"
        .replace(':id', currentTripId);
    });
</script>
@endsection
