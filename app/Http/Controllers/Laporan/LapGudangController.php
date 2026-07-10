<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\KelBarang;
use App\Models\Kapal;
use App\Models\Cabang;
use App\Models\KodeForm;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\FormISM;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;
use App\Exports\LapGudangExport;
use Maatwebsite\Excel\Facades\Excel;

class LapGudangController extends Controller
{
    public function index()
    {
        $data['active'] = "lapgudang";  
        return view('laporan.gudang.index', $data);
    }

    public function data(Request $request) {
        $id_perusahaan = $request->input('id_perusahaan');
        $roleJenis = Session::get('previllage');
        $activeCompany = Session::get('id_perusahaan');
        $activeShip = Session::get('id_kapal');   
        $query = Kapal::where('status', 'A')
            ->when($roleJenis == 2 && $activeCompany, function ($q) use ($activeCompany) {
                return $q->where('pemilik', $activeCompany);
            })
            ->when($roleJenis == 3 && $activeShip, function ($q) use ($activeShip) {
                return $q->where('id', $activeShip);
            })
            ->when($id_perusahaan, function ($q) use ($id_perusahaan) {
                return $q->where('pemilik', $id_perusahaan);
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('cabang', function ($row) {
                $cabang = Cabang::find($row->id_cabang);
                return $cabang ? $cabang->cabang : '-';
            })
            ->make(true);
    }

    public function pdf($uid) {
        $show = Kapal::where('uid', $uid)->first();
        $nama = $show->nama;
        $id_perusahaan = $show->pemilik;
        $form = DB::table('kode_form as a')
                ->leftJoin('t_ism as b', function($join) use ($id_perusahaan) {
                    $join->on('a.id', '=', 'b.id_form')
                        ->where('b.id_perusahaan', $id_perusahaan)
                        ->where('b.is_delete', 0);
                })
                ->select('a.*', 'b.judul')
                ->where('a.id', 84)->first();
        $data['show'] = $show;
        $data['form'] = $form;
        $data['perusahaan'] = Perusahaan::find($id_perusahaan);
        $kel = KelBarang::where('is_delete', 0)->get();
        foreach ($kel as $k) {
            $get[$k->id] = DB::table('t_gudang as a')
                            ->leftJoin('m_barang as b', 'a.id_barang', '=', 'b.id')
                            ->select('a.*', 'b.nama as barang', 'b.kode', 'b.deskripsi as des')
                            ->where('b.id_kel_barang', $k->id)->where('b.is_delete', 0)
                            ->where('a.id_kapal', $show->id)
                            ->get();
        }
        $data['kel'] = $kel;
        $data['gudang'] = $get; 
        $pdf = Pdf::loadView('laporan.gudang.pdf', $data)
                ->setPaper('a3', 'landscap');
        return $pdf->stream($form->ket.' '.$nama.'.pdf');
    }

    public function elemen($uid)
    {
        $data['active'] = "form_ism";  
        $get = FormISM::where('uid', $uid)->first();; 
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
        return view('laporan.gudang.elemen', $data);
    }

     public function export(Request $request)
    {
        $id = $request->input('id');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        return Excel::download(new LapGudangExport($id, $start, $end), 'lap_gudang.xlsx');
    }
}
