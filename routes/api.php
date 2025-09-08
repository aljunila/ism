<?php
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/pdf', App\Http\Controllers\Api\PendaftaranController::class);
Route::apiResource('/akun', App\Http\Controllers\Api\AkunController::class);
