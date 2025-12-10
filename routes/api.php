<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Perusahaan;
use App\Models\Karyawan;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/pdf', App\Http\Controllers\Api\PendaftaranController::class);
Route::apiResource('/akun', App\Http\Controllers\Api\AkunController::class);

// Token refresh endpoint (API)
Route::post('/auth/refresh', [\App\Http\Controllers\LoginController::class, 'refreshToken']);

// Perusahaan & karyawan list (untuk select) – lewat bearer token + ACL
Route::middleware(['auth.token', 'active.role', 'menu.access'])->group(function () {
    Route::get('/perusahaan/all', function () {
        return Perusahaan::select('id', 'kode', 'nama')->get();
    });

    Route::get('/karyawan/all', function (Request $request) {
        $query = Karyawan::select('id', 'nama', 'nik', 'id_perusahaan');
        if ($request->filled('perusahaan_id')) {
            $query->where('id_perusahaan', $request->get('perusahaan_id'));
        }
        return $query->get();
    });
});
