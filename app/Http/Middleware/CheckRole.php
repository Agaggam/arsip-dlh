<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->role) {
            Auth::logout();
            return redirect()->route('login')->with('status_error', 'Akun Anda belum memiliki Role. Hubungi Admin.');
        }

        $userRole = $request->user()->role->name;
        
        // Ubah string roles menjadi array (mendukung variadic arg maupun dipisah koma)
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // Jika role user termasuk dalam daftar yang diizinkan, LANJUTKAN (tidak redirect)
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // Jika role tidak diizinkan, redirect berdasarkan role
        if ($userRole === 'user') {
            return redirect()->route('dashboard');
        }

        // Untuk role lain (super_admin, admin) yang tidak diizinkan
        // seharusnya tidak terjadi karena mereka diizinkan di atas
        // tapi jika terjadi, redirect ke admin.dashboard
        return redirect()->route('admin.dashboard');
    }
}