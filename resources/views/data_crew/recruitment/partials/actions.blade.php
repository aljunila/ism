
<div class="btn-group">
    <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>
    <div class="dropdown-menu">
        <a href="/data_crew/recruitment/form/{{$row->uid}}" class="dropdown-item">Interview</a>
        <a type="button" class="dropdown-item btn-edit-recruitment" 
           data-id="{{ $row->id }}"
        data-id_perusahaan="{{ $row->id_perusahaan }}"
        data-id_jabatan="{{ $row->id_jabatan }}"
        data-nama="{{ $row->nama }}"
        data-alamat="{{ $row->alamat }}"
        data-telp="{{ $row->telp }}">Edit</a>
        <a type="button"data-id="{{ $row->id }}" class="dropdown-item btn-delete-recruitment">Hapus</a>
    </div>
</div>