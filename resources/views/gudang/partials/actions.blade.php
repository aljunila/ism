<div class="d-flex gap-50">
    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-gudang"
        data-id="{{ $row->id }}"
        data-barang="{{ $row->barang }} ({{$row->kode}})"
        data-jumlah="{{ $row->jumlah }}"
        data-baik="{{ $row->baik }}"
        data-habis="{{ $row->habis }}"
        data-keterangan="{{ $row->keterangan }}"
    >Perbarui kondisi
    </button>
</div>
