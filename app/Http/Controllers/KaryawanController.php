<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Jabatan;
use App\Models\Role;
use App\Models\User;
use App\Models\Akses;
use App\Models\StatusPTKP;
use App\Models\MasterFile;
use App\Models\FileUpload;
use App\Models\Mutasi;
use App\Models\JenisCuti;
use App\Models\Cuti;
use App\Models\KodeForm;
use App\Models\FormISM;
use App\Models\Cabang;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
Use DB;
use App\Exports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\RoleContext;

class KaryawanController extends Controller
{
    public function show()
    {
        $data['active'] = "karyawan";
        $data['perusahaan'] = Perusahaan::get();
        $data['kapal'] = Kapal::where('status', 'A')->get();
        $data['cabang'] = Cabang::where('is_delete', 0)->get();
        return view('karyawan.show', $data);
    }

    public function getData(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $perusahaan = (($roleJenis == 2) or ($roleJenis == 3)) ? Session::get('id_perusahaan') : $request->input('id_perusahaan');
        $kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $kel = $request->input('kel');
        $cabang = $request->input('id_cabang');

        $karyawan = DB::table('karyawan')
                ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id')
                ->leftJoin('perusahaan', 'perusahaan.id', '=', 'karyawan.id_perusahaan')
                ->leftJoin('kapal', 'kapal.id', '=', 'karyawan.id_kapal')
                ->leftJoin('m_cabang', 'm_cabang.id', '=', 'karyawan.id_cabang')
                ->select(
                    'karyawan.id',
                    'karyawan.uid',
                    'karyawan.nama',
                    'karyawan.nik',
                    'karyawan.nip',
                    'kapal.nama as kapal',
                    'jabatan.nama as jabatan',
                    'm_cabang.cabang'
                )
                ->where('karyawan.resign', 'N')
                ->where('karyawan.status','A')
                ->when($kel, function($query, $kel) {
                    return $query->where('jabatan.kel', $kel);
                })
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('perusahaan.id', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('karyawan.id_kapal', $kapal);
                })
                ->when($cabang, function($query, $cabang) {
                    return $query->where('karyawan.id_cabang', $cabang);
                });
        // print_r($kapal);die();
        return DataTables::of($karyawan)
        ->filterColumn('kapal', function($query, $keyword) {
            $query->where('kapal.nama', 'like', "%{$keyword}%");
        })
        ->filterColumn('cabang', function($query, $keyword) {
            $query->where('cabang.cabang', 'like', "%{$keyword}%");
        })
        ->filterColumn('jabatan', function($query, $keyword) {
            $query->where('jabatan.nama', 'like', "%{$keyword}%");
        })
        ->make(true);
    }

    public function add() 
    {
        $data['active'] = "karyawan";
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['perusahaan'] = Perusahaan::where('status','A')->get();
        $ctx = RoleContext::get();
        if($ctx['is_superadmin']) {
            $data['kapal'] = Kapal::where('status', 'A')->get();
        } elseif($ctx['jenis']==2) {
            $id_perusahaan = $ctx['perusahaan_id'];
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
        } else{
            $id_kapal = $ctx['kapal_id'];
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
        }
        $data['roles'] = Role::orderBy('nama')->get();
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
            'kontak_darurat' => $request->input('kontak_darurat'),
            'nama_kontak' => $request->input('nama_kontak'),
            'telp_kontak' => $request->input('telp_kontak'),
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
                'id_previllage' => $request->input('role_id'), // mirror legacy
                'role_id' => $request->input('role_id'),
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
        $ctx = RoleContext::get();
        if($ctx['is_superadmin']) {
            $data['kapal'] = Kapal::where('status', 'A')->get();
        } elseif($ctx['jenis']==2) {
            $id_perusahaan = $ctx['perusahaan_id'];
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
        } else{
            $id_kapal = $ctx['kapal_id'];
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
        }
        $data['roles'] = Role::orderBy('nama')->get();
        $data['ptkp'] = StatusPTKP::get();
        return view('karyawan.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Karyawan::find($id)->update($request->all());  
        if($request->input('nama')) {
             $update = User::where('id_karyawan', $id)->update([
                'nama' => $request->input('nama')
            ]); 
        };
        if($request->input('id_perusahaan')) {
            $update = User::where('id_karyawan', $id)->update([
                'id_perusahaan' => $request->input('id_perusahaan'),
                'id_kapal' => $request->input('id_kapal'),
                'id_previllage' => $request->input('role_id'), // mirror legacy
                'role_id' => $request->input('role_id'),
                'changed_by' => Session::get('userid'),
            ]); 
        }   
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
                    ->select('karyawan.*', 'user.id as user', 'karyawan.id_perusahaan', 'perusahaan.nama as perusahaan', 
                        'karyawan.id_kapal', 'kapal.nama as kapal', 'jabatan.nama as jabatan', 'jabatan.kel', 'user.username', 'user.role_id', 'roles.nama as role_nama')
                    ->leftjoin('user', 'user.id_karyawan', '=', 'karyawan.id')
                    ->leftjoin('roles', 'roles.id', '=', 'user.role_id')
                    ->leftjoin('perusahaan', 'karyawan.id_perusahaan', '=', 'perusahaan.id')
                    ->leftjoin('kapal', 'karyawan.id_kapal', '=', 'kapal.id')
                    ->leftjoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id')
                    ->where('karyawan.uid', $uid)->first();
        if ($show) {
            $show->tanda_tangan_url = $show->tanda_tangan && 
                file_exists(public_path('ttd_karyawan/' . $show->tanda_tangan))
                ? asset('ttd_karyawan/' . $show->tanda_tangan)
                : asset('ttd_karyawan/no-signature.png');
        }
        $id_karyawan = $show->id;
        if($show->kel==1) {
            $type='Crew Laut';
        } else {
            $type='Crew Darat';
        }
        $data['file'] = DB::table('master_file as a')
                ->leftJoin('file_upload as b', function($join) use ($id_karyawan) {
                    $join->on('a.id', '=', 'b.id_file')
                        ->where('b.id_karyawan', $id_karyawan); 
                })
                ->where('a.type', 'S')
                ->where('a.status', 'A')
                ->where(function ($q) use ($type) {
                    $q->where('a.ket', $type)
                    ->orWhereNull('a.ket');
                })
                ->select('a.*', 'b.file', 'b.no', 'b.penerbit', 'b.tgl_terbit', 'tgl_expired')
                ->orderBy('a.no_urut', 'ASC')
                ->get();
        $data['show'] = $show;
        $data['jabatan'] = Jabatan::where('status', 'A')->get();
        $data['perusahaan'] = Perusahaan::get();
        $roleJenis = Session::get('previllage');
         if($roleJenis==1) {
            $data['kapal'] = Kapal::where('status', 'A')->get();
        } elseif($roleJenis==2) {
            $id_perusahaan = Session::get('id_perusahaan');
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
        } else{
            $id_kapal = Session::get('id_kapal');
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
        }
        $data['roles'] = Role::orderBy('nama')->get();
        // $data['menu'] = Akses::where('id_karyawan', $show->id)->get();
        $data['active'] = "karyawan";
        $data['ptkp'] = StatusPTKP::get();
        $data['mutasi'] = Mutasi::where('id_karyawan', $show->id)->orderBy('tgl_naik', 'DESC')->where('status', 'A')->get();
        $data['jeniscuti'] = JenisCuti::where('is_delete', 0)->get();
        $data['karyawan'] = Karyawan::where('id_perusahaan', $show->id_perusahaan)->where('status', 'A')->where('resign', 'N')->get();
        return view('karyawan.profile',$data);
    }

     public function resign(Request $request)
    {
        $id = $request->post('id');
        $post = Karyawan::where('id',$id)->update([
            'resign' => 'Y', 
            'tgl_resign' => $request->post('tgl_resign'),
            'alasan' => $request->post('alasan'),
            'changed_by' => Session::get('userid')
        ]);
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
            'file' => 'nullable|file|mimes:pdf|max:80480',
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
                'tgl_terbit' => $request->input('tgl_terbit'),
                'tgl_expired' => $request->input('tgl_expired'),
                'no' => $request->input('no'),
                'penerbit' => $request->input('penerbit'),
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

    public function crewlist($uid)
    {
        $data['active'] = "form_ism";
        $get = FormISM::where('uid', $uid)->first();
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
        return view('karyawan.crewlist', $data);
    }

    public function pdfcrewlist($uid) {
        $show =  Kapal::where('uid', $uid)->first();
        $nama = $show->nama;
        $id_perusahaan = $show->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 44)->first();
        $crew = DB::table('karyawan as k')
                ->leftJoin('jabatan as j', 'j.id', '=', 'k.id_jabatan')

                // IJAZAH (id=27)
                ->leftJoin('file_upload as ij', function ($join) {
                    $join->on('ij.id_karyawan', '=', 'k.id')
                        ->where('ij.id_file', 27);
                })

                // ENDORSEMENT (id=99)
                ->leftJoin('file_upload as en', function ($join) {
                    $join->on('en.id_karyawan', '=', 'k.id')
                        ->where('en.id_file', 99);
                })

                // BUKU PELAUT (id=28)
                ->leftJoin('file_upload as bp', function ($join) {
                    $join->on('bp.id_karyawan', '=', 'k.id')
                        ->where('bp.id_file', 28);
                })

                ->select(
                    'k.id',
                    'k.nama',
                    'j.nama as jabatan',
                    'ij.no as ijazah_no',
                    'en.no as endorse_no',
                    'en.tgl_expired as endorse_berlaku',
                    'bp.no as buku_no',
                    'bp.tgl_expired as buku_berlaku'
                )
                ->where('k.id_kapal', $show->id)->where('k.status', 'A')->where('k.resign', 'N')
                ->orderBy('j.id', 'ASC')->get();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['crew'] = $crew;   
        $pdf = Pdf::loadView('karyawan.pdfcrewlist', $data)
                ->setPaper('a3', 'portrait');
        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function mutasi($uid)
    {
        $data['active'] = "form_ism";
        $get = FormISM::where('uid', $uid)->first();
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
        $data['karyawan'] = Karyawan::where('status', 'A')->where('resign', 'N')
            ->when($roleJenis == 1 || $roleJenis == 2, function ($q) use ($activeCompany) {
                return $q->where('id_perusahaan', $activeCompany);
            })
            ->when($roleJenis == 3 && $activeShip, function ($q) use ($activeShip) {
                return $q->where('id_kapal', $activeShip);
            })->get();  
            
        $data['form'] = KodeForm::find($get->id_form);
        $data['perusahaan'] = Perusahaan::get();
        $data['id_perusahaan'] = $get->id_perusahaan;
        return view('karyawan.mutasi', $data);
    }

    public function pdfcontact($uid) {
        $show =  Kapal::where('uid', $uid)->first();
        $nama = $show->nama;
        $id_perusahaan = $show->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 46)->first();
        $crew = DB::table('karyawan as k')
                ->leftJoin('jabatan as j', 'j.id', '=', 'k.id_jabatan')
                ->select('k.nama', 'j.nama as jabatan', 'k.kontak_darurat', 'k.nama_kontak', 'k.telp_kontak')
                ->where('k.id_kapal', $show->id)->where('k.status', 'A')->where('k.resign', 'N')
                ->orderBy('j.id', 'ASC')->get();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['crew'] = $crew;   
        $pdf = Pdf::loadView('karyawan.pdfcontact', $data)
                ->setPaper('a3', 'portrait');
        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }

    public function dataresign(Request $request)
    {
        $roleJenis = Session::get('previllage');
         $perusahaan = (($roleJenis == 2) or ($roleJenis == 3)) ? Session::get('id_perusahaan') : null;
        $kapal = ($roleJenis == 3) ? Session::get('id_kapal') : null;
        $kel = $request->input('kel');

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
                    'karyawan.tgl_resign',
                    'karyawan.alasan'
                )
                ->where('karyawan.resign', 'Y')
                ->where('karyawan.status','A')
                ->when($kel, function($query, $kel) {
                    return $query->where('jabatan.kel', $kel);
                })
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('perusahaan.id', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('karyawan.id_kapal', $kapal);
                });
        // print_r($kapal);die();
        return DataTables::of($karyawan)
        ->filterColumn('kapal', function($query, $keyword) {
            $query->where('kapal.nama', 'like', "%{$keyword}%");
        })
        ->filterColumn('jabatan', function($query, $keyword) {
            $query->where('jabatan.nama', 'like', "%{$keyword}%");
        })
        ->make(true);
    }
}
