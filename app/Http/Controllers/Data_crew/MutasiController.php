<?php

namespace App\Http\Controllers\Data_crew;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Mutasi;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\KodeForm;
use App\Models\FormISM;
use App\Models\Kapal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;

class MutasiController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_crew/mutasi";
        $data['perusahaan'] = Perusahaan::where('status', 'A')->get();
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['karyawan'] = Karyawan::where('status', 'A')->get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('data_crew.mutasi.index', $data);
    }

    public function data()
    {
        $query = Mutasi::where('status', 'A')->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('jabatan', function ($row) {
                $jabatan = Jabatan::find($row->id_jabatan);
                return $jabatan ? $jabatan->nama : '-';
            })
            ->addColumn('dari_perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->dari_perusahaan);
                return $perusahaan ? $perusahaan->kode : '-';
            })
            ->addColumn('ke_perusahaan', function ($row) {
                $ps = Perusahaan::find($row->ke_perusahaan);
                return $ps ? $ps->kode : '-';
            })
            ->addColumn('dari_kapal', function ($row) {
                $kapal = Kapal::find($row->dari_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('ke_kapal', function ($row) {
                $kp = Kapal::find($row->ke_kapal);
                return $kp ? $kp->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.mutasi.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Mutasi::where('status', 'A')->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
       $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $kapal = Kapal::findorFail($request->input('ke_kapal'));
        $karyawan = Karyawan::findorFail($request->input('id_karyawan'));

        $save = Mutasi::create([
          'uid' => Str::uuid()->toString(),
          'kode' => 'el0610',
          'dari_perusahaan' => $karyawan->id_perusahaan,
          'dari_kapal' => $karyawan->id_kapal,
          'ke_perusahaan' => $request->input('ke_perusahaan'),
          'ke_kapal' => $request->input('ke_kapal'),
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $karyawan->id_jabatan,
          'tgl_naik' => $request->input('tgl_naik'),
          'tgl_turun' => $request->input('tgl_turun'),
          'ket' => $request->input('keterangan'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);

        $save = Karyawan::where('id',$save->id_karyawan)->update([
          'id_perusahaan' => $save->ke_perusahaan,
          'id_kapal' => $save->ke_kapal,
          'changed_by' => Session::get('userid'),
        ]); 
        return response()->json(['message' => 'Mutasi ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $get = Mutasi::find($id);
        $up = Mutasi::find($id)->update([
          'ke_perusahaan' => $request->input('ke_perusahaan'),
          'ke_kapal' => $request->input('ke_kapal'),
          'tgl_naik' => $request->input('tgl_naik'),
          'tgl_turun' => $request->input('tgl_turun'),
          'ket' => $request->input('keterangan'),
          'changed_by' => Session::get('userid'),
        ]);
        
        $save = Karyawan::where('id',$get->id_karyawan)->update([
          'id_perusahaan' => $get->ke_perusahaan,
          'id_kapal' => $get->ke_kapal,
          'changed_by' => Session::get('userid'),
        ]); 
        return response()->json(['message' => 'Mutasi diperbarui']);
    }

    public function destroy($id)
    {
        $up = Mutasi::findOrFail($id);
        $save = Karyawan::where('id',$up->id_karyawan)->update([
          'id_perusahaan' => $up->dari_perusahaan,
          'id_kapal' => $up->dari_kapal,
          'changed_by' => Session::get('userid'),
        ]); 
        $up->update(['status' => 'D']);
        return response()->json(['message' => 'Mutasi dihapus']);
    }

    public function pdf(Request $request) {
        $idform = 45;
        $id_perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal');
        $start = $request->input('start');
        if($request->input('end')) {
            $end = $request->input('end');
        } else {
            $end = date("Y-m-d");
        }

        $perusahaan = Perusahaan::findOrFail($id_perusahaan);
        $show =  Mutasi::where('dari_perusahaan', $id_perusahaan)->where('kode', 'el0610')
                ->where('tgl_naik', '>=', $start)->where('tgl_naik', '<=', $end)->where('status','A')->get();
         $data['form'] = $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', $idform)->first();
        $data['show'] = $show;
        $data['perusahaan'] = $perusahaan;
        $pdf = Pdf::loadView('data_crew.mutasi.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$perusahaan->kode.'.pdf');
    }

    public function elemen($uid)
    {   
        
        $data['active'] = "form_ism";
        $get = FormISM::where('uid', $uid)->first();; 
        $roleJenis = Session::get('previllage');
        $activeCompany = $get->id_perusahaan;
        $activeShip = Session::get('id_kapal');  
        $data['form'] = KodeForm::find($get->id_form);
        $data['id_perusahaan'] = $get->id_perusahaan;
        $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $activeCompany)->get();
        return view('data_crew.mutasi.elemen', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
        $ctx = RoleContext::get();

        // $daftar = DB::table('t_mutasi as a')
        //         ->leftjoin('perusahaan as b', 'a.dari_perusahaan', '=', 'b.id')
        //         ->leftjoin('kapal as c', 'a.dari_kapal', '=', 'c.id')
        //         ->leftjoin('perusahaan as d', 'a.ke_perusahaan', '=', 'd.id')
        //         ->leftjoin('kapal as e', 'a.ke_kapal', '=', 'e.id')
        //         ->leftJoin('karyawan as f', 'f.id', '=', 'a.id_karyawan')
        //         ->leftJoin('jabatan as g', 'g.id', '=', 'a.id_jabatan')
        //         ->select('a.*', 'b.nama as dari_perusahaan', 'c.nama as dari_kapal', 'f.nama as karyawan', 'g.nama as jabatan', 'd.nama as ke_perusahaan', 'e.nama as ke_kapal')
        //         ->where('a.kode', $request->input('kode'))
        //         ->where('a.status','A')
        //         ->when($perusahaan, fn($query, $perusahaan) => $query->where('a.dari_perusahaan', $perusahaan))
        //         ->when($kapal, fn($query, $kapal) => $query->where('a.dari_kapal', $kapal))
        //         ->when($ctx['jenis']==2 && $ctx['perusahaan_id'], fn($q) => $q->where('a.dari_perusahaan', $ctx['perusahaan_id']))
        //         ->when($ctx['jenis']==3 && $ctx['kapal_id'], fn($q) => $q->where('a.dari_kapal', $ctx['kapal_id']))
        //         ->orderBy('a.id', 'DESC')
        //         ->get();
        
        $daftar = Mutasi::where('status', 'A')
        ->where(function ($q) use ($perusahaan) {
            $q->where('dari_perusahaan', $perusahaan)
              ->orWhere('ke_perusahaan', $perusahaan);
        })
        ->orderBy('id', 'DESC');

        return DataTables::of($daftar)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('jabatan', function ($row) {
                $jabatan = Jabatan::find($row->id_jabatan);
                return $jabatan ? $jabatan->nama : '-';
            })
            ->addColumn('dari_perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->dari_perusahaan);
                return $perusahaan ? $perusahaan->kode : '-';
            })
            ->addColumn('ke_perusahaan', function ($row) {
                $ps = Perusahaan::find($row->ke_perusahaan);
                return $ps ? $ps->kode : '-';
            })
            ->addColumn('dari_kapal', function ($row) {
                $kapal = Kapal::find($row->dari_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('ke_kapal', function ($row) {
                $kp = Kapal::find($row->ke_kapal);
                return $kp ? $kp->nama : '-';
            })
            ->make(true);

        return response()->json([
            'data' => $daftar
        ]);
    }
}
