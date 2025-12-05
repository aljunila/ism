<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-menu"
        data-id="{{ $row->id }}"
        data-nama="{{ $row->nama }}"
        data-kode="{{ $row->kode }}"
        data-link="{{ $row->link }}"
        data-icon="{{ $row->icon }}"
        data-parent="{{ $row->id_parent }}"
        data-no="{{ $row->no }}"
        data-menu_user="{{ $row->menu_user }}"
        data-status="{{ $row->status }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-menu" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
