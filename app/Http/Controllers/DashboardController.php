<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\Periode;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Sk;
use Alert;
use Session;
Use Carbon\Carbon;

class DashboardController extends Controller
{
    public function show()
    {
        $data['active'] = "dashboard";
        return view('dashboard.show', $data);
    }
}