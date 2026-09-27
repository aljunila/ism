<?php

namespace App\Http\Controllers\Data_teknik;

use App\Http\Controllers\Controller;
use App\Models\Docking;
use App\Models\BiayaDocking;
use App\Models\KelDock;
use App\Models\SubJobDock;
use App\Models\Kapal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Str;
use Session;

class DockingController extends Controller
{
    public function index()
    {
        $data['active'] = "/data_teknik/docking";
        $data['job'] = KelDock::where('is_delete', 0)->get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('data_teknik.docking.index', $data);
    }

    public function data(Request $request)
    {
        $id_kapal = $request->input('id_kapal');
        $query = Docking::where('is_delete', 0)
                ->when($id_kapal, function($query, $id_kapal) {
                    return $query->where('id_kapal', $id_kapal);
                });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kapal', function ($row) {
                $kapal = Kapal::find($row->id_kapal);
                return $kapal ? $kapal->nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                return view('data_teknik.docking.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $cek = Docking::where('id_kapal', $request->post('id_kapal'))->where('tgl_mulai', $request->post('tgl_mulai'))->where('is_delete', 0)->exists();
        if($cek){
             return response()->json(['status' => 'error', 'message' => 'Maaf, data sudah ada'],422);
        } else {
            $validated = ([
                'uid' => Str::uuid()->toString(),
                'id_kapal' => $request->post('id_kapal'),
                'bulan' => $request->post('bulan'),
                'tahun' => $request->post('tahun'),
                'tempat' => $request->post('tempat'),
                'tgl_mulai' => $request->post('tgl_mulai'),
                'tgl_selesai' => $request->post('tgl_selesai'),
                'durasi' => $request->post('durasi'),
                'is_delete' => 0,
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s'),
            ]);
            $save = Docking::create($validated);
            
            if($request->hasFile('file')) {
                $request->validate([
                'file' => 'required|file|mimes:pdf|max:20480',
                ]);
                $file = $request->file('file');
                $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
            
                // isi dengan nama folder tempat kemana file diupload
                $tujuan_upload = public_path('file_docking');
                $file->move($tujuan_upload,$nama_file);
                $save = Docking::find($save->id)->update(['file' => $nama_file]); 
            }
            return response()->json(['status' => 'success', 'message' => 'Data docking berhasil disimpan'],200);
        }
    }

    public function update(Request $request, $id)
    {
        $cek = Docking::where('id_kapal', $request->post('id_kapal'))->where('tgl_mulai', $request->post('tgl_mulai'))->where('is_delete', 0)->exists();
        if($cek>1){
             return response()->json(['status' => 'error', 'message' => 'Maaf, data sudah ada'],422);
        } else {
            $validated = ([
                'id_kapal' => $request->post('id_kapal'),
                'bulan' => $request->post('bulan'),
                'tahun' => $request->post('tahun'),
                'tempat' => $request->post('tempat'),
                'tgl_mulai' => $request->post('tgl_mulai'),
                'tgl_selesai' => $request->post('tgl_selesai'),
                'durasi' => $request->post('durasi'),
                'changed_by' => Session::get('userid'),
            ]);
            $up = Docking::findOrFail($id);
            $up->update($validated);
            if($request->hasFile('file')) {
                $request->validate([
                'file' => 'required|file|mimes:pdf|max:20480',
                ]);
                $file = $request->file('file');
                $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
            
                // isi dengan nama folder tempat kemana file diupload
                $tujuan_upload = public_path('file_docking');
                $file->move($tujuan_upload,$nama_file);
                $save = Docking::find($id)->update(['file' => $nama_file]); 
            }
            return response()->json(['status' => 'success', 'message' => 'Data docking berhasil diubah'],200);
        }
    }

    public function destroy($id)
    {
        $cek = BiayaDocking::where('id_docking', $id);
        $cek->update(['is_delete' => 1]);
        
        $up = Docking::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Data dihapus']);
    }

    public function biaya($id)
    {
        $data['active'] = "/data_teknik/docking";
        $data['show'] = Docking::where('uid', $id)->first();
        $data['job'] = KelDock::where('is_delete', 0)->get();
        return view('data_teknik.docking.biaya', $data);
    }

    public function databiaya(Request $request)
    {
        $id = $request->input('id_docking');
        $query = DB::table('t_biaya_docking as a')
                ->leftJoin('m_subjob_dock as b', 'a.id_subjob', '=', 'b.id')
                ->leftJoin('m_kel_dock as c', 'b.id_job', '=', 'c.id')
                ->select('a.*', 'b.nama as subjob', 'c.nama as job', 'c.id as id_job')
                ->where('id_docking', $id)->where('a.is_delete', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return view('data_teknik.docking.partials.actions_biaya', compact('row'))->render();
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

     public function storebiaya(Request $request)
    {
        
        $validated = ([
            'uid' => Str::uuid()->toString(),
            'id_docking' => $request->post('id_docking'),
            'id_subjob' => $request->post('id_subjob'),
            'deskripsi' => $request->post('deskripsi'),
            'unit' => $request->post('unit'),
            'volume' => $request->post('volume'),
            'harga' => $request->post('harga'),
            'total' => $request->post('total'),
            'keterangan' => $request->post('keterangan'),
            'is_delete' => 0,
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s'),
        ]);
        $save = BiayaDocking::create($validated);
        return response()->json(['status' => 'Berhasil', 'message' => 'Data biaya berhasil disimpan'],200);
    }

    public function updatebiaya(Request $request, $id)
    {
        $validated = ([
                'id_subjob' => $request->post('id_subjob'),
                'deskripsi' => $request->post('deskripsi'),
                'unit' => $request->post('unit'),
                'volume' => $request->post('volume'),
                'harga' => $request->post('harga'),
                'total' => $request->post('total'),
                'keterangan' => $request->post('keterangan'),
                'changed_by' => Session::get('userid'),
            ]);
            $up = BiayaDocking::findOrFail($id);
            $up->update($validated);
            return response()->json(['status' => 'Berhasil', 'message' => 'Data biaya berhasil diubah'],200);
    }

    
    public function destroybiaya($id)
    {
        $up = BiayaDocking::findOrFail($id);
        $up->update(['is_delete' => 1]);
        return response()->json(['message' => 'Data dihapus']);
    }

     public function updatefile(Request $request, $id)
    {
        
        if($request->hasFile('docking')) {
            $request->validate([
            'docking' => 'required|file|mimes:pdf|max:20480',
            ]);
            $file = $request->file('docking');
            $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
        
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = public_path('file_docking');
            $file->move($tujuan_upload,$nama_file);
            $save = Docking::find($id)->update(['file' => $nama_file]); 
        }
        return response()->json(['status' => 'success', 'message' => 'Data docking berhasil diubah'],200);
    }

}
