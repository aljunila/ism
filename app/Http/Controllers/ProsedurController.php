<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Prosedur;
use App\Models\Karyawan;
use App\Models\ViewProsedur;
use App\Models\ChecklistProsedur;
use App\Models\FormISM;
use App\Models\KodeForm;
use App\Models\Kapal;
use App\Models\User;
use Alert;
use Session;
use Carbon\Carbon;
use Str;
use DB;

class ProsedurController extends Controller
{
    public function show()
    {
        $data['active'] = "procedures";
        return view('prosedur.show', $data);
    }

    public function getData(Request $request) {
        $perusahaan = $request->input('id_perusahaan');
        $get = DB::table('prosedur')
                ->leftjoin('karyawan as a', 'a.id', '=', 'prosedur.prepered_by')
                ->leftjoin('karyawan as b', 'b.id', '=', 'prosedur.enforced_by')
                ->select('prosedur.*', 'a.nama as prepered', 'b.nama as enforced')
                ->where('prosedur.status', 'A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('prosedur.id_perusahaan', $perusahaan);
                })
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add() 
    {
        $data['active'] = "prosedur";
        $roleJenis = Session::get('previllage');
        if($roleJenis==1) { // superadmin
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } else {
            $id_perusahaan = Session::get('id_perusahaan');
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        }
        $data['perusahaan'] = Perusahaan::get();
        return view('prosedur.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Prosedur::create([
          'uid' => Str::uuid()->toString(),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'kode' => $request->input('kode'),
          'judul' => $request->input('judul'),
          'no_dokumen' => $request->input('no_dokumen'),
          'edisi' => $request->input('edisi'),
          'tgl_terbit' => $request->input('tgl_terbit'),
          'status_manual' => $request->input('status_manual'),
          'cover' => $request->input('cover'),
          'isi' => $request->input('isi'),
          'prepered_by' => $request->input('prepered_by'),
          'enforced_by' => $request->input('enforced_by'),
          'status' => 'A',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s'),
        ]);

