<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-kel"
        data-id="{{ $row->id }}"
        data-nama="{{ $row->nama }}"
        data-kategori="{{ $row->kategori }}"
        data-kode="{{ $row->kode }}"
        data-ket="{{ $row->ket }}"
    >Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-kel" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
