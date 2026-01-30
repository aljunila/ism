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
        $id_perusahaan = Session::get('id_perusahaan');
        $id_kapal = Session::get('id_kapal');
        $roleJenis = Session::get('previllage');

        $query = DB::table('t_trip as a')
                ->leftjoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->select('a.*')
                ->where('a.is_delete', 0)
                ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
                ->when($roleJenis == 2 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                    return $q->where('b.pemilik', $id_perusahaan);
                })
                ->when($roleJenis == 3 && $id_kapal, function ($q) use ($id_kapal) {
                    return $q->where('a.id_kapal', $id_kapal);
                });

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
        //$data = $request->gol;
        $id_pelabuhan = $request->input('id_pelabuhan');
        $kapal = Kapal::find($request->input('id_kapal'));
        $data = [];
        foreach ($request->gol as $idgol => $value) {
            $biaya = BiayaPenumpang::where('id_pelabuhan', $id_pelabuhan)
                ->where('id_kendaraan', $idgol)
                ->where('kelas', $kapal->gol)
                ->first();

            $nominal = $biaya ? $biaya->nominal : 0;

            $data[$idgol] = [
                'jumlah'       => (int) $value,
                'nominal'      => $nominal,
                'total'        => $value * $nominal
            ];
        }

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
        //$data = $request->gol;
        $up = Trip::find($id);
        $kapal = Kapal::find($up->id_kapal);
        $data = [];
        foreach ($request->gol as $idgol => $value) {
            $biaya = BiayaPenumpang::where('id_pelabuhan', $trip->id_pelabuhan)
                ->where('id_kendaraan', $idgol)
                ->where('kelas', $kapal->gol)
                ->first();

            $nominal = $biaya ? $biaya->nominal : 0;

            $data[$idgol] = [
                'jumlah'       => (int) $value,
                'nominal'      => $nominal,
                'total'        => $value * $nominal
            ];
        }
        $up->update([
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
        $id_kapal = Session::get('id_kapal');
        $roleJenis = Session::get('previllage');
        $data['active'] = "/data_kapal/trip";
        $data['kapal'] = Kapal::where('status','A')
            ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
            ->when($roleJenis == 2 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                return $q->where('pemilik', $id_perusahaan);
            })
            ->when($roleJenis == 3 && $id_kapal, function ($q) use ($id_kapal) {
                return $q->where('id', $id_kapal);
            })
            ->get();
        $data['pelabuhan'] = Pelabuhan::where('is_delete', 0)->get();
        $data['kendaraan'] = Kendaraan::where('is_delete', 0)->get();
        $trip = null;
        if ($uid) {
            $trip = Trip::where('uid', $uid)->first();
            $data['trip'] = $trip;
            $data['gol'] = $trip->data;
        }

        return view('data_kapal.trip.form', $data);
    }

    public function amount($id) 
    {
        $trip = Trip::where('id', $id)->first();
        $kendaraan = $trip->data; // otomatis array kalau pakai $casts
        $kelas = $trip->get_kapal()->gol;
        $result = [];

        foreach ($kendaraan as $id => $row) {
            $kend = Kendaraan::where('id', $id)->first();

            $result[] = [
                'id_kendaraan' => $id,
                'nama'         => $kend->kode,
                'jumlah'       => $row['jumlah'],
                'nominal'      => $row['nominal'],
                'total'        => $row['total']
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
