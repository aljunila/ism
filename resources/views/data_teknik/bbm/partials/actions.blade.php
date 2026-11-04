<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-bbm"
        data-id="{{ $row->id }}"
        data-id_kapal="{{ $row->id_kapal }}"
        data-tanggal="{{ $row->tanggal }}"
        data-jumlah="{{ $row->jumlah }}"
        data-status="{{ $row->status }}"
        data-id_jenis="{{ $row->id_jenis }}"
        data-me="{{ $row->me }}"
        data-ae="{{ $row->ae }}"
        data-jam_me="{{ $row->jam_me }}"
        data-jam_ae="{{ $row->jam_ae }}"
        data-tgl_mulai="{{ $row->tgl_mulai }}">
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-bbm" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
