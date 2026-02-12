<?php

namespace App\Http\Controllers\Data_crew;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\ChecklistItem;
use App\Models\KodeForm;
use App\Models\FormISM;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;

class RecruitmentController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_crew/recruitment";
        $data['perusahaan'] = Perusahaan::where('status', 'A')->get();
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        return view('data_crew.recruitment.index', $data);
    }

    public function data()
    {
        $id_perusahaan = Session::get('id_perusahaan');
        $id_kapal = Session::get('id_kapal');
        $roleJenis = Session::get('previllage');

        $query = Recruitment::where('is_delete', 0)
                 ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
                ->when($roleJenis == 2 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                    return $q->where('id_perusahaan', $id_perusahaan);
                })
                ->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('jabatan', function ($row) {
                $jabatan = Jabatan::find($row->id_jabatan);
                return $jabatan ? $jabatan->nama : '-';
            })
            ->addColumn('perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->id_perusahaan);
                return $perusahaan ? $perusahaan->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.recruitment.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return Recruitment::where('is_delete', 0)->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        // $data = $request->ck;
        $save = Recruitment::create([
          'uid' => Str::uuid()->toString(),
          'kode' => 'el0607',
          'id_perusahaan' => $request->input('id_perusahaan'),
          'nama' => $request->input('nama'),
          'alamat' => $request->input('alamat'),
          'telp' => $request->input('telp'),
          'id_jabatan' => $request->input('id_jabatan'),
          'status' => 'N',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d')
       ]);
        return response()->json(['message' => 'Recruitment ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $up = Recruitment::find($id)->update([
          'id_perusahaan' => $request->input('id_perusahaan'),
          'nama' => $request->input('nama'),
          'alamat' => $request->input('alamat'),
          'telp' => $request->input('telp'),
          'id_jabatan' => $request->input('id_jabatan'),
          'changed_by' => Session::get('userid'),
        ]);
        return response()->json(['message' => 'Recruitment diperbarui']);
    }

    public function destroy($id)
    {
        $up = Recruitment::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Recruitment dihapus']);
    }

    public function form(Request $request, $uid) 
    {
        $data['active'] = "/data_crew/recruitment";
        $kode = 'el0607';
        $show = Recruitment::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['karyawan'] = Karyawan::where('id_perusahaan', $show->id_perusahaan)->where('status','A')->where('resign', 'N')->get();
        $data['item'] = ChecklistItem::where('kode', $kode)->where('status', 'A')->where('parent_id',0)->get();
        $data['dataItem'] = $show->data;
        return view('data_crew.recruitment.form', $data);
    }

    public function savedata(Request $request, $id)
    {
        $data = [];
        foreach ($request->item as $iditem => $value) {
            $data[$iditem] = [
                'value' => (int) $value,
                'ket'   => $request->ket[$iditem] ?? null,
            ];
        }

        $up = Recruitment::find($id)->update([
          'data' => $data,
          'note' => $request->input('note'),
          'id_periksa' => $request->input('id_periksa'),
          'tgl_periksa' => $request->input('tgl_periksa'),
          'id_menyetujui' => $request->input('id_menyetujui'),
          'status' => $request->input('status'),
          'changed_by' => Session::get('userid')
        ]);
        if($request->input('status')=='A') {
            $get = Recruitment::findorFail($id);
            $save = Karyawan::create([
                    'uid' => Str::uuid()->toString(),
                    'nama' => $get->nama,
                    'alamat' => $get->alamat,
                    'telp' => $get->telp,
                    'id_perusahaan' => $get->id_perusahaan,
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d')
                ]);

        }

        return response()->json(['message' => 'Recruitment diperbarui']);
    }

    public function pdf($uid) {
        $show =  Recruitment::where('uid', $uid)->first();
        $nama = $show->nama;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['show'] = $show;
        $data['item'] = ChecklistItem::where('kode', $show->kode)->where('status', 'A')->where('parent_id',0)->get();   
        $data['dataItem'] = $show->data;
        $pdf = Pdf::loadView('data_crew.recruitment.pdf', $data)
                ->setPaper('a3', 'portrait');
        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function elemen($uid)
    {   
        
        $data['active'] = "/data_master/kode_form";
        $get = FormISM::where('uid', $uid)->first();; 
        $roleJenis = Session::get('previllage');
        $activeCompany = $get->id_perusahaan;
        $activeShip = Session::get('id_kapal');  
        $data['form'] = KodeForm::find($get->id_form);
        $data['id_perusahaan'] = $get->id_perusahaan;
        return view('data_crew.recruitment.elemen', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        
        $daftar = DB::table('t_recruitment as a')
                ->leftjoin('jabatan as b', 'b.id', '=', 'a.id_jabatan')
                ->select('a.*', 'b.nama as jabatan')
                ->where('a.is_delete',0)
                ->when($perusahaan, fn($query, $perusahaan) => $query->where('a.id_perusahaan', $perusahaan))
                ->orderBy('a.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }
}
