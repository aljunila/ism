<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Aturan;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class AturanController extends Controller
{
    public function show()
    {
        $data['active'] = "elemen2";
        return view('aturan.show', $data);
    }

    public function getData(Request $request) {
        $perusahaan = $request->input('id_perusahaan');
        $get = DB::table('form_aturan')
                ->leftjoin('karyawan as b', 'b.id', '=', 'form_aturan.enforced_by')
                ->select('form_aturan.*', 'b.nama as enforced')
                ->where('form_aturan.status', 'A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('form_aturan.id_perusahaan', $perusahaan);
                })
                ->orderBy('form_aturan.id', 'DESC')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add() 
    {
        $data['active'] = "elemen2";
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $id_kapal)->get();
        }
        return view('aturan.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Aturan::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'nama' => $request->input('nama'),
          'isi' => $request->input('isi'),
          'enforced_by' => $request->input('enforced_by'),
          'publish' => $request->input('publish'),
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
            $tujuan_upload = 'file_elemen';
            $file->move($tujuan_upload,$nama_file);
            $savefile = Aturan::find($save->id)->update(['file' => $nama_file]); 
        }
        return;
    }

    public function edit($uid)
    {
        $show = Aturan::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "elemen2";
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $id_kapal)->get();
        }
        return view('aturan.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Aturan::find($id)->update($request->all());   
      
       if($request->hasFile('file')) {
            $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
            ]);
            $file = $request->file('file');
            $nama_file = time()."_".str_replace(" ","_",$file->getClientOriginalName());
        
            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'file_elemen';
            $file->move($tujuan_upload,$nama_file);
            $savefile = Aturan::find($id)->update(['file' => $nama_file]); 
        }
    }

    public function delete($id)
    {
       $post = Aturan::where('id',$id)->update(['status' => 'D']);
       return ;
    }

    public function aturanPdf($uid) {
        $show = Aturan::where('uid', $uid)->first();
        $data['show'] = $show;
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
        $pdf = Pdf::loadView('aturan.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream('Form '.$show->kode.'.pdf');
    }

    public function getKaryawan($id_perusahaan)
    {
        $karyawan = DB::table('karyawan as a')
                    ->select('a.id', 'a.nama')
                    ->where('a.id_perusahaan', $id_perusahaan)->where('a.status','A')->where('a.resign', 'N')
                    ->get();
        return response()->json($karyawan);
    }
}