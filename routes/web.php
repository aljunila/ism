<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AksesController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProsedurController;
use App\Http\Controllers\RefrensiDocController;
use App\Http\Controllers\AturanController;

Route::get('/', function () {
    return view('login/show');
});

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/login/password', [LoginController::class, 'password']);
Route::get('/get-menu/parents', [LoginController::class, 'getParents']);
Route::get('/get-menu/{id}', [LoginController::class, 'getChildren']);
Route::get('/getMenu', [LoginController::class, 'getMenu']);

Route::get('/dashboard', [DashboardController::class, 'show'])->name('show')->middleware('auth');
Route::post('/upload-image', [UploadController::class, 'upload'])->name('upload.image');

Route::get('/pendaftaran', [PendaftaranController::class, 'show'])->name('pendaftaran')->middleware('auth');
Route::get('/pendaftaran/add', [PendaftaranController::class, 'add' ])->middleware('auth');
Route::post('pendaftaran/create', [PendaftaranController::class, 'store']);
Route::get('/pendaftaran/edit/{id}', [PendaftaranController::class, 'edit'])->middleware('auth');
Route::post('/pendaftaran/update/{id}', [PendaftaranController::class, 'update']);
Route::post('/pendaftaran/delete/{id}', [PendaftaranController::class, 'delete']);
Route::get('/pendaftaran/bill/{id}', [PendaftaranController::class, 'bill'])->middleware('auth');
Route::post('pendaftaran/addbill', [PendaftaranController::class, 'addbill']);
Route::post('/pendaftaran/deletebill/{id}', [PendaftaranController::class, 'deletebill']);
Route::get('/ppdb/{id}', [PendaftaranController::class, 'ppdb'])->name('ppdb');
Route::post('ppdb/save', [PendaftaranController::class, 'saveppdb']);
Route::get('/cp', [PendaftaranController::class, 'cp'])->name('cp')->middleware('auth');
Route::get('/cp/delete/{id}', [PendaftaranController::class, 'cpdel'])->name('cpdel');

Route::get('/siswa/profil/{id}', [SiswaController::class, 'profil'])->middleware('auth');
Route::get('/siswa/edit/{id}', [SiswaController::class, 'edit'])->middleware('auth');
Route::post('/siswa/update/{id}', [SiswaController::class, 'update']);
Route::get('/siswa/delete/{id}', [SiswaController::class, 'delete']);

Route::get('/perusahaan', [PerusahaanController::class, 'show'])->name('perusahaan')->middleware('auth');
Route::get('/perusahaan/add', [PerusahaanController::class, 'add' ])->middleware('auth');
Route::post('perusahaan/create', [PerusahaanController::class, 'store']);
Route::get('/perusahaan/profil/{id}', [PerusahaanController::class, 'profil'])->middleware('auth');
Route::get('/perusahaan/edit/{id}', [PerusahaanController::class, 'edit'])->middleware('auth');
Route::post('/perusahaan/update/{id}', [PerusahaanController::class, 'update']);
Route::get('/perusahaan/delete/{id}', [PerusahaanController::class, 'delete']);

Route::get('/kapal', [KapalController::class, 'show'])->name('kapal')->middleware('auth');
Route::get('/kapal/data', [KapalController::class, 'getData'])->middleware('auth');
Route::get('/kapal/add', [KapalController::class, 'add' ])->middleware('auth');
Route::post('kapal/store', [KapalController::class, 'store']);
Route::get('/kapal/profil/{id}', [KapalController::class, 'profil'])->middleware('auth');
Route::get('/kapal/edit/{id}', [KapalController::class, 'edit'])->middleware('auth');
Route::post('/kapal/update/{id}', [KapalController::class, 'update']);
Route::get('/kapal/delete/{id}', [KapalController::class, 'delete']);

Route::get('/jabatan', [JabatanController::class, 'show'])->name('jabatan')->middleware('auth');
Route::get('/jabatan/data', [JabatanController::class, 'getData'])->middleware('auth');
Route::get('/jabatan/add', [JabatanController::class, 'add' ])->middleware('auth');
Route::post('jabatan/store', [JabatanController::class, 'store'])->name('jabatan.store');
Route::get('/jabatan/profil/{id}', [JabatanController::class, 'profil'])->middleware('auth');
Route::get('/jabatan/edit/{id}', [JabatanController::class, 'edit'])->middleware('auth');
Route::post('/jabatan/update/{id}', [JabatanController::class, 'update']);
Route::post('/jabatan/delete/{id}', [JabatanController::class, 'delete']);

Route::get('/karyawan', [KaryawanController::class, 'show'])->name('karyawan')->middleware('auth');
Route::post('/karyawan/data', [KaryawanController::class, 'getData'])->middleware('auth');
Route::get('/karyawan/add', [KaryawanController::class, 'add' ])->middleware('auth');
Route::post('karyawan/store', [KaryawanController::class, 'store']);
Route::get('/karyawan/profil/{id}', [KaryawanController::class, 'profil'])->middleware('auth');
Route::get('/karyawan/edit/{id}', [KaryawanController::class, 'edit'])->middleware('auth');
Route::post('/karyawan/update/{id}', [KaryawanController::class, 'update']);
Route::post('/karyawan/delete/{id}', [KaryawanController::class, 'delete']);
Route::post('/karyawan/resign/{id}', [KaryawanController::class, 'resign']);
Route::post('/karyawan/updatettd/{id}', [KaryawanController::class, 'updatettd']);
Route::post('/karyawan/password/{id}', [KaryawanController::class, 'password']);

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
Route::get('/prosedur/data', [ProsedurController::class, 'getData'])->middleware('auth');
Route::get('/prosedur/add', [ProsedurController::class, 'add' ])->middleware('auth');
Route::post('prosedur/store', [ProsedurController::class, 'store']);
Route::get('/prosedur/profil/{id}', [ProsedurController::class, 'profil'])->middleware('auth');
Route::get('/prosedur/edit/{id}', [ProsedurController::class, 'edit'])->middleware('auth');
Route::post('/prosedur/update/{id}', [ProsedurController::class, 'update']);
Route::post('/prosedur/delete/{id}', [ProsedurController::class, 'delete']);
Route::get('/prosedur/pdf/{id}', [ProsedurController::class, 'prosedurPdf'])->name('prosedur.pdf')->middleware('auth');

Route::get('/el0101', [RefrensiDocController::class, 'el0101'])->name('el0101')->middleware('auth');
Route::post('/refrensi/data', [RefrensiDocController::class, 'getData'])->middleware('auth');
Route::get('/refrensi/add', [RefrensiDocController::class, 'add' ])->middleware('auth');
Route::post('/refrensi/store', [RefrensiDocController::class, 'store'])->name('store');
Route::get('/refrensi/profil/{id}', [RefrensiDocController::class, 'profil'])->middleware('auth');
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
Route::get('/aturan/profil/{id}', [AturanController::class, 'profil'])->middleware('auth');
Route::get('/aturan/edit/{id}', [AturanController::class, 'edit'])->middleware('auth');
Route::post('/aturan/update/{id}', [AturanController::class, 'update']);
Route::post('/aturan/delete/{id}', [AturanController::class, 'delete']);
Route::get('/aturan/pdf/{id}', [AturanController::class, 'aturanPdf'])->name('aturan.pdf')->middleware('auth');
