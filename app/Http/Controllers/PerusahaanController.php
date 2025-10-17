<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Perusahaan;
use App\Models\MasterFile;
use App\Models\FileUpload;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
use DB;
use App\Exports\PerusahaanExport;
use Maatwebsite\Excel\Facades\Excel;

class PerusahaanController extends Controller
{
    public function show()
    {
        $data['daftar'] = Perusahaan::where('status','A')->get();
        $data['active'] = "perusahaan";
        return view('perusahaan.show', $data);
    }

    public function add() 
    {
        $data['active'] = "perusahaan";
          return view('perusahaan.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Perusahaan::create([
          'uid' => Str::uuid()->toString(),
          'nama' => strtoupper($request->input('nama')),
          'kode' => $request->input('kode'),
          'alamat' => $request->input('alamat'),
          'email' => $request->input('email'),
          'telp' => $request->input('telp'),
          'npwp' => $request->input('npwp'),
          'nib' => $request->input('nib'),
          ]);

        $id = $save->id;
        if($request['logo']) {
            $request->validate([
            'logo' => 'image|mimes:jpeg,png,jpg|max:20480',
            ]);
            $file = $request->file('logo');
            $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
        
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'img';
            $file->move($tujuan_upload,$nama_file);
            $save = Perusahaan::find($id)->update(['logo' => $nama_file]); 
        }
        // return redirect()->route('perusahaan')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($uid)
    {
        $show = Perusahaan::where('uid', $uid)->first();
         $data['show'] = $show;
        $data['active'] = "perusahaan";
        return view('perusahaan.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $post = Perusahaan::find($id)->update([
            'nama' => strtoupper($request->input('nama')),
            'alamat' => $request->input('alamat'),
            'kode' => $request->input('kode'),
            'email' => $request->input('email'),
            'telp' => $request->input('telp'),
            'npwp' => $request->input('npwp'),
            'nib' => $request->input('nib'),
        ]);  
        
        if($request['logo']) {
            $request->validate([
            'logo' => 'image|mimes:jpeg,png,jpg|max:20480',
            ]);
            $file = $request->file('logo');
            $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
        
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'img';
            $file->move($tujuan_upload,$nama_file);
            $save = Perusahaan::find($id)->update(['logo' => $nama_file]); 
        }   
    //   return redirect('/perusahaan')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
       $post = Perusahaan::where('id',$id)->update(['status' => 'D']);
    //    return redirect('/perusahaan')->with('danger', 'Data berhasil dihapus');
    }

    public function export(Request $request)
    {
        return Excel::download(new PerusahaanExport(), 'data_perusahaan.xlsx');
    }

    public function profil($uid)
    {
        $show = Perusahaan::where('uid',$uid)->first();
        $id_perusahaan = $show->id;
        $data['show'] = $show;
        $data['active'] = "perusahaan";
        $data['file'] = DB::table('master_file as a')
                ->leftJoin('file_upload as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_file')
                        ->where('b.id_perusahaan', $id_perusahaan); 
                })
                ->where('a.type', 'P')
                ->where('a.status', 'A')
                ->select('a.*', 'b.file')
                ->get();
        return view('perusahaan.profile',$data);
    }

    public function savefile(Request $request, $id)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $cek = FileUpload::where('id_file', $id)->where('id_perusahaan', $request->input('id_perusahaan'))->first();

        if ($request->hasFile('file')) {
            if ($cek) {
                Storage::disk('public')->delete($cek->file);
                $del = FileUpload::where('id', $cek->id)->delete();
            }
            // upload file baru
            $file = $request->file('file');
            $nama_file = time() . "_" . str_replace(" ", "_", $file->getClientOriginalName());
            $file->move(public_path('file_upload'), $nama_file);

            $save = FileUpload::insert([
                'id_perusahaan' => $request->input('id_perusahaan'),
                'id_file'  => $id,
                'file' => $nama_file,
                'status' => 'A',
                'created_by' => Session::get('userid'),
            ]); 
            return response()->json($save);
        }
    }

    public function pdf($uid) {
        $show =  Perusahaan::where('uid', $uid)->first();
        $nama = $show->nama;
        $data['show'] = $show;
        $pdf = Pdf::loadView('perusahaan.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($nama.'.pdf');
    }
}