<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Jabatan;
use App\Models\Previllage;
use App\Models\User;
use App\Models\Akses;
use App\Models\StatusPTKP;
use App\Models\MasterFile;
use App\Models\FileUpload;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
Use DB;
use App\Exports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    public function show()
    {
        $data['active'] = "karyawan";
        $data['perusahaan'] = Perusahaan::get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        return view('karyawan.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal');

        $karyawan = DB::table('karyawan')
                ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id')
                ->leftJoin('perusahaan', 'perusahaan.id', '=', 'karyawan.id_perusahaan')
                ->leftJoin('kapal', 'kapal.id', '=', 'karyawan.id_kapal')
                ->select(
                    'karyawan.id',
                    'karyawan.uid',
                    'karyawan.nama',
                    'karyawan.nik',
                    'karyawan.nip',
                    'kapal.nama as kapal',
                    'jabatan.nama as jabatan'
                )
                ->where('karyawan.resign', 'N')
                ->where('karyawan.status','A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('perusahaan.id', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('kapal.id', $kapal);
                })
                ->get();
        // print_r($kapal);die();
        return response()->json(['data' => $karyawan]);
    }

    public function add() 
    {
        $data['active'] = "karyawan";
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['perusahaan'] = Perusahaan::where('status','A')->get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['previllage'] = Previllage::orderBy('id', 'DESC')->get();
        $data['ptkp'] = StatusPTKP::get();
        return view('karyawan.add', $data);
    }
  
    public function store(Request $request)
    {
        $get = Karyawan::where('resign', 'N')->where('status', 'A')->where('id_perusahaan', $request->input('id_perusahaan'))   
                ->orderBy('id', 'DESC')->limit(1)->first();
        $getnip = explode('-',$get->nip);
        $kode = $getnip[0];
        $num = str_pad(($getnip[1]+1), 3, '0', STR_PAD_LEFT);
        $nip = $kode.'-'.$num;
        
        $save = Karyawan::create([
            'uid' => Str::uuid()->toString(),
            'nama' => strtoupper($request->input('nama')),
            'nik' => $request->input('nik'),
            'nip' => $nip,
            'jk' => $request->input('jk'),
            'tmp_lahir' => $request->input('tmp_lahir'),
            'tgl_lahir' => $request->input('tgl_lahir'),
            'status_kawin' => $request->input('status_kawin'),
            'agama' => $request->input('agama'),
            'gol_darah' => $request->input('gol_darah'),
            'pend' => $request->input('pend'),
            'institusi_pend' => $request->input('institusi_pend'),
            'jurusan' => $request->input('jurusan'),
            'sertifikat' => $request->input('sertifikat'),
            'telp' => $request->input('telp'),
            'email' => $request->input('email'),
            'alamat' => $request->input('alamat'),
            'nama_bank' => $request->input('nama_bank'),
            'no_rekening' => $request->input('no_rekening'),
            'nama_rekening' => $request->input('nama_rekening'),
            'npwp' => $request->input('npwp'),
            'status_ptkp' => $request->input('status_ptkp'),
            'bpjs_kes' => $request->input('bpjs_kes'),
            'bpjs_tk' => $request->input('bpjs_tk'),
            'tgl_mulai' => $request->input('tgl_mulai'),
            'status_karyawan' => $request->input('status_karyawan'),
            'id_jabatan' => $request->input('id_jabatan'),
            'id_perusahaan' => $request->input('id_perusahaan'),
            'id_kapal' => $request->input('id_kapal'),
            'status' => 'A',
            'resign' => 'N',
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s')
        ]);

        $id = $save->id;
        if($request['tanda_tangan']) {
            $request->validate([
            'tanda_tangan' => 'image|mimes:jpeg,png,jpg|max:20480',
            ]);
            $file = $request->file('tanda_tangan');
            $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
        
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'ttd_karyawan';
            $file->move($tujuan_upload,$nama_file);
            $save = Karyawan::find($id)->update(['tanda_tangan' => $nama_file]); 
        }

        if($request->input('username')){
                $akun = User::create([
                'nama' => strtoupper($request->input('nama')),
                'username' => $request->input('username'),
                'password' => Hash::make('123456'),
                'id_previllage' => $request->input('id_previllage'),
                'id_perusahaan' => $request->input('id_perusahaan'),
                'id_kapal' => $request->input('id_kapal'),
                'id_karyawan'=> $id,
                'status' => 'A',
                'created_by' => Session::get('userid'),
                'created_date' => date('Y-m-d H:i:s')
                ]);
        }
        if($save) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => true]);
        };
    }

    public function edit($uid)
    {
        $show = Karyawan::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "karyawan";
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['perusahaan'] = Perusahaan::get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['previllage'] = Previllage::orderBy('id', 'DESC')->get();
        $data['ptkp'] = StatusPTKP::get();
        return view('karyawan.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Karyawan::find($id)->update($request->all());  

       $update = User::where('id_karyawan', $id)->update([
        'id_perusahaan' => $request->input('id_perusahaan'),
        'id_kapal' => $request->input('id_kapal'),
        'id_previllage' => $request->input('id_previllage'),
        'changed_by' => Session::get('userid'),
      ]);    
      return response()->json($post);
    }

    public function delete($id)
    {
       $post = Karyawan::where('id',$id)->update(['status' => 'D', 'changed_by' => Session::get('userid')]);
       return response()->json(['success' => true]);
    }

    public function profil($uid)
    {
        $show = DB::table('karyawan')
                    ->select('karyawan.*', 'user.id as user', 'user.id_perusahaan', 'perusahaan.nama as perusahaan', 
                        'user.id_kapal', 'kapal.nama as kapal', 'jabatan.nama as jabatan', 'user.username', 'user.id_previllage', 'previllage.nama as previllage')
                    ->leftjoin('user', 'user.id_karyawan', '=', 'karyawan.id')
                    ->leftjoin('perusahaan', 'user.id_perusahaan', '=', 'perusahaan.id')
                    ->leftjoin('kapal', 'user.id_kapal', '=', 'kapal.id')
                    ->leftjoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id')
                    ->leftjoin('previllage', 'user.id_previllage', '=', 'previllage.id')
                    ->where('karyawan.uid', $uid)->first();
        if ($show) {
            $show->tanda_tangan_url = $show->tanda_tangan && 
                file_exists(public_path('ttd_karyawan/' . $show->tanda_tangan))
                ? asset('ttd_karyawan/' . $show->tanda_tangan)
                : asset('ttd_karyawan/no-signature.png');
        }
        $id_karyawan = $show->id;
        $data['file'] = DB::table('master_file as a')
                ->leftJoin('file_upload as b', function($join) use ($id_karyawan) {
                    $join->on('a.id', '=', 'b.id_file')
                        ->where('b.id_karyawan', $id_karyawan); 
                })
                ->where('a.type', 'S')
                ->where('a.status', 'A')
                ->select('a.*', 'b.file')
                ->get();
        $data['show'] = $show;
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['perusahaan'] = Perusahaan::get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['previllage'] = Previllage::orderBy('id', 'DESC')->get();
        $data['menu'] = Akses::where('id_karyawan', $show->id)->get();
        $data['active'] = "karyawan";
         $data['ptkp'] = StatusPTKP::get();
        return view('karyawan.profile',$data);
    }

     public function resign($id)
    {
       $post = Karyawan::where('id',$id)->update(['resign' => 'Y', 'changed_by' => Session::get('userid')]);
       return response()->json(['success' => true]);
    }

    public function updatettd(Request $request, $id)
    {
        $request->validate([
            'tanda_tangan' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
        ]);
        $karyawan = Karyawan::findOrFail($id);

        if ($request->hasFile('tanda_tangan')) {
            if ($karyawan->tanda_tangan && Storage::disk('public')->exists($karyawan->tanda_tangan)) {
                Storage::disk('public')->delete($karyawan->tanda_tangan);
            }

            // upload file baru
            $file = $request->file('tanda_tangan');
            $nama_file = time() . "_" . str_replace(" ", "_", $file->getClientOriginalName());
            $file->move(public_path('ttd_karyawan'), $nama_file);

            $save = Karyawan::find($id)->update(['tanda_tangan' => $nama_file]); 
            return response()->json($save);
        }
    }

    public function export(Request $request)
    {
        $id_perusahaan = $request->input('id_perusahaan');
        $id_kapal = $request->input('id_kapal');

        return Excel::download(new KaryawanExport($id_perusahaan, $id_kapal), 'data_karyawan.xlsx');
    }

    public function savefile(Request $request, $id)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $cek = FileUpload::where('id_file', $id)->where('id_karyawan', $request->input('id_karyawan'))->first();

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
                'id_karyawan' => $request->input('id_karyawan'),
                'id_file'  => $id,
                'file' => $nama_file,
                'status' => 'A',
                'created_by' => Session::get('userid'),
            ]); 
            return response()->json($save);
        }
    }

    public function pdf($uid) {
        $show =  Karyawan::where('uid', $uid)->first();
        $nama = $show->nama;
        $data['show'] = $show;
        $pdf = Pdf::loadView('karyawan.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($nama.'.pdf');
    }
}