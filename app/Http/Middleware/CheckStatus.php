<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Symfony\Component\HttpFoundation\Response;

class CheckStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (Auth::check()) {
            $user = Auth::user();

            // 2. Jika statusnya BUKAN approved
            if ($user->status !== 'approved') {
                
                // Pengecualian: Super Admin boleh lewat meski statusnya aneh (biar admin gak terkunci)
                if ($user->role && $user->role->name === 'super_admin') {
                    return $next($request);
                }

                // 3. Ambil status untuk pesan error
                $status = $user->status;

                // 4. Logout paksa
                Auth::logout();

                // 5. Hancurkan session agar benar-benar bersih
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = $status === 'pending' 
                    ? 'Akun Anda sedang dalam tinjauan admin. Mohon tunggu.' 
                    : 'Akun Anda ditolak. Silakan hubungi admin.';

                return redirect()->route('login')->with('status_error', $message);
            }
        }

        return $next($request);
    }
}