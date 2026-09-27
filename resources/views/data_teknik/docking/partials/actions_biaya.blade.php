<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-biaya"
        data-id="{{ $row->id }}"
        data-job="{{ $row->id_job }}"
        data-subjob="{{ $row->id_subjob }}"
        data-deskripsi="{{ $row->deskripsi }}"
        data-unit="{{ $row->unit }}"
        data-volume="{{ $row->volume }}"
        data-harga="{{ $row->harga }}"
        data-total="{{ $row->total }}"
        data-keterangan="{{ $row->keterangan }}">
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-biaya" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
