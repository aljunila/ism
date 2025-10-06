<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Jabatan;
use App\Models\Previllage;
use App\Models\User;
use App\Models\Akses;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
Use DB;

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
        $data['perusahaan'] = Perusahaan::get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['previllage'] = Previllage::orderBy('id', 'DESC')->get();
        return view('karyawan.add', $data);
    }
  
    public function store(Request $request)
    {
        $cek = User::where('username', $request->input('username'))
                ->get();
        $count = count($cek);
        if($count>0){
            return response()->json(['error' => true]);
        } else {
            $save = Karyawan::create([
                'uid' => Str::uuid()->toString(),
                'nama' => $request->input('nama'),
                'nik' => $request->input('nik'),
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
                    'nama' => $request->input('nama'),
                    'username' => $request->input('username'),
                    'password' => Hash::make($request->input('password')),
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
    }

    public function edit($uid)
    {
        $show = Karyawan::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "karyawan";
        return view('karyawan.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Karyawan::find($id)->update([
        'nama' => $request->input('nama'),
        'nik' => $request->input('nik'),
        'id_jabatan' => $request->input('id_jabatan'),
        'changed_by' => Session::get('userid'),
      ]);  

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
        $data['show'] = $show;
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['perusahaan'] = Perusahaan::get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['previllage'] = Previllage::orderBy('id', 'DESC')->get();
        $data['menu'] = Akses::where('id_karyawan', $show->id)->get();
        $data['active'] = "karyawan";
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
}