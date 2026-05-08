<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KelBarang;
use App\Models\Kapal;
use App\Models\Perusahaan;
use App\Models\ChecklistData;
use App\Models\KodeForm;
use App\Models\User;
use App\Models\Cabang;
use App\Models\TurunBarang;
use App\Models\DetailTurun;
use App\Models\FormISM;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;

class PenurunanController extends Controller
{
     public function index()
    {
        $data['active'] = "penurunan";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        return view('penurunan.index', $data);
    }

    public function form(Request $request, $uid=null)
    {
        $data['active'] = "permintaan";
        $roleJenis = Session::get('previllage');
        $id_perusahaan = Session::get('id_perusahaan');
        if($roleJenis==2) {
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->get();
        } else if($roleJenis==3) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get();
        } else {
            $data['kapal'] = Kapal::where('status','A')->get();
        }
        if ($uid) {
            $get = $this->visiblePermintaanByUid($uid);
            abort_unless($get, 404);
            $data['data'] = $get;
            $data['detail'] = DetailTurun::where('id_turun', $get->id)->where('is_delete',0)->get();
        }
        return view('penurunan.form', $data);
    }

     private function currentRoleJenis(): int
    {
        return (int) Session::get('previllage');
    }

    public function store(Request $request)
    {   
        $request->validate([
            'id_kapal' => ['required', 'integer'],
            'bagian' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'jumlah.*' => 'nullable|numeric|min:0'
        ]);

        $kapal = Kapal::where('status', 'A')->findOrFail($request->input('id_kapal'));
        $id_cabang = $kapal->id_cabang;
        if ((int) $this->currentRoleJenis() === 2 && (int) $kapal->pemilik !== (int) Session::get('id_perusahaan')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }
        if ((int) $this->currentRoleJenis() === 3 && (int) $kapal->id !== (int) Session::get('id_kapal')) {
            return response()->json(['message' => 'Kapal tidak valid untuk role aktif'], 403);
        }

        $bagian = $request->input('bagian');
        if($bagian==1) { $kat= 'Deck'; } else { $kat= 'Mesin'; }
        $tanggal = Carbon::parse($request->input('tanggal'))->format('dmY');
        $nomor = $kapal->call_sign.'/'.$kat.'/'.$tanggal;

            $save = TurunBarang::create([
              'uid' => Str::uuid()->toString(),
              'id_kapal' => $request->input('id_kapal'),
              'id_cabang' => $id_cabang,
              'nomor' => $nomor,
              'bagian' => $kat,
              'tanggal' => $request->input('tanggal'),
              'is_delete' => 0,
              'created_by' => Session::get('userid'),
              'created_date' => date('Y-m-d H:i:s')
            ]);

            $barangs = (array) $request->input('item', []);
            $jumlah = (array) $request->input('jumlah', []);
            $keterangan = (array) $request->input('ket', []);
            $validItems = [];
            foreach ($barangs as $item => $value) {
                $jum = $jumlah[$item] ?? null;
                $ket = $keterangan[$item] ?? null;
                if ($value && $jum !== null && $jum !== '') {
                    $validItems[] = [
                        'barang' => $value,
                        'jumlah' => $jum,
                        'ket' => $ket,
                    ];
                }
            }

            if (empty($validItems)) {
                return response()->json(['message' => 'Minimal satu barang dan jumlah harus diisi'], 422);
            }
            
            foreach ($validItems as $payload) {
                $cek = Gudang::where('id_kapal', $save->id_kapal)
                        ->where('id_barang',  $payload['barang'])
                        ->orderByDesc('id')
                        ->first();

                if ($payload['jumlah'] > $cek->jumlah) {
                    return back()->with('error', 'Jumlah melebihi stok');
                } else {
                    $savedetail = DetailTurun::create([
                        'uid' => Str::uuid()->toString(),
                        'id_turun' => $save->id,
                        'id_barang' => $payload['barang'],
                        'jumlah' => $payload['jumlah'],
                        'kondisi' => $payload['ket'],
                        'is_delete' => 0,
                        'created_by' => Session::get('userid'),
                        'created_date' => date('Y-m-d H:i:s')
                    ]);
                    
                    $idgudang = $cek->id;
                    $total = max(0, $cek->jumlah - $payload['jumlah']);
                    Gudang::where('id', $idgudang)->update([
                            'jumlah' => $total,
                            'changed_date' => date('Y-m-d H:i:s')
                        ]);
                }
            }

        return response()->json(['success' => true, 'message' => 'Permintaan berhasil disimpan']);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = TurunBarang::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                });

        if ((int) $roleJenis === 2) {
            $query->whereIn('id_kapal', Kapal::where('pemilik', Session::get('id_perusahaan'))->pluck('id'));
        }

        $query->orderByDesc('tanggal')->orderByDesc('id');

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
                return view('penurunan.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi', 'crew'])
            ->make(true);
    }

    public function get($id) 
    {

        $result = DB::table('t_detail_turun as a')
                ->leftjoin('m_barang as c', 'c.id', '=', 'b.id_barang')
                ->select('a.*', 'c.nama as barang', 'c.deskripsi as satuan')
                ->where('id_turun', $id)->where('a.is_delete', 0)->get();

        return response()->json($result);
    }

    public function pdf ($uid) {
        $show = TurunBarang::where('uid', $uid)->where('is_delete', 0)->first();
        $nama = $show->get_kapal()->call_sign;
        $id_perusahaan = $show->get_kapal()->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 55)->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['perusahaan'] = Perusahaan::find($id_perusahaan);
        $data['item'] =  DB::table('t_detail_turun as a')
                        ->leftjoin('m_barang as c', 'c.id', '=', 'a.id_barang')
                        ->select('a.*', 'c.nama as barang', 'c.deskripsi as satuan')
                        ->where('id_turun', $show->id)->where('a.is_delete', 0)->get();
        $pdf = Pdf::loadView('penurunan.pdf', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream($form->ket.' '.$nama.'.pdf');
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
        return view('permintaan.elemen', $data);
    }

    public function dataByIdp(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $request->input('id_perusahaan');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = DB::table('t_permintaan_barang as a')
                ->leftJoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->select('a.*')
                ->where('a.is_delete', 0)
                ->where('b.pemilik', $id_perusahaan)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('a.tanggal', $tanggal);
                });

        $query->orderByDesc('a.tanggal')->orderByDesc('a.id');

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

    public function elemenkirim($uid)
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
        return view('permintaan.elemenkirim', $data);
    }

     public function kirimByIdp(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_perusahaan = $request->input('id_perusahaan');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $query = DB::table('t_kirim_barang as a')
                ->leftJoin('kapal as b', 'a.id_kapal', '=', 'b.id')
                ->select('a.*')
                ->where('a.is_delete', 0)
                ->where('b.pemilik', $id_perusahaan)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('a.id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('a.tanggal', $tanggal);
                });

        $query->orderByDesc('a.tanggal')->orderByDesc('a.id');

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

    public function datagudang(Request $request) {
        $id_kapal = $request->input('id_kapal');
        $item = $request->input('item');

        $cek = Gudang::where('id_kapal', $id_kapal)->where('id_barang', $item)->orderByDesc('id')->first();
        return response()->json([
            'stok' => $cek ? $cek->jumlah : 0
        ]);
    }

}
