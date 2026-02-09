<div class="d-flex gap-50">
    <a href="/data_crew/kondite/form/{{$row->uid}}" class="btn btn-sm btn-outline-success">Detail</a>
    <a type="button" class="btn btn-sm btn-outline-warning btn-edit-kondite" 
            data-id="{{ $row->id }}"
            data-id_kapal="{{ $row->id_kapal }}"
            data-bulan="{{ $row->bulan }}"
            data-tahun="{{ $row->tahun }}">Edit</a>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-kondite" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>