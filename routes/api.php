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
Route::get('/ppdb/status/{nik}', [PPDBController::class, 'checkStatus']);
