<div class="d-flex gap-50">
    @if ($row->status== 1)
    <button type="button" class="btn btn-sm btn-outline-success btn-edit-cuti" 
        data-id="{{ $row->id }}"
        data-id_karyawan="{{ $row->id_karyawan }}"
        data-id_kapal="{{ $row->id_kapal }}"
        data-id_m_cuti="{{ $row->id_m_cuti }}"
        data-tgl_mulai="{{ $row->tgl_mulai }}"
        data-tgl_selesai="{{ $row->tgl_selesai }}"
        data-jml_hari="{{ $row->jml_hari }}"
        data-note="{{ $row->note }}"
        data-id_pengganti="{{ $row->id_pengganti }}"
        >Setujui</button>
    <button type="button" data-id="{{ $row->id }}" class="btn btn-sm btn-outline-warning btn-reject-cuti">Tolak</button>
    @endif
    <button type="button" data-id="{{ $row->id }}" class="btn btn-sm btn-outline-danger btn-delete-cuti">Hapus</button>
</div>
