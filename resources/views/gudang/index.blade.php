@extends('main')

@section('content')
@section('scriptheader')
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('/vuexy/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
@endsection

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Gudang</h4>
        <!-- <button class="btn btn-primary btn-sm" id="btn-add-gudang">Tambah Data</button> -->
    </div>
    <div class="card-body">
        <div class="card-header border-bottom">
            @if(Session::get('previllage')!=3)
            <div class="col-sm-3">
                <select name="jenis" id="jenis" class="form-control">
                    <option value="0">Pilih</option>
                    <option value="1">Gudang Kapal</option>
                    <option value="2">Gudang Cabang</option>
                </select>
            </div>
            @endif
            <div class="col-sm-4">
                <select name="kel" id="kel" class="form-control kapal">
                    <option value="">Pilih Kelompok Barang</option>
                    @foreach($kelompok as $kel)
                        <option value="{{$kel->id}}">{{$kel->nama}} ({{$kel->kode}})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <select name="id_kapal" id="id_kapal" class="form-control kapal">
                    <option value="">Pilih Kapal</option>
                    @foreach($kapal as $k)
                        <option value="{{$k->id}}">{{$k->nama}}</option>
                    @endforeach
                </select>
                <select name="id_cabang" id="id_cabang" class="form-control">
                    <option value="">Pilih Cabang</option>
                    @foreach($cabang as $c)
                    <option value="{{$c->id}}">{{$c->cabang}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <table id="table-gudang" class="table table-striped w-100">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Kelompok</th>
                    <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Kode Barang</th>
                    <th colspan="2">Kondisi</th>
                    <th rowspan="2">Total</th>
                    <th rowspan="2">Aksi</th>
                </tr>
                 <tr>
                    <th>Baik</th>
                    <th>Rusak</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-gudang" tabindex="-1" aria-labelledby="modal-gudang-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-gudang-label">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Barang</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="gudang-barang" disabled class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Jumlah</label>
                        </div>
                        <div class="col-9">
                            <input type="number" id="gudang-jumlah" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Kondisi</label>
                        </div>
                        <div class="col-4">
                            <input type="number" id="gudang-baik" class="form-control" placeholder="Baik">
                        </div>
                        <div class="col-4">
                            <input type="number" id="gudang-habis" class="form-control" placeholder="Rusak">
                        </div>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Keterangan</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="gudang-keterangan" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-save-gudang">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- ── Modal Pemakaian Barang ── --}}
<div class="modal fade" id="modal-pemakaian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Catat Pemakaian Barang</h5>
                    <small class="text-muted" id="pemakaian-barang-sub"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1 row">
                    <div class="col-4">
                        <label class="col-form-label">QTY Digunakan</label>
                    </div>
                    <div class="col-8">
                        <input type="number" id="pemakaian-qty" class="form-control" min="1" placeholder="0">
                        <small class="text-muted">Stok tersedia: <strong id="pemakaian-stok-info">-</strong></small>
                    </div>
                </div>
                <div class="mb-1 row">
                    <div class="col-4">
                        <label class="col-form-label">Tanggal</label>
                    </div>
                    <div class="col-8">
                        <input type="date" id="pemakaian-tanggal" class="form-control">
                    </div>
                </div>
                <div class="mb-1 row">
                    <div class="col-4">
                        <label class="col-form-label">Keterangan</label>
                    </div>
                    <div class="col-8">
                        <input type="text" id="pemakaian-keterangan" class="form-control" placeholder="Opsional">
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-lihat-riwayat">
                    <i data-feather="clock" style="width:13px;height:13px;"></i> Lihat Riwayat
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-simpan-pemakaian">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Riwayat Pemakaian ── --}}
<div class="modal fade" id="modal-riwayat-pemakaian" tabindex="-1" aria-hidden="true" style="z-index:1070;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Riwayat Pemakaian</h5>
                    <small class="text-muted" id="riwayat-barang-sub"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0" id="table-riwayat-pemakaian">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;" class="ps-1">No</th>
                            <th style="width:120px;">Tanggal</th>
                            <th style="width:80px;">QTY</th>
                            <th>Keterangan</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody id="riwayat-pemakaian-body">
                        <tr><td colspan="5" class="text-center text-muted py-2">Memuat data...</td></tr>
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
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#id_kapal').hide();
        $('#id_cabang').hide();

        $(document).on('change', '#jenis', function() {
            let jenis = $(this).val();
            if(jenis==1){
                $('#id_kapal').show();
                $('#id_cabang').hide();
                table.ajax.reload();
            } else {
                $('#id_kapal').hide();
                $('#id_cabang').show();
                table.ajax.reload();
            }
        })

        $('#id_kapal').on('change', function () {
            table.ajax.reload();
            $('#id_cabang').val('');
        });

        $('#id_cabang').on('change', function () {
            table.ajax.reload();
            $('#id_kapal').val('');
        });

        $('#kel').on('change', function () {
         table.ajax.reload();
        });

        const table = $('#table-gudang').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: "/gudang/data",
                type: "POST",
                data: function(d){
                    d.id_kapal= $('#id_kapal').val(),
                    d.id_cabang= $('#id_cabang').val(),
                    d.kel= $('#kel').val(),
                    d._token= "{{ csrf_token() }}"
                },
            },
            columns: [
                { data: null, 
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1; 
                    },
                    orderable: false,
                    searchable: false
                },
                { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        render: function (data, type, row) {
                            return `${row.kelompok} (${row.part})`;
                        }
                },
                { data: 'barang', name: 'barang' },
                { data: 'kode', name: 'kode' },
                { data: 'baik', name: 'baik' },
                { data: 'habis', name: 'habis' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.btn-edit-gudang', function () {
            const btn = $(this);
            $('#modal-gudang-label').text('Edit Data');
            $('#gudang-barang').val(btn.data('barang'));
            $('#gudang-kode').val(btn.data('kode'));
            $('#gudang-jumlah').val(btn.data('jumlah'));
            $('#gudang-baik').val(btn.data('baik'));
            $('#gudang-habis').val(btn.data('habis'));
            $('#gudang-keterangan').val(btn.data('keterangan'));
            $('#btn-save-gudang').data('mode', 'edit').data('id', btn.data('id'));
            $('#modal-gudang').modal('show');
        });

        $('#btn-save-gudang').on('click', function () {
            const mode = $(this).data('mode') || 'create';
            const id = $(this).data('id');
            const payload = {
                jumlah: $('#gudang-jumlah').val(),
                baik: $('#gudang-baik').val(),
                habis: $('#gudang-habis').val(),
                keterangan: $('#gudang-keterangan').val(),
            };
            const ajaxOpts = {
                url: '{{ url('gudang') }}/' + id,
                type: 'PUT',
                data: payload
            };
            $.ajax(ajaxOpts)
            .done(() => {
                Swal.fire('Sukses', mode === 'edit' ? 'data diperbarui' : 'data ditambahkan', 'success');
                $('#modal-gudang').modal('hide');
                table.ajax.reload(null, false);
                loadCabang();
            })
            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'));
        });

        // ── Pemakaian Barang ──
        let pemakaianGudangId = null;

        $(document).on('click', '.btn-pemakaian-gudang', function () {
            const btn = $(this);
            pemakaianGudangId = btn.data('id');
            const stok = btn.data('stok');
            const barang = btn.data('barang');

            $('#pemakaian-barang-sub').text(barang);
            $('#pemakaian-stok-info').text(stok);
            $('#pemakaian-qty').val('').attr('max', stok);
            $('#pemakaian-tanggal').val(new Date().toISOString().slice(0, 10));
            $('#pemakaian-keterangan').val('');
            $('#btn-lihat-riwayat').data('barang', barang).data('id', pemakaianGudangId);
            $('#modal-pemakaian').modal('show');
            feather.replace();
        });

        $('#btn-simpan-pemakaian').on('click', function () {
            const qty = parseInt($('#pemakaian-qty').val(), 10);
            const stok = parseInt($('#pemakaian-qty').attr('max'), 10);
            const tanggal = $('#pemakaian-tanggal').val();

            if (!qty || qty < 1) {
                Swal.fire('Peringatan', 'QTY harus diisi minimal 1', 'warning'); return;
            }
            if (qty > stok) {
                Swal.fire('Peringatan', `QTY tidak boleh melebihi stok tersedia (${stok})`, 'warning'); return;
            }
            if (!tanggal) {
                Swal.fire('Peringatan', 'Tanggal harus diisi', 'warning'); return;
            }

            $.ajax({
                url: `/gudang/${pemakaianGudangId}/pemakaian`,
                type: 'POST',
                data: {
                    qty: qty,
                    tanggal: tanggal,
                    keterangan: $('#pemakaian-keterangan').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function (res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1600, showConfirmButton: false });
                $('#modal-pemakaian').modal('hide');
            })
            .fail(function (xhr) {
                const errors = xhr.responseJSON?.errors;
                const msg = errors ? Object.values(errors).flat().join('\n') : (xhr.responseJSON?.message || 'Terjadi kesalahan');
                Swal.fire('Gagal', msg, 'error');
            });
        });

        $(document).on('click', '#btn-lihat-riwayat', function () {
            const id = $(this).data('id');
            const barang = $(this).data('barang');
            openRiwayatPemakaian(id, barang);
        });

        document.getElementById('modal-riwayat-pemakaian').addEventListener('show.bs.modal', function () {
            setTimeout(function () {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) backdrops[backdrops.length - 1].style.zIndex = '1065';
            }, 0);
        });
        document.getElementById('modal-riwayat-pemakaian').addEventListener('hidden.bs.modal', function () {
            if ($('#modal-pemakaian').hasClass('show')) document.body.classList.add('modal-open');
        });
    });

    function escapeHtml(v) {
        const m = { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' };
        return (v ?? '').toString().replace(/[&<>"']/g, c => m[c]);
    }

    function formatTgl(val) {
        if (!val) return '-';
        const m = String(val).match(/^(\d{4})-(\d{2})-(\d{2})/);
        return m ? `${m[3]}-${m[2]}-${m[1]}` : val;
    }

    function openRiwayatPemakaian(id, barang) {
        $('#riwayat-barang-sub').text(barang || '');
        $('#riwayat-pemakaian-body').html('<tr><td colspan="5" class="text-center text-muted py-2">Memuat data...</td></tr>');
        $('#modal-riwayat-pemakaian').modal('show');

        $.get(`/gudang/${id}/pemakaian`)
            .done(function (rows) {
                if (!rows.length) {
                    $('#riwayat-pemakaian-body').html('<tr><td colspan="5" class="text-center text-muted py-2">Belum ada riwayat pemakaian.</td></tr>');
                    return;
                }
                let html = '';
                rows.forEach(function (row, i) {
                    html += `<tr>
                        <td class="ps-1">${i + 1}</td>
                        <td>${formatTgl(row.tanggal)}</td>
                        <td><span class="badge bg-light-primary text-primary fw-bold">${escapeHtml(row.qty)}</span></td>
                        <td>${escapeHtml(row.keterangan || '-')}</td>
                        <td>${escapeHtml(row.created_by || '-')}</td>
                    </tr>`;
                });
                $('#riwayat-pemakaian-body').html(html);
            })
            .fail(function () {
                $('#riwayat-pemakaian-body').html('<tr><td colspan="5" class="text-center text-danger py-2">Gagal memuat riwayat.</td></tr>');
            });
    }
</script>
@endsection
