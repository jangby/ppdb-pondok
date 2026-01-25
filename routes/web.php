<?php

use Illuminate\Support\Facades\Route;
// Import Controller Default & Auth
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

// Import Controller Fitur Utama
use App\Http\Controllers\VerificationController;      // Untuk User Upload Berkas
use App\Http\Controllers\RegistrationController;      // Untuk User Isi Biodata
use App\Http\Controllers\AdminVerificationController; // Admin ACC Berkas

// Import Controller Admin Manajemen
use App\Http\Controllers\AdminCandidateController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminFinanceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\DormitoryController; // [PENTING] Import Controller Asrama

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
Route::get('/pendaftaran/verifikasi', [VerificationController::class, 'showUploadForm'])->name('pendaftaran.create');
Route::post('/pendaftaran/verifikasi', [VerificationController::class, 'store'])->name('pendaftaran.verify.store');
Route::get('/pendaftaran/verifikasi/sukses', [VerificationController::class, 'showSuccess'])->name('pendaftaran.verify.success');

// --- TAHAP 2: PENGISIAN FORMULIR (LINK DARI WA) ---
Route::get('/pendaftaran/form/{token}', [RegistrationController::class, 'showForm'])->name('pendaftaran.form');
Route::post('/pendaftaran/store', [RegistrationController::class, 'store'])->name('pendaftaran.store');
Route::get('/sukses/{no_daftar}', [RegistrationController::class, 'sukses'])->name('pendaftaran.sukses');


// ROUTE KHUSUS WAWANCARA (Tanpa Login, Pakai Token)
Route::prefix('e-interview')->name('interview.')->group(function () {
    // Panitia
    Route::get('/panitia/{token}', [App\Http\Controllers\PanitiaInterviewController::class, 'index'])->name('panitia.index');
    Route::get('/panitia/{token}/form/{candidate_id}', [App\Http\Controllers\PanitiaInterviewController::class, 'form'])->name('panitia.form');
    Route::post('/panitia/{token}/store/{candidate_id}', [App\Http\Controllers\PanitiaInterviewController::class, 'store'])->name('panitia.store');

    // Santri (Asesmen Mandiri)
    Route::get('/santri/login', [App\Http\Controllers\SantriInterviewController::class, 'login'])->name('santri.login');
    Route::post('/santri/check', [App\Http\Controllers\SantriInterviewController::class, 'check'])->name('santri.check');
    Route::get('/santri/form', [App\Http\Controllers\SantriInterviewController::class, 'form'])->name('santri.form');
    Route::post('/santri/store', [App\Http\Controllers\SantriInterviewController::class, 'store'])->name('santri.store');
    Route::get('/santri/success', [App\Http\Controllers\SantriInterviewController::class, 'success'])->name('santri.success');
});


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

    // --- MANAJEMEN VERIFIKASI BERKAS ---
    Route::get('/admin/verifikasi', [AdminVerificationController::class, 'index'])->name('admin.verifications.index');
    Route::post('/admin/verifikasi/{id}/approve', [AdminVerificationController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('/admin/verifikasi/{id}/reject', [AdminVerificationController::class, 'reject'])->name('admin.verifications.reject');

    // --- PENGATURAN PPDB ---
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
    
    Route::get('/admin/santri/export', [AdminCandidateController::class, 'export'])->name('admin.candidates.export');
    Route::get('/admin/santri/create', [AdminCandidateController::class, 'create'])->name('admin.candidates.create');
    Route::post('/admin/santri/store', [AdminCandidateController::class, 'store'])->name('admin.candidates.store');

    // Route Wildcard Santri
    Route::get('/admin/santri/{id}', [AdminCandidateController::class, 'show'])->name('admin.candidates.show');
    Route::get('/admin/santri/{id}/edit', [AdminCandidateController::class, 'edit'])->name('admin.candidates.edit');
    Route::put('/admin/santri/{id}', [AdminCandidateController::class, 'update'])->name('admin.candidates.update');
    Route::delete('/admin/santri/{id}', [AdminCandidateController::class, 'destroy'])->name('admin.candidates.destroy');
    Route::patch('/admin/santri/{id}/status', [AdminCandidateController::class, 'updateStatus'])->name('admin.candidates.updateStatus');
    Route::get('/admin/candidates/{id}/print', [AdminCandidateController::class, 'printCard'])->name('admin.candidates.print');

    // --- [BARU] MANAJEMEN ASRAMA ---
    // Menggunakan prefix group agar nama routenya konsisten 'admin.dormitories.*'
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('dormitories', DormitoryController::class)->only(['index', 'store', 'destroy']);
        
        // Route untuk tombol "Distribusi Otomatis"
        Route::post('dormitories/auto-distribute', [DormitoryController::class, 'autoDistribute'])->name('dormitories.distribute');
    });

    // --- TRANSAKSI PEMBAYARAN ---
    Route::post('/admin/santri/{id}/pay', [AdminTransactionController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transaksi/{id}/cetak', [AdminTransactionController::class, 'print'])->name('admin.transactions.print');

    // --- KEUANGAN (PENGELUARAN) ---
    Route::get('/admin/keuangan', [AdminFinanceController::class, 'index'])->name('admin.finance.index');
    Route::post('/admin/keuangan', [AdminFinanceController::class, 'store'])->name('admin.finance.store');
    Route::delete('/admin/keuangan/{id}', [AdminFinanceController::class, 'destroy'])->name('admin.finance.destroy');

    // --- SELEKSI & WAWANCARA (ADMIN) ---
    Route::prefix('admin/interview')->name('admin.interview.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\InterviewDashboardController::class, 'index'])->name('dashboard');
        Route::get('/result/{id}', [App\Http\Controllers\InterviewDashboardController::class, 'result'])->name('result');
        Route::get('/export', [App\Http\Controllers\InterviewDashboardController::class, 'exportExcel'])->name('export');
        
        // Questions
        Route::get('/questions', [App\Http\Controllers\InterviewQuestionController::class, 'index'])->name('questions.index');
        Route::post('/questions', [App\Http\Controllers\InterviewQuestionController::class, 'store'])->name('questions.store');
        Route::delete('/questions/{id}', [App\Http\Controllers\InterviewQuestionController::class, 'destroy'])->name('questions.destroy');
        
        // Sessions
        Route::get('/sessions', [App\Http\Controllers\InterviewSessionController::class, 'index'])->name('sessions.index');
        Route::post('/sessions', [App\Http\Controllers\InterviewSessionController::class, 'store'])->name('sessions.store');
        Route::patch('/sessions/{id}/toggle', [App\Http\Controllers\InterviewSessionController::class, 'toggle'])->name('sessions.toggle');
        
        Route::get('/result/{id}/print', [App\Http\Controllers\InterviewDashboardController::class, 'printResult'])->name('result.print');
    });
});

require __DIR__.'/auth.php';