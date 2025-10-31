<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\RefrensiDoc;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Perusahaan;
use App\Models\Kapal;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
use DB;

class RefrensiDocController extends Controller
{
    public function el0101()
    {
        $data['active'] = "el0101";
        $data['refrensi'] = KodeForm::where('kode', 'el0101')->first();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_perusahaan', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->where('id_kapal', $id_kapal)->get();
        }

        return view('refrensi.show', $data);
    }

    public function getData(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;
       
        $daftar = DB::table('refrensi_doc')
                ->leftjoin('karyawan', 'karyawan.id', '=', 'refrensi_doc.id_pj')
                ->leftjoin('perusahaan', 'perusahaan.id', '=', 'refrensi_doc.id_perusahaan')
                ->leftjoin('kapal', 'kapal.id', '=', 'refrensi_doc.id_kapal')
                ->select('refrensi_doc.*', 'karyawan.nama as pj', 'kapal.nama as kapal', 'perusahaan.nama as perusahaan')
                ->where('refrensi_doc.kode', $request->input('kode'))
                ->where('refrensi_doc.status','A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('refrensi_doc.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('refrensi_doc.id_kapal', $kapal);
                })
                ->orderBy('refrensi_doc.id', 'DESC')
                ->get();

        return response()->json([
            'data' => $daftar
        ]);
    }

    public function store(Request $request)
    {
        $created = Session::get('userid');
        $date = date('Y-m-d H:i:s');
        $save = RefrensiDoc::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('idp'),
          'id_kapal' => $request->input('idk'),
          'nama_doc' => $request->input('nama_doc'),
          'edisi' => $request->input('edisi'),
          'id_pj' => $request->input('id_pj'),
          'lokasi' => $request->input('lokasi'),
          'status' => 'A',
          'created_by' => $created,
          'created_date' => $date
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
            $savefile = RefrensiDoc::find($save->id)->update(['file' => $nama_file]); 
        }
        if($save) {
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $data = RefrensiDoc::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
      $post = RefrensiDoc::where('id',$id)->update([
          'id_perusahaan' => $request->input('idp'),
          'id_kapal' => $request->input('idk'),
          'nama_doc' => $request->input('nama_doc'),
          'edisi' => $request->input('edisi'),
          'id_pj' => $request->input('id_pj'),
          'lokasi' => $request->input('lokasi'),
          'status' => 'A',
          'changed_by' => Session::get('userid'),
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
            $savefile = RefrensiDoc::find($id)->update(['file' => $nama_file]); 
        }
      return response()->json(['success' => true]);
    }

    public function delete($id)
    {
       $post = RefrensiDoc::where('id',$id)->update(['status' => 'D']);
        return response()->json(['success' => true]);
    }

    public function el0102()
    {
        $data['active'] = "el0102";
        $data['refrensi'] = KodeForm::where('kode', 'el0102')->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
        }
        return view('refrensi.show', $data);
    }

    public function el0103()
    {
        $data['active'] = "el0103";
        $data['refrensi'] = KodeForm::where('kode', 'el0103')->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
        }
        return view('refrensi.show', $data);
    }

    public function el0104()
    {
        $data['active'] = "el0104";
        $data['refrensi'] = KodeForm::where('kode', 'el0104')->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $id_perusahaan = Session::get('id_perusahaan');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
            $data['kapal'] = Kapal::where('status', 'A')->get();
        } elseif(Session::get('previllage')==2) {
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', $id_perusahaan)->get();
        } else {
            $id_kapal = Session::get('id_kapal');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
            $data['kapal'] = Kapal::where('status', 'A')->where('id', $id_kapal)->get();
        }
        return view('refrensi.show', $data);
    }

    public function pdf(Request $request) {
        $kode = $request->input('kode');
        $id_perusahaan = $request->input('id_perusahaan');

        $perusahaan = Perusahaan::findOrFail($id_perusahaan);
        $data['show']= RefrensiDoc::where('kode', $kode)->where('id_perusahaan', $id_perusahaan)->where('status', 'A')->orderBy('id','DESC')->get();
        $form = KodeForm::where('kode', $kode)->first();
        $data['refrensi'] = $form;
        $data['perusahaan'] = $perusahaan;
        $pdf = Pdf::loadView('refrensi.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream('Form '.$form->ket.' '.$perusahaan->kode.'.pdf');
    }
}