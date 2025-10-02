<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Prosedur;
use App\Models\Karyawan;
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

    public function getData() {
        $get = DB::table('prosedur')
                ->leftjoin('karyawan as a', 'a.id', '=', 'prosedur.prepered_by')
                ->leftjoin('karyawan as b', 'b.id', '=', 'prosedur.enforced_by')
                ->select('prosedur.*', 'a.nama as prepered', 'b.nama as enforced')
                ->where('prosedur.status', 'A')
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
        $data['show'] = Prosedur::where('status','A')->get();
        $data['active'] = "prosedur";
        return view('prosedur.view', $data);
    }
}