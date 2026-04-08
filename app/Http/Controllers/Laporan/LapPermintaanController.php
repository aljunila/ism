<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use App\Models\Barang;
use App\Models\DetailPermintaan;
use App\Models\LogBarang;
use App\Models\Kapal;
use App\Models\ChecklistData;
use App\Models\KodeForm;
use App\Models\User;
use App\Models\StatusBarang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;

class LapPermintaanController extends Controller
{
     public function laporan()
    {
        $data['active'] = "lappermintaan";
        return view('laporan.permintaan.index', $data);
    }
}
