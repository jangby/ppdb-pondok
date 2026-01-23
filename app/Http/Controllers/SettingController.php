<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        // Decode JSON Data
        $requirements = json_decode($settings['syarat_pendaftaran'] ?? '[]', true) ?? [];
        $facilities = json_decode($settings['fasilitas_sekolah'] ?? '[]', true) ?? [];
        $galleries = json_decode($settings['galeri_sekolah'] ?? '[]', true) ?? [];

        return view('admin.settings.index', compact('settings', 'requirements', 'facilities', 'galleries'));
    }

    public function update(Request $request)
    {
        // 1. Simpan Data Teks Biasa
        $generalKeys = ['nama_sekolah', 'status_ppdb', 'tgl_buka', 'tgl_tutup', 'whatsapp_admin', 'pengumuman', 'nama_gelombang', 'deskripsi_banner'];
        foreach ($generalKeys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
            }
        }

        // 2. Upload & Kompres Banner (Single Image)
        if ($request->hasFile('banner_image')) {
            $path = $this->compressAndUpload($request->file('banner_image'), 'banners', 1200); // Max width 1200px
            Setting::updateOrCreate(['key' => 'banner_image'], ['value' => $path]);
        }

        // 3. Upload & Kompres Galeri (Multiple Images)
        if ($request->hasFile('gallery_files')) {
            $currentGallery = json_decode(Setting::getValue('galeri_sekolah', '[]'), true) ?? [];
            
            foreach ($request->file('gallery_files') as $file) {
                $path = $this->compressAndUpload($file, 'gallery', 800); // Max width 800px
                $currentGallery[] = $path;
            }
            
            Setting::updateOrCreate(['key' => 'galeri_sekolah'], ['value' => json_encode($currentGallery)]);
        }

        // 5. Upload Template Perjanjian (Oleh Admin)
        if ($request->hasFile('template_perjanjian')) {
            $path = $request->file('template_perjanjian')->store('templates', 'public');
            Setting::updateOrCreate(['key' => 'template_perjanjian'], ['value' => $path]);
        }

        // 4. Simpan Persyaratan (JSON)
        $reqNames = $request->input('syarat_nama', []);
        $reqQtys = $request->input('syarat_jumlah', []);
        $reqData = [];
        foreach ($reqNames as $i => $name) {
            if (!empty($name)) $reqData[] = ['nama' => $name, 'jumlah' => $reqQtys[$i] ?? 1];
        }
        Setting::updateOrCreate(['key' => 'syarat_pendaftaran'], ['value' => json_encode($reqData)]);

        // 5. Simpan Fasilitas (JSON)
        $facNames = $request->input('fasilitas_nama', []);
        $facData = array_filter($facNames, fn($val) => !empty($val)); // Hapus input kosong
        Setting::updateOrCreate(['key' => 'fasilitas_sekolah'], ['value' => json_encode(array_values($facData))]);

        return back()->with('success', 'Pengaturan dan media berhasil diperbarui.');
    }

    // Fitur Hapus Foto Galeri Tertentu
    public function deleteGallery(Request $request)
    {
        $index = $request->index;
        $currentGallery = json_decode(Setting::getValue('galeri_sekolah', '[]'), true) ?? [];

        if (isset($currentGallery[$index])) {
            // Hapus file fisik
            if (Storage::disk('public')->exists($currentGallery[$index])) {
                Storage::disk('public')->delete($currentGallery[$index]);
            }
            // Hapus dari array DB
            unset($currentGallery[$index]);
            // Re-index array agar urutan 0,1,2... kembali benar
            $currentGallery = array_values($currentGallery);
            
            Setting::updateOrCreate(['key' => 'galeri_sekolah'], ['value' => json_encode($currentGallery)]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // --- HELPER KOMPRESI GAMBAR (Native PHP) ---
    private function compressAndUpload($file, $folder, $maxWidth = 1000)
    {
        // Pastikan folder ada
        $path = storage_path("app/public/{$folder}");
        if (!file_exists($path)) mkdir($path, 0777, true);

        // Buat nama file unik
        $filename = uniqid() . '.jpg';
        $destination = "{$path}/{$filename}";

        // Baca Info Gambar
        list($width, $height, $type) = getimagesize($file);
        
        // Load Gambar berdasarkan tipe
        switch ($type) {
            case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($file); break;
            case IMAGETYPE_PNG:  $source = imagecreatefrompng($file); break;
            case IMAGETYPE_WEBP: $source = imagecreatefromwebp($file); break;
            default: return $file->store($folder, 'public'); // Fallback jika format aneh
        }

        // Hitung Ukuran Baru (Resize)
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Buat Canvas Baru & Resize
        $virtualImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Handle transparansi untuk PNG/WEBP
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagecolortransparent($virtualImage, imagecolorallocatealpha($virtualImage, 0, 0, 0, 127));
            imagealphablending($virtualImage, false);
            imagesavealpha($virtualImage, true);
        }

        imagecopyresampled($virtualImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Simpan sebagai JPG (Quality 80%) untuk ukuran kecil
        // Jika aslinya PNG transparan, sebaiknya tetap PNG, tapi untuk foto kegiatan JPG lebih hemat
        imagejpeg($virtualImage, $destination, 80);

        // Bersihkan memori
        imagedestroy($virtualImage);
        imagedestroy($source);

        return "{$folder}/{$filename}";
    }
}