<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AksesController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProsedurController;
use App\Http\Controllers\RefrensiDocController;
use App\Http\Controllers\AturanController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\NotulenController;
use App\Http\Controllers\DaftarHadirController;
use App\Http\Controllers\GantiKKMController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Purchasing\PurchasingController;

Route::get('/', function () {
    if (Session::get('login') || Auth::check()) {
        return redirect()->route('show');
    }
    return view('login/show');
});

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('login/password', [LoginController::class, 'password'])->middleware('auth');
Route::post('login/storeuser', [LoginController::class, 'storeuser']);
Route::get('getMenu', [LoginController::class, 'getMenu']);
Route::get('buatakun', [LoginController::class, 'buatakun']);
Route::post('carinik', [LoginController::class, 'carinik']);
Route::get('lupapassword', [LoginController::class, 'lupapassword']);
Route::post('resetpassword', [LoginController::class, 'resetpassword'])->middleware('auth');
Route::get('login/reset/{id}', [LoginController::class, 'reset'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'show'])->name('show');
    Route::post('upload-image', [UploadController::class, 'upload'])->name('upload.image');

    Route::prefix('perusahaan')->group(function () {
        Route::get('/', [PerusahaanController::class, 'show'])->name('perusahaan');
        Route::get('add', [PerusahaanController::class, 'add']);
        Route::post('create', [PerusahaanController::class, 'store']);
        Route::get('profil/{id}', [PerusahaanController::class, 'profil']);
        Route::get('edit/{id}', [PerusahaanController::class, 'edit']);
        Route::post('update/{id}', [PerusahaanController::class, 'update']);
        Route::post('delete/{id}', [PerusahaanController::class, 'delete']);
        Route::get('export', [PerusahaanController::class, 'export']);
        Route::post('savefile/{id}', [PerusahaanController::class, 'savefile']);
        Route::get('pdf/{id}', [PerusahaanController::class, 'pdf'])->name('perusahaan.pdf');
    });

    Route::prefix('kapal')->group(function () {
        Route::get('/', [KapalController::class, 'show'])->name('kapal');
        Route::post('data', [KapalController::class, 'getData']);
        Route::get('add', [KapalController::class, 'add']);
        Route::post('store', [KapalController::class, 'store']);
        Route::get('profil/{id}', [KapalController::class, 'profil']);
        Route::get('edit/{id}', [KapalController::class, 'edit']);
        Route::post('update/{id}', [KapalController::class, 'update']);
        Route::post('delete/{id}', [KapalController::class, 'delete']);
        Route::post('export', [KapalController::class, 'export']);
        Route::post('savefile/{id}', [KapalController::class, 'savefile']);
        Route::get('pdf/{id}', [KapalController::class, 'pdf'])->name('kapal.pdf');
    });
    Route::get('get-kapal/{id_perusahaan}', [KapalController::class, 'getKapal']);

    Route::prefix('jabatan')->group(function () {
        Route::get('/', [JabatanController::class, 'show'])->name('jabatan');
        Route::get('data', [JabatanController::class, 'getData']);
        Route::get('add', [JabatanController::class, 'add']);
        Route::post('store', [JabatanController::class, 'store'])->name('jabatan.store');
        Route::get('edit/{id}', [JabatanController::class, 'edit']);
        Route::post('update/{id}', [JabatanController::class, 'update']);
        Route::post('delete/{id}', [JabatanController::class, 'delete']);
    });

    Route::prefix('karyawan')->group(function () {
        Route::get('/', [KaryawanController::class, 'show'])->name('karyawan');
        Route::post('data', [KaryawanController::class, 'getData']);
        Route::get('add', [KaryawanController::class, 'add']);
        Route::post('store', [KaryawanController::class, 'store']);
        Route::get('profil/{id}', [KaryawanController::class, 'profil']);
        Route::get('edit/{uid}', [KaryawanController::class, 'edit']);
        Route::post('update/{id}', [KaryawanController::class, 'update']);
        Route::post('delete/{id}', [KaryawanController::class, 'delete']);
        Route::post('resign/{id}', [KaryawanController::class, 'resign']);
        Route::post('updatettd/{id}', [KaryawanController::class, 'updatettd']);
        Route::post('password/{id}', [KaryawanController::class, 'password']);
        Route::post('export', [KaryawanController::class, 'export']);
        Route::post('savefile/{id}', [KaryawanController::class, 'savefile']);
        Route::get('pdf/{id}', [KaryawanController::class, 'pdf'])->name('karyawan.pdf');
    });

    Route::prefix('akses')->group(function () {
        Route::get('/', [AksesController::class, 'show'])->name('akses');
        Route::get('data', [AksesController::class, 'getData']);
        Route::get('add', [AksesController::class, 'add']);
        Route::post('store', [AksesController::class, 'store'])->name('akses.store');
        Route::get('profil/{id}', [AksesController::class, 'profil']);
        Route::get('edit/{id}', [AksesController::class, 'edit']);
        Route::post('update/{id}', [AksesController::class, 'update']);
        Route::post('delete/{id}', [AksesController::class, 'delete']);
        Route::post('save', [AksesController::class, 'saveChecked'])->name('akses.save');
    });
    Route::get('menu', [AksesController::class, 'menu']);

    Route::prefix('prosedur')->group(function () {
        Route::get('/', [ProsedurController::class, 'show'])->name('prosedur');
        Route::post('data', [ProsedurController::class, 'getData']);
        Route::get('add', [ProsedurController::class, 'add']);
        Route::post('store', [ProsedurController::class, 'store']);
        Route::get('edit/{id}', [ProsedurController::class, 'edit']);
        Route::post('update/{id}', [ProsedurController::class, 'update']);
        Route::post('delete/{id}', [ProsedurController::class, 'delete']);
        Route::get('pdf/{id}', [ProsedurController::class, 'prosedurPdf'])->name('prosedur.pdf');
        Route::get('pdfdownload/{id}', [ProsedurController::class, 'pdfdownload'])->name('prosedur.pdf');
        Route::get('view', [ProsedurController::class, 'view'])->name('view');
        Route::post('viewuser', [ProsedurController::class, 'viewuser']);
        Route::post('viewdetail', [ProsedurController::class, 'viewdetail']);
    });
    Route::get('view-file/{uid}', [ProsedurController::class, 'view_file'])->name('view_file');
    Route::get('download_file/{uid}', [ProsedurController::class, 'download_file'])->name('download_file');

    Route::prefix('refrensi')->group(function () {
        Route::post('data', [RefrensiDocController::class, 'getData']);
        Route::get('add', [RefrensiDocController::class, 'add']);
        Route::post('store', [RefrensiDocController::class, 'store'])->name('store');
        Route::get('edit/{id}', [RefrensiDocController::class, 'edit']);
        Route::post('update/{id}', [RefrensiDocController::class, 'update']);
        Route::post('delete/{id}', [RefrensiDocController::class, 'delete']);
        Route::get('pdf', [RefrensiDocController::class, 'pdf'])->name('refrensi.pdf');
    });
    Route::get('el0101', [RefrensiDocController::class, 'el0101'])->name('el0101');
    Route::get('el0102', [RefrensiDocController::class, 'el0102'])->name('el0102');
    Route::get('el0103', [RefrensiDocController::class, 'el0103'])->name('el0103');
    Route::get('el0104', [RefrensiDocController::class, 'el0104'])->name('el0104');

    Route::get('elemen2', [AturanController::class, 'show'])->name('aturan');
    Route::prefix('aturan')->group(function () {
        Route::post('data', [AturanController::class, 'getData']);
        Route::get('add', [AturanController::class, 'add']);
        Route::post('store', [AturanController::class, 'store']);
        Route::get('edit/{id}', [AturanController::class, 'edit']);
        Route::post('update/{id}', [AturanController::class, 'update']);
        Route::post('delete/{id}', [AturanController::class, 'delete']);
        Route::get('pdf/{id}', [AturanController::class, 'aturanPdf'])->name('aturan.pdf');
    });
    Route::get('get-karyawanbyCom/{id_kapal}', [AturanController::class, 'getKaryawan']);

    // Purchasing
    Route::prefix('purchas')->group(function(){
        Route::get('/', [PurchasingController::class, 'index']);
    });

    Route::get('el0302', [ChecklistController::class, 'el0302'])->name('el0302');
    Route::get('el0303', [ChecklistController::class, 'el0303'])->name('el0303');
    Route::get('el0304', [ChecklistController::class, 'el0304'])->name('el0304');
    Route::get('el0305', [ChecklistController::class, 'el0305'])->name('el0305');
    Route::get('el0307', [ChecklistController::class, 'el0307'])->name('el0307');
    Route::get('el0311', [ChecklistController::class, 'el0311'])->name('el0311');
    Route::get('el0312', [ChecklistController::class, 'el0312'])->name('el0312');
    Route::get('el0308', [ChecklistController::class, 'el0308'])->name('el0308');
    Route::get('el0309', [ChecklistController::class, 'el0309'])->name('el0309');
    Route::post('checklist/data', [ChecklistController::class, 'getData']);
    Route::get('checklist/add/{kode}', [ChecklistController::class, 'add']);
    Route::post('checklist/store', [ChecklistController::class, 'store'])->name('store');
    Route::get('checklist/edit/{id}', [ChecklistController::class, 'edit']);
    Route::post('checklist/update/{id}', [ChecklistController::class, 'update']);
    Route::post('checklist/delete/{id}', [ChecklistController::class, 'delete']);
    Route::get('checklist/pdf/{id}', [ChecklistController::class, 'pdf'])->name('checklist.pdf');
    Route::get('checklist/item/{kode}', [ChecklistController::class, 'item']);
    Route::post('checklist/dataitem', [ChecklistController::class, 'getItem']);
    Route::post('checklist/listGanti', [ChecklistController::class, 'getGanti']);
    Route::get('checklist/addganti/{kode}', [ChecklistController::class, 'addganti']);
    Route::post('checklist/storeganti', [ChecklistController::class, 'storeganti'])->name('storeganti');
    Route::get('checklist/editganti/{id}', [ChecklistController::class, 'editganti']);
    Route::post('checklist/updateganti/{id}', [ChecklistController::class, 'updateganti']);
    Route::post('checklist/deleteganti/{id}', [ChecklistController::class, 'deleteganti']);
    Route::get('checklist/gantipdf/{id}', [ChecklistController::class, 'gantipdf'])->name('checklist.gantipdf');
    Route::post('checklist/getChecklist', [ChecklistController::class, 'getChecklist']);
    Route::post('checklist/save', [ChecklistController::class, 'save'])->name('save');
    Route::get('checklist/nahkodapdf/{uid}/{kode}', [ChecklistController::class, 'nahkodapdf'])->name('checklist.nahkodapdf');
    Route::post('form/intruksi', [ChecklistController::class, 'saveform']);
    Route::post('checklist/storeitem', [ChecklistController::class, 'storeitem']);
    Route::get('checklist/edititem/{id}', [ChecklistController::class, 'edititem']);
    Route::post('checklist/updateitem/{id}', [ChecklistController::class, 'updateitem']);
    Route::post('checklist/deleteitem/{id}', [ChecklistController::class, 'deleteitem']);
    Route::get('get-karyawan/{id_kapal}', [ChecklistController::class, 'getKaryawan']);

    Route::get('el0301', [NotulenController::class, 'show'])->name('notulen');
    Route::post('notulen/data', [NotulenController::class, 'getData']);
    Route::get('notulen/add', [NotulenController::class, 'add']);
    Route::post('notulen/store', [NotulenController::class, 'store']);
    Route::get('notulen/edit/{id}', [NotulenController::class, 'edit']);
    Route::post('notulen/update/{id}', [NotulenController::class, 'update']);
    Route::post('notulen/delete/{id}', [NotulenController::class, 'delete']);
    Route::get('notulen/pdf/{id}', [NotulenController::class, 'notulenPdf'])->name('notulen.pdf');
    Route::get('el0404', [NotulenController::class, 'el0404'])->name('el0404');
    Route::get('el0402', [NotulenController::class, 'el0402'])->name('el0402');
    Route::get('el0403', [NotulenController::class, 'el0403'])->name('el0403');
    Route::post('notulen/GetAgenda', [NotulenController::class, 'GetAgenda']);
    Route::post('notulen/data4', [NotulenController::class, 'getData4']);
    Route::get('notulen/add4/{kode}', [NotulenController::class, 'add4']);
    Route::get('notulen/edit4/{id}', [NotulenController::class, 'edit4']);
    Route::post('notulen/deleteagenda/{id}', [NotulenController::class, 'deleteagenda']);
    Route::get('notulen/pdf4/{id}', [NotulenController::class, 'Pdf'])->name('notulen.pdf4');
    Route::get('notulen/hadir/{id}', [NotulenController::class, 'hadir']);
    Route::get('el0405', [NotulenController::class, 'el0405'])->name('el0405');
    Route::post('notulen/gethadir', [NotulenController::class, 'gethadir']);
    Route::get('notulen/pdfhadir/{id}', [NotulenController::class, 'Pdfhadir'])->name('notulen.pdfhadir');

    Route::get('el0306', [DaftarHadirController::class, 'el0306'])->name('el0306');
    Route::post('hadir/data', [DaftarHadirController::class, 'getData']);
    Route::get('hadir/add/{kode}', [DaftarHadirController::class, 'add']);
    Route::post('hadir/store', [DaftarHadirController::class, 'store']);
    Route::get('hadir/edit/{id}', [DaftarHadirController::class, 'edit']);
    Route::post('hadir/update/{id}', [DaftarHadirController::class, 'update']);
    Route::post('hadir/delete/{id}', [DaftarHadirController::class, 'delete']);
    Route::get('hadir/pdf/{id}', [DaftarHadirController::class, 'hadirPdf'])->name('hadir.pdf');
    Route::post('hadir/KaryawanHadir', [DaftarHadirController::class, 'KaryawanHadir']);
    Route::post('hadir/deletedetail/{id}', [DaftarHadirController::class, 'deletedetail']);

    Route::get('el0310', [GantiKKMController::class, 'show'])->name('kkm');
    Route::post('kkm/data', [GantiKKMController::class, 'getData']);
    Route::get('kkm/add', [GantiKKMController::class, 'add']);
    Route::post('kkm/store', [GantiKKMController::class, 'store'])->name('kkm.store');
    Route::get('kkm/edit/{id}', [GantiKKMController::class, 'edit']);
    Route::post('kkm/update/{id}', [GantiKKMController::class, 'update']);
    Route::post('kkm/delete/{id}', [GantiKKMController::class, 'delete']);
    Route::get('kkm/pdf/{id}', [GantiKKMController::class, 'pdf'])->name('kkm.pdf');

    Route::prefix('file')->group(function () {
        Route::get('/', [FileController::class, 'show'])->name('file');
        Route::post('data', [FileController::class, 'getData']);
        Route::get('add', [FileController::class, 'add']);
        Route::post('store', [FileController::class, 'store'])->name('file.store');
        Route::get('edit/{id}', [FileController::class, 'edit']);
        Route::post('update/{id}', [FileController::class, 'update']);
        Route::post('delete/{id}', [FileController::class, 'delete']);
    });
});
