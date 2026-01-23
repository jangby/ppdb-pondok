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
        
        $requirements = json_decode($settings['syarat_pendaftaran'] ?? '[]', true) ?? [];
        $facilities = json_decode($settings['fasilitas_sekolah'] ?? '[]', true) ?? [];
        $galleries = json_decode($settings['galeri_sekolah'] ?? '[]', true) ?? [];
        $jenjangs = json_decode($settings['list_jenjang'] ?? '["SMP","SMK"]', true) ?? [];

        return view('admin.settings.index', compact('settings', 'requirements', 'facilities', 'galleries', 'jenjangs'));
    }

    public function update(Request $request)
    {
        // 1. Simpan Data Teks Biasa
        $generalKeys = [
            'nama_sekolah', 'alamat_sekolah', 'status_ppdb', 'tgl_buka', 'tgl_tutup', 
            'whatsapp_admin', 'pengumuman', 'nama_gelombang', 'deskripsi_banner',
            'verification_active' 
        ];

        foreach ($generalKeys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
            }
        }

        // 2. Simpan Daftar Jenjang
        $jenjangNames = $request->input('jenjang_nama', []);
        $jenjangData = array_filter($jenjangNames, fn($val) => !empty($val)); 
        Setting::updateOrCreate(['key' => 'list_jenjang'], ['value' => json_encode(array_values($jenjangData))]);

        // 3. Upload Banner
        if ($request->hasFile('banner_image')) {
            $path = $this->compressAndUpload($request->file('banner_image'), 'banners', 1200);
            Setting::updateOrCreate(['key' => 'banner_image'], ['value' => $path]);
        }

        // 4. [BARU] Upload Logo Sekolah
        if ($request->hasFile('logo_sekolah')) {
            // Ukuran logo lebih kecil (max 500px)
            $path = $this->compressAndUpload($request->file('logo_sekolah'), 'logo', 500);
            Setting::updateOrCreate(['key' => 'logo_sekolah'], ['value' => $path]);
        }

        // 5. Upload Galeri
        if ($request->hasFile('gallery_files')) {
            $currentGallery = json_decode(Setting::getValue('galeri_sekolah', '[]'), true) ?? [];
            foreach ($request->file('gallery_files') as $file) {
                $path = $this->compressAndUpload($file, 'gallery', 800);
                $currentGallery[] = $path;
            }
            Setting::updateOrCreate(['key' => 'galeri_sekolah'], ['value' => json_encode($currentGallery)]);
        }

        // 6. Upload Template
        if ($request->hasFile('template_perjanjian')) {
            $path = $request->file('template_perjanjian')->store('templates', 'public');
            Setting::updateOrCreate(['key' => 'template_perjanjian'], ['value' => $path]);
        }

        // 7. Simpan Persyaratan & Fasilitas
        $reqNames = $request->input('syarat_nama', []);
        $reqQtys = $request->input('syarat_jumlah', []);
        $reqData = [];
        foreach ($reqNames as $i => $name) {
            if (!empty($name)) $reqData[] = ['nama' => $name, 'jumlah' => $reqQtys[$i] ?? 1];
        }
        Setting::updateOrCreate(['key' => 'syarat_pendaftaran'], ['value' => json_encode($reqData)]);

        $facNames = $request->input('fasilitas_nama', []);
        $facData = array_filter($facNames, fn($val) => !empty($val));
        Setting::updateOrCreate(['key' => 'fasilitas_sekolah'], ['value' => json_encode(array_values($facData))]);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function deleteGallery(Request $request)
    {
        $index = $request->index;
        $currentGallery = json_decode(Setting::getValue('galeri_sekolah', '[]'), true) ?? [];

        if (isset($currentGallery[$index])) {
            if (Storage::disk('public')->exists($currentGallery[$index])) {
                Storage::disk('public')->delete($currentGallery[$index]);
            }
            unset($currentGallery[$index]);
            $currentGallery = array_values($currentGallery);
            Setting::updateOrCreate(['key' => 'galeri_sekolah'], ['value' => json_encode($currentGallery)]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    private function compressAndUpload($file, $folder, $maxWidth = 1000)
    {
        $path = storage_path("app/public/{$folder}");
        if (!file_exists($path)) mkdir($path, 0777, true);
        
        $filename = uniqid() . '.jpg';
        $destination = "{$path}/{$filename}";
        
        list($width, $height, $type) = getimagesize($file);
        switch ($type) {
            case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($file); break;
            case IMAGETYPE_PNG:  $source = imagecreatefrompng($file); break;
            case IMAGETYPE_WEBP: $source = imagecreatefromwebp($file); break;
            default: return $file->store($folder, 'public');
        }

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $virtualImage = imagecreatetruecolor($newWidth, $newHeight);
        
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagecolortransparent($virtualImage, imagecolorallocatealpha($virtualImage, 0, 0, 0, 127));
            imagealphablending($virtualImage, false);
            imagesavealpha($virtualImage, true);
        }

        imagecopyresampled($virtualImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($virtualImage, $destination, 80);
        
        imagedestroy($virtualImage);
        imagedestroy($source);

        return "{$folder}/{$filename}";
    }
}