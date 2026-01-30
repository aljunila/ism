<div class="card-header border-bottom">
    <button class="btn btn-primary btn-sm float-right me-1" data-bs-toggle="modal" data-bs-target="#FormCuti">Tambah Data</button>
</div>
<table class="table table-bordered table-striped" id="table" width="100%">
    <thead>
        <tr align="center">
            <th>No</th>
            <th>Jenis Cuti</th>
            <th>Tanggal Cuti</th>
            <th>Jml Hari</th>
            <th>Pengganti</th>
            <th>Status</th>
            <th>Diproses Oleh</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="modal fade text-start" id="FormCuti" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Pengajuan Cuti</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_cuti" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Jenis Cuti</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="id_m_cuti" id="id_m_cuti"  class="form-control">
                                <option value="">Pilih</option>
                                @foreach($jeniscuti as $c)
                                    <option value="{{$c->id}}">{{$c->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Tgl Cuti</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="tgl_mulai" id="start_date">
                        </div>
                        <div class="col-sm-1">-</div>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="tgl_selesai" id="end_date">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Total Hari</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="jml_hari" id="jml_hari">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Keterangan</label>
                        </div>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="note" id="note"></textarea>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                            <label class="col-form-label" for="first-name">Pengganti</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="id_pengganti" id="id_pengganti" class="form-control">
                                <option value="">Pilih</option>
                                @foreach($karyawan as $k)
                                    <option value="{{$k->id}}">{{$k->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id_karyawan" value="{{$show->id}}">
                    <button type="submit" class="btn btn-primary" id="simpan_cuti">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('end_date').addEventListener('change', hitungHari);
    document.getElementById('start_date').addEventListener('change', hitungHari);

    function hitungHari() {
        const tglMulai   = document.getElementById('start_date').value;
        const tglSelesai = document.getElementById('end_date').value;

        if (!tglMulai || !tglSelesai) {
            document.getElementById('jml_hari').value = '';
            return;
        }

        let start = new Date(tglMulai);
        let end   = new Date(tglSelesai);

        start.setDate(start.getDate());

        if (end < start) {
            document.getElementById('jml_hari').value = 0;
            return;
        }

        let totalHari = 0;

        while (start <= end) {
            if (start.getDay() !== 0) { // 0 = Minggu
                totalHari++;
            }
            start.setDate(start.getDate() + 1);
        }

        document.getElementById('jml_hari').value = totalHari;
    }
</script>
