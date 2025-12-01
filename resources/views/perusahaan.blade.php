 @php
    use App\Models\Perusahaan;
    use App\Models\Kapal;
    use Illuminate\Support\Facades\DB;
@endphp
    @if(Session::get('previllage')==1)
@php
    $perusahaan = Perusahaan::get();
@endphp
        <div class="col-sm-4">
            <select name="id_perusahaan" id="id_perusahaan" class="form-control perusahaan">
                <option value="">Semua</option>
                @foreach($perusahaan as $p)
                    <option value="{{$p->id}}">{{$p->nama}}</option>
                @endforeach
            </select>
        </div>
    @else
@php
$id_perusahaan = Session::get('id_perusahaan');
@endphp
    <input type="hidden" name="id_perusahaan" id="id_perusahaan" value="{{$id_perusahaan}}">
    @endif
