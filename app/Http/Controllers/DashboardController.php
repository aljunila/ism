<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Kapal;
use Alert;
use Session;
Use Carbon\Carbon;
Use DB;

class DashboardController extends Controller
{
    public function show()
    {
        $data['active'] = "dashboard";
        $data['pre'] = Session::get('previllage');
        if(Session::get('previllage')==1) {
            $data['perusahaan'] = Perusahaan::count();
            $data['kapal'] = Kapal::where('status','A')->count();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->count();
        } elseif(Session::get('previllage')==2) {
            $id_perusahaan = Session::get('id_perusahaan');
            $data['kapal'] = Kapal::where('status','A')->where('pemilik', $id_perusahaan)->count();
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->where('id_perusahaan', $id_perusahaan)->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->where('karyawan.id_perusahaan', $id_perusahaan)
                            ->count();
        } elseif(Session::get('previllage')==3) {
            $id_kapal = Session::get('id_kapal');
            $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->where('id_kapal', $id_kapal)->count();
            $data['user'] = DB::table('user')
                            ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                            ->where('karyawan.status','A')->where('karyawan.resign','N')
                            ->where('karyawan.id_kapal', $id_kapal)
                            ->count();
        } else {
            $id_user = Session::get('userid');
            $id_perusahaan = Session::get('id_perusahaan');

            $data['prosedur'] = DB::table('prosedur as a')
                ->leftJoin('view_prosedur as b', function($join) use ($id_user) {
                    $join->on('a.id', '=', 'b.id_prosedur')
                        ->where('b.id_user', '=', $id_user);
                })
                ->select(
                    'a.kode',
                    'b.jml_lihat',
                    'b.jml_download',
                    'b.update_lihat',
                    'b.update_download'
                )
                ->where('a.status', 'A')
                ->where('a.id_perusahaan', $id_perusahaan)
                ->orderBy('a.id')
                ->get();
        }
        return view('dashboard.show', $data);
    }
}