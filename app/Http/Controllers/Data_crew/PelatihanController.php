<?php

namespace App\Http\Controllers\Data_crew;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
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

class PelatihanController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_crew/pelatihan";
        $data['perusahaan'] = Perusahaan::where('status', 'A')->get();
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['karyawan'] = Karyawan::where('status', 'A')->get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('data_crew.pelatihan.index', $data);
    }

    public function data()
    {
        $query = Pelatihan::where('status', 'A')->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('karyawan', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('jabatan', function ($row) {
                $jabatan = Jabatan::find($row->id_jabatan);
                return $jabatan ? $jabatan->nama : '-';
            })
            ->addColumn('perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->id_perusahaan);
                return $perusahaan ? $perusahaan->kode : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.pelatihan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Pelatihan::where('status', 'A')->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $karyawan = Karyawan::findorFail($request->input('id_karyawan'));
        if($karyawan->get_jabatan()->kel==1) { $kode = 38; } else { $kode= 37; }

        $save = Pelatihan::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $kode,
          'id_perusahaan' => $karyawan->id_perusahaan,
          'id_kapal' => $karyawan->id_kapal,
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $karyawan->id_jabatan,
          'nama' => $request->input('nama'),
          'tgl_mulai' => $request->input('tgl_mulai'),
          'tgl_selesai' => $request->input('tgl_selesai'),
          'tempat' => $request->input('tempat'),
          'hasil' => $request->input('hasil'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
        ]);
        return response()->json(['message' => 'Pelatihan ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $up = Pelatihan::find($id)->update([
          'nama' => $request->input('nama'),
          'tgl_mulai' => $request->input('tgl_mulai'),
          'tgl_selesai' => $request->input('tgl_selesai'),
          'tempat' => $request->input('tempat'),
          'hasil' => $request->input('hasil'),
          'changed_by' => Session::get('userid'),
        ]);
        
        return response()->json(['message' => 'Pelatihan diperbarui']);
    }

    public function destroy($id)
    {
        $up = Pelatihan::findOrFail($id);
        $up->update(['status' => 'D']);
        return response()->json(['message' => 'Pelatihan dihapus']);
    }

    public function pdf(Request $request) {
        $id_perusahaan = $request->input('id_perusahaan');
        $idform = $request->input('idform');
        $start = $request->input('start');
        if($request->input('end')) {
            $end = $request->input('end');
        } else {
            $end = date("Y-m-d");
        }

        $perusahaan = Perusahaan::findOrFail($id_perusahaan);
        $show =  Pelatihan::where('id_perusahaan', $id_perusahaan)->where('kode', $idform)
                ->where('tgl_mulai', '>=', $start)->where('tgl_selesai', '<=', $end)->where('status','A')->get();
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
        $pdf = Pdf::loadView('data_crew.pelatihan.pdf', $data)
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
        return view('data_crew.pelatihan.elemen', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $id_form = $request->input('id_form');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
        $ctx = RoleContext::get();
        
        $daftar = Pelatihan::where('status', 'A')
        ->where('id_perusahaan', $perusahaan)
        ->where('kode', $id_form)
        ->when($kapal, function($query, $kapal) {
                    return $query->where('id_kapal', $kapal);
                })
        ->orderBy('id', 'DESC');

        return DataTables::of($daftar)
            ->addIndexColumn()
            ->addColumn('karyawan', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('jabatan', function ($row) {
                $jabatan = Jabatan::find($row->id_jabatan);
                return $jabatan ? $jabatan->nama : '-';
            })
            ->make(true);

        return response()->json([
            'data' => $daftar
        ]);
    }
}
