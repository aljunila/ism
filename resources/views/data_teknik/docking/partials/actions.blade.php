<div class="d-flex gap-50">
    <a type="button" href="/data_teknik/docking/biaya/{{$row->uid}}" class="btn btn-sm btn-outline-success">Biaya</a>
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-docking"
        data-id="{{ $row->id }}"
        data-id_kapal="{{ $row->id_kapal }}"
        data-bulan="{{ $row->bulan }}"
        data-tahun="{{ $row->tahun }}"
        data-tempat="{{ $row->tempat }}"
        data-durasi="{{ $row->durasi }}"
        data-tgl_mulai="{{ $row->tgl_mulai }}"
        data-tgl_selesai="{{ $row->tgl_selesai }}">
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-docking" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
