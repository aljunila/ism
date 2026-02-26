<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-barang"
        data-id="{{ $row->id }}"
        data-id_kel_barang="{{ $row->id_kel_barang }}"
        data-nama="{{ $row->nama }}"
        data-kode="{{ $row->kode }}"
        data-deskripsi="{{ $row->deskripsi }}">
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-barang" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
