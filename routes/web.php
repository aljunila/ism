<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProsedurController;
use App\Http\Controllers\RefrensiDocController;
use App\Http\Controllers\AturanController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\NotulenController;
use App\Http\Controllers\DaftarHadirController;
use App\Http\Controllers\GantiKKMController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\BbmController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EvaluasiController;

// use App\Http\Controllers\InterviewController;
use App\Http\Controllers\KonditeController;
use App\Http\Controllers\Purchasing\PurchasingController;
use App\Http\Controllers\Data_master\KodeFormController;
use App\Http\Controllers\Data_master\MenuController;
use App\Http\Controllers\Data_master\KendaraanController;
use App\Http\Controllers\Data_master\PelabuhanController;
use App\Http\Controllers\Data_master\BiayaController;
use App\Http\Controllers\Data_master\JenisCutiController;
use App\Http\Controllers\AclController;
use App\Http\Controllers\Acl\RoleController;
use App\Http\Controllers\Acl\UserController;
use App\Http\Controllers\Acl\CabangController;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Http\Controllers\Data_kapal\TripController;
use App\Http\Controllers\Data_crew\RecruitmentController;
use App\Http\Controllers\Data_crew\FamiliarisasiController;
use App\Http\Controllers\Data_crew\GantiController;
use App\Http\Controllers\Data_crew\CutiController;
use App\Http\Controllers\Data_crew\MutasiController;
use App\Http\Controllers\Data_crew\PelatihanController;

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

// Pilih context role/perusahaan (auth saja)
Route::middleware(['auth'])->group(function () {
    Route::get('active-context/options', [\App\Http\Controllers\ActiveContextController::class, 'options']);
    Route::post('active-context/set', [\App\Http\Controllers\ActiveContextController::class, 'set']);
});

