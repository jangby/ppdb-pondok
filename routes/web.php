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
use App\Http\Controllers\DormitoryController;
use App\Http\Controllers\InterviewAttendanceController; // Controller Absensi
use App\Http\Controllers\QueueController;               // Controller Loket
use App\Models\WebhookLog;

/*
|--------------------------------------------------------------------------
| Web Routes (Publik & Admin)
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. HALAMAN PUBLIK (TANPA LOGIN)
// =========================================================================

// Halaman Depan
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- TAHAP 1: VERIFIKASI BERKAS ---
Route::get('/pendaftaran/verifikasi', [VerificationController::class, 'showUploadForm'])->name('pendaftaran.create');
Route::post('/pendaftaran/verifikasi', [VerificationController::class, 'store'])->name('pendaftaran.verify.store');
Route::get('/pendaftaran/verifikasi/sukses', [VerificationController::class, 'showSuccess'])->name('pendaftaran.verify.success');

// [BARU] TAHAP 1.5: UPLOAD BUKTI BAYAR
Route::get('/pendaftaran/bayar/{token}', [VerificationController::class, 'showPaymentForm'])->name('pendaftaran.payment');
Route::post('/pendaftaran/bayar/{token}', [VerificationController::class, 'storePayment'])->name('pendaftaran.payment.store');
Route::get('/pendaftaran/bayar/sukses/ok', [VerificationController::class, 'showPaymentSuccess'])->name('pendaftaran.payment.success');

// --- TAHAP 2: PENGISIAN FORMULIR (LINK DARI WA) ---
Route::get('/pendaftaran/form/{token}', [RegistrationController::class, 'showForm'])->name('pendaftaran.form');
Route::post('/pendaftaran/store', [RegistrationController::class, 'store'])->name('pendaftaran.store');
Route::get('/sukses/{no_daftar}', [RegistrationController::class, 'sukses'])->name('pendaftaran.sukses');

// --- HALAMAN KARTU TES (PUBLIK) ---
Route::get('/kartu-tes/{no_daftar}', [HomeController::class, 'kartuTes'])->name('public.kartu_tes');

// Halaman awal input nomor pendaftaran
Route::get('/cek-pendaftaran', [App\Http\Controllers\PublicFinanceController::class, 'index'])
    ->name('public.pendaftaran.index');

// Proses pengecekan nomor pendaftaran
Route::post('/cek-pendaftaran', [App\Http\Controllers\PublicFinanceController::class, 'check'])
    ->name('public.pendaftaran.check');

// Halaman rincian status & keuangan
Route::get('/cek-pendaftaran/{no_daftar}', [App\Http\Controllers\PublicFinanceController::class, 'show'])
    ->name('public.pendaftaran.show');

// Unduh struk
Route::get('/cek-pendaftaran/{no_daftar}/struk/{transaction_id}', [App\Http\Controllers\PublicFinanceController::class, 'downloadReceipt'])
    ->name('public.pendaftaran.receipt');

// [BARU] Route Edit Data Mandiri
Route::get('/pendaftaran/edit/{no_daftar}', [App\Http\Controllers\RegistrationController::class, 'editPublic'])
    ->name('pendaftaran.edit_public');
Route::put('/pendaftaran/update/{no_daftar}', [App\Http\Controllers\RegistrationController::class, 'updatePublic'])
    ->name('pendaftaran.update_public');

// --- LOKET PANGGILAN (MOBILE VIEW - TANPA LOGIN) ---
Route::get('/loket-panggilan', [QueueController::class, 'publicIndex'])->name('public.queue.index');
Route::post('/loket-panggilan/next', [QueueController::class, 'callNext'])->name('public.queue.next');

// --- MONITOR ANTRIAN (UNTUK ORANG TUA) ---
Route::get('/pantau-antrian', [QueueController::class, 'publicMonitor'])->name('public.queue.monitor');
Route::get('/pantau-antrian/check', [QueueController::class, 'getCurrentStatus'])->name('public.queue.check');

// --- ROUTE WAWANCARA (TOKEN BASED) ---
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

Route::post('/e-interview/scan-qr', [App\Http\Controllers\InterviewSessionController::class, 'scanQr'])
    ->name('panitia.interview.scan');


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
    Route::post('/admin/verifikasi/{id}/resend-wa/{tahap}', [App\Http\Controllers\AdminVerificationController::class, 'resendWa'])->name('admin.verifications.resend_wa');

    Route::post('/admin/verifications/{id}/register-basic', [AdminVerificationController::class, 'registerBasic'])
     ->name('admin.verifications.register_basic');

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

    // --- RINCIAN PEMASUKAN DASHBOARD ---
    Route::get('/dashboard/pemasukan', [DashboardController::class, 'rincianPemasukan'])->name('dashboard.pemasukan.rincian');

    Route::get('/admin/santri/lanjutan/create', [AdminCandidateController::class, 'createLanjutan'])->name('admin.candidates.lanjutan.create');
    Route::post('/admin/santri/lanjutan', [AdminCandidateController::class, 'storeLanjutan'])->name('admin.candidates.lanjutan.store');

    // Route Wildcard Santri
    Route::get('/admin/santri/{id}', [AdminCandidateController::class, 'show'])->name('admin.candidates.show');
    Route::get('/admin/santri/{id}/edit', [AdminCandidateController::class, 'edit'])->name('admin.candidates.edit');
    Route::put('/admin/santri/{id}', [AdminCandidateController::class, 'update'])->name('admin.candidates.update');
    Route::delete('/admin/santri/{id}', [AdminCandidateController::class, 'destroy'])->name('admin.candidates.destroy');
    Route::patch('/admin/santri/{id}/status', [AdminCandidateController::class, 'updateStatus'])->name('admin.candidates.updateStatus');
    Route::post('/admin/santri/{id}/generate-missing-bills', [AdminCandidateController::class, 'generateMissingBills'])->name('admin.candidates.generate_missing_bills');
    Route::get('/admin/candidates/{id}/print', [AdminCandidateController::class, 'printCard'])->name('admin.candidates.print');
    Route::post('/candidates/{candidate}/upload-perjanjian', [AdminCandidateController::class, 'uploadPerjanjian'])->name('admin.candidates.upload_perjanjian');

    // [TAMBAHKAN INI] Route Kirim Notifikasi Tagihan
    Route::post('/admin/santri/{id}/notify-bill', [AdminCandidateController::class, 'sendBillNotification'])
        ->name('admin.candidates.notify_bill');

    // --- MANAJEMEN ASRAMA ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('dormitories', DormitoryController::class)->only(['index', 'store', 'destroy']);
        Route::post('dormitories/auto-distribute', [DormitoryController::class, 'autoDistribute'])->name('dormitories.distribute');
    });

    // --- TRANSAKSI PEMBAYARAN ---
    Route::post('/admin/santri/{id}/pay', [AdminTransactionController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transaksi/{id}/cetak', [AdminTransactionController::class, 'print'])->name('admin.transactions.print');
    Route::get('/admin/transaksi/{id}/print-data', [App\Http\Controllers\AdminTransactionController::class, 'getDataForPrinter'])
    ->name('admin.transactions.print_data');
    Route::delete('/admin/transaksi/{id}/batal', [AdminTransactionController::class, 'destroy'])->name('admin.transactions.destroy');
    Route::post('/admin/tagihan/{id}/rekonstruksi', [App\Http\Controllers\AdminFinanceController::class, 'reconstructBill'])->name('admin.bills.reconstruct');
    Route::post('/admin/tagihan/{id}/fix-anomaly', [App\Http\Controllers\AdminFinanceController::class, 'fixAnomaly'])->name('admin.bills.fix_anomaly');

    // --- KEUANGAN (PENGELUARAN) ---
    Route::get('/admin/keuangan', [AdminFinanceController::class, 'index'])->name('admin.finance.index');
    Route::get('/admin/keuangan/rincian', [AdminFinanceController::class, 'rincian'])->name('admin.finance.rincian');
    Route::post('/admin/keuangan', [AdminFinanceController::class, 'store'])->name('admin.finance.store');
    Route::delete('/admin/keuangan/{id}', [AdminFinanceController::class, 'destroy'])->name('admin.finance.destroy');
    Route::get('/admin/keuangan/cetak-laporan', [AdminFinanceController::class, 'printReport'])->name('admin.finance.print');

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

    // --- FITUR ABSENSI & WAWANCARA (SCANNER & WA) ---
    Route::prefix('admin/attendance')->name('admin.attendance.')->group(function () {
        // Halaman Scanner
        Route::get('/', [InterviewAttendanceController::class, 'index'])->name('index');
        Route::post('/process', [InterviewAttendanceController::class, 'processScan'])->name('process');
        
        // WA Gateway
        Route::post('/send-qr/{id}', [InterviewAttendanceController::class, 'sendQrToWa'])->name('send_qr');
        Route::post('/remind/{id}', [InterviewAttendanceController::class, 'sendReminder'])->name('remind');
        Route::post('/mass-remind', [InterviewAttendanceController::class, 'massRemind'])->name('mass_remind');

        // Rekapitulasi
        Route::get('/recap', [InterviewAttendanceController::class, 'recap'])->name('recap');
    });

    // --- MANAJEMEN RUANGAN TES ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('test_rooms', \App\Http\Controllers\TestRoomController::class)->only(['index', 'store', 'destroy']);
    Route::get('test_rooms/{id}/print', [\App\Http\Controllers\TestRoomController::class, 'print'])->name('test_rooms.print');
    Route::post('test_rooms/distribute', [\App\Http\Controllers\TestRoomController::class, 'autoDistribute'])->name('test_rooms.distribute');
});

});

Route::get('/cek-webhook', function () {
    // Ambil 50 log terakhir
    $logs = WebhookLog::latest()->take(50)->get();
    return view('debug_webhook', compact('logs'));
});

Route::get('/debug-wa', function (\Illuminate\Http\Request $request) {
    // Gunakan nomor HP Anda sendiri untuk testing (Ganti 628xxx)
    $no_tujuan = $request->query('no', '628xxxxxxxxxx') . '@c.us'; 
    $pesan = "Halo! Ini adalah pesan test dari Server Ubuntu pada " . now();
    
    $url = env('WAHA_BASE_URL', 'http://72.61.208.130:3001') . '/api/sendText';
    $apiKey = env('WAHA_API_KEY', '0f0eb5d196b6459781f7d854aac5050e');
    
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Api-Key'    => $apiKey,
            'Accept'       => 'application/json',
        ])->post($url, [
            'session' => 'default',
            'chatId'  => $no_tujuan,
            'text'    => $pesan
        ]);
        
        return response()->json([
            '1_KESIMPULAN' => $response->successful() ? 'API BERHASIL DIHIT!' : 'API GAGAL DIHIT!',
            '2_URL_YANG_DIPAKAI' => $url,
            '3_HTTP_STATUS' => $response->status(),
            '4_RESPONSE_DARI_WAHA' => $response->json(),
            '5_SARAN' => 'Jika status 201/200 tapi WA tidak masuk, masalah BUKAN di kodingan Laravel, melainkan di Session WAHA Server Ubuntu Anda yang terputus.'
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            '1_KESIMPULAN' => 'LARAVEL GAGAL KONEK KE WAHA',
            '2_ERROR_MESSAGE' => $e->getMessage(),
            '3_SARAN' => 'Ini masalah jaringan server. Pastikan Firewall Ubuntu (UFW) tidak memblokir port keluar.'
        ]);
    }
});

require __DIR__.'/auth.php';