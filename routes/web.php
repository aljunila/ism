<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
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

Route::get('/', function () {
    return view('login/show');
});

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/login/password', [LoginController::class, 'password']);
Route::post('/login/storeuser', [LoginController::class, 'storeuser']);
// Route::get('/get-menu/parents', [LoginController::class, 'getParents']);
// Route::get('/get-menu/{id}', [LoginController::class, 'getChildren']);
Route::get('/getMenu', [LoginController::class, 'getMenu']);
Route::get('/buatakun', [LoginController::class, 'buatakun']);
Route::post('/carinik', [LoginController::class, 'carinik']);
Route::get('/dashboard', [DashboardController::class, 'show'])->name('show')->middleware('auth');
Route::post('/upload-image', [UploadController::class, 'upload'])->name('upload.image');
Route::get('/lupapassword', [LoginController::class, 'lupapassword']);
Route::post('/resetpassword', [LoginController::class, 'resetpassword'])->middleware('auth');
Route::get('/login/reset/{id}', [LoginController::class, 'reset'])->middleware('auth');

Route::get('/perusahaan', [PerusahaanController::class, 'show'])->name('perusahaan')->middleware('auth');
Route::get('/perusahaan/add', [PerusahaanController::class, 'add' ])->middleware('auth');
Route::post('perusahaan/create', [PerusahaanController::class, 'store']);
Route::get('/perusahaan/profil/{id}', [PerusahaanController::class, 'profil'])->middleware('auth');
Route::get('/perusahaan/edit/{id}', [PerusahaanController::class, 'edit'])->middleware('auth');
Route::post('/perusahaan/update/{id}', [PerusahaanController::class, 'update']);
Route::post('/perusahaan/delete/{id}', [PerusahaanController::class, 'delete']);
Route::get('/perusahaan/export', [PerusahaanController::class, 'export'])->middleware('auth');
Route::get('/perusahaan/profil/{id}', [PerusahaanController::class, 'profil'])->middleware('auth');
Route::post('/perusahaan/savefile/{id}', [PerusahaanController::class, 'savefile'])->middleware('auth');
Route::get('/perusahaan/pdf/{id}', [PerusahaanController::class, 'pdf'])->name('perusahaan.pdf')->middleware('auth');

Route::get('/kapal', [KapalController::class, 'show'])->name('kapal')->middleware('auth');
Route::post('/kapal/data', [KapalController::class, 'getData'])->middleware('auth');
Route::get('/kapal/add', [KapalController::class, 'add' ])->middleware('auth');
Route::post('kapal/store', [KapalController::class, 'store']);
Route::get('/kapal/profil/{id}', [KapalController::class, 'profil'])->middleware('auth');
Route::get('/kapal/edit/{id}', [KapalController::class, 'edit'])->middleware('auth');
Route::post('/kapal/update/{id}', [KapalController::class, 'update']);
Route::get('/kapal/delete/{id}', [KapalController::class, 'delete']);
Route::get('/get-kapal/{id_perusahaan}', [KapalController::class, 'getKapal']);
Route::post('/kapal/export', [KapalController::class, 'export'])->middleware('auth');
Route::post('/kapal/savefile/{id}', [KapalController::class, 'savefile']);
Route::get('/kapal/pdf/{id}', [KapalController::class, 'pdf'])->name('kapal.pdf')->middleware('auth');

Route::get('/jabatan', [JabatanController::class, 'show'])->name('jabatan')->middleware('auth');
Route::get('/jabatan/data', [JabatanController::class, 'getData'])->middleware('auth');
Route::get('/jabatan/add', [JabatanController::class, 'add' ])->middleware('auth');
Route::post('jabatan/store', [JabatanController::class, 'store'])->name('jabatan.store');
Route::get('/jabatan/edit/{id}', [JabatanController::class, 'edit'])->middleware('auth');
Route::post('/jabatan/update/{id}', [JabatanController::class, 'update']);
Route::post('/jabatan/delete/{id}', [JabatanController::class, 'delete']);

