<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController; // Pastikan ini ada
use App\Http\Controllers\AdminCandidateController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController; // Pastikan ini ada
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\AdminFinanceController;

// 1. HALAMAN DEPAN (HOMEPAGE)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. JALUR PENDAFTARAN (PUBLIC / TAMU)
// Ganti [AdminCandidateController::class, 'create'] menjadi [RegistrationController::class, 'index']
Route::get('/pendaftaran', [RegistrationController::class, 'index'])->name('pendaftaran.create');
Route::post('/pendaftaran', [RegistrationController::class, 'store'])->name('pendaftaran.store');
Route::get('/sukses/{id}', [RegistrationController::class, 'sukses'])->name('pendaftaran.sukses');

// 3. DASHBOARD ADMIN (Butuh Login)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Pengaturan PPDB (Admin)
    Route::get('/admin/pengaturan', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/admin/pengaturan', [SettingController::class, 'update'])->name('admin.settings.update');

    Route::get('/admin/jenis-pembayaran', [PaymentTypeController::class, 'index'])->name('admin.payment_types.index');
    Route::post('/admin/jenis-pembayaran', [PaymentTypeController::class, 'store'])->name('admin.payment_types.store');
    Route::put('/admin/jenis-pembayaran/{id}', [PaymentTypeController::class, 'update'])->name('admin.payment_types.update');
    Route::delete('/admin/jenis-pembayaran/{id}', [PaymentTypeController::class, 'destroy'])->name('admin.payment_types.destroy');

    // Route CRUD Admin Santri
    Route::get('/admin/santri', [AdminCandidateController::class, 'index'])->name('admin.candidates.index');
    Route::get('/admin/santri/create', [AdminCandidateController::class, 'create'])->name('admin.candidates.create');
    Route::post('/admin/santri/store', [AdminCandidateController::class, 'store'])->name('admin.candidates.store');
    Route::get('/admin/santri/{id}', [AdminCandidateController::class, 'show'])->name('admin.candidates.show');
    Route::get('/admin/santri/{id}/edit', [AdminCandidateController::class, 'edit'])->name('admin.candidates.edit');
    Route::put('/admin/santri/{id}', [AdminCandidateController::class, 'update'])->name('admin.candidates.update');
    Route::patch('/admin/santri/{id}/status', [AdminCandidateController::class, 'updateStatus'])->name('admin.candidates.updateStatus');
    Route::get('/admin/candidates/{id}/print', [AdminCandidateController::class, 'printCard'])->name('admin.candidates.print');

    // Route Transaksi
    Route::post('/admin/santri/{id}/pay', [AdminTransactionController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transaksi/{id}/cetak', [AdminTransactionController::class, 'print'])->name('admin.transactions.print');

    // Route Keuangan
    Route::get('/admin/keuangan', [AdminFinanceController::class, 'index'])->name('admin.finance.index');
    Route::post('/admin/keuangan', [AdminFinanceController::class, 'store'])->name('admin.finance.store');
    Route::delete('/admin/keuangan/{id}', [AdminFinanceController::class, 'destroy'])->name('admin.finance.destroy');
});

require __DIR__.'/auth.php';