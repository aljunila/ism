<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-role"
        data-id="{{ $row->id }}"
        data-kode="{{ $row->kode }}"
        data-nama="{{ $row->nama }}"
        data-status="{{ $row->status }}"
        data-superadmin="{{ $row->is_superadmin }}"
        data-jenis="{{ $row->jenis }}"
    >
        Edit
    </button>
    @if(!$row->is_superadmin)
    <button type="button" class="btn btn-sm btn-outline-secondary btn-map-menu" data-id="{{ $row->id }}" data-nama="{{ $row->nama }}">
        Map Menu
    </button>
    @endif
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-role" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
