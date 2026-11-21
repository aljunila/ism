<div class="d-flex gap-50">
    @if($row->status==1)
     <button type="button" class="btn btn-sm btn-outline-primary btn-proses-bbm" data-id="{{ $row->id }}" data-jumlah="{{ $row->jumlah }}">
        Proses
    </button>
    <button type="button" class="btn btn-sm btn-outline-success btn-edit-bbm"
        data-id="{{ $row->id }}"
        data-id_kapal="{{ $row->id_kapal }}"
        data-tanggal="{{ $row->tanggal }}"
        data-jam="{{ $row->jam }}"
        data-jumlah="{{ $row->jumlah }}"
        data-lintasan="{{ $row->lintasan }}"
        data-jarak="{{ $row->jarak }}">
        Edit
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-bbm" data-id="{{ $row->id }}">
        Hapus
    </button>
    @else
    <a type="button" href="/permintaan/pdfkirim/{{$row->uid_kirim}}" target="_blank" class="btn btn-sm btn-outline-success">PDF Pemenuhan</a>
    @endif
    
</div>
