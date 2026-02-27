<table class="table table-bordered table-striped" id="{{ $tableId }}" width="100%">
    <thead>
        <tr align="center">
            <th>No</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Tanggal Permintaan</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="modal fade text-start" id="prosesModal" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Proses Permintaan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_proses" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">                    
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Ketersediaan</label>
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
                            <label class="col-form-label" for="first-name">Pembelian</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="status" id="status" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="mb-1 row" id="zahir">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Masukkan kode P.O.</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="kode_po" id="kode_po">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Tgl</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" name="tanggal" id="tanggal">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Upload File</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="file" name="img" id="img" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="current_status">
                    <input type="hidden" name="id" id="proses_id">
                    <button type="submit" class="btn btn-primary" id="simpan_cuti">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
</script>
