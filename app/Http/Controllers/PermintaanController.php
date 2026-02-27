<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use App\Models\Barang;
use App\Models\DetailPermintaan;
use App\Models\LogBarang;
use App\Models\Kapal;
use App\Models\Perusahaan;
use App\Models\ChecklistData;
use App\Models\KodeForm;
use App\Models\User;
use App\Models\StatusBarang;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;

class PermintaanController extends Controller
{
    public function index()
    {
        $data['active'] = "permintaan";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $$data['kapal'] = Kapal::find(Session::get('id_kapal'));
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        $data['statusbarang'] = StatusBarang::where('is_delete',0)->get();
        return view('permintaan.index', $data);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = Permintaan::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('created', function ($row) {
                $created = User::find($row->created_by);
                return $created ? $created->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('permintaan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function store(Request $request)
    {   
        $bagian = $request->input('bagian');
        $tanggal = Carbon::parse($request->input('tanggal'))->format('dmY');
        $kapal = Kapal::find($request->input('id_kapal'));
        $call = $kapal->call_sign;
        $nomor = $call.'/'.$bagian.'/'.$tanggal;
        $save = Permintaan::create([
          'uid' => Str::uuid()->toString(),
          'id_kapal' => $request->input('id_kapal'),
          'nomor' => $nomor,
          'bagian' => $bagian,
          'tanggal' => $request->input('tanggal'),
          'is_delete' => 0,
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s')
        ]);

        $barangs  = $request->input('item');
        $jumlah  = $request->input('jumlah');
        foreach ($barangs as $item => $value) {
            $jum = $jumlah[$item] ?? null;
            $savedetail = DetailPermintaan::create([
                'uid' => Str::uuid()->toString(),
                'id_permintaan' => $save->id,
                'id_barang' => $value,
                'jumlah' => $jum,
                'status' => 1,
                'is_delete' => 0,
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s')
            ]);

            $savelog = LogBarang::create([
                'uid' => Str::uuid()->toString(),
                'id_detail_permintaan' => $savedetail->id,
                'tanggal' => $save->tanggal,
                'status' => $savedetail->status,
                'is_delete' => 0,
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s')
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $cek = DetailPermintaan::where('id_permintaan', $id)->where('status', '!=', 1)->where('is_delete',0)->get();
        if($cek) {
            return response()->json(['status' => 'error', 'message' => 'Maaf, permintaan tidak dapat dibatalkan karena sudah diproses'],422); 
        } else {            
            $up = Permintaan::findOrFail($id);
            $up->update(['is_delete' => 1, 'changed_by' => Session::get('userid')]);
            $updetail = DetailPermintaan::where('id_permintaan', $id)->update(['is_delete' => 1, 'changed_by' => Session::get('userid')]);
            return response()->json(['status' => 'success', 'message' => 'Permintaan berhasil dibatalkan'],200);
        }
    }

     public function form(Request $request, $uid=null)
    {
        $data['active'] = "permintaan";
        $roleJenis = Session::get('previllage');
            $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $$data['kapal'] = Kapal::find(Session::get('id_kapal'));
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        $data['barang'] = Barang::where('is_delete', 0)->orderBy('id_kel_barang', 'ASC')->get();
        if ($uid) {
            $get = Permintaan::where('uid', $uid)->first();
            $data['data'] = $get;
            $data['detail'] = DetailPermintaan::where('id_permintaan', $get->id)->where('is_delete',0)->get();
        }
        return view('permintaan.form', $data);
    }

    public function deldetail($id)
    {
        $up = DetailPermintaan::findOrFail($id);
        $up->update(['is_delete' => 1, 'changed_by' => Session::get('userid')]);
        return response()->json(['status' => 'success', 'message' => 'Permintaan berhasil dibatalkan'],200);
    }

    public function update(Request $request, $id)
    {   
        $barangs  = $request->input('item');
        $jumlah  = $request->input('jumlah');
        foreach ($barangs as $item => $value) {
            $jum = $jumlah[$item] ?? null;
            $savedetail = DetailPermintaan::create([
            'uid' => Str::uuid()->toString(),
            'id_permintaan' => $id,
            'id_barang' => $value,
            'jumlah' => $jum,
            'status' => 1,
            'is_delete' => 0,
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s')
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function get($id) 
    {
        $result = DB::table('t_detail_permintaan as a')
                ->leftjoin('m_barang as b', 'b.id', '=', 'a.id_barang')
                ->leftjoin('m_status_barang as c', 'c.id', '=', 'a.status')
                ->select('a.*', 'b.nama as barang', 'b.deskripsi as satuan', 'c.nama as status')
                ->where('id_permintaan', $id)->where('a.is_delete', 0)->get();
        return response()->json($result);
    }

    public function datalog(Request $request)
    {
        $status = $request->input('status');
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = DB::table('t_detail_permintaan as a')
                ->leftjoin('t_permintaan_barang as b', 'b.id', '=', 'a.id_permintaan')
                ->select('a.*', 'b.tanggal', 'b.nomor', 'b.id_kapal')
                ->where('a.is_delete', 0)
                ->where('a.status', $status)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('b.id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('b.tanggal', $tanggal);
                });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('barang', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->nama : '-';
            })
            ->addColumn('satuan', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->deskripsi : '-';
            })
            ->addColumn('cabang', function ($row) {
                $cabang = Cabang::find($row->id_cabang);
                return $cabang ? $cabang->cabang : '-';
            })
            ->make(true);
    }

    public function getcabang($idkapal)
    {
        $kapal = Kapal::find($idkapal);
        $data = Cabang::where('id', $kapal->id_cabang)->orWhere('id', 5)->get();
        return response()->json($data);
    }

     public function proses(Request $request)
    {   
        $id  = $request->input('id');
        if($request->input('sedia')==0){
            $get = explode("|", $request->input('status'));
            $status = $get[0];
            $id_cabang = $get[1];
        } else {
            $status =$request->input('sedia');
            $id_cabang = null;
        }
        $up = DetailPermintaan::findOrFail($id);
        $up->update(['status' => $status, 
                'id_cabang' => $id_cabang, 
                'kode_po' => $request->input('kode_po'),
                'changed_by' => Session::get('userid')
        ]);

        $savelog = LogBarang::create([
            'uid' => Str::uuid()->toString(),
            'id_detail_permintaan' => $id,
            'tanggal' => $request->input('tanggal'),
            'status' => $status,
            'is_delete' => 0,
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s')
        ]);
        return response()->json(['success' => true]);
    }

     public function laporan()
    {
        $data['active'] = "lappermintaan";
        return view('laporan.permintaan.index', $data);
    }

    public function datalaporan(Request $request)
    {
        $status = $request->input('status');
        $query = DB::table('t_detail_permintaan as a')
                ->leftjoin('t_permintaan_barang as b', 'b.id', '=', 'a.id_permintaan')
                ->leftjoin('user as u', 'u.id', '=', 'b.created_by')
                ->select('a.*', 'b.tanggal', 'b.nomor', 'b.id_kapal', 'b.bagian', 'u.nama as peminta')
                ->where('a.is_delete', 0)
                ->orderBy('b.tanggal', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('barang', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->nama : '-';
            })
            ->addColumn('satuan', function ($row) {
                $barang = Barang::find($row->id_barang);
                return $barang ? $barang->deskripsi : '-';
            })
            ->addColumn('status', function ($row) {
                $status = StatusBarang::find($row->status);
                return $status ? $status->nama : '-';
            })
            ->make(true);
    }

    public function getlog($id) 
    {
        $result = DB::table('t_log_barang as a')
                ->leftjoin('m_status_barang as c', 'c.id', '=', 'a.status')
                ->leftjoin('user as d', 'd.id', '=', 'a.created_by')
                ->select('a.*','c.nama as status', 'd.nama as created')
                ->where('a.id_detail_permintaan', $id)
                ->orderBy('c.id', 'DESC')
                ->get();
        return response()->json($result);
    }

     public function pdf($uid) {
        $show =  Permintaan::where('uid', $uid)->first();
        $nama = $show->get_kapal()->call_sign;
        $id_perusahaan = $show->get_kapal()->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 47)->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['perusahaan'] = Perusahaan::find($id_perusahaan);
        $data['item'] = DetailPermintaan::where('id_permintaan', $show->id)->where('is_delete', 0)->get(); 
        $data['created'] = User::find($show->created_by);
        $pdf = Pdf::loadView('permintaan.pdf', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream($form->ket.' '.$nama.'.pdf');
    }
}
