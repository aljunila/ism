<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-kend"
        data-id="{{ $row->id }}"
        data-kode="{{ $row->kode }}"
        data-nama="{{ $row->nama }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-kend" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
