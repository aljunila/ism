<?php

namespace App\Http\Controllers\Ck_kapal;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\ChecklistData;
use App\Models\Perusahaan;
use App\Models\Pelabuhan;
use App\Models\Jabatan;
use App\Models\ChecklistItem;
use App\Models\KodeForm;
use App\Models\FormISM;
use App\Models\Kapal;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;

class BerlayarController extends Controller
{
    public function index()
    {
        $data['active'] = "/ck_kapal/berlayar";    
        $data['kapal'] = Kapal::where('status','A')->get();
        $data['pelabuhan'] = Pelabuhan::where('is_delete',0)->get();
        $data['form'] = KodeForm::where('id_menu', 79)->get();
        return view('ck_kapal.berlayar.index', $data);
    }

    public function data()
    {
        $id_perusahaan = Session::get('id_perusahaan');
        $id_kapal = Session::get('id_kapal');
        $roleJenis = Session::get('previllage');
        
        $query = DB::table('checklist_data as a')
                ->leftjoin('kode_form as b', 'a.id_form', '=', 'b.id') 
                ->leftjoin('kapal as c', 'a.id_kapal', '=', 'c.id')
                ->select('a.*')       
                ->where('a.status', 'A')->where('b.id_menu', 79)
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
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
           ->addColumn('pelabuhan', function ($row) {
            $ket = json_decode($row->keterangan, true);
            return $ket['plb_asal'] ?? '-';
            })
            ->addColumn('kode', function ($row) {
                $kode = KodeForm::find($row->id_form);
                return $kode ? $kode->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('ck_kapal.berlayar.partials.actions', compact('row'))->render();
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
        $kapal = Kapal::find($request->input('id_kapal'));

        $plb_asal = Pelabuhan::find($request->input('id_pelabuhan'));
        $plb_tujuan = Pelabuhan::where('id_cabang', $plb_asal->id_cabang)->where('id', '!=', $plb_asal->id)->first();
        $keterangan = [
            'plb_asal' => $plb_asal->nama,
            'plb_tujuan' => $plb_tujuan->nama
        ];
        $save = ChecklistData::create([
          'uid' => Str::uuid()->toString(),
          'id_form' => $request->input('id_form'),
          'id_perusahaan' => $kapal->pemilik,
          'id_kapal' => $kapal->id,
          'date' => $request->input('date'),
          'status' => 'A',
          'keterangan' => $keterangan,
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d')
       ]);

        if($request->hasFile('file')) {
            $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
            ]);
            $file = $request->file('file');
            $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
        
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'checklist';
            $file->move($tujuan_upload,$nama_file);
            $save = ChecklistData::find($save->id)->update(['file' => $nama_file]); 
        }
        return response()->json(['message' => 'Data ditambahkan']);
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $data = ChecklistData::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $kapal = kapal::find($request->input('id_kapal'));
        $mengetahui = Karyawan::where('id_perusahaan', $show->id_perusahaan)->where('status','A')->where('resign', 'N')->where('id_jabatan',5)->first();
        $pj = [
            'mengetahui' => $mengetahui->id,
            'manager'   => $request->input('manager'),
            'mualim'  => $request->input('mualim'),
        ];
        $up = ChecklistData::find($id)->update([
          'id_form' => $request->input('id_form'),
          'id_kapal' => $request->input('id_kapal'),
          'id_perusahaan' => $kapal->id_perusahaan,
          'pj' => $pj,
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
        $pdf = Pdf::loadView('data_crew.berlayar.pdf', $data)
                ->setPaper('a3', 'portrait');
        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function elemen($uid)
    {
        $data['active'] = "form_ism";
        $get = FormISM::where('uid', $uid)->first();; 
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $get->id_perusahaan;
        if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } 
        $data['id_perusahaan'] = $id_perusahaan;
        $data['form'] = KodeForm::find($get->id_form);
        return view('ck_kapal.berlayar.elemen', $data);
    }

     public function databyIdp(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $request->input('id_perusahaan');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        
        $query = DB::table('checklist_data as a')
                ->leftjoin('kode_form as b', 'a.id_form', '=', 'b.id') 
                ->leftjoin('kapal as c', 'a.id_kapal', '=', 'c.id')
                ->select('a.*')       
                ->where('a.status', 'A')->where('a.id_form', $request->input('kode'))
                ->where('a.id_perusahaan', $id_perusahaan)
                ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->orderBy('a.id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
           ->addColumn('pelabuhan', function ($row) {
            $ket = json_decode($row->keterangan, true);
            return $ket['plb_asal'] ?? '-';
            })
            ->addColumn('kode', function ($row) {
                $kode = KodeForm::find($row->id_form);
                return $kode ? $kode->nama : '-';
            })
            ->make(true);
    }

}
