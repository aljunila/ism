<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\MasterFile;
use App\Models\FileUpload;
use Alert;
use Session;
Use Carbon\Carbon;
use Str;
use DB;
use App\Exports\KapalExport;
use Maatwebsite\Excel\Facades\Excel;

class KapalController extends Controller
{
    public function show()
    {
        $data['daftar'] = Kapal::where('status', 'A')->get();
        $data['active'] = "kapal";
        return view('kapal.show', $data);
    }

    public function getData(Request $request) {
        $perusahaan = $request->input('id_perusahaan');
        $kapal = Session::get('previllage') >= 3 ? Session::get('id_kapal') : null;
        $get = DB::table('kapal')
                ->leftjoin('perusahaan', 'perusahaan.id', '=', 'kapal.pemilik')
                ->select('kapal.*', 'perusahaan.nama as perusahaan')
                ->where('kapal.status', 'A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('perusahaan.id', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('kapal.id', $kapal);
                })
                ->get();
        return response()->json(['data' => $get]);
    }

    public function add() 
    {
        $data['active'] = "kapal";
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
        } else {
            $id_perusahaan = Session::get('id_perusahaan');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
        }
        return view('kapal.add', $data);
    }
  
    public function store(Request $request)
    {
        $created = Session::get('username');
        $date = date('Y-m-d H:i:s');
        $save = Kapal::create([
          'uid' => Str::uuid()->toString(),
          'nama' => strtoupper($request->input('nama')),
          'pendaftaran' => $request->input('pendaftaran'),
          'no_siup' => $request->input('no_siup'),
          'no_akte' => $request->input('no_akte'),
          'dikeluarkan_di' => $request->input('dikeluarkan_di'),
          'selar' => $request->input('selar'),
          'pemilik' => $request->input('pemilik'),
          'call_sign' => $request->input('call_sign'),
          'galangan' => $request->input('galangan'),
          'konstruksi' => $request->input('konstruksi'),
          'type' => $request->input('type'),
          'loa' => $request->input('loa'),
          'lbp' => $request->input('lbp'),
          'lebar' => $request->input('lebar'),
          'dalam' => $request->input('dalam'),
          'summer_draft' => $request->input('summer_draft'),
          'winter_draft' => $request->input('winter_draft'),
          'draft_air_tawar' => $request->input('draft_air_tawar'),
          'tropical_draft' => $request->input('tropical_draft'),
          'isi_kotor' => $request->input('isi_kotor'),
          'bobot_mati' => $request->input('bobot_mati'),
          'nt' => $request->input('nt'),
          'merk_mesin_induk' => $request->input('merk_mesin_induk'),
          'tahun_mesin_induk' => $request->input('tahun_mesin_induk'),
          'no_mesin_induk' => $request->input('no_mesin_induk'),
          'merk_mesin_bantu' => $request->input('merk_mesin_bantu'),
          'tahun_mesin_bantu' => $request->input('tahun_mesin_bantu'),
          'no_mesin_bantu' => $request->input('no_mesin_bantu'),
          'max_speed' => $request->input('max_speed'),
          'normal_speed' => $request->input('normal_speed'),
          'min_speed' => $request->input('min_speed'),
          'bahan_bakar' => $request->input('bahan_bakar'),
          'jml_butuh' => $request->input('jml_butuh'),
          'status' => 'A',
          'created_by' => Session::get('userid'),
          'created_date' => date('Y-m-d H:i:s'),
          ]);
        return redirect()->route('kapal')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($uid)
    {
        $show = Kapal::where('uid', $uid)->first();
        $data['show'] = $show;
        $data['active'] = "Kapal";
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::where('status','A')->get();
        } else {
            $id_perusahaan = Session::get('id_perusahaan');
            $data['perusahaan'] = Perusahaan::where('status','A')->where('id', $id_perusahaan)->get();
        }
        return view('kapal.edit',$data);
    }

    public function update(Request $request, $id)
    {
      $post = Kapal::find($id)->update([
          'nama' => strtoupper($request->input('nama')),
          'pendaftaran' => $request->input('pendaftaran'),
          'no_siup' => $request->input('no_siup'),
          'no_akte' => $request->input('no_akte'),
          'dikeluarkan_di' => $request->input('dikeluarkan_di'),
          'selar' => $request->input('selar'),
          'pemilik' => $request->input('pemilik'),
          'call_sign' => $request->input('call_sign'),
          'galangan' => $request->input('galangan'),
          'konstruksi' => $request->input('konstruksi'),
          'type' => $request->input('type'),
          'loa' => $request->input('loa'),
          'lbp' => $request->input('lbp'),
          'lebar' => $request->input('lebar'),
          'dalam' => $request->input('dalam'),
          'summer_draft' => $request->input('summer_draft'),
          'winter_draft' => $request->input('winter_draft'),
          'draft_air_tawar' => $request->input('draft_air_tawar'),
          'tropical_draft' => $request->input('tropical_draft'),
          'isi_kotor' => $request->input('isi_kotor'),
          'bobot_mati' => $request->input('bobot_mati'),
          'nt' => $request->input('nt'),
          'merk_mesin_induk' => $request->input('merk_mesin_induk'),
          'tahun_mesin_induk' => $request->input('tahun_mesin_induk'),
          'no_mesin_induk' => $request->input('no_mesin_induk'),
          'merk_mesin_bantu' => $request->input('merk_mesin_bantu'),
          'tahun_mesin_bantu' => $request->input('tahun_mesin_bantu'),
          'no_mesin_bantu' => $request->input('no_mesin_bantu'),
          'max_speed' => $request->input('max_speed'),
          'normal_speed' => $request->input('normal_speed'),
          'min_speed' => $request->input('min_speed'),
          'bahan_bakar' => $request->input('bahan_bakar'),
          'jml_butuh' => $request->input('jml_butuh'),
          'status' => 'A',
          'changed_by' => Session::get('userid'),
      ]);     
      return redirect('/kapal')->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
       $post = Kapal::where('id',$id)->update(['status' => 'D']);
       return redirect('/kapal')->with('danger', 'Data berhasil dihapus');
    }

    public function profil($uid)
    {
        $show = Kapal::where('uid',$uid)->first();
        $id_kapal = $show->id;
        $data['show'] = $show;
        $data['active'] = "kapal";
        $data['file'] = DB::table('master_file as a')
                ->leftJoin('file_upload as b', function($join) use ($id_kapal) {
                    $join->on('a.id', '=', 'b.id_file')
                        ->where('b.id_kapal', $id_kapal); 
                })
                ->where('a.type', 'K')
                ->where('a.status', 'A')
                ->select('a.*', 'b.file')
                ->get();
        return view('kapal.profile',$data);
    }

    public function getKapal($id_perusahaan)
    {
        $kapal = DB::table('kapal as a')
                    ->join('perusahaan as b', 'b.id', '=', 'a.pemilik', 'left')
                    ->select('a.id', 'a.nama')
                    ->where('a.pemilik', $id_perusahaan)->where('a.status','A')
                    ->get();
        return response()->json($kapal);
    }

    public function export(Request $request)
    {
        $id_perusahaan = $request->input('id_perusahaan');

        return Excel::download(new KapalExport($id_perusahaan), 'data_kapal.xlsx');
    }

    public function savefile(Request $request, $id)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $cek = FileUpload::where('id_file', $id)->where('id_kapal', $request->input('id_kapal'))->first();

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
                'id_kapal' => $request->input('id_kapal'),
                'id_file'  => $id,
                'file' => $nama_file,
                'status' => 'A',
                'created_by' => Session::get('userid'),
            ]); 
            return response()->json($save);
        }
    }

    public function pdf($uid) {
        $show =  Kapal::where('uid', $uid)->first();
        $nama = $show->nama;
        $data['show'] = $show;
        $pdf = Pdf::loadView('kapal.pdf', $data)
                ->setPaper('a3', 'portrait');

        return $pdf->stream($nama.'.pdf');
    }
}