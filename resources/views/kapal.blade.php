
<div class="col-sm-3">
    <select name="id_kapal" id="id_kapal" class="form-control kapal">
        <option value="">Pilih Kapal</option>
        @foreach($kapal as $k)
            <option value="{{$k->id}}">{{$k->nama}}</option>
        @endforeach
    </select>
</div>
