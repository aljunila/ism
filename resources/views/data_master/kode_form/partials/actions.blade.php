<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit"
        data-id="{{ $row->id }}"
        data-kode="{{ $row->ket }}"
        data-nama="{{ $row->nama }}"
        data-pj="{{ $row->pj }}"
        data-periode="{{ $row->periode }}"
        data-link="{{ $row->link }}"
        data-kode_file="{{ $row->kode_file }}"
        data-kel="{{ $row->kel }}"
        data-id_perusahaan="{{ $row->id_perusahaan }}"
        data-ket="{{ $row->kode }}"
        data-intruksi="{{ htmlentities($row->intruksi ?? '', ENT_QUOTES, 'UTF-8') }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
