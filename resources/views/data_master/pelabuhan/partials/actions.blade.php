<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-pel"
        data-id="{{ $row->id }}"
        data-id_cabang="{{ $row->id_cabang }}"
        data-nama="{{ $row->nama }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-pel" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
