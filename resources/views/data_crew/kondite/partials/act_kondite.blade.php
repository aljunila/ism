<div class="d-flex gap-50">
    <a type="button" class="btn btn-sm btn-outline-success btn-edit-kondite" 
            data-id="{{ $row->id }}"
            data-karyawan="{{ $row->karyawan }}"
            data-jabatan="{{ $row->jabatan }}"
            data-data="{{ $row->data }}"
            data-rekomendasi="{{ $row->rekomendasi }}"
            data-note="{{ $row->note }}"
            data-id_penilai_1="{{ $row->id_penilai_1 }}"
            data-id_penilai_2="{{ $row->id_penilai_2 }}"
            data-id_mengetahui="{{ $row->id_mengetahui }}"
            >Nilai</a>
    @if($row->data)
    <a href="/data_crew/kondite/pdf/{{$row->uid}}" type="button" class="btn btn-sm btn-outline-warning" target="_blank" title="Cetak PDF">
    Cetak PDF</a>
    @endif
</div>