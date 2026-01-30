<?php

namespace App\Http\Controllers\Data_crew;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\ChecklistData;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\ChecklistItem;
use App\Models\KodeForm;
use App\Models\FormISM;
use App\Models\Kapal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;

class FamiliarisasiController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_crew/familiarisasi";
        $data['perusahaan'] = Perusahaan::where('status', 'A')->get();     
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $data['form'] = KodeForm::where('id_menu', 57)->get();
        return view('data_crew.familiarisasi.index', $data);
    }

    public function data()
    {
        $id_perusahaan = Session::get('id_perusahaan');
        $id_kapal = Session::get('id_kapal');
        $roleJenis = Session::get('previllage');
        
        $query = DB::table('checklist_data as a')
                ->leftjoin('kode_form as b', 'a.id_form', '=', 'b.id') 
                ->leftjoin('karyawan as c', 'a.id_karyawan', '=', 'c.id')
                ->select('a.*')       
                ->where('a.status', 'A')->where('b.id_menu', 57)
                ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
                ->when($roleJenis == 2 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                    return $q->where('a.id_perusahaan', $id_perusahaan);
                })
                ->when($roleJenis == 3 && $id_kapal, function ($q) use ($id_kapal) {
                    return $q->where('c.id_kapal', $id_kapal);
                })
                ->orderBy('a.id', 'DESC');

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
            ->addColumn('kode', function ($row) {
                $kode = KodeForm::find($row->id_form);
                return $kode ? $kode->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.familiarisasi.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function all()
    {
        return ChecklistData::where('status', 'A')->get(['id', 'nama', 'kode']);
    }

    public function store(Request $request)
    {
        $karyawan = Karyawan::find($request->input('id_karyawan'));
        $form = KodeForm::where('kode', $request->input('id_form'))->where('id_perusahaan', $karyawan->id_perusahaan)->first();
        // $data = $request->ck;
        $save = ChecklistData::create([
          'uid' => Str::uuid()->toString(),
          'id_form' => $request->input('id_form'),
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $karyawan->id_jabatan,
          'id_perusahaan' => $karyawan->id_perusahaan,
          'id_kapal' => $karyawan->id_kapal,
          'date' => $request->input('date'),
          'status' => 'A',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d')
       ]);
        return response()->json(['message' => 'Data ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::find($request->input('id_karyawan'));
        $up = ChecklistData::find($id)->update([
          'id_form' => $request->input('id_form'),
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $karyawan->id_jabatan,
          'id_perusahaan' => $karyawan->id_perusahaan,
          'id_kapal' => $karyawan->id_kapal,
          'date' => $request->input('date'),
          'changed_by' => Session::get('userid'),
        ]);
        return response()->json(['message' => 'Data diperbarui']);
    }

    public function destroy($id)
    {
        $up = ChecklistData::findOrFail($id);
        $up->update(['status' => 'D']);
        return response()->json(['message' => 'Data dihapus']);
    }

    public function form(Request $request, $uid = null) 
    {
        $data['active'] = "/data_crew/familiarisasi";
        $show = ChecklistData::where('uid', $uid)->first();
        $form = KodeForm::find($show->id_form);
        $data['show'] = $show;
        $data['form'] = $form;
        $data['item'] = ChecklistItem::where('kode', $form->kode)->where('status', 'A')->where('parent_id',0)->get();
        $data['karyawan'] = Karyawan::where('id_perusahaan', $show->id_perusahaan)->where('status','A')->where('resign', 'N')->get();
        $data['dataItem'] = $show->data;
        $data['pj'] = $show->pj;
        return view('data_crew.familiarisasi.form', $data);
    }

    public function savedata(Request $request, $id)
    {
        $show = ChecklistData::find($id);
        $data = [];
        foreach ($request->item as $iditem => $value) {
            $data[$iditem] = [
                'value' => (int) $value,
            ];
        }
        if($request->input('kode')=='el0302') { 
            $mengetahui = Karyawan::where('id_perusahaan', $show->id_perusahaan)->where('status','A')->where('resign', 'N')->where('id_jabatan',4)->first();
        } else {
            $mengetahui = Karyawan::where('id_perusahaan', $show->id_perusahaan)->where('status','A')->where('resign', 'N')->where('id_jabatan',5)->first();   
        }
        $pj = [
            'mengetahui' => $mengetahui->id,
            'memberi'   => $request->input('id_memberi'),
            'menerima'  => $show->id_karyawan
        ];

        $up = ChecklistData::find($id)->update([
          'data' => $data,
          'note' => $request->input('note'),
          'pj' => $pj,
          'changed_by' => Session::get('userid')
        ]);

        return response()->json(['message' => 'Data diperbarui']);
    }

    public function pdf($uid) {
        $show =  ChecklistData::where('uid', $uid)->first();
        $nama = $show->nama;
        $id_perusahaan = $show->id_perusahaan;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', $show->id_form)->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['item'] = ChecklistItem::where('kode', $form->kode)->where('status', 'A')->where('parent_id',0)->get();   
        $data['dataItem'] = $show->data;
        $pj = $show->pj;
        $data['mengetahui'] = Karyawan::find($pj['mengetahui']);
        $data['memberi'] = Karyawan::find($pj['memberi']);
        $data['menerima'] = Karyawan::find($pj['menerima']);
        $pdf = Pdf::loadView('data_crew.familiarisasi.pdf', $data)
                ->setPaper('a3', 'portrait');
        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

     public function elemen($uid)
    {   
        
        $data['active'] = "/form_ism";
        $get = FormISM::where('uid', $uid)->first();; 
        $roleJenis = Session::get('previllage');
        $activeCompany = $get->id_perusahaan;
        $activeShip = Session::get('id_kapal');

        $data['kapal'] = Kapal::where('status', 'A')
            ->when($roleJenis == 1 || $roleJenis == 2, function ($q) use ($activeCompany) {
                return $q->where('pemilik', $activeCompany);
            })
            ->when($roleJenis == 3 && $activeShip, function ($q) use ($activeShip) {
                return $q->where('id', $activeShip);
            })->get();    
        $data['form'] = KodeForm::find($get->id_form);
        $data['id_perusahaan'] = $get->id_perusahaan;
        return view('data_crew.familiarisasi.elemen', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
        $ctx = RoleContext::get();
        
        $daftar = DB::table('checklist_data')
                ->leftjoin('karyawan', 'karyawan.id', '=', 'checklist_data.id_karyawan')
                ->leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.id_jabatan')
                ->leftjoin('kapal', 'kapal.id', '=', 'checklist_data.id_kapal')
                ->select('checklist_data.*', 'karyawan.nama as nama', 'jabatan.nama as jabatan', 'kapal.nama as kapal')
                ->where('checklist_data.id_form', $request->input('kode'))
                ->where('checklist_data.status','A')
                ->when($perusahaan, fn($query, $perusahaan) => $query->where('checklist_data.id_perusahaan', $perusahaan))
                ->when($kapal, fn($query, $kapal) => $query->where('checklist_data.id_kapal', $kapal))
                ->when($ctx['jenis'] == 2 && $ctx['perusahaan_id'], fn($query) => $query->where('checklist_data.id_perusahaan', $ctx['perusahaan_id']))
                ->when($ctx['jenis'] == 3 && $ctx['kapal_id'], fn($query) => $query->where('checklist_data.id_kapal', $ctx['kapal_id']))
                ->orderBy('checklist_data.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }
}
