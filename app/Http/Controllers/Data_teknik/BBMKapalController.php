<?php

namespace App\Http\Controllers\Data_teknik;

use App\Http\Controllers\Controller;
use App\Models\BBMKapal;
use App\Models\LogBBM;
use App\Models\Kapal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Str;
use Session;

class BBMKapalController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_teknik/bbm";
        if(Session::get('previllage')==2) {
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', Session::get('id_perusahaan'))->get(); 
        } else if(Session::get('previllage')==3) {
          $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get(); 
        } else if(Session::get('previllage')==6) {
            $data['kapal'] = Kapal::where('status', 'A')->where('id_cabang', Session::get('id_cabang'))->get(); 
        } else {
            $data['kapal'] = Kapal::where('status', 'A')->get();  
        }
        return view('data_teknik.bbm.index', $data);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $id_kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $tanggal = $request->input('tanggal');
        $jenis = $request->input('jenis');

        $query = LogBBM::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                })
                ->when($tanggal, function($query, $tanggal) {
                    return $query->where('tanggal', $tanggal);
                })
                ->when($jenis, function($query, $jenis) {
                    return $query->where('id_jenis', $jenis);
                })
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_teknik.bbm.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $cek = LogBBM::where('id_kapal', $request->post('id_kapal'))->where('tanggal', $request->post('tanggal'))->where('id_jenis', $request->post('id_jenis'))->where('is_delete', 0)->exists();
        $rob_awal = BBMKapal::where('id_kapal', $request->post('id_kapal'))->first();
       
        if($cek){
             return response()->json(['status' => 'error', 'message' => 'Maaf, data sudah ada'],422);
        } else {
            if($request->post('id_jenis')==1) {
                $jml = $request->post('me') + $request->post('ae');
                $saldo_akhir = $rob_awal->saldo - $jml;
            } else {
                $jml = $request->post('jumlah');
                $saldo_akhir = $rob_awal->saldo + $jml;
            }
            $validated = ([
                'uid' => Str::uuid()->toString(),
                'id_kapal' => $request->post('id_kapal'),
                'saldo_awal' => $rob_awal->saldo,
                'id_jenis' => $request->post('id_jenis'),
                'status' => $request->post('status'),
                'tanggal' => $request->post('tanggal'),
                'me' => $request->post('me'),
                'ae' => $request->post('ae'),
                'jam' => $request->post('jam'),
                'jumlah' => $jml,
                'saldo_akhir' => $saldo_akhir,
                'is_delete' => 0,
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s'),
            ]);
            $save = LogBBM::create($validated);
            $upsaldo = BBMKapal::where('id_kapal', $save->id_kapal)->update(['saldo' => $save->saldo_akhir, 'changed_by' => Session::get('userid')]);
            return response()->json(['status' => 'success', 'message' => 'Data bbm berhasil disimpan'],200);
        }
    }

    public function update(Request $request, $id)
    {
        $cek = LogBBM::where('id_kapal', $request->post('id_kapal'))->where('tanggal', $request->post('tanggal'))->where('id_jenis', $request->post('id_jenis'))->where('is_delete', 0)->exists();
        if($cek>1){
             return response()->json(['status' => 'error', 'message' => 'Maaf, data sudah ada'],422);
        } else {
            $get = LogBBM::findorFail($id);
            $saldo_awal = $get->saldo_awal;
            if($request->post('id_jenis')==1) {
                $jml = $request->post('me') + $request->post('ae');
                $saldo_akhir = $saldo_awal - $jml;
            } else {
                $jml = $request->post('jumlah');
                $saldo_akhir = $saldo_awal + $jml;
            }
            $validated = ([
                'id_kapal' => $request->post('id_kapal'),
                'saldo_awal' => $saldo_awal,
                'id_jenis' => $request->post('id_jenis'),
                'status' => $request->post('status'),
                'tanggal' => $request->post('tanggal'),
                'me' => $request->post('me'),
                'ae' => $request->post('ae'),
                'jam' => $request->post('jam'),
                'jumlah' => $jml,
                'saldo_akhir' => $saldo_akhir,
                'changed_by' => Session::get('userid'),
            ]);
            $up = LogBBM::findOrFail($id);
            $up->update($validated);
            $upsaldo = BBMKapal::where('id_kapal', $request->post('id_kapal'))->update(['saldo' => $saldo_akhir, 'changed_by' => Session::get('userid')]);
            return response()->json(['status' => 'success', 'message' => 'Data bbm berhasil diubah'],200);
        }
    }

    public function destroy($id)
    {
        $up = LogBBM::findOrFail($id);
        $jumlah = $up->jumlah;
        $cek = BBMKapal::where('id_kapal', $up->id_kapal)->first();
        if($up->id_jenis==1) {
            $saldo = $cek->saldo + $jumlah;
            $upsaldo = BBMKapal::where('id', $cek->id)->update(['saldo' => $saldo, 'changed_by' => Session::get('userid')]);
        } else {
            $saldo = $cek->saldo - $jumlah;
            $upsaldo = BBMKapal::where('id', $cek->id)->update(['saldo' => $saldo, 'changed_by' => Session::get('userid')]);
        }

        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Data dihapus']);
    }
}