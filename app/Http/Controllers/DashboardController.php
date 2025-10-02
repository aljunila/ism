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
        $data['perusahaan'] = Perusahaan::count();
        $data['kapal'] = Kapal::where('status','A')->count();
        $data['karyawan'] = Karyawan::where('status','A')->where('resign','N')->count();
        $data['user'] = DB::table('user')
                        ->leftjoin('karyawan', 'karyawan.id', 'user.id_karyawan')
                        ->where('karyawan.status','A')->where('karyawan.resign','N')
                        ->count();
        return view('dashboard.show', $data);
    }
}