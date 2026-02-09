<?php

namespace App\Http\Controllers\Data_crew;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\PeriodeKondite;
use App\Models\Kondite;
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

class KonditeController extends Controller
{
    public function index()
    {
        $data['active'] = "kondite";
        $id_perusahaan = Session::get('id_perusahaan');
        $id_kapal = Session::get('id_kapal');
        $roleJenis = Session::get('previllage');
        $data['kapal'] = Kapal::where('status','A')
            ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
            ->when($roleJenis == 2 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                return $q->where('pemilik', $id_perusahaan);
            })
            ->when($roleJenis == 3 && $id_kapal, function ($q) use ($id_kapal) {
                return $q->where('id', $id_kapal);
            })
            ->get();
        return view('data_crew.kondite.index', $data);
    }

    public function data()
    {
        $id_perusahaan = Session::get('id_perusahaan');
        $id_kapal = Session::get('id_kapal');
        $roleJenis = Session::get('previllage');
        
        $query = PeriodeKondite::where('status','A')->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('perusahaan', function ($row) {
                $perusahaan = Perusahaan::find($row->id_perusahaan);
                return $perusahaan ? $perusahaan->nama : '-';
            })
             ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_crew.kondite.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');

        $cek = PeriodeKondite::where('id_kapal', $request->input('id_kapal'))->where('bulan', $request->input('bulan'))->where('tahun', $request->input('tahun'))->where('status', 'A')->exists();
        if($cek) {    
            return response()->json(['error' => true]);
        } else {
            $kapal = Kapal::find($request->input('id_kapal'));
            $save = PeriodeKondite::create([
                'uid' => Str::uuid()->toString(),
                'kode' => $request->input('kode'),
                'id_perusahaan' => $kapal->pemilik,
                'id_kapal' => $request->input('id_kapal'),
                'bulan' => $request->input('bulan'),
                'tahun' => $request->input('tahun'),
                'status' => 'A',
                'created_by' => $created,
                'created_date' => $date
            ]);

            $karyawan = DB::table('karyawan')->where('id_kapal', $request->input('id_kapal'))->where('status', 'A')->where('resign','N')->get();
            foreach($karyawan as $data) {
                $insert = Kondite::create([
                    'uid' => Str::uuid()->toString(),
                    'id_periode' => $save->id,
                    'id_karyawan' => $data->id,
                    'id_jabatan' => $data->id_jabatan,
                    'status' => 'A',
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d'),
                ]);
            }
            if($save){
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => false]);
            }
        }
    }

    public function update(Request $request, $id)
    {
        $cek = PeriodeKondite::where('id_kapal', $request->input('id_kapal'))->where('bulan', $request->input('bulan'))
            ->where('tahun', $request->input('tahun'))->where('status', 'A')->where('id', '!=', $id)->exists();
        if($cek) {    
           return response()->json(['error' => true]);
        } else {
            $save = PeriodeKondite::where('id',$id)->update([
                'bulan' => $request->input('bulan'),
                'tahun' => $request->input('tahun'),
                'changed_by' => Session::get('userid'),
            ]);
            return response()->json(['success' => true]);
        }
    }

    public function destroy($id)
    {
        $up = PeriodeKondite::findOrFail($id);
        $up->update(['status' => 'D']);
        return response()->json(['message' => 'Data dihapus']);
    }

     public function form($uid)
    {
        $periode = PeriodeKondite::where('uid', $uid)->first();
        $data['active'] = "kondite";
        $data['form'] = KodeForm::where('id', 43)->first();
        $data['item'] = ChecklistItem::where('kode', 'el0608')->where('status', 'A')->get();
        // $data['karyawan'] = Kondite::where('status', 'A')->where('id_periode', $periode->id)->get();
        $data['penilai'] = Karyawan::where('id_perusahaan', $periode->id_perusahaan)->where(function ($q) use ($periode) 
                        { $q->where('id_kapal', $periode->id_kapal)->orWhere('id_kapal', 0);})->get();
        $data['periode'] = $periode;
         return view('data_crew.kondite.form', $data);
    }

    public function getDetail(Request $request)
    {
        $daftar = DB::table('t_kondite as a')
                ->leftJoin('karyawan as b', 'a.id_karyawan', 'b.id')
                ->leftJoin('jabatan as c', 'a.id_jabatan', 'c.id')
                ->select('a.*', 'b.nama as karyawan', 'c.nama as jabatan')
                ->where('a.status','A')->where('id_periode', $request->input('id'))
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    //  public function getChecklist(Request $request) {
    //     $id   = $request->input('id');  
    //     $kode = $request->input('kode');
    //     $data = DB::table('checklist_item as a')
    //                 ->leftJoin('checklist_kondite_detail as b', function($join) use ($id) {
    //                     $join->on('a.id', '=', 'b.checklist_item_id')
    //                         ->where('b.kondite_id', '=', $id);
    //                 })
    //                 ->select('a.*', 'b.value', 'b.ket', 'b.kondite_id')
    //                 ->where('a.kode', $kode)
    //                 ->where('a.status', 'A')
    //                 ->orderBy('a.id', 'ASC')
    //                 ->get();
    //     return response()->json(['data' => $data]);
    // }

    // public function getKondite(Request $request)
    // {
    //     $id = $request->id;
    //     $kondite = Kondite::findOrFail($id);
    //     return response()->json($kondite);
    // }

    public function savedata(Request $request)
    {
        $id = $request->input('id');
        $data = [];
        foreach ($request->item as $iditem => $value) {
            $data[$iditem] = [
                'value' => (int) $value,
            ];
        }
        $save = Kondite::where('id',$id)->update([
                'tgl_nilai' => date('Y-m-d'),
                'rekomendasi' => $request->input('rekomendasi'),
                'note' => $request->input('note'),
                'data' => $data,
                'id_penilai_1' => $request->input('id_penilai_1'),
                'id_penilai_2' => $request->input('id_penilai_2'),
                'id_mengetahui' => $request->input('id_mengetahui'),
            ]);

    }

    public function pdf($uid) {
        $show =  Kondite::where('uid', $uid)->first();
        $nama = $show->get_karyawan()->nama;
        $get = PeriodeKondite::find($show->id_periode);
        $id_perusahaan = $get->id_perusahaan;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.kode', 'el0608')->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $item = ChecklistItem::where('kode', $form->kode)->where('status', 'A')->where('parent_id',0)->get();
        $data['item'] = $item;
        $data['dataItem'] = $show->data;
        $data['periode'] = $get;
        $pdf = Pdf::loadView('data_crew.kondite.pdf', $data)
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
        return view('data_crew.kondite.elemen', $data);
    }

    public function getData(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $request->input('id_perusahaan');
        $id_form = $request->input('id_form');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');

        $query = DB::table('checklist_data as a')
                ->leftjoin('kode_form as b', 'a.id_form', '=', 'b.id') 
                ->leftjoin('karyawan as c', 'a.id_karyawan', '=', 'c.id')
                ->select('a.*')       
                ->where('a.status', 'A')->where('a.id_form', $id_form)
                ->when((($roleJenis == 1) or ($roleJenis == 5)), function ($q) { return $q; })
                ->when($roleJenis == 2 && $id_perusahaan, function ($q) use ($id_perusahaan) {
                    return $q->where('a.id_perusahaan', $id_perusahaan);
                })
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->orderBy('a.id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                $karyawan = Karyawan::find($row->id_karyawan);
                return $karyawan ? $karyawan->nama : '-';
            })
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('jabatan', function ($row) {
                $jabatan = Jabatan::find($row->id_jabatan);
                return $jabatan ? $jabatan->nama : '-';
            })
            ->addColumn('kode', function ($row) {
                $kode = KodeForm::find($row->id_form);
                return $kode ? $kode->nama : '-';
            })
            ->make(true);
    }

    public function datakondite(Request $request)
    {
        $id = $request->input('id');
        
        $query = DB::table('t_kondite as a')
                ->leftjoin('karyawan as b', 'a.id_karyawan', '=', 'b.id')
                ->leftjoin('jabatan as c', 'a.id_jabatan', '=', 'c.id')
                ->select('a.*', 'b.nama as karyawan', 'c.nama as jabatan')
                ->where('a.id_periode', $id);

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
            ->addColumn('aksi', function ($row) {
                return view('data_crew.kondite.partials.act_kondite', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
