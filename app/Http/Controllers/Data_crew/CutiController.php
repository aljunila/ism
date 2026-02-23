<?php

namespace App\Http\Controllers\Data_crew;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\JenisCuti;
use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Kapal;
use App\Models\User;
use App\Models\ChecklistData;
use App\Models\KodeForm;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;

class CutiController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_crew/cuti";
        $roleJenis = Session::get('previllage');
            $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $$data['kapal'] = Kapal::find(Session::get('id_kapal'));
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        $data['jeniscuti'] = JenisCuti::where('is_delete', 0)->get();
        return view('data_crew.cuti.index', $data);
    }

    public function data()
    {
        $query = Cuti::where('is_delete', 0)->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('karyawan', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('approval', function ($row) {
                $approval = User::find($row->approved_by);
                return $approval ? $approval->nama : '-';
            })
            ->addColumn('pengganti', function ($row) {
                $pengganti = Karyawan::find($row->id_pengganti);
                return $pengganti ? $pengganti->nama : '-';
            })
            ->addColumn('jenis', function ($row) {
                $jenis = JenisCuti::find($row->id_m_cuti);
                return $jenis ? $jenis->nama : '-';
            })
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : 'aa';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.cuti.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function store(Request $request)
    {   
        if($request->input('id_karyawan')){          
            $get_karyawan = Karyawan::find($request->post('id_karyawan'));
            $jabatan =  $get_karyawan->id_jabatan;
            $perusahaan = $get_karyawan->id_perusahaan;
            $karyawan = $get_karyawan->id;
            $kapal = null;
            $data = null;
        } else {
            $get_kapal = Kapal::find($request->post('id_kapal'));
            $perusahaan = $get_kapal->pemilik;
            $kapal = $request->post('id_kapal');
            $data = $request->crew;
            $karyawan = null;
            $jabatan = null;
        }

        $save = Cuti::create([
          'uid' => Str::uuid()->toString(),
          'id_perusahaan' => $perusahaan,
          'id_karyawan' => $karyawan,
          'id_jabatan' => $jabatan, 
          'id_kapal' => $kapal,
          'data' => $data,
          'id_m_cuti' => $request->input('id_m_cuti'),
          'note' => $request->input('note'),
          'tgl_mulai' => $request->input('tgl_mulai'),
          'tgl_selesai' => $request->input('tgl_selesai'),
          'jml_hari' => $request->input('jml_hari'),
          'id_pengganti' => $request->input('id_pengganti'),
          'status' => 1,
          'is_delete' => 0,
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s')
        ]);

        return response()->json(['success' => true]);
    }

    public function databyId(Request $request)
    {
        $id = $request->input('id');
        $query = Cuti::where('is_delete', 0)->where('id_karyawan', $id)->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('karyawan', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('approval', function ($row) {
                $approval = User::find($row->approved_by);
                return $approval ? $approval->nama : '-';
            })
            ->addColumn('pengganti', function ($row) {
                $pengganti = Karyawan::find($row->id_pengganti);
                return $pengganti ? $pengganti->nama : '-';
            })
            ->addColumn('jenis', function ($row) {
                $jenis = JenisCuti::find($row->id_m_cuti);
                return $jenis ? $jenis->nama : '-';
            })
            ->make(true);
    }

    public function update(Request $request, $id)
    {
        $get = Karyawan::find($request->input('id_karyawan'));
        $total = Cuti::where('id_karyawan', $request->input('id_karyawan'))
                ->where('id_m_cuti', $request->input('id_m_cuti'))
                ->where('is_delete', 0)->where('status',2)
                ->sum('jml_hari');
        $data = JenisCuti::find($request->input('id_m_cuti'));
        $sisa_cuti = $data->jumlah - $total;

        if($sisa_cuti>=$request->input('jml_hari')) {
            $up = Cuti::find($id)->update([
                'tgl_mulai' => $request->input('tgl_mulai'),
                'tgl_selesai' => $request->input('tgl_selesai'),
                'jml_hari' => $request->input('jml_hari'),
                'id_pengganti' => $request->input('id_pengganti'),
                'status' => 2,
                'changed_by' => Session::get('userid'),
                'approved_by' => Session::get('userid'), 
                'approved_date' => date("Y-m-d")
            ]);

            $id_jabatan = $get->id_jabatan;
            $kel = match (true) {
                $id_jabatan == 5 => 'Nahkoda',
                $id_jabatan == 6 => 'KKM',
                $id_jabatan >= 12 && $id <= 15 => 'Mualim',
                default => '',
            };
            $form = KodeForm::where('kel', $kel)->where('id_menu', 58)->get();
            foreach($form as $f) {
                $save = ChecklistData::create([
                    'uid' => Str::uuid()->toString(),
                    'id_form' => $f->id,
                    'id_karyawan' => $get->id,
                    'id_jabatan' => $get->id_jabatan,
                    'id_perusahaan' => $get->id_perusahaan,
                    'id_kapal' => $get->id_kapal,
                    'id_karyawan2' => $request->input('id_pengganti'),
                    'date' => $request->input('tgl_mulai'),
                    'status' => 'A',
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d')
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Cuti berhasil disetujui'],200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Maaf, jumlah cuti melebihi sisa cuti tahun ini'],422); 
        }
       
        
    }

    public function destroy($id)
    {
        $date = date("Y-m-d");
        $up = Cuti::findOrFail($id);

        if($up->tgl_mulai>$date) {
            $up->update(['is_delete' => 1]);
            return response()->json(['status' => 'success', 'message' => 'Cuti berhasil dibatalkan'],200);
        } else {
           return response()->json(['status' => 'error', 'message' => 'Maaf, cuti tidak bisa dibatalkan'],422);  
        }
    }

    public function reject($id)
    {
        $up = Cuti::find($id)->update([
            'status' => 3, 
            'approved_by' => Session::get('userid'), 
            'approved_date' => date("Y-m-d")
        ]);
        return response()->json(['message' => 'Data direject']);
    }

    public function form()
    {
        $data['active'] = "/data_crew/cuti";
        $roleJenis = Session::get('previllage');
            $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $$data['kapal'] = Kapal::find(Session::get('id_kapal'));
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        $data['jeniscuti'] = JenisCuti::where('is_delete', 0)->get();
        return view('data_crew.cuti.form', $data);
    }

     public function get($id) 
    {
        $cuti = Cuti::where('id', $id)->first();
        $data = $cuti->data; 
        $result = [];

        foreach ($data as $row) {
            $karyawan = Karyawan::where('id', $row)->first();
            $jabatan = Jabatan::where('id', $karyawan->id_jabatan)->first();

            $result[] = [
                'nama'         => $karyawan->nama,
                'jabatan'      => $jabatan->nama
            ];
        }

        return response()->json($result);
    }
}
