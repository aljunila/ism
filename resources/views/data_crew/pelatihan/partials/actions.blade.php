
 <div class="btn-group">
    <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>
    <div class="dropdown-menu">
        <a type="button" class="dropdown-item btn-edit-pelatihan" 
            data-id="{{ $row->id }}"
            data-id_karyawan="{{ $row->id_karyawan }}"
            data-nama="{{ $row->nama }}"
            data-tempat="{{ $row->tempat }}"
            data-tgl_mulai="{{ $row->tgl_mulai }}"
            data-tgl_selesai="{{ $row->tgl_selesai }}"
            data-hasil="{{ $row->hasil }}"
            >Edit</a>
        <a type="button"data-id="{{ $row->id }}" class="dropdown-item btn-delete-pelatihan">Hapus</a>
    </div>
</div>
