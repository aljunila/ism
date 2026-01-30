
 <div class="btn-group">
    <button class="btn btn-flat-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>
    <div class="dropdown-menu">
        <a type="button" class="dropdown-item btn-edit-mutasi" 
            data-id="{{ $row->id }}"
            data-id_karyawan="{{ $row->id_karyawan }}"
            data-ke_perusahaan="{{ $row->ke_perusahaan }}"
            data-ke_kapal="{{ $row->ke_kapal }}"
            data-tgl_naik="{{ $row->tgl_naik }}"
            data-tgl_turun="{{ $row->tgl_turun }}"
            data-keterangan="{{ $row->ket }}"
            >Edit</a>
        <a type="button"data-id="{{ $row->id }}" class="dropdown-item btn-delete-mutasi">Hapus</a>
    </div>
</div>
