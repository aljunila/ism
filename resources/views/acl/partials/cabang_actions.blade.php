<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-cabang"
        data-id="{{ $row->id }}"
        data-cabang="{{ $row->cabang }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-cabang" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
