<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->role) {
            Auth::logout();
            return redirect()->route('login')->with('status_error', 'Akun Anda belum memiliki Role. Hubungi Admin.');
        }

        $userRole = $request->user()->role->name;

        // Jika role user sama dengan yang diminta, lanjutkan
        if ($userRole === $role) {
            return $next($request);
        }

        // Jika tidak sama, arahkan berdasarkan role user
        if (in_array($userRole, ['super_admin', 'admin'])) {
            // Jika admin atau super_admin coba akses route yang bukan untuk mereka, arahkan ke admin.dashboard
            return redirect()->route('admin.dashboard');
        }

        if ($userRole === 'user') {
            return redirect()->route('dashboard');
        }

        // Default logout
        Auth::logout();
        return redirect()->route('login')->withErrors(['error' => 'Akses ditolak.']);
    }
}