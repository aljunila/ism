<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-kriteria"
        data-id="{{ $row->id }}"
        data-id_jabatan="{{ $row->id_jabatan }}"
        data-id_perusahaan="{{ $row->id_perusahaan }}"
        data-kriteria="{{ $row->kriteria }}"
        data-des="{{ $row->des }}"
    >Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-kriteria" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
