<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-kel"
        data-id="{{ $row->id }}"
        data-nama="{{ $row->nama }}"
    >Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-kel" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
