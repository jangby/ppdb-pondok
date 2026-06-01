<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WahaWebhookController;
use App\Http\Controllers\Api\PPDBController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/waha/webhook', [WahaWebhookController::class, 'handle']);
Route::get('/ppdb/stat', [PPDBController::class, 'getStat']);
Route::get('/ppdb/status/{nomor_pendaftaran}', [PPDBController::class, 'checkStatus']);
Route::get('/ppdb/rincian', [PPDBController::class, 'getRincian']);

// Endpoint untuk sistem pendaftaran via WA
Route::get('/ppdb/wa-check-token/{token}', [PPDBController::class, 'checkTokenWA']);
Route::post('/ppdb/wa-submit-daftar', [PPDBController::class, 'submitDaftarWA']);
Route::get('/ppdb/peserta-lulus', [App\Http\Controllers\Api\PPDBController::class, 'getPesertaLulus']);
Route::get('/ppdb/kontak-wali', [App\Http\Controllers\Api\PPDBController::class, 'getKontakWali']);
