
 <div class="btn-group">
    <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>
    <div class="dropdown-menu">
        <a href="/data_crew/ganti/form/{{$row->uid}}" class="dropdown-item">Form Checklist</a>
        <a type="button" class="dropdown-item btn-edit-ganti" 
            data-id="{{ $row->id }}"
            data-id_karyawan="{{ $row->id_karyawan }}"
            data-id_karyawan2="{{ $row->id_karyawan2 }}"
            data-kode="{{ $row->kode }}"
            data-date="{{ $row->date }}">Edit</a>
        <a type="button"data-id="{{ $row->id }}" class="dropdown-item btn-delete-ganti">Hapus</a>
    </div>
</div>
