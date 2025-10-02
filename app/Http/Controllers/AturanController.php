<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Aturan;
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

    public function getData() {
        $get = DB::table('form_aturan')
                ->leftjoin('karyawan as b', 'b.id', '=', 'form_aturan.enforced_by')
                ->select('form_aturan.*', 'b.nama as enforced')
                ->where('form_aturan.status', 'A')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add() 
    {
        $data['active'] = "elemen2";
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('aturan.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Aturan::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'nama' => $request->input('nama'),
          'isi' => $request->input('isi'),
          'enforced_by' => $request->input('enforced_by'),
          'publish' => 'Y',
          'status' => 'A',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s'),
        ]);
        return;
    }

    public function edit($uid)
    {
        $show = Aturan::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "elemen2";
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('aturan.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Aturan::find($id)->update($request->all());     
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

        return $pdf->stream('Form'.$show->kode.'.pdf');
    }
}