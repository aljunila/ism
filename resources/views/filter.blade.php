 @php
    use App\Models\Perusahaan;
    use App\Models\Kapal;
    use Illuminate\Support\Facades\DB;
@endphp
    @if(Session::get('previllage')==1)
@php
    $perusahaan = Perusahaan::get();
    $kapal = Kapal::where('status', 'A')->get();
@endphp
        <div class="col-sm-4">
            <select name="id_perusahaan" id="id_perusahaan" class="form-control">
                <option value="">Pilih Perusahaan</option>
                @foreach($perusahaan as $p)
                    <option value="{{$p->id}}">{{$p->nama}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <select name="id_kapal" id="id_kapal" class="form-control">
                <option value="">Pilih Kapal</option>
                @foreach($kapal as $k)
                    <option value="{{$k->id}}">{{$k->nama}}</option>
                @endforeach
            </select>
        </div>
    @elseif(Session::get('previllage')==2)
@php
$id_perusahaan = Session::get('id_perusahaan');
$kapal = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
@endphp
        <div class="col-sm-4">
            <input type="hidden" name="id_perusahaan" id="id_perusahaan" value="{{$id_perusahaan}}">
            <select name="id_kapal" id="id_kapal" class="form-control">
                <option value="">Pilih Kapal</option>
                @foreach($kapal as $k)
                    <option value="{{$k->id}}">{{$k->nama}}</option>
                @endforeach
            </select>
        </div>
    @else
@php
$id_kapal = Session::get('id_kapal');
@endphp
    <input type="hidden" name="id_kapal" id="id_kapal" value="{{$id_kapal}}">
    @endif

<script>
    
</script>