        if($request->hasFile('file')) {
            $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
            ]);
            $file = $request->file('file');
            $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
        
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'file_prosedur';
            $file->move($tujuan_upload,$nama_file);
            $save = Prosedur::find($save->id)->update(['file' => $nama_file]); 
        }
        return;
    }

    public function edit($uid)
    {
        $show = Prosedur::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "prosedur";
        $data['perusahaan'] = Perusahaan::get();
        $roleJenis = Session::get('previllage');
        if($roleJenis==1) { // superadmin
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } else {
            $id_perusahaan = Session::get('id_perusahaan');
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        }
        return view('prosedur.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Prosedur::find($id)->update($request->all());     
    }

    public function delete($id)
    {
       $post = Prosedur::where('id',$id)->update(['status' => 'D']);
       return redirect('/prosedur')->with('danger', 'Data berhasil dihapus');
    }

    public function prosedurPdf($uid) {  
        $show = Prosedur::where('uid', $uid)->first();

        $roleJenis = Session::get('previllage');
        if($roleJenis==4){
            $id_user = Session::get('userid');
            $id_prosedur = $show->id;

            $cek = ViewProsedur::where('id_user', $id_user)->where('id_prosedur', $id_prosedur)->first();
            if($cek) {
                $id = $cek->id;
                $jml = $cek->jml_lihat+1;

                $update = ViewProsedur::where('id', $id)->update(['jml_lihat' => $jml, 'update_lihat' => date('Y-m-d H:i:s')]);
            } else {
                $save = ViewProsedur::create([
                    'id_user' => $id_user,
                    'id_prosedur' => $id_prosedur,
                    'jml_lihat' => 1,
                    'update_lihat' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $data['cover'] = preg_replace_callback(
            '/<img[^>]+src="([^">]+)"/i',
            function ($matches) {
                $url = $matches[1];
                if (str_contains($url, '/storage/')) {
                    // ambil path setelah /storage/
                    $path = str_replace(asset('storage') . '/', '', $url);
                    return '<img src="file://' . public_path('storage/' . $path) . '"';
                }
                return $matches[0];
            },
            $show->cover
        );

        $data['isi'] = preg_replace_callback(
            '/<img[^>]+src="([^">]+)"/i',
            function ($matches) {
                $url = $matches[1];
                if (str_contains($url, '/storage/')) {
                    // ambil path setelah /storage/
                    $path = str_replace(asset('storage') . '/', '', $url);
                    return '<img src="file://' . public_path('storage/' . $path) . '"';
                }
                return $matches[0];
            },
            $show->isi
        );
        $data['show'] = $show;
        $pdf = Pdf::loadView('prosedur.pdf', $data)
                ->setPaper([0, 0, 612, 900], 'portrait');

        return $pdf->stream($show->kode .'-'.$show->judul.'.pdf');
    }

    public function pdfdownload($uid) {
        $show = Prosedur::where('uid', $uid)->first();
        if($roleJenis==4){
            $id_user = Session::get('userid');
            $id_prosedur = $show->id;

            $cek = ViewProsedur::where('id_user', $id_user)->where('id_prosedur', $id_prosedur)->first();
            if($cek) {
                $id = $cek->id;
                $jml = $cek->jml_download+1;

                $update = ViewProsedur::where('id', $id)->update(['jml_download' => $jml, 'update_download' => date('Y-m-d H:i:s')]);
            } else {
                $save = ViewProsedur::create([
                    'id_user' => $id_user,
                    'id_prosedur' => $id_prosedur,
                    'jml_download' => 1,
                    'update_download' => date('Y-m-d H:i:s')
                ]);
            }
        }
        $data['cover'] = preg_replace_callback(
            '/<img[^>]+src="([^">]+)"/i',
            function ($matches) {
                $url = $matches[1];
                if (str_contains($url, '/storage/')) {
                    // ambil path setelah /storage/
                    $path = str_replace(asset('storage') . '/', '', $url);
                    return '<img src="file://' . public_path('storage/' . $path) . '"';
                }
                return $matches[0];
            },
            $show->cover
        );

        $data['isi'] = preg_replace_callback(
            '/<img[^>]+src="([^">]+)"/i',
            function ($matches) {
                $url = $matches[1];
                if (str_contains($url, '/storage/')) {
                    // ambil path setelah /storage/
                    $path = str_replace(asset('storage') . '/', '', $url);
                    return '<img src="file://' . public_path('storage/' . $path) . '"';
                }
                return $matches[0];
            },
            $show->isi
        );
        $data['show'] = $show;
        $pdf = Pdf::loadView('prosedur.pdf', $data)
                ->setPaper([0, 0, 612, 900], 'portrait');

        return $pdf->download($show->kode .'-'.$show->judul.'.pdf');
    }

    public function view()
    {
        $id_perusahaan = Session::get('id_perusahaan');
        $data['show'] = Prosedur::where('status','A')->where('id_perusahaan', $id_perusahaan)->get();
        $data['active'] = "prosedur";
        return view('prosedur.view', $data);
    }

    public function view_file($uid){
        $id_perusahaan = Session::get('id_perusahaan');
        $id_kapal = Session::get('id_kapal');
        $id_karyawan = Session::get('id_karyawan');
        $year = date('Y');
        
        $show = Prosedur::where('uid', $uid)->first();
        if(Session::get('id_kapal')!=0){
            $id_user = Session::get('userid');
            $id_prosedur = $show->id;

            $cek = ViewProsedur::where('id_user', $id_user)->where('id_prosedur', $id_prosedur)->first();
            if($cek) {
                $id = $cek->id;
                $jml = $cek->jml_lihat+1;

                $update = ViewProsedur::where('id', $id)->update(['jml_lihat' => $jml, 'update_lihat' => date('Y-m-d H:i:s')]);
            } else {
                $save = ViewProsedur::create([
                    'id_user' => $id_user,
                    'id_prosedur' => $id_prosedur,
                    'jml_lihat' => 1,
                    'update_lihat' => date('Y-m-d H:i:s')
                ]);
            }

            $cekprosedur = ChecklistProsedur::where('id_karyawan', $id_karyawan)->where('id_kapal', $id_kapal)->first();
            if(empty($cekprosedur)){
                $karyawan = Karyawan::findOrFail($id_karyawan);
                $save = ChecklistProsedur::insert([
                    'id_karyawan'       => $id_karyawan,
                    'id_jabatan'        => $karyawan->id_jabatan,
                    'id_prosedur'       => $id_prosedur,
                    'last_seen'         => date('Y-m-d')
                ]);
            } else {
                $update = ChecklistProsedur::where('id', $cekprosedur->id)->update([
                    'id_prosedur'       => $id_prosedur,
                    'last_seen'         => date('Y-m-d')
                ]);
            }
        }
        $filename = $show->file;
        $path = public_path('file_prosedur/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    public function download_file($uid){
        $show = Prosedur::where('uid', $uid)->first();
        if(Session::get('id_kapal')!=0){
            $id_user = Session::get('userid');
            $id_prosedur = $show->id;

            $cek = ViewProsedur::where('id_user', $id_user)->where('id_prosedur', $id_prosedur)->first();
            if($cek) {
                $id = $cek->id;
                $jml = $cek->jml_download+1;

                $update = ViewProsedur::where('id', $id)->update(['jml_download' => $jml, 'update_download' => date('Y-m-d H:i:s')]);
            } else {
                $save = ViewProsedur::create([
                    'id_user' => $id_user,
                    'id_prosedur' => $id_prosedur,
                    'jml_download' => 1,
                    'update_download' => date('Y-m-d H:i:s')
                ]);
            }

            $cekprosedur = ChecklistProsedur::where('id_karyawan', $id_karyawan)->where('id_kapal', $id_kapal)->first();
            if(empty($cekprosedur)){
                $karyawan = Karyawan::findOrFail($id_karyawan);
                $save = ChecklistProsedur::insert([
                    'id_karyawan'       => $id_karyawan,
                    'id_jabatan'        => $karyawan->id_jabatan,
                    'id_prosedur'       => $id_prosedur,
                    'last_seen'         => date('Y-m-d')
                ]);
            } else {
                $update = ChecklistProsedur::where('id', $cekprosedur->id)->update([
                    'id_prosedur'       => $id_prosedur,
                    'last_seen'         => date('Y-m-d')
                ]);
            }
        }
        $filename = $show->file;
        $path = public_path('file_prosedur/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->download($path, [
            'Content-Type' => mime_content_type($path),
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    public function viewuser(Request $request) {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal');

        $get = DB::table('user as b')
                ->leftJoin('karyawan as a', 'a.id', '=', 'b.id_karyawan')
                ->leftJoin('view_prosedur as c', 'b.id', '=', 'c.id_user')
                ->leftJoin('kapal as d', 'd.id', '=', 'b.id_kapal')
                ->select(
                    'b.id', 'a.nama', 'd.nama as kapal',
                    DB::raw('COUNT(CASE WHEN c.jml_lihat > 0 THEN 1 END) as lihat'),
                    DB::raw('COUNT(CASE WHEN c.jml_download > 0 THEN 1 END) as download')
                )
                ->where('a.resign', 'N')
                ->where('a.status', 'A')
                ->whereNotNull('b.id_kapal')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('b.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('b.id_kapal', $kapal);
                })
                ->groupBy('b.id', 'a.nama', 'd.nama')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function viewdetail(Request $request) {
        $id = $request->input('id');
        $get = User::where('id', $id)->first();
        $id_perusahaan = $get->id_perusahaan;
        $get = DB::table('prosedur as a')
            ->leftJoin('view_prosedur as b', function($join) use ($id) {
                $join->on('a.id', '=', 'b.id_prosedur')
                     ->where('b.id_user', '=', $id);
            })
            ->select(
                'a.kode',
                'b.jml_lihat',
                'b.jml_download',
                'b.update_lihat',
                'b.update_download'
            )
            ->where('a.status', 'A')
            ->where('a.id_perusahaan', $id_perusahaan)
            ->orderBy('a.id')
            ->get();
        $data = $get->map(function ($item) {
            return [
                'kode' => $item->kode,
                'jml_lihat' => $item->jml_lihat,
                'jml_download' => $item->jml_download,
                'update_lihat' => $item->update_lihat 
                    ? Carbon::parse($item->update_lihat)->addHours(7)->format('d-m-Y H:i')
                    : '-',
                'update_download' => $item->update_download 
                    ? Carbon::parse($item->update_download)->addHours(7)->format('d-m-Y H:i')
                    : '-',
            ];
        });
        return response()->json(['data' => $data]);
    }

    public function kapallist($uid)
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
        return view('prosedur.elemen', $data);
    }

    public function pdfchecklist($uid){
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
                ->where('a.id', 93)->first();
        $get = "SELECT a.nama, a.id_jabatan, a.tanda_tangan, b.last_seen FROM karyawan a left join t_checklist_prosedur b on (a.id=b.id_karyawan and a.id_kapal = b.id_jabatan) where a.id_kapal=2";
        $doc = DB::table(' as a')
                ->leftJoin('master_file as b', 'a.id_file', '=', 'b.id')
                ->select(
                    'b.*',
                    'a.no',
                    'a.tgl_terbit',
                    'a.tgl_expired',
                    'a.penerbit'
                )
                ->where('a.id_kapal', $show->id)->where('b.status', 'A')->where('a.status', 'A')->where('b.type', 'K')
                ->whereNotNull('a.tgl_expired')
                ->orderBy('b.no_urut', 'ASC')->get();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['doc'] = $doc;   
        $pdf = Pdf::loadView('kapal.pdfdoclist', $data)
                ->setPaper('a3', 'portrait');
        return $pdf->stream($data['form']->ket.' '.$nama.'.pdf');
    }
}
