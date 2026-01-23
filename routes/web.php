<?php

use Illuminate\Support\Facades\Route;
// Import Controller Default & Auth
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

// Import Controller Fitur Utama
use App\Http\Controllers\VerificationController;      // [BARU] Untuk User Upload Berkas
use App\Http\Controllers\RegistrationController;      // Untuk User Isi Biodata
use App\Http\Controllers\AdminVerificationController; // [BARU] Admin ACC Berkas

// Import Controller Admin Manajemen
use App\Http\Controllers\AdminCandidateController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminFinanceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PaymentTypeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. HALAMAN PUBLIK (TANPA LOGIN)
// =========================================================================

// Halaman Depan (Landing Page)
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- TAHAP 1: VERIFIKASI BERKAS (ENTRY POINT) ---
// Halaman untuk download template & upload berkas bertanda tangan
Route::get('/pendaftaran/verifikasi', [VerificationController::class, 'showUploadForm'])->name('pendaftaran.create');
// Proses simpan berkas verifikasi
Route::post('/pendaftaran/verifikasi', [VerificationController::class, 'store'])->name('pendaftaran.verify.store');

Route::get('/pendaftaran/verifikasi/sukses', [VerificationController::class, 'showSuccess'])->name('pendaftaran.verify.success');

// --- TAHAP 2: PENGISIAN FORMULIR (LINK DARI WA) ---
// Halaman form biodata (Hanya bisa diakses jika punya Token valid dari WA)
Route::get('/pendaftaran/form/{token}', [RegistrationController::class, 'showForm'])->name('pendaftaran.form');
// Proses simpan data santri
Route::post('/pendaftaran/store', [RegistrationController::class, 'store'])->name('pendaftaran.store');

// Halaman Sukses
Route::get('/sukses/{no_daftar}', [RegistrationController::class, 'sukses'])->name('pendaftaran.sukses');


// =========================================================================
// 2. HALAMAN ADMIN (BUTUH LOGIN)
// =========================================================================

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // --- PROFILE ADMIN ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- [BARU] MANAJEMEN VERIFIKASI BERKAS ---
    Route::get('/admin/verifikasi', [AdminVerificationController::class, 'index'])->name('admin.verifications.index');
    Route::post('/admin/verifikasi/{id}/approve', [AdminVerificationController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('/admin/verifikasi/{id}/reject', [AdminVerificationController::class, 'reject'])->name('admin.verifications.reject');

    // --- PENGATURAN PPDB (BANNER, GALERI, TEMPLATE) ---
    Route::get('/admin/pengaturan', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/admin/pengaturan', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::delete('/admin/settings/gallery', [SettingController::class, 'deleteGallery'])->name('admin.settings.delete_gallery');

    // --- MANAJEMEN JENIS PEMBAYARAN ---
    Route::get('/admin/jenis-pembayaran', [PaymentTypeController::class, 'index'])->name('admin.payment_types.index');
    Route::post('/admin/jenis-pembayaran', [PaymentTypeController::class, 'store'])->name('admin.payment_types.store');
    Route::put('/admin/jenis-pembayaran/{id}', [PaymentTypeController::class, 'update'])->name('admin.payment_types.update');
    Route::delete('/admin/jenis-pembayaran/{id}', [PaymentTypeController::class, 'destroy'])->name('admin.payment_types.destroy');

    // --- MANAJEMEN DATA SANTRI ---
    Route::get('/admin/santri', [AdminCandidateController::class, 'index'])->name('admin.candidates.index');
    Route::post('/admin/keuangan/export-setor', [AdminFinanceController::class, 'exportDeposit'])->name('admin.finance.export_deposit');
    
    // (PENTING: Route Export harus SEBELUM route {id} agar tidak 404)
    Route::get('/admin/santri/export', [AdminCandidateController::class, 'export'])->name('admin.candidates.export');
    
    Route::get('/admin/santri/create', [AdminCandidateController::class, 'create'])->name('admin.candidates.create');
    Route::post('/admin/santri/store', [AdminCandidateController::class, 'store'])->name('admin.candidates.store');
    
    // Route dengan Wildcard {id} ditaruh di bawah
    Route::get('/admin/santri/{id}', [AdminCandidateController::class, 'show'])->name('admin.candidates.show');
    Route::get('/admin/santri/{id}/edit', [AdminCandidateController::class, 'edit'])->name('admin.candidates.edit');
    Route::put('/admin/santri/{id}', [AdminCandidateController::class, 'update'])->name('admin.candidates.update');
    Route::delete('/admin/santri/{id}', [AdminCandidateController::class, 'destroy'])->name('admin.candidates.destroy'); // Tambahan delete
    
    Route::patch('/admin/santri/{id}/status', [AdminCandidateController::class, 'updateStatus'])->name('admin.candidates.updateStatus');
    Route::get('/admin/candidates/{id}/print', [AdminCandidateController::class, 'printCard'])->name('admin.candidates.print');

    // --- TRANSAKSI PEMBAYARAN ---
    Route::post('/admin/santri/{id}/pay', [AdminTransactionController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transaksi/{id}/cetak', [AdminTransactionController::class, 'print'])->name('admin.transactions.print');

    // --- KEUANGAN (PENGELUARAN) ---
    Route::get('/admin/keuangan', [AdminFinanceController::class, 'index'])->name('admin.finance.index');
    Route::post('/admin/keuangan', [AdminFinanceController::class, 'store'])->name('admin.finance.store');
    Route::delete('/admin/keuangan/{id}', [AdminFinanceController::class, 'destroy'])->name('admin.finance.destroy');
});

require __DIR__.'/auth.php';