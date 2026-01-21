<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Tambahkan ini
use App\Models\Setting; // Tambahkan ini
use Illuminate\Support\Facades\Schema; // Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cek dulu apakah tabel settings sudah ada agar tidak error saat migrate awal
        if (Schema::hasTable('settings')) {
            // Ambil data setting (hanya 1 baris)
            $setting = Setting::first();
            
            // Bagikan variabel $global_setting ke SEMUA view
            // Jadi di view manapun bisa panggil {{ $global_setting->nama_gelombang ?? '' }}
            View::share('global_setting', $setting);
        }
    }
}