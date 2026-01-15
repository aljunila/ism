<div class="d-flex gap-50">
    
    <a type="button" href="/data_crew/recruitment/form/{{$row->uid}}" class="btn btn-sm btn-outline-success">Interview</a>
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-recruitment"
        data-id="{{ $row->id }}"
        data-id_perusahaan="{{ $row->id_perusahaan }}"
        data-id_jabatan="{{ $row->id_jabatan }}"
        data-nama="{{ $row->nama }}"
        data-alamat="{{ $row->alamat }}"
        data-telp="{{ $row->telp }}"
    >
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-recruitment" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