Route::get('/karyawan', [KaryawanController::class, 'show'])->name('karyawan')->middleware('auth');
Route::post('/karyawan/data', [KaryawanController::class, 'getData'])->middleware('auth');
Route::get('/karyawan/add', [KaryawanController::class, 'add' ])->middleware('auth');
Route::post('karyawan/store', [KaryawanController::class, 'store']);
Route::get('/karyawan/profil/{id}', [KaryawanController::class, 'profil'])->middleware('auth');
Route::get('/karyawan/edit/{uid}', [KaryawanController::class, 'edit'])->middleware('auth');
Route::post('/karyawan/update/{id}', [KaryawanController::class, 'update'])->middleware('auth');
Route::post('/karyawan/delete/{id}', [KaryawanController::class, 'delete'])->middleware('auth');
Route::post('/karyawan/resign/{id}', [KaryawanController::class, 'resign'])->middleware('auth');
Route::post('/karyawan/updatettd/{id}', [KaryawanController::class, 'updatettd'])->middleware('auth');
Route::post('/karyawan/password/{id}', [KaryawanController::class, 'password'])->middleware('auth');
Route::post('/karyawan/export', [KaryawanController::class, 'export'])->middleware('auth');
Route::post('/karyawan/savefile/{id}', [KaryawanController::class, 'savefile'])->middleware('auth');
Route::get('/karyawan/pdf/{id}', [KaryawanController::class, 'pdf'])->name('karyawan.pdf')->middleware('auth');

Route::get('/akses', [AksesController::class, 'show'])->name('akses')->middleware('auth');
Route::get('/akses/data', [AksesController::class, 'getData'])->middleware('auth');
Route::get('/menu', [AksesController::class, 'menu']);
Route::get('/akses/add', [AksesController::class, 'add' ])->middleware('auth');
Route::post('akses/store', [AksesController::class, 'store'])->name('akses.store');
Route::get('/akses/profil/{id}', [AksesController::class, 'profil'])->middleware('auth');
Route::get('/akses/edit/{id}', [AksesController::class, 'edit'])->middleware('auth');
Route::post('/akses/update/{id}', [AksesController::class, 'update']);
Route::post('/akses/delete/{id}', [AksesController::class, 'delete']);
Route::post('/akses/save', [AksesController::class, 'saveChecked'])->name('akses.save');

Route::get('/prosedur', [ProsedurController::class, 'show'])->name('prosedur')->middleware('auth');
Route::post('/prosedur/data', [ProsedurController::class, 'getData'])->middleware('auth');
Route::get('/prosedur/add', [ProsedurController::class, 'add' ])->middleware('auth');
Route::post('prosedur/store', [ProsedurController::class, 'store']);
Route::get('/prosedur/edit/{id}', [ProsedurController::class, 'edit'])->middleware('auth');
Route::post('/prosedur/update/{id}', [ProsedurController::class, 'update']);
Route::post('/prosedur/delete/{id}', [ProsedurController::class, 'delete']);
Route::get('/prosedur/pdf/{id}', [ProsedurController::class, 'prosedurPdf'])->name('prosedur.pdf')->middleware('auth');
Route::get('/prosedur/pdfdownload/{id}', [ProsedurController::class, 'pdfdownload'])->name('prosedur.pdf')->middleware('auth');
Route::get('/prosedur/view', [ProsedurController::class, 'view'])->name('view')->middleware('auth');
Route::get('/view-file/{uid}', [ProsedurController::class, 'view_file'])->name('view_file')->middleware('auth');
Route::get('/download_file/{uid}', [ProsedurController::class, 'download_file'])->name('download_file')->middleware('auth');
Route::post('/prosedur/viewuser', [ProsedurController::class, 'viewuser'])->middleware('auth');
Route::post('/prosedur/viewdetail', [ProsedurController::class, 'viewdetail'])->middleware('auth');

Route::get('/el0101', [RefrensiDocController::class, 'el0101'])->name('el0101')->middleware('auth');
Route::post('/refrensi/data', [RefrensiDocController::class, 'getData'])->middleware('auth');
Route::get('/refrensi/add', [RefrensiDocController::class, 'add' ])->middleware('auth');
Route::post('/refrensi/store', [RefrensiDocController::class, 'store'])->name('store');
Route::get('/refrensi/edit/{id}', [RefrensiDocController::class, 'edit'])->middleware('auth');
Route::post('/refrensi/update/{id}', [RefrensiDocController::class, 'update']);
Route::post('/refrensi/delete/{id}', [RefrensiDocController::class, 'delete']);
Route::get('/el0102', [RefrensiDocController::class, 'el0102'])->name('el0102')->middleware('auth');
Route::get('/el0103', [RefrensiDocController::class, 'el0103'])->name('el0103')->middleware('auth');
Route::get('/el0104', [RefrensiDocController::class, 'el0104'])->name('el0104')->middleware('auth');
Route::get('/refrensi/pdf/{id}', [RefrensiDocController::class, 'pdf'])->name('refrensi.pdf')->middleware('auth');

