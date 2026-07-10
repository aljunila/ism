<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Role;
use App\Models\User;
use App\Models\MasterFile;
use App\Models\FileUpload;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use ZipArchive;
use Str;
use Session;
use DB;
use App\Support\RoleContext;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;

class LapKapalController extends Controller
{
    public function index()
    {
        $roleJenis = Session::get('previllage');
        $activeCompany = Session::get('id_perusahaan');
        $activeShip = Session::get('id_kapal');
        $data['active'] = "lapkapal";
        return view('laporan.kapal.index', $data);
    }

    public function data(Request $request)
    {
        $perusahaan = $request->input('id_perusahaan');
        $roleJenis = Session::get('previllage');
        $kapal = ($roleJenis == 3) ? Session::get('id_kapal') : null;
        $activeCompany = Session::get('id_perusahaan');
        $get = DB::table('kapal')
                ->leftjoin('perusahaan', 'perusahaan.id', '=', 'kapal.pemilik')
                ->leftjoin('m_cabang', 'm_cabang.id', '=', 'kapal.id_cabang')
                ->select('kapal.*', 'perusahaan.nama as perusahaan', 'm_cabang.cabang')
                ->where('kapal.status', 'A')
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('perusahaan.id', $perusahaan);
                })
                ->when($roleJenis == 2 && $activeCompany, fn($query) => $query->where('perusahaan.id', $activeCompany))
                ->when($roleJenis == 6, fn($query) => $query->where('kapal.id_cabang', Session::get('id_cabang')))
                ->when($kapal, fn($query) => $query->where('kapal.id', $kapal))
                ->get();
        return response()->json(['data' => $get]);
    }

    public function downloadZip($id)
    {
        $kapal = Kapal::find($id);
        $files = DB::table('master_file as a')
            ->leftJoin('file_upload as b', function ($join) use ($id) {
                $join->on('a.id', '=', 'b.id_file')
                    ->where('b.id_kapal', $id);
            })
            ->where('a.type', 'K')
            ->where('a.status', 'A')
            ->whereNotNull('b.file')
            ->select('a.nama', 'b.file')
            ->orderBy('a.no_urut')
            ->get();

        if ($files->isEmpty()) {
            return back()->with('error', 'Tidak ada dokumen.');
        }

        $zipName = 'Dokumen_'.$kapal->nama.'.zip';
        $zipPath = storage_path('app/temp/'.$zipName);

        if (!File::exists(dirname($zipPath))) {
            File::makeDirectory(dirname($zipPath), 0755, true);
        }

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        foreach ($files as $row) {

            $path = public_path('file_upload/'.$row->file);

            if (file_exists($path)) {

                // Nama file di dalam ZIP
                $extension = pathinfo($row->file, PATHINFO_EXTENSION);

                $zip->addFile(
                    $path,
                    $row->nama.'.'.$extension
                );
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
