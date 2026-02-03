
<div class="btn-group">
    <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>
    <div class="dropdown-menu">
        <a href="/data_crew/evaluasi/form/{{$row->uid}}" class="dropdown-item">Form Checklist</a>
        <a type="button" class="dropdown-item btn-edit-evaluasi" 
            data-id="{{ $row->id }}"
            data-id_karyawan="{{ $row->id_karyawan }}"
            data-id_form="{{ $row->id_form }}"
            data-date="{{ $row->date }}">Edit</a>
        <a type="button"data-id="{{ $row->id }}" class="dropdown-item btn-delete-evaluasi">Hapus</a>
    </div>
</div>
