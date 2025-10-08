<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Prosedur;
use App\Models\Karyawan;
use App\Models\ViewProsedur;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class ProsedurController extends Controller
{
    public function show()
    {
        $data['active'] = "prosedur";
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
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('prosedur.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Prosedur::create([
          'uid' => Str::uuid()->toString(),
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
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
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

        if(Session::get('previllage')==4){
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
        if(Session::get('previllage')==4){
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
        $show = Prosedur::where('uid', $uid)->first();
        if(Session::get('previllage')==4){
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
        if(Session::get('previllage')==4){
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

    public function viewuser() {
        $get = DB::table('karyawan as a')
                ->leftJoin('user as b', 'a.id', '=', 'b.id_karyawan')
                ->leftJoin('view_prosedur as c', 'b.id', '=', 'c.id_user')
                ->leftJoin('kapal as d', 'd.id', '=', 'a.id_kapal')
                ->select(
                    'b.id', 'a.nama', 'd.nama as kapal',
                    DB::raw('COUNT(CASE WHEN c.jml_lihat > 0 THEN 1 END) as lihat'),
                    DB::raw('COUNT(CASE WHEN c.jml_download > 0 THEN 1 END) as download')
                )
                ->where('a.resign', 'N')
                ->where('a.status', 'A')
                ->whereNotNull('a.id_kapal')
                ->groupBy('b.id', 'a.nama', 'd.nama')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function viewdetail(Request $request) {
        $id = $request->input('id');
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
            ->orderBy('a.id')
            ->get();
        return response()->json(['data' => $get]);
    }
}