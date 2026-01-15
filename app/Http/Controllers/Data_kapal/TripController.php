<?php

namespace App\Http\Controllers\Data_kapal;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Kapal;
use App\Models\Pelabuhan;
use App\Models\Kendaraan;
use App\Models\BiayaPenumpang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Session;
use Str;
use App\Exports\TripbyIdExport;
use Maatwebsite\Excel\Facades\Excel;

class TripController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_kapal/trip";
        return view('data_kapal.trip.index', $data);
    }

     public function data()
    {
        $query = Trip::where('is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('pelabuhan', function ($row) {
                $pelabuhan = Pelabuhan::find($row->id_pelabuhan);
                return $pelabuhan ? $pelabuhan->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_kapal.trip.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Trip::where('is_delete', 0)->get();
    }

    public function store(Request $request)
    {
        $data = $request->gol;
        $save = Trip::create([
          'uid' => Str::uuid()->toString(),
          'id_kapal' => $request->input('id_kapal'),
          'id_pelabuhan' => $request->input('id_pelabuhan'),
          'tanggal' => $request->input('tanggal'),
          'trip' => $request->input('trip'),
          'jam' => $request->input('jam'),
          'data' => $data,
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s'),
       ]);
        return response()->json(['message' => 'Trip ditambahkan']);
    }

    public function update(Request $request, $id)
    {
         $data = $request->gol;
        $up = Trip::find($id)->update([
          'data' => $data,
          'changed_by' => Session::get('userid'),
        ]);
        return response()->json(['message' => 'Trip diperbarui']);
    }

    public function destroy($id)
    {
        $up = Trip::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Trip dihapus']);
    }

    public function form(Request $request, $uid = null) 
    {
        $id_perusahaan = Session::get('id_perusahaan');
        $roleJenis = Session::get('previllage');
        $data['active'] = "/data_kapal/trip";
        $data['kapal'] = Kapal::where('status','A')
            ->when($roleJenis == 1, function ($q) { return $q; })
            ->when($roleJenis != 1 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                return $q->where('id', $id_perusahaan);
            })
            ->get();
        $data['pelabuhan'] = Pelabuhan::where('is_delete', 0)->get();
        $data['kendaraan'] = Kendaraan::where('is_delete', 0)->get();
        $trip = null;
        if ($uid) {
            $data['trip'] = Trip::where('uid', $uid)->first();
        }

        return view('data_kapal.trip.form', $data);
    }

    public function amount($id) 
    {
        $trip = Trip::where('id', $id)->first();
        $kendaraan = $trip->data; // otomatis array kalau pakai $casts
        $kelas = $trip->get_kapal()->gol;
        $result = [];

        foreach ($kendaraan as $id_kendaraan => $jumlah) {
            $kend = Kendaraan::where('id', $id_kendaraan)->first();
            $biaya = BiayaPenumpang::where('id_pelabuhan', $trip->id_pelabuhan)
                ->where('id_kendaraan', $id_kendaraan)
                ->where('kelas', $kelas)
                ->first();

            $nominal = $biaya ? $biaya->nominal : 0;

            $result[] = [
                'id_kendaraan' => $id_kendaraan,
                'nama'         => $kend->kode,
                'jumlah'       => $jumlah,
                'nominal'      => $nominal,
                'total'        => $jumlah * $nominal
            ];
        }

        return response()->json($result);
    }

    public function TripExcel($id_trip)
    {
        return Excel::download(
            new TripbyIdExport($id_trip),
            'Trip-'.$id_trip.'.xlsx'
        );
    }
}
