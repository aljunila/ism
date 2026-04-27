
<div class="btn-group">
    <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>
    <div class="dropdown-menu">
        <a type="button" class="dropdown-item btn-edit-latihan" 
            data-id="{{ $row->id }}"
            data-id_kapal="{{ $row->id_kapal }}"
            data-id_form="{{ $row->id_form }}"
            data-date="{{ $row->date }}">Edit</a>
        <a type="button"data-id="{{ $row->id }}" class="dropdown-item btn-delete-latihan">Hapus</a>
    </div>
</div>
