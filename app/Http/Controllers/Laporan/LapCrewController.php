<?php

namespace App\Http\Controllers\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Kapal;
use App\Models\Jabatan;
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

class LapCrewController extends Controller
{
    public function index()
    {
        $data['active'] = "lapcrew";
        $data['perusahaan'] = Perusahaan::get();
        if(Session::get('previllage')==2) {
            $data['kapal'] = Kapal::where('status', 'A')->where('pemilik', Session::get('id_perusahaan'))->get();  
            $data['cabang'] = Cabang::where('is_delete', 0)->get();
        } else if(Session::get('previllage')==3) {
          $data['kapal'] = Kapal::where('status', 'A')->where('id', Session::get('id_kapal'))->get(); 
            $data['cabang'] = Cabang::where('is_delete', 0)->get(); 
        } else if(Session::get('previllage')==6) {
          $data['kapal'] = Kapal::where('status', 'A')->where('id_cabang', Session::get('id_cabang'))->get();  
            $data['cabang'] = Cabang::where('id', Session::get('id_cabang'))->get();
        } else {
            $data['kapal'] = Kapal::where('status', 'A')->get();  
            $data['cabang'] = Cabang::where('is_delete', 0)->get();
        }
        return view('laporan.crew.index', $data);
    }

    public function data(Request $request)
    {
        $roleJenis = Session::get('previllage');
        $perusahaan = ($roleJenis == 2) ? Session::get('id_perusahaan') : $request->input('id_perusahaan');
        $kapal = ($roleJenis == 3) ? Session::get('id_kapal') : $request->input('id_kapal');
        $cabang = $request->input('id_cabang');
        $kel = $request->input('kel');
        if(($kel==2) && ($roleJenis==6)) {
            $cabang = Session::get('id_cabang');
        } 

        $karyawan = DB::table('karyawan')
                ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id')
                ->leftJoin('perusahaan', 'perusahaan.id', '=', 'karyawan.id_perusahaan')
                ->leftJoin('kapal', 'kapal.id', '=', 'karyawan.id_kapal')
                ->leftJoin('m_cabang', 'm_cabang.id', '=', 'karyawan.id_cabang')
                ->select(
                    'karyawan.id',
                    'karyawan.uid',
                    'karyawan.nama',
                    'karyawan.nik',
                    'karyawan.nip',
                    'kapal.nama as kapal',
                    'jabatan.nama as jabatan',
                    'm_cabang.cabang'
                )
                ->where('karyawan.resign', 'N')
                ->where('karyawan.status','A')
                ->when($kel, function($query, $kel) {
                    return $query->where('jabatan.kel', $kel);
                })
                ->when($perusahaan, function($query, $perusahaan) {
                    return $query->where('perusahaan.id', $perusahaan);
                })
                ->when($kapal, function($query, $kapal) {
                    return $query->where('karyawan.id_kapal', $kapal);
                })
                ->when($cabang, function($query, $cabang) {
                    return $query->where('karyawan.id_cabang', $cabang);
                });
        // print_r($kapal);die();
        return DataTables::of($karyawan)
        ->filterColumn('kapal', function($query, $keyword) {
            $query->where('kapal.nama', 'like', "%{$keyword}%");
        })
        ->filterColumn('cabang', function($query, $keyword) {
            $query->where('cabang.cabang', 'like', "%{$keyword}%");
        })
        ->filterColumn('jabatan', function($query, $keyword) {
            $query->where('jabatan.nama', 'like', "%{$keyword}%");
        })
        ->make(true);
    }

    public function mergePdf($id)
    {
        $files = DB::table('master_file as a')
            ->leftJoin('file_upload as b', function ($join) use ($id) {
                $join->on('a.id', '=', 'b.id_file')
                    ->where('b.id_karyawan', $id);
            })
            ->where('a.type', 'S')
            ->where('a.status', 'A')
            ->whereNotNull('b.file')
            ->select('b.file')
            ->orderBy('a.no_urut')
            ->get();

        if ($files->isEmpty()) {
            return back()->with('error', 'Tidak ada file PDF.');
        }

        $tempDir = storage_path('app/temp');

        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $outputFile = $tempDir . '/dokumen_' . $id . '.pdf';

        // Sesuaikan jika lokasi Ghostscript berbeda
        $gs = '"C:\Program Files\gs\gs10.07.1\bin\gswin64c.exe"';

        $command = $gs .
            ' -dBATCH -dNOPAUSE -q' .
            ' -sDEVICE=pdfwrite' .
            ' -sOutputFile="' . $outputFile . '"';

        foreach ($files as $row) {

            $path = public_path('file_upload/' . $row->file);

            if (file_exists($path)) {
                $command .= ' "' . $path . '"';
            }
        }

        // Untuk debug, aktifkan ini dulu
        // dd($command);

        exec($command, $output, $status);

        if ($status != 0 || !file_exists($outputFile)) {
            dd($output, $status, $command);

            // atau:
            // return back()->with('error', 'Gagal merge PDF');
        }

        return response()->file($outputFile, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Dokumen_Karyawan.pdf"',
        ]);
    }

    public function convertPdf($inputFile)
    {
        $outputFile = storage_path('app/temp/' . basename($inputFile));

        if (!File::exists(dirname($outputFile))) {
            File::makeDirectory(dirname($outputFile), 0755, true);
        }

        $gs = '"C:\Program Files\gs\gs10.07.1\bin\gswin64c.exe"';

        $command = $gs .
            ' -sDEVICE=pdfwrite' .
            ' -dCompatibilityLevel=1.4' .
            ' -dNOPAUSE -dQUIET -dBATCH' .
            ' -sOutputFile="' . $outputFile . '"' .
            ' "' . $inputFile . '"';

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("Gagal convert PDF");
        }

        return $outputFile;
    }

public function downloadZip($id)
{
    $crew = Karyawan::find($id);
    $files = DB::table('master_file as a')
        ->leftJoin('file_upload as b', function ($join) use ($id) {
            $join->on('a.id', '=', 'b.id_file')
                 ->where('b.id_karyawan', $id);
        })
        ->where('a.type', 'S')
        ->where('a.status', 'A')
        ->whereNotNull('b.file')
        ->select('a.nama', 'b.file')
        ->orderBy('a.no_urut')
        ->get();

    if ($files->isEmpty()) {
        return back()->with('error', 'Tidak ada dokumen.');
    }

    $zipName = 'Dokumen_'.$crew->nama.'.zip';
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
