<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // 2. PROTEKSI: Cek apakah relasi role ada (mencegah error "property on null")
        if (!$request->user()->role) {
            Auth::logout(); // Logout paksa karena datanya rusak/kosong
            return redirect()->route('login')->with('status_error', 'Akun Anda belum memiliki Role. Hubungi Admin.');
        }

        $userRole = $request->user()->role->name;

        // 3. Cek apakah Role sesuai dengan yang diminta Route
        if ($userRole !== $role) {
            
            // Jika dia admin tapi nyasar ke route user biasa
            if ($userRole === 'super_admin') {
                return redirect()->route('admin.dashboard');
            }
            
            // Jika dia user biasa tapi nyasar ke route admin
            if ($userRole === 'user') {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