Route::get('/elemen2', [AturanController::class, 'show'])->name('aturan')->middleware('auth');
Route::get('/aturan/data', [AturanController::class, 'getData'])->middleware('auth');
Route::get('/aturan/add', [AturanController::class, 'add' ])->middleware('auth');
Route::post('aturan/store', [AturanController::class, 'store']);
Route::get('/aturan/edit/{id}', [AturanController::class, 'edit'])->middleware('auth');
Route::post('/aturan/update/{id}', [AturanController::class, 'update']);
Route::post('/aturan/delete/{id}', [AturanController::class, 'delete']);
Route::get('/aturan/pdf/{id}', [AturanController::class, 'aturanPdf'])->name('aturan.pdf')->middleware('auth');

Route::get('/el0302', [ChecklistController::class, 'el0302'])->name('el0302')->middleware('auth');
Route::post('/checklist/data', [ChecklistController::class, 'getData'])->middleware('auth');
Route::get('/checklist/add/{kode}', [ChecklistController::class, 'add' ])->middleware('auth');
Route::post('/checklist/store', [ChecklistController::class, 'store'])->name('store');
Route::get('/checklist/edit/{id}', [ChecklistController::class, 'edit'])->middleware('auth');
Route::post('/checklist/update/{id}', [ChecklistController::class, 'update']);
Route::post('/checklist/delete/{id}', [ChecklistController::class, 'delete']);
Route::get('/el0303', [ChecklistController::class, 'el0303'])->name('el0303')->middleware('auth');
Route::get('/el0304', [ChecklistController::class, 'el0304'])->name('el0304')->middleware('auth');
Route::get('/el0305', [ChecklistController::class, 'el0305'])->name('el0305')->middleware('auth');
Route::get('/checklist/pdf/{id}', [ChecklistController::class, 'pdf'])->name('checklist.pdf')->middleware('auth');
Route::get('/checklist/item/{kode}', [ChecklistController::class, 'item' ])->middleware('auth');
Route::post('/checklist/dataitem', [ChecklistController::class, 'getItem'])->middleware('auth');

Route::get('/el0307', [ChecklistController::class, 'el0307'])->name('el0307')->middleware('auth');
Route::get('/el0311', [ChecklistController::class, 'el0311'])->name('el0311')->middleware('auth');
Route::get('/el0312', [ChecklistController::class, 'el0312'])->name('el0312')->middleware('auth');
Route::post('/checklist/listGanti', [ChecklistController::class, 'getGanti'])->middleware('auth');
Route::get('/checklist/addganti/{kode}', [ChecklistController::class, 'addganti' ])->middleware('auth');
Route::post('/checklist/storeganti', [ChecklistController::class, 'storeganti'])->name('storeganti');
Route::get('/checklist/editganti/{id}', [ChecklistController::class, 'editganti'])->middleware('auth');
Route::post('/checklist/updateganti/{id}', [ChecklistController::class, 'updateganti']);
Route::post('/checklist/deleteganti/{id}', [ChecklistController::class, 'deleteganti']);
Route::get('/checklist/gantipdf/{id}', [ChecklistController::class, 'gantipdf'])->name('checklist.gantipdf')->middleware('auth');
Route::get('/get-karyawan/{id_kapal}', [ChecklistController::class, 'getKaryawan']);

Route::get('/el0308', [ChecklistController::class, 'el0308'])->name('el0308')->middleware('auth');
Route::get('/el0309', [ChecklistController::class, 'el0309'])->name('el0309')->middleware('auth');
Route::post('/checklist/getChecklist', [ChecklistController::class, 'getChecklist'])->middleware('auth');
Route::post('/checklist/save', [ChecklistController::class, 'save'])->name('save');
Route::get('/checklist/nahkodapdf/{uid}/{kode}', [ChecklistController::class, 'nahkodapdf'])->name('checklist.nahkodapdf')->middleware('auth');

Route::post('/form/intruksi', [ChecklistController::class, 'saveform'])->middleware('auth');
Route::post('/checklist/storeitem', [ChecklistController::class, 'storeitem'])->middleware('auth');
Route::get('/checklist/edititem/{id}', [ChecklistController::class, 'edititem'])->middleware('auth');
Route::post('/checklist/updateitem/{id}', [ChecklistController::class, 'updateitem']);
Route::post('/checklist/deleteitem/{id}', [ChecklistController::class, 'deleteitem']);

