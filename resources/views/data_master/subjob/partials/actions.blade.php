<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-subjob"
        data-id="{{ $row->id }}"
        data-nama="{{ $row->nama }}"
        data-id_job="{{ $row->id_job }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-subjob" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
