<div class="d-flex gap-50">
    <a type="button" href="/penurunan/pdf/{{$row->uid}}" target="_blank" class="btn btn-sm btn-outline-success">Cetak PDF</a>
    <a type="button" href="/penurunan/form/{{$row->uid}}" class="btn btn-sm btn-outline-primary">Edit</a>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-turun" data-id="{{ $row->id }}">
        Hapus
    </button>
</div>