Route::get('/el0301', [NotulenController::class, 'show'])->name('notulen')->middleware('auth');
Route::get('/notulen/data', [NotulenController::class, 'getData'])->middleware('auth');
Route::get('/notulen/add', [NotulenController::class, 'add' ])->middleware('auth');
Route::post('notulen/store', [NotulenController::class, 'store']);
Route::get('/notulen/edit/{id}', [NotulenController::class, 'edit'])->middleware('auth');
Route::post('/notulen/update/{id}', [NotulenController::class, 'update']);
Route::post('/notulen/delete/{id}', [NotulenController::class, 'delete']);
Route::get('/notulen/pdf/{id}', [NotulenController::class, 'notulenPdf'])->name('notulen.pdf')->middleware('auth');

Route::get('/el0404', [NotulenController::class, 'el0404'])->name('el0404')->middleware('auth');
Route::get('/el0402', [NotulenController::class, 'el0402'])->name('el0402')->middleware('auth');
Route::get('/el0403', [NotulenController::class, 'el0403'])->name('el0403')->middleware('auth');
Route::post('/notulen/GetAgenda', [NotulenController::class, 'GetAgenda'])->middleware('auth');
Route::post('/notulen/data4', [NotulenController::class, 'getData4'])->middleware('auth');
Route::get('/notulen/add4/{kode}', [NotulenController::class, 'add4' ])->middleware('auth');
Route::get('/notulen/edit4/{id}', [NotulenController::class, 'edit4'])->middleware('auth');
Route::post('/notulen/deleteagenda/{id}', [NotulenController::class, 'deleteagenda']);
Route::get('/notulen/pdf4/{id}', [NotulenController::class, 'Pdf'])->name('notulen.pdf4')->middleware('auth');
Route::get('/notulen/hadir/{id}', [NotulenController::class, 'hadir'])->middleware('auth');

Route::get('/el0306', [DaftarHadirController::class, 'el0306'])->name('el0306')->middleware('auth');
Route::post('/hadir/data', [DaftarHadirController::class, 'getData'])->middleware('auth');
Route::get('/hadir/add/{kode}', [DaftarHadirController::class, 'add' ])->middleware('auth');
Route::post('hadir/store', [DaftarHadirController::class, 'store']);
Route::get('/hadir/edit/{id}', [DaftarHadirController::class, 'edit'])->middleware('auth');
Route::post('/hadir/update/{id}', [DaftarHadirController::class, 'update']);
Route::post('/hadir/delete/{id}', [DaftarHadirController::class, 'delete']);
Route::get('/hadir/pdf/{id}', [DaftarHadirController::class, 'hadirPdf'])->name('hadir.pdf')->middleware('auth');
Route::post('/hadir/KaryawanHadir', [DaftarHadirController::class, 'KaryawanHadir'])->middleware('auth');
Route::post('/hadir/deletedetail/{id}', [DaftarHadirController::class, 'deletedetail']);

Route::get('/el0310', [GantiKKMController::class, 'show'])->name('kkm')->middleware('auth');
Route::get('/kkm/data', [GantiKKMController::class, 'getData'])->middleware('auth');
Route::get('/kkm/add', [GantiKKMController::class, 'add' ])->middleware('auth');
Route::post('kkm/store', [GantiKKMController::class, 'store'])->name('kkm.store');
Route::get('/kkm/edit/{id}', [GantiKKMController::class, 'edit'])->middleware('auth');
Route::post('/kkm/update/{id}', [GantiKKMController::class, 'update']);
Route::post('/kkm/delete/{id}', [GantiKKMController::class, 'delete']);
Route::get('/kkm/pdf/{id}', [GantiKKMController::class, 'pdf'])->name('kkm.pdf')->middleware('auth');

Route::get('/file', [FileController::class, 'show'])->name('file')->middleware('auth');
Route::post('/file/data', [FileController::class, 'getData'])->middleware('auth');
Route::get('/file/add', [FileController::class, 'add' ])->middleware('auth');
Route::post('file/store', [FileController::class, 'store'])->name('file.store');
Route::get('/file/edit/{id}', [FileController::class, 'edit'])->middleware('auth');
Route::post('/file/update/{id}', [FileController::class, 'update']);
Route::post('/file/delete/{id}', [FileController::class, 'delete']);
