<div class="d-flex gap-50">
    <a type="button" href="/permintaan/pdf/{{$row->uid}}" target="_blank" class="btn btn-sm btn-outline-success">Cetak PDF</a>
    <a type="button" href="/permintaan/form/{{$row->uid}}" class="btn btn-sm btn-outline-primary">Edit</a>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-permintaan" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>
