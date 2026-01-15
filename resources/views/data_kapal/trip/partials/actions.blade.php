<div class="d-flex gap-50">
    <button type="button"
        class="btn btn-sm btn-outline-success"
        onclick="openKendaraanModal({{ $row->id }})">
    Pendapatan
    </button>
    <a type="button" href="/data_kapal/trip/form/{{$row->uid}}" class="btn btn-sm btn-outline-primary">Edit</a>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-pel" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
