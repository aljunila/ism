<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\RefrensiDoc;
use App\Models\Karyawan;
use App\Models\KodeForm;
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
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('refrensi.show', $data);
    }

    public function getData(Request $request)
    {
        $daftar = DB::table('refrensi_doc')
                ->leftjoin('karyawan', 'karyawan.id', '=', 'refrensi_doc.id_pj')
                ->select('refrensi_doc.*', 'karyawan.nama as pj')
                ->where('refrensi_doc.kode', $request->input('kode'))
                ->where('refrensi_doc.status','A')
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
            'file' => 'required|file|mimes:doc,docx,pdf|max:20480',
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
          'nama_doc' => $request->input('nama_doc'),
          'edisi' => $request->input('edisi'),
          'id_pj' => $request->input('id_pj'),
          'lokasi' => $request->input('lokasi'),
          'status' => 'A',
          'changed_by' => Session::get('userid'),
        ]);     
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
        return view('refrensi.show', $data);
    }

    public function el0103()
    {
        $data['active'] = "el0103";
        $data['refrensi'] = KodeForm::where('kode', 'el0103')->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('refrensi.show', $data);
    }

    public function el0104()
    {
        $data['active'] = "el0104";
        $data['refrensi'] = KodeForm::where('kode', 'el0104')->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('refrensi.show', $data);
    }

    public function pdf($kode) {
        $show = RefrensiDoc::where('kode', $kode)->where('status', 'A')->get();
        $data['refrensi'] = KodeForm::where('kode', $kode)->first();
        $data['show'] = $show;
        $pdf = Pdf::loadView('refrensi.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->download('Form '.$kode.'.pdf');
    }
}