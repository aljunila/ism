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
use App\Models\Kapal;
use App\Models\Pelabuhan;
use App\Models\FormISM;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;

class GantiController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_crew/ganti";
        $data['perusahaan'] = Perusahaan::where('status', 'A')->get();     
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $data['form'] = KodeForm::where('id_menu', 58)->select('kel')->distinct()->get();
        return view('data_crew.ganti.index', $data);
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
                ->where('a.status', 'A')->where('b.id_menu', 58)
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
            ->addColumn('dari', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('kepada', function ($row) {
                $karyawan2 = Karyawan::find($row->id_karyawan2);
                return $karyawan2 ? $karyawan2->nama : '-';
            })
            ->addColumn('kode', function ($row) {
                $kode = KodeForm::find($row->id_form);
                return $kode ? $kode->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.ganti.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    // public function all()
    // {
    //     return ChecklistData::where('status', 'A')->get(['id', 'nama', 'kode']);
    // }

    public function store(Request $request)
    {
        $dari = Karyawan::find($request->input('id_karyawan'));
        $kepada = Karyawan::find($request->input('id_karyawan2'));
        $form = KodeForm::where('kel', $request->input('kode'))->where('id_menu', 58)->get();

        foreach($form as $f) {
            $save = ChecklistData::create([
                'uid' => Str::uuid()->toString(),
                'id_form' => $f->id,
                'id_karyawan' => $request->input('id_karyawan'),
                'id_jabatan' => $dari->id_jabatan,
                'id_perusahaan' => $dari->id_perusahaan,
                'id_kapal' => $dari->id_kapal,
                'id_karyawan2' => $request->input('id_karyawan2'),
                'id_jabatan2' => $kepada->id_jabatan,
                'date' => $request->input('date'),
                'status' => 'A',
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d')
            ]);
        }
        
        return response()->json(['message' => 'Data ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $dari = Karyawan::find($request->input('id_karyawan'));
        $kepada = Karyawan::find($request->input('id_karyawan2'));

        $up = ChecklistData::find($id)->update([
          'id_form' => $request->input('id_form'),
          'id_karyawan' => $request->input('id_karyawan'),
          'id_jabatan' => $dari->id_jabatan,
          'id_perusahaan' => $dari->id_perusahaan,
          'id_kapal' => $dari->id_kapal,
          'id_karyawan2' => $request->input('id_karyawan2'),
          'id_jabatan2' => $kepada->id_jabatan,
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
        $data['active'] = "/data_crew/ganti";
        $show = ChecklistData::where('uid', $uid)->first();
        $form = KodeForm::find($show->id_form);
        $get = Kapal::find($show->id_kapal);
        $data['show'] = $show;
        $data['form'] = $form;
        $data['item'] = ChecklistItem::where('kode', $form->kode)->where('status', 'A')->where('parent_id',0)->get();
        $data['karyawan'] = Karyawan::where('id_perusahaan', $show->id_perusahaan)->where('status','A')->where('resign', 'N')->get();
        $data['pelabuhan'] = Pelabuhan::where('id_cabang', $get->id_cabang)->where('is_delete',0)->get();
        $data['dataItem'] = $show->data;
        $data['keterangan'] = $show->keterangan;
        if($form->id==16){
            return view('data_crew.ganti.handover', $data);
        } else {
            return view('data_crew.ganti.form', $data);
        }
    }

    public function savedata(Request $request, $id)
    {
        $show = ChecklistData::find($id);
        $get = Kapal::find($show->id_kapal);
        if($get->id_cabang==1) {
            $wi = "WIB";
        } else {
            $wi = "WITA";
        }
        if($show->id_form==16) {
            $data = [
                'no' => $request->input('no'),
                'fo' => $request->input('fo'),
                'do' => $request->input('do'),
                'fw' => $request->input('fw'),
            ];
        } else {
            $data = [];
            foreach ($request->item as $iditem => $value) {
                $data[$iditem] = [
                    'value' => (int) $value,
                    'ket'   => $request->ket[$iditem] ?? null,
                ];
            }
        }
        $pj = [
            'lama' => $show->id_karyawan,
            'baru'   => $show->id_karyawan2,
        ];
        $keterangan = [
            'pelabuhan' => $request->input('pelabuhan'),
            'wi'       => $wi
        ];

        $up = ChecklistData::find($id)->update([
          'data' => $data,
          'note' => $request->input('note'),
          'pj' => $pj,
          'keterangan' => $keterangan,
          'time' => $request->input('jam'),
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
        $data['keterangan'] = $show->keterangan;
        if($show->id_form==16) { $page = "pdfover"; } else { $page = "pdf"; }
        $pdf = Pdf::loadView('data_crew.ganti.'.$page, $data)
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
        return view('data_crew.ganti.elemen', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
        $ctx = RoleContext::get();
        
        $daftar = DB::table('checklist_data as a')
                ->leftjoin('karyawan as b', 'b.id', '=', 'a.id_karyawan')
                ->leftjoin('karyawan as c', 'c.id', '=', 'a.id_karyawan2')
                ->leftjoin('kapal', 'kapal.id', '=', 'a.id_kapal')
                ->select('a.*', 'b.nama as dari', 'c.nama as kepada', 'kapal.nama as kapal')
                ->where('a.id_form', $request->input('kode'))
                ->where('a.status','A')
                ->when($perusahaan, fn($query, $perusahaan) => $query->where('a.id_perusahaan', $perusahaan))
                ->when($kapal, fn($query, $kapal) => $query->where('a.id_kapal', $kapal))
                ->when($ctx['jenis'] == 3 && $ctx['kapal_id'], fn($query) => $query->where('a.id_kapal', $ctx['kapal_id']))
                ->orderBy('a.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }
}
