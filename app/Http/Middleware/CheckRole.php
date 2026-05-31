<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login dan rolenya sesuai dengan yang diizinkan
        if (auth()->check() && in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses, kembalikan ke halaman dashboard/awal dengan pesan error
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}