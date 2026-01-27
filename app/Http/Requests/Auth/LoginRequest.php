<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Handle rate limiting dan autentikasi.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Coba Login
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            
            // --- LOGIKA HUKUMAN BERTINGKAT ---
            $key = $this->throttleKey();
            $levelKey = $key . ':level';

            // Tambah 1 Hit (Percobaan Gagal)
            RateLimiter::hit($key);

            // Cek apakah sudah melewati batas (5 kali)?
            if (RateLimiter::tooManyAttempts($key, 5)) {
                
                // 1. Naikkan Level Hukuman
                // Level 1 = 60s, Level 2 = 120s, Level 3 = 300s, dst.
                $currentLevel = \Illuminate\Support\Facades\Cache::get($levelKey, 0) + 1;
                
                $decaySeconds = match ($currentLevel) {
                    1 => 60,    // 1 Menit
                    2 => 120,   // 2 Menit
                    3 => 300,   // 5 Menit
                    4 => 600,   // 10 Menit
                    default => 900, // 15 Menit
                };

                // 2. Simpan Level Hukuman ke Cache (Simpan selama 2 jam)
                \Illuminate\Support\Facades\Cache::put($levelKey, $currentLevel, now()->addHours(2));

                // 3. TERAPKAN HUKUMAN WAKTU (Override Default Laravel)
                // Kita reset dulu limiternya, lalu isi penuh "slot"-nya dengan durasi baru
                RateLimiter::clear($key);
                
                // Isi 5x hit sekaligus dengan durasi decay yang baru
                for ($i = 0; $i <= 5; $i++) {
                    RateLimiter::hit($key, $decaySeconds);
                }
            }

            // Lempar Error "Kredensial Salah"
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Jika Login SUKSES, Hapus semua hukuman
        RateLimiter::clear($this->throttleKey());
        \Illuminate\Support\Facades\Cache::forget($this->throttleKey() . ':level');
    }

    /**
     * Cek apakah user sedang dikunci.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
