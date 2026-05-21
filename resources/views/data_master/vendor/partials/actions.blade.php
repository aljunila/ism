<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-vendor"
        data-id="{{ $row->id }}"
        data-id_cabang="{{ $row->id_cabang }}"
        data-nama="{{ $row->nama }}"
        data-alamat="{{ $row->alamat }}"
        data-telp="{{ $row->telp }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-vendor" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
