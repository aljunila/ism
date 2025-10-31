<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Notulen;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Karyawan;
use App\Models\KodeForm;
use App\Models\Agenda;
use App\Models\DaftarHadir;
use App\Models\DaftarHadirDetail;
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

    public function getData(Request $request) {
        $kode = $request->input('kode');
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

        $get = DB::table('notulen as a')
                ->leftjoin('karyawan as b', 'b.id', '=', 'a.id_nahkoda')
                ->leftjoin('karyawan as c', 'c.id', '=', 'a.id_notulen')
                ->select('a.*', 'b.nama as nahkoda', 'c.nama as notulen')
                ->where('a.status', 'A')
                ->where('a.kode', $kode)
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('a.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('a.id_kapal', $kapal);
                })
                ->orderBy('a.id', 'DESC')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add() 
    {
        $data['active'] = "el0301";
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
        return view('notulen.add', $data);
    }
  
    public function store(Request $request)
    {
        $save = Notulen::create([
          'uid' => Str::uuid()->toString(),
          'kode' => $request->input('kode'),
          'id_perusahaan' => $request->input('id_perusahaan'),
          'id_kapal' => $request->input('id_kapal'),
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

        $save_hadir = DaftarHadir::create([
            'uid' => Str::uuid()->toString(),
            'kode' => $request->input('kode'),
            'id_perusahaan' => $request->input('id_perusahaan'),
            'id_kapal' => $request->input('id_kapal'),
            'id_notulen' => $save->id,
            'status' => 'A',
            'created_by' => Session::get('userid'),
            'created_date' => date('Y-m-d H:i:s'),
        ]);

        if($request->input('agenda')) {
            $agendas = $request->input('agenda'); 
            $kets  = $request->input('ket');
            foreach ($agendas as $iditem => $value) {
                $keterangan = $kets[$iditem] ?? null;
                $item = Agenda::insert([
                    'uid' => Str::uuid()->toString(),
                    'kode' => $save->kode,
                    'id_notulen' => $save->id,
                    'agenda' => $value,
                    'ket' => $keterangan,
                    'status' => 'A',
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d')
                ]);
            }
        }

        return;
    }

    public function edit($uid)
    {
        $show = Notulen::where('uid', $uid)->first();
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
        $data['show'] = $show;
        $data['active'] = "el0301";
        return view('notulen.edit',$data);
    }

    public function update(Request $request, $id)
    {
        $post = Notulen::find($id)->update($request->all()); 
        $show = Notulen::where('id', $id)->first();
        if($request->input('agenda')) {
            $agendas = $request->input('agenda'); 
            $kets  = $request->input('ket');
            foreach ($agendas as $iditem => $value) {
                $keterangan = $kets[$iditem] ?? null;
                $item = Agenda::insert([
                    'uid' => Str::uuid()->toString(),
                    'kode' => $show->kode,
                    'id_notulen' => $show->id,
                    'agenda' => $value,
                    'ket' => $keterangan,
                    'status' => 'A',
                    'created_by' => Session::get('userid'),
                    'created_date' => date('Y-m-d')
                ]);
            }
        }   
    }

    public function delete($id)
    {
       $post = Notulen::where('id',$id)->update(['status' => 'D', 'changed_by' => Session::get('userid')]);
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

        return $pdf->stream('EL-03-01 '.$show->tanggal.'.pdf');
    }

    public function el0402()
    {
        $data['active'] = "el0402";
        $data['form'] = KodeForm::where('kode', 'el0402')->first();
        return view('notulen.elemen4', $data);
    }

    public function el0403()
    {
        $data['active'] = "el0403";
        $data['form'] = KodeForm::where('kode', 'el0403')->first();
        return view('notulen.elemen4', $data);
    }

    public function el0404()
    {
        $data['active'] = "el0404";
        $data['form'] = KodeForm::where('kode', 'el0404')->first();
        return view('notulen.elemen4', $data);
    }

    public function getData4(Request $request) {
        $kode = $request->input('kode');
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

        $get = DB::table('notulen as a')
                ->leftjoin('karyawan as b', 'b.id', '=', 'a.id_nahkoda')
                ->leftjoin('karyawan as c', 'c.id', '=', 'a.id_notulen')
                ->select('a.*', 'b.nama as nahkoda', 'c.nama as notulen')
                ->where('a.status', 'A')
                ->where('a.kode', $kode)
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('a.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('a.id_kapal', $kapal);
                })
                ->orderBy('a.id', 'DESC')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add4($kode) 
    {
        $data['form'] = KodeForm::where('kode', $kode)->first();
        $data['active'] = $kode;
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
        return view('notulen.add4', $data);
    }

    public function edit4($uid)
    {
        $show = Notulen::where('uid', $uid)->first();
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')->get();
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
        $data['show'] = $show;
        $data['active'] = $show->kode;
        return view('notulen.edit4',$data);
    }

    public function GetAgenda(Request $request) {
       $get = Agenda::where('id_notulen', $request->input('id'))
                ->where('status','A')
                ->get();
        return response()->json(['data' => $get]);
    }

     public function deleteagenda($id)
    {
       $post = Agenda::where('id',$id)->update(['status' => 'D']);
       return ;
    }

    public function Pdf($uid) {
        $show = Notulen::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $data['agenda'] = Agenda::where('id_notulen', $show->id)->where('status', 'A')->get();
        $get = DaftarHadir::where('id_notulen', $show->id)->first();
        $data['detail'] = DaftarHadirDetail::where('id_daftar_hadir', $get->id)->get();
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
        $pdf = Pdf::loadView('notulen.pdf4', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($data['form']->ket.' '.$show->tanggal.'.pdf');
    }

    public function hadir($uid)
    {
        $show = Notulen::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = $show->kode;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $kapal = $show->id_kapal;
        $data['karyawan'] = Karyawan::where('status','A')->where('resign', 'N')
                            ->where('id_perusahaan', $show->id_perusahaan)
                            ->where(function($q) use($kapal) {
                                $q->where('id_kapal', $kapal)
                                ->orWhereNull('id_kapal');
                            })->get();
        $get = DaftarHadir::where('id_notulen', $show->id)->first();
        $data['id_hadir'] = $get->id;
        $data['detail'] = DaftarHadirDetail::where('id_daftar_hadir', $get->id)->get();
        return view('notulen.hadir',$data);
    }

     public function el0405()
    {
        $data['active'] = "el0405";
        $data['form'] = KodeForm::where('kode', 'el0405')->first();
        return view('notulen.daftar', $data);
    }

     public function gethadir(Request $request) {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = $request->input('id_kapal') ? $request->input('id_kapal') : null;

        $get = DB::table('daftar_hadir as a')
                ->leftjoin('kode_form as b', 'b.kode', '=', 'a.kode')
                ->leftjoin('notulen as c', 'c.id', '=', 'a.id_notulen')
                ->select('a.id', 'a.uid', 'b.ket as ket', 'c.tanggal', 'c.tempat')
                ->where('a.status', 'A')
                ->where('c.status', 'A')
                ->where('a.kode', 'like', 'el04%')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('c.id_perusahaan', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('c.id_kapal', $kapal);
                })
                ->orderBy('a.id', 'DESC')
                ->get();
        return response()->json(['data' => $get]);
    }

    public function Pdfhadir($uid) {
        $get = DaftarHadir::where('uid', $uid)->first();
        $data['detail'] = DaftarHadirDetail::where('id_daftar_hadir', $get->id)->get();
        $show = Notulen::where('id', $get->id_notulen)->first();
        $data['show'] = $show;
        $data['form'] = KodeForm::where('kode', $show->kode)->first();
        $pdf = Pdf::loadView('notulen.pdfhadir', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream('Daftar Hadir '.$data['form']->ket.' '.$show->tanggal.'.pdf');
    }
}