<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit"
        data-id="{{ $row->id }}"
        data-kode="{{ $row->kode }}"
        data-nama="{{ $row->nama }}"
        data-ket="{{ $row->ket }}"
        data-intruksi="{{ htmlentities($row->intruksi ?? '', ENT_QUOTES, 'UTF-8') }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
