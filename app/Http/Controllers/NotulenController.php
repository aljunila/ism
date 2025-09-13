<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Notulen;
use App\Models\Karyawan;
use Alert;
use Session;
\Carbon\Carbon::setLocale('id');
use Str;
use DB;

class NotulenController extends Controller
{
    public function show()
    {
        $data['active'] = "el0301";
        return view('notulen.show', $data);
    }

    public function getData() {
        $get = DB::table('notulen as a')
                ->leftjoin('karyawan as b', 'b.id', '=', 'a.id_nahkoda')
                ->leftjoin('karyawan as c', 'c.id', '=', 'a.id_notulen')
                ->select('a.*', 'b.nama as nahkoda', 'c.nama as notulen')
                ->where('a.status', 'A')->get();
        return response()->json(['data' => $get]);
    }

    public function add() 
    {
        $data['active'] = "el0301";
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        return view('notulen.add', $data);
    }
  
    public function store(Request $request)
    {
        $save = Notulen::create([
          'uid' => Str::uuid()->toString(),
          'tanggal' => $request->input('tanggal'),
          'tempat' => $request->input('tempat'),
          'materi' => $request->input('materi'),
          'id_nahkoda' => $request->input('id_nahkoda'),
          'id_notulen' => $request->input('id_notulen'),
          'hal' => $request->input('hal'),
          'status' => 'A',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s'),
        ]);
        return;
    }

    public function edit($uid)
    {
        $show = Notulen::where('uid', $uid)->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
        $data['show'] = $show;
        $data['active'] = "el0301";
        return view('notulen.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Notulen::find($id)->update($request->all());     
    }

    public function delete($id)
    {
       $post = Notulen::where('id',$id)->update(['status' => 'D']);
       return ;
    }

    public function notulenPdf($uid) {
        $show = Notulen::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['materi'] = preg_replace_callback(
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
            $show->materi
        );
        $pdf = Pdf::loadView('notulen.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->download('EL-03-01 '.$show->tanggal.'.pdf');
    }
}