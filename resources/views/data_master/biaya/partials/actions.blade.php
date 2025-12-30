<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-biaya"
        data-id="{{ $row->id }}"
        data-id_pelabuhan="{{ $row->id_pelabuhan }}"
        data-id_kendaraan="{{ $row->id_kendaraan }}"
        data-kelas="{{ $row->kelas }}"
        data-nominal="{{ $row->nominal }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-biaya" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
