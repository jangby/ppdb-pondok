<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi (mass assignment)
    protected $guarded = ['id'];

    // Relasi ke User (Akun Login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi 1 Santri punya 1 Alamat
    public function address()
    {
        return $this->hasOne(CandidateAddress::class);
    }

    // Relasi 1 Santri punya 1 Data Orang Tua
    public function parent()
    {
        return $this->hasOne(CandidateParent::class);
    }

    // Relasi 1 Santri punya Banyak Tagihan
    public function bills()
    {
        return $this->hasMany(CandidateBill::class);
    }

    // Relasi 1 Santri punya Banyak Riwayat Transaksi (Kwitansi)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Relasi ke Jawaban Wawancara
    public function interview_answers()
    {
        return $this->hasMany(InterviewAnswer::class);
    }

    // Helper untuk cek apakah sudah wawancara (Untuk Auto Lulus)
    public function hasCompletedInterview()
    {
        // Cek apakah ada jawaban dari kategori Santri DAN Wali
        $hasSantri = $this->interview_answers()->whereHas('question', function($q){
            $q->where('target', 'Santri');
        })->exists();

        $hasWali = $this->interview_answers()->whereHas('question', function($q){
            $q->where('target', 'Wali');
        })->exists();

        return $hasSantri && $hasWali;
    }

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class);
    }

    public function test_room()
{
    return $this->belongsTo(TestRoom::class);
}

public function santri_room()
    {
        return $this->belongsTo(TestRoom::class, 'santri_room_id');
    }

    public function wali_room()
    {
        return $this->belongsTo(TestRoom::class, 'wali_room_id');
    }

    /**
     * The "booted" method of the model.
     * Logika Hapus Otomatis (Cascading Delete)
     */
    protected static function booted()
    {
        static::deleting(function ($candidate) {
            // [URUTAN SANGAT PENTING] 
            
            // 1. Hapus Transaksi & Detailnya TERLEBIH DAHULU
            // (Karena TransactionDetail punya FK ke CandidateBill, jadi detail harus hilang dulu sebelum bill dihapus)
            foreach ($candidate->transactions()->get() as $transaction) {
                $transaction->details()->delete(); // Hapus detail transaksi (memutus link ke tagihan)
                $transaction->delete();            // Hapus header transaksi
            }

            // 2. Baru Hapus Tagihan (Bills)
            // (Aman dihapus sekarang karena tidak ada transaction_details yang mengikatnya lagi)
            $candidate->bills()->delete();

            // 3. Hapus Data Relasi Lainnya
            $candidate->address()->delete();
            $candidate->parent()->delete();
            $candidate->interview_answers()->delete();
            
            // 4. Hapus File Fisik (Jika Ada)
            if (!empty($candidate->file_perjanjian)) {
                \Illuminate\Support\Facades\Storage::delete($candidate->file_perjanjian);
            }
            
            if (!empty($candidate->pas_foto)) {
                \Illuminate\Support\Facades\Storage::delete($candidate->pas_foto);
            }

            // 5. Hapus Akun Login (User) jika terhubung
            if ($candidate->user) {
                $candidate->user->delete();
            }
        });
    }

    // Tambahkan ini agar tidak error "Undefined relationship"
    public function verification()
    {
        return $this->hasOne(Verification::class);
    }
}