Route::middleware(['auth', 'active.role', 'menu.access'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'show'])->name('show');
    Route::get('form_ism', [KodeFormController::class, 'form']);
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
        Route::post('delfile/{id}', [KapalController::class, 'delfile']);
        Route::post('docking_store/{id}', [KapalController::class, 'docking_store']);
        Route::get('pdf/{id}', [KapalController::class, 'pdf'])->name('kapal.pdf');
        Route::post('datafile', [KapalController::class, 'getFile']);
        Route::get('doclist/{id}', [KapalController::class, 'doclist'])->name('kapal.doclist');
        Route::get('pdfdoclist/{id}', [KapalController::class, 'pdfdoclist'])->name('kapal.pdfdoclist');
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
        Route::get('crewlist/{id}', [KaryawanController::class, 'crewlist'])->name('karyawan.crewlist');
        Route::get('pdfcrewlist/{id}', [KaryawanController::class, 'pdfcrewlist'])->name('karyawan.pdfcrewlist');
        Route::get('pdfcontact/{id}', [KaryawanController::class, 'pdfcontact'])->name('karyawan.pdfcontact');
    });

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
    Route::get('/refrensi/pdf', [RefrensiDocController::class, 'pdf'])->name('refrensi.pdf')->middleware('auth');
    Route::get('/el0503', [RefrensiDocController::class, 'el0503'])->name('el0503')->middleware('auth');
    Route::post('/refrensi/data-sampah', [RefrensiDocController::class, 'getsampah'])->middleware('auth');
    Route::get('/refrensi/addsampah', [RefrensiDocController::class, 'addsampah' ])->middleware('auth');
    Route::post('/refrensi/storesampah', [RefrensiDocController::class, 'storesampah'])->name('store');
    Route::get('/refrensi/editsampah/{id}', [RefrensiDocController::class, 'editsampah'])->middleware('auth');
    Route::post('/refrensi/updatesampah/{id}', [RefrensiDocController::class, 'updatesampah']);
    Route::post('/refrensi/delsampah/{id}', [RefrensiDocController::class, 'delsampah']);
    Route::get('/refrensi/pdfsampah', [RefrensiDocController::class, 'pdfsampah'])->name('refrensi.pdfsampah')->middleware('auth');
    Route::get('/el0507', [RefrensiDocController::class, 'el0507'])->name('el0507')->middleware('auth');
    Route::post('/refrensi/data-minyak', [RefrensiDocController::class, 'getminyak'])->middleware('auth');
    Route::get('/refrensi/addminyak', [RefrensiDocController::class, 'addminyak' ])->middleware('auth');
    Route::post('/refrensi/storeminyak', [RefrensiDocController::class, 'storeminyak'])->name('store');
    Route::get('/refrensi/editminyak/{id}', [RefrensiDocController::class, 'editminyak'])->middleware('auth');
    Route::post('/refrensi/updateminyak/{id}', [RefrensiDocController::class, 'updateminyak']);
    Route::post('/refrensi/delminyak/{id}', [RefrensiDocController::class, 'delminyak']);
    Route::get('/refrensi/pdfminyak', [RefrensiDocController::class, 'pdfminyak'])->name('refrensi.pdfminyak')->middleware('auth');
    Route::get('/el0510', [RefrensiDocController::class, 'el0510'])->name('el0510')->middleware('auth');
    Route::post('/refrensi/data-peta', [RefrensiDocController::class, 'getpeta'])->middleware('auth');
    Route::get('/refrensi/addpeta', [RefrensiDocController::class, 'addpeta' ])->middleware('auth');
    Route::post('/refrensi/storepeta', [RefrensiDocController::class, 'storepeta'])->name('store');
    Route::get('/refrensi/editpeta/{id}', [RefrensiDocController::class, 'editpeta'])->middleware('auth');
    Route::post('/refrensi/updatepeta/{id}', [RefrensiDocController::class, 'updatepeta']);
    Route::post('/refrensi/delpeta/{id}', [RefrensiDocController::class, 'delpeta']);
    Route::get('/refrensi/pdfpeta', [RefrensiDocController::class, 'pdfpeta'])->name('refrensi.pdfpeta')->middleware('auth');

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

    Route::prefix('data_master')->group(function() {
        Route::get('kode_form', [KodeFormController::class, 'index']);
        Route::get('kode_form/data', [KodeFormController::class, 'data'])->name('kode_form.data');
        Route::get('kode_form/ism', [KodeFormController::class, 'ism'])->name('kode_form.ism');
        Route::post('kode_form', [KodeFormController::class, 'store'])->name('kode_form.store');
        Route::put('kode_form/{id}', [KodeFormController::class, 'update'])->name('kode_form.update');
        Route::delete('kode_form/{id}', [KodeFormController::class, 'destroy'])->name('kode_form.destroy');

        // Placeholder ACL menu
        Route::get('menu', [MenuController::class, 'index'])->name('acl.menu');
        Route::get('menu/data', [MenuController::class, 'data'])->name('acl.menu.data');
        Route::post('menu', [MenuController::class, 'store'])->name('acl.menu.store');
        Route::put('menu/{id}', [MenuController::class, 'update'])->name('acl.menu.update');
        Route::delete('menu/{id}', [MenuController::class, 'destroy'])->name('acl.menu.destroy');

        Route::get('kendaraan', [KendaraanController::class, 'index']);
        Route::get('kendaraan/data', [KendaraanController::class, 'data'])->name('kendaraan.data');
        Route::post('kendaraan', [KendaraanController::class, 'store'])->name('kendaraan.store');
        Route::put('kendaraan/{id}', [KendaraanController::class, 'update'])->name('kendaraan.update');
        Route::delete('kendaraan/{id}', [KendaraanController::class, 'destroy'])->name('kendaraan.destroy');

        Route::get('pelabuhan', [PelabuhanController::class, 'index']);
        Route::get('pelabuhan/data', [PelabuhanController::class, 'data'])->name('pelabuhan.data');
        Route::post('pelabuhan', [PelabuhanController::class, 'store'])->name('pelabuhan.store');
        Route::put('pelabuhan/{id}', [PelabuhanController::class, 'update'])->name('pelabuhan.update');
        Route::delete('pelabuhan/{id}', [PelabuhanController::class, 'destroy'])->name('pelabuhan.destroy');

        Route::get('biaya', [BiayaController::class, 'index']);
        Route::get('biaya/data', [BiayaController::class, 'data'])->name('biaya.data');
        Route::post('biaya', [BiayaController::class, 'store'])->name('biaya.store');
        Route::put('biaya/{id}', [BiayaController::class, 'update'])->name('biaya.update');
        Route::delete('biaya/{id}', [BiayaController::class, 'destroy'])->name('biaya.destroy');

        Route::get('mcuti', [JenisCutiController::class, 'index']);
        Route::get('mcuti/data', [JenisCutiController::class, 'data'])->name('mcuti.data');
        Route::post('mcuti', [JenisCutiController::class, 'store'])->name('mcuti.store');
        Route::put('mcuti/{id}', [JenisCutiController::class, 'update'])->name('mcuti.update');
        Route::delete('mcuti/{id}', [JenisCutiController::class, 'destroy'])->name('mcuti.destroy');
    });
    
    Route::get('get-pelabuhan/{id_kapal}', [PelabuhanController::class, 'getPelabuhan']);
    Route::prefix('acl')->group(function () {
        Route::get('roles', [AclController::class, 'roles'])->name('acl.roles');
        Route::get('users', [AclController::class, 'users'])->name('acl.users');
        Route::get('roles/data', [RoleController::class, 'data'])->name('acl.roles.data');
        Route::get('roles/all', [RoleController::class, 'all'])->name('acl.roles.all');
        Route::post('roles', [RoleController::class, 'store'])->name('acl.roles.store');
        Route::put('roles/{id}', [RoleController::class, 'update'])->name('acl.roles.update');
        Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('acl.roles.destroy');
        Route::get('roles/menu/{roleId}', [RoleController::class, 'getRoleMenus'])->name('acl.roles.menu');
        Route::post('roles/map-menu', [RoleController::class, 'mapMenu'])->name('acl.roles.mapmenu');

        Route::get('users/data', [UserController::class, 'data'])->name('acl.users.data');
        Route::post('users', [UserController::class, 'store'])->name('acl.users.store');
        Route::put('users/{id}/status', [UserController::class, 'toggleStatus'])->name('acl.users.status');
        Route::get('users/{id}', [UserController::class, 'show'])->name('acl.users.show');
        Route::put('users/{id}', [UserController::class, 'update'])->name('acl.users.update');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('acl.users.destroy');

        Route::get('cabang', [AclController::class, 'cabang'])->name('acl.cabang');
        Route::get('cabang/data', [CabangController::class, 'data'])->name('acl.cabang.data');
        Route::post('cabang', [CabangController::class, 'store'])->name('acl.cabang.store');
        Route::put('cabang/{id}/status', [CabangController::class, 'toggleStatus'])->name('acl.cabang.status');
        Route::get('cabang/{id}', [CabangController::class, 'show'])->name('acl.cabang.show');
        Route::put('cabang/{id}', [CabangController::class, 'update'])->name('acl.cabang.update');
        Route::delete('cabang/{id}', [CabangController::class, 'destroy'])->name('acl.cabang.destroy');

        // Simple API helpers for select options
        Route::get('api/perusahaan/all', function () {
            return Perusahaan::select('id', 'kode', 'nama')->get();
        });
        Route::get('api/karyawan/all', function (Request $request) {
            $query = Karyawan::select('id', 'nama', 'nik', 'id_perusahaan');
            if ($request->filled('perusahaan_id')) {
                $query->where('id_perusahaan', $request->get('perusahaan_id'));
            }
            return $query->get();
        });
    });

    Route::prefix('data_kapal')->group(function() {
        Route::get('trip', [TripController::class, 'index']);
        Route::post('trip/data', [TripController::class, 'data'])->name('trip.data');
        Route::get('trip/form', [TripController::class, 'form'])->name('trip.form');
        Route::get('/trip/form/{id}', [TripController::class, 'form'])->name('trip.edit');
        Route::get('/trip/{id}/amount', [TripController::class, 'amount'])->name('trip.amount');
        Route::post('trip', [TripController::class, 'store'])->name('trip.store');
        Route::post('/trip/update/{uid}', [TripController::class, 'update'])->name('trip.update');
        Route::delete('trip/{id}', [TripController::class, 'destroy'])->name('trip.destroy');
        Route::get('/trip/{id}/excel', [TripController::class, 'TripExcel'])->name('trip.excel');
    });

    Route::prefix('data_crew')->group(function() {
        Route::get('recruitment', [RecruitmentController::class, 'index']);
        Route::get('recruitment/data', [RecruitmentController::class, 'data'])->name('recruitment.data');
        Route::get('recruitment/form', [RecruitmentController::class, 'form'])->name('recruitment.form');
        Route::get('/recruitment/form/{id}', [RecruitmentController::class, 'form'])->name('recruitment.form');
        Route::post('recruitment', [RecruitmentController::class, 'store'])->name('recruitment.store');
        Route::put('recruitment/{id}', [RecruitmentController::class, 'update'])->name('recruitment.update');
        Route::post('recruitment/savedata/{id}', [RecruitmentController::class, 'savedata'])->name('recruitment.savedata');
        Route::delete('recruitment/{id}', [RecruitmentController::class, 'destroy'])->name('recruitment.destroy');
        Route::get('/recruitment/pdf/{id}', [RecruitmentController::class, 'pdf'])->name('recruitment.pdf');
        Route::get('/recruitment/{id}', [RecruitmentController::class, 'elemen'])->name('recruitment.elemen');
        Route::post('/recruitment/getData', [RecruitmentController::class, 'getData'])->name('recruitment.getData');

        Route::get('familiarisasi', [FamiliarisasiController::class, 'index']);
        Route::get('familiarisasi/data', [FamiliarisasiController::class, 'data'])->name('familiarisasi.data');
        Route::get('familiarisasi/form', [FamiliarisasiController::class, 'form'])->name('familiarisasi.form');
        Route::get('/familiarisasi/form/{id}', [FamiliarisasiController::class, 'form'])->name('familiarisasi.form');
        Route::post('familiarisasi', [FamiliarisasiController::class, 'store'])->name('familiarisasi.store');
        Route::put('familiarisasi/{id}', [FamiliarisasiController::class, 'update'])->name('familiarisasi.update');
        Route::post('familiarisasi/savedata/{id}', [FamiliarisasiController::class, 'savedata'])->name('familiarisasi.savedata');
        Route::delete('familiarisasi/{id}', [FamiliarisasiController::class, 'destroy'])->name('familiarisasi.destroy');
        Route::get('/familiarisasi/pdf/{id}', [FamiliarisasiController::class, 'pdf'])->name('familiarisasi.pdf');
        Route::get('/familiarisasi/{id}', [FamiliarisasiController::class, 'elemen'])->name('familiarisasi.elemen');
        Route::post('/familiarisasi/getData', [FamiliarisasiController::class, 'getData'])->name('familiarisasi.getData');

        Route::get('ganti', [GantiController::class, 'index']);
        Route::get('ganti/data', [GantiController::class, 'data'])->name('ganti.data');
        Route::get('ganti/form', [GantiController::class, 'form'])->name('ganti.form');
        Route::get('/ganti/form/{id}', [GantiController::class, 'form'])->name('ganti.form');
        Route::post('ganti', [GantiController::class, 'store'])->name('ganti.store');
        Route::put('ganti/{id}', [GantiController::class, 'update'])->name('ganti.update');
        Route::post('ganti/savedata/{id}', [GantiController::class, 'savedata'])->name('ganti.savedata');
        Route::delete('ganti/{id}', [GantiController::class, 'destroy'])->name('ganti.destroy');
        Route::get('/ganti/pdf/{id}', [GantiController::class, 'pdf'])->name('ganti.pdf');
        Route::get('/ganti/{id}', [GantiController::class, 'elemen'])->name('ganti.elemen');
        Route::post('/ganti/getData', [GantiController::class, 'getData'])->name('ganti.getData');

        Route::get('cuti', [CutiController::class, 'index']);
        Route::get('cuti/data', [CutiController::class, 'data'])->name('cuti.data');
        Route::post('cuti', [CutiController::class, 'store'])->name('cuti.store');
        Route::put('cuti/{id}', [CutiController::class, 'update'])->name('cuti.update');
        Route::delete('cuti/{id}', [CutiController::class, 'destroy'])->name('cuti.destroy');
        Route::delete('cuti/reject/{id}', [CutiController::class, 'reject'])->name('cuti.reject');
        Route::post('cuti/databyId', [CutiController::class, 'databyId'])->name('cuti.databyId');

        Route::get('mutasi', [MutasiController::class, 'index']);
        Route::get('mutasi/data', [MutasiController::class, 'data'])->name('mutasi.data');
        Route::post('mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
        Route::put('mutasi/{id}', [MutasiController::class, 'update'])->name('mutasi.update');
        Route::delete('mutasi/{id}', [MutasiController::class, 'destroy'])->name('mutasi.destroy');
        Route::get('/mutasi/pdf', [MutasiController::class, 'pdf'])->name('mutasi.pdf');
        Route::get('/mutasi/{id}', [MutasiController::class, 'elemen'])->name('mutasi.elemen');
        Route::post('/mutasi/getData', [MutasiController::class, 'getData'])->name('mutasi.getData');

        Route::get('pelatihan', [PelatihanController::class, 'index']);
        Route::get('pelatihan/data', [PelatihanController::class, 'data'])->name('pelatihan.data');
        Route::post('pelatihan', [PelatihanController::class, 'store'])->name('pelatihan.store');
        Route::put('pelatihan/{id}', [PelatihanController::class, 'update'])->name('pelatihan.update');
        Route::delete('pelatihan/{id}', [PelatihanController::class, 'destroy'])->name('pelatihan.destroy');
        Route::get('/pelatihan/pdf', [PelatihanController::class, 'pdf'])->name('pelatihan.pdf');
        Route::get('/pelatihan/{id}', [PelatihanController::class, 'elemen'])->name('pelatihan.elemen');
        Route::post('/pelatihan/getData', [PelatihanController::class, 'getData'])->name('pelatihan.getData');
    });

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

    // Purchasing
    Route::prefix('purchasing')->group(function(){
        Route::get('/', [PurchasingController::class, 'index']);
    });


    // Route::get('el0302', [ChecklistController::class, 'el0302'])->name('el0302');
    // Route::get('el0303', [ChecklistController::class, 'el0303'])->name('el0303');
    // Route::get('el0304', [ChecklistController::class, 'el0304'])->name('el0304');
    // Route::get('el0305', [ChecklistController::class, 'el0305'])->name('el0305');
    // Route::get('el0307', [ChecklistController::class, 'el0307'])->name('el0307');
    // Route::get('el0311', [ChecklistController::class, 'el0311'])->name('el0311');
    // Route::get('el0312', [ChecklistController::class, 'el0312'])->name('el0312');
    // Route::get('el0308', [ChecklistController::class, 'el0308'])->name('el0308');
    // Route::get('el0309', [ChecklistController::class, 'el0309'])->name('el0309');
    // Route::post('checklist/data', [ChecklistController::class, 'getData']);
    // Route::get('checklist/add/{kode}', [ChecklistController::class, 'add']);
    // Route::post('checklist/store', [ChecklistController::class, 'store'])->name('store');
    // Route::get('checklist/edit/{id}', [ChecklistController::class, 'edit']);
    // Route::post('checklist/update/{id}', [ChecklistController::class, 'update']);
    // Route::post('checklist/delete/{id}', [ChecklistController::class, 'delete']);
    // Route::get('checklist/pdf/{id}', [ChecklistController::class, 'pdf'])->name('checklist.pdf');
    // Route::get('checklist/item/{kode}', [ChecklistController::class, 'item']);
    // Route::post('checklist/dataitem', [ChecklistController::class, 'getItem']);
    // Route::post('checklist/listGanti', [ChecklistController::class, 'getGanti']);
    // Route::get('checklist/addganti/{kode}', [ChecklistController::class, 'addganti']);
    // Route::post('checklist/storeganti', [ChecklistController::class, 'storeganti'])->name('storeganti');
    // Route::get('checklist/editganti/{id}', [ChecklistController::class, 'editganti']);
    // Route::post('checklist/updateganti/{id}', [ChecklistController::class, 'updateganti']);
    // Route::post('checklist/deleteganti/{id}', [ChecklistController::class, 'deleteganti']);
    // Route::get('checklist/gantipdf/{id}', [ChecklistController::class, 'gantipdf'])->name('checklist.gantipdf');
    // Route::post('checklist/getChecklist', [ChecklistController::class, 'getChecklist']);
    // Route::post('checklist/save', [ChecklistController::class, 'save'])->name('save');
    // Route::get('checklist/nahkodapdf/{uid}/{kode}', [ChecklistController::class, 'nahkodapdf'])->name('checklist.nahkodapdf');
    // Route::post('form/intruksi', [ChecklistController::class, 'saveform']);
    // Route::post('checklist/storeitem', [ChecklistController::class, 'storeitem']);
    // Route::get('checklist/edititem/{id}', [ChecklistController::class, 'edititem']);
    // Route::post('checklist/updateitem/{id}', [ChecklistController::class, 'updateitem']);
    // Route::post('checklist/deleteitem/{id}', [ChecklistController::class, 'deleteitem']);
    Route::get('get-karyawan/{id_kapal}', [ChecklistController::class, 'getKaryawan']);

    Route::get('/el0501', [ChecklistController::class, 'el0501'])->name('el0501')->middleware('auth');
    Route::get('/el0502', [ChecklistController::class, 'el0502'])->name('el0502')->middleware('auth');
    Route::get('/el0511', [ChecklistController::class, 'el0511'])->name('el0511')->middleware('auth');
    Route::get('/el0505', [ChecklistController::class, 'el0505'])->name('el0505')->middleware('auth');
    Route::get('/el0508', [ChecklistController::class, 'el0508'])->name('el0508')->middleware('auth');
    Route::get('/el0509', [ChecklistController::class, 'el0509'])->name('el0509')->middleware('auth');   
    Route::get('/checklist/parentadd/{kode}', [ChecklistController::class, 'parentadd' ])->middleware('auth');
    Route::post('/checklist/parentstore', [ChecklistController::class, 'parentstore'])->name('parentstore');
    Route::get('/checklist/parentedit/{id}', [ChecklistController::class, 'parentedit'])->middleware('auth');
    Route::get('/checklist/parentpdf/{id}', [ChecklistController::class, 'parentpdf'])->name('checklist.parentpdf')->middleware('auth');
    Route::post('/checklist/parentupdate/{id}', [ChecklistController::class, 'parentupdate']);
    Route::get('/checklist/parentitem/{kode}', [ChecklistController::class, 'parentitem' ])->middleware('auth');
    Route::get('/checklist/panasadd/{kode}', [ChecklistController::class, 'panasadd' ])->middleware('auth');
    Route::post('/checklist/panasstore', [ChecklistController::class, 'panasstore'])->name('panasstore');
    Route::get('/checklist/panasedit/{id}', [ChecklistController::class, 'panasedit'])->middleware('auth');
    Route::get('/checklist/panaspdf/{id}', [ChecklistController::class, 'panaspdf'])->name('checklist.panaspdf')->middleware('auth');
    Route::post('/checklist/panasupdate/{id}', [ChecklistController::class, 'panasupdate']);
    Route::get('/checklist/panasitem/{kode}', [ChecklistController::class, 'panasitem' ])->middleware('auth');
    Route::post('/checklist/GetPersonil', [ChecklistController::class, 'GetPersonil'])->middleware('auth');
    Route::post('/checklist/deletepersonil/{id}', [ChecklistController::class, 'deletepersonil']);
    Route::post('/checklist/store09', [ChecklistController::class, 'store09'])->name('store09');

    Route::get('el0604', [EvaluasiController::class, 'el0604'])->name('el0604');
    Route::get('el0605', [EvaluasiController::class, 'el0605'])->name('el0605');
    Route::post('evaluasi/data', [EvaluasiController::class, 'getData']);
    Route::get('/evaluasi/add/{kode}', [EvaluasiController::class, 'add' ])->middleware('auth');
    Route::post('/evaluasi/store', [EvaluasiController::class, 'store'])->name('store');
    Route::get('/evaluasi/edit/{id}', [EvaluasiController::class, 'edit'])->middleware('auth');
    Route::post('/evaluasi/update/{id}', [EvaluasiController::class, 'update']);
    Route::post('/evaluasi/delete/{id}', [EvaluasiController::class, 'delete']);
    Route::get('/evaluasi/pdf/{id}', [EvaluasiController::class, 'pdf'])->name('evaluasi.pdf')->middleware('auth');

    Route::get('el0301', [NotulenController::class, 'show'])->name('notulen');
    Route::get('el0404', [NotulenController::class, 'el0404'])->name('el0404');
    Route::get('el0402', [NotulenController::class, 'el0402'])->name('el0402');
    Route::get('el0403', [NotulenController::class, 'el0403'])->name('el0403');
    Route::get('el0401', [NotulenController::class, 'el0401'])->name('el0401');
    Route::post('notulen/data', [NotulenController::class, 'getData']);
    Route::get('notulen/add', [NotulenController::class, 'add']);
    Route::post('notulen/store', [NotulenController::class, 'store']);
    Route::get('notulen/edit/{id}', [NotulenController::class, 'edit']);
    Route::post('notulen/update/{id}', [NotulenController::class, 'update']);
    Route::post('notulen/delete/{id}', [NotulenController::class, 'delete']);
    Route::get('notulen/pdf/{id}', [NotulenController::class, 'notulenPdf'])->name('notulen.pdf');
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
    Route::post('notulen/data41', [NotulenController::class, 'getData41']);
    Route::get('/notulen/pdf41/{id_perusahaan}/{tahun}', [NotulenController::class, 'pdf41']);

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

    Route::get('/el0504', [BbmController::class, 'el0504'])->name('el0504')->middleware('auth');
    Route::post('/bbm/data', [BbmController::class, 'getData'])->middleware('auth');
    Route::get('/bbm/add/{kode}', [BbmController::class, 'add' ])->middleware('auth');
    Route::post('/bbm/store', [BbmController::class, 'store'])->name('store');
    Route::get('/bbm/edit/{id}', [BbmController::class, 'edit'])->middleware('auth');
    Route::post('/bbm/update/{id}', [BbmController::class, 'update']);
    Route::post('/bbm/delete/{id}', [BbmController::class, 'delete']);
    Route::get('/bbm/pdf/{id}', [BbmController::class, 'pdf'])->name('bbm.pdf')->middleware('auth');

    Route::get('/el0506', [AlarmController::class, 'el0506'])->name('el0506')->middleware('auth');
    Route::post('/alarm/data', [AlarmController::class, 'getData'])->middleware('auth');
    Route::get('/alarm/add/{kode}', [AlarmController::class, 'add' ])->middleware('auth');
    Route::post('/alarm/store', [AlarmController::class, 'store'])->name('store');
    Route::get('/alarm/edit/{id}', [AlarmController::class, 'edit'])->middleware('auth');
    Route::post('/alarm/update/{id}', [AlarmController::class, 'update']);
    Route::post('/alarm/delete/{id}', [AlarmController::class, 'delete']);
    Route::get('/alarm/pdf/{id}', [AlarmController::class, 'pdf'])->name('alarm.pdf')->middleware('auth');

    Route::get('/el0512', [ReviewController::class, 'el0512'])->name('el0512')->middleware('auth');
    Route::post('/review/data', [ReviewController::class, 'getData'])->middleware('auth');
    Route::get('/review/add/{kode}', [ReviewController::class, 'add' ])->middleware('auth');
    Route::post('/review/store', [ReviewController::class, 'store'])->name('store');
    Route::get('/review/edit/{id}', [ReviewController::class, 'edit'])->middleware('auth');
    Route::post('/review/update/{id}', [ReviewController::class, 'update']);
    Route::post('/review/delete/{id}', [ReviewController::class, 'delete']);
    Route::get('/review/pdf/{id}', [ReviewController::class, 'pdf'])->name('review.pdf')->middleware('auth');
    Route::get('/review/get/{id}', [ReviewController::class, 'get'])->middleware('auth');
    Route::post('/review/updatedpa/{id}', [ReviewController::class, 'updatedpa']);

    Route::get('/el0602', [PelatihanController::class, 'el0602'])->name('el0602')->middleware('auth');
    Route::get('/el0603', [PelatihanController::class, 'el0603'])->name('el0603')->middleware('auth');
    Route::post('/pelatihan/data', [PelatihanController::class, 'getData'])->middleware('auth');
    Route::post('/pelatihan/store', [PelatihanController::class, 'store'])->name('store');
    Route::get('/pelatihan/edit/{id}', [PelatihanController::class, 'edit'])->middleware('auth');
    Route::post('/pelatihan/update/{id}', [PelatihanController::class, 'update']);
    Route::post('/pelatihan/delete/{id}', [PelatihanController::class, 'delete']);
    Route::get('/pelatihan/pdf', [PelatihanController::class, 'pdf'])->name('pelatihan.pdf')->middleware('auth');
    Route::post('get-karyawanbyJab', [PelatihanController::class, 'getKaryawan']);

    Route::get('/el0608', [KonditeController::class, 'el0608'])->name('el0608')->middleware('auth');
    Route::post('/kondite/data', [KonditeController::class, 'getData'])->middleware('auth');
    Route::post('/kondite/store', [KonditeController::class, 'store'])->name('store');
    Route::get('/kondite/edit/{id}', [KonditeController::class, 'edit'])->middleware('auth');
    Route::post('/kondite/update/{id}', [KonditeController::class, 'update']);
    Route::post('/kondite/delete/{id}', [KonditeController::class, 'delete']);
    Route::get('/kondite/pdf/{id}', [KonditeController::class, 'pdf'])->name('kondite.pdf')->middleware('auth');
    Route::get('/kondite/detail/{uid}', [KonditeController::class, 'detail'])->middleware('auth');
    Route::post('/kondite/datadetail', [KonditeController::class, 'getDetail'])->middleware('auth');
    Route::post('/kondite/getChecklist', [KonditeController::class, 'getChecklist'])->middleware('auth');
    Route::post('/kondite/getKondite', [KonditeController::class, 'getKondite'])->middleware('auth');
    Route::post('/kondite/saveform', [KonditeController::class, 'saveform'])->name('saveform');

    Route::prefix('file')->group(function () {
        Route::get('/', [FileController::class, 'show'])->name('file');
        Route::post('data', [FileController::class, 'getData']);
        Route::get('add', [FileController::class, 'add']);
        Route::post('store', [FileController::class, 'store'])->name('file.store');
        Route::get('edit/{id}', [FileController::class, 'edit']);
        Route::post('update/{id}', [FileController::class, 'update']);
        Route::post('delete/{id}', [FileController::class, 'delete']);
    });

    // Route::prefix('kendaraan')->group(function () {
    //     Route::get('/', [KendaraanController::class, 'index'])->name('kendaraan');
    //     Route::post('data', [KendaraanController::class, 'data']);
    //     Route::get('add', [KendaraanController::class, 'add']);
    //     Route::post('store', [KendaraanController::class, 'store'])->name('file.store');
    //     Route::get('edit/{id}', [KendaraanController::class, 'edit']);
    //     Route::post('update/{id}', [KendaraanController::class, 'update']);
    //     Route::post('delete/{id}', [KendaraanController::class, 'delete']);
    // });

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
        Route::get('view-file/{uid}', [ProsedurController::class, 'view_file'])->name('view_file');
        Route::get('download_file/{uid}', [ProsedurController::class, 'download_file'])->name('download_file');
    });
});
