<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PortalTamanSsoController extends Controller
{
    /**
     * Generate SSO token dan redirect ke Portal Taman.
     * Hanya bisa diakses oleh super_admin.
     */
    public function redirect(): RedirectResponse
    {
        $user = Auth::user();

        // Hanya super admin yang boleh SSO ke Portal Taman
        $isSuperAdmin = $user && ($user->isPureSuperAdmin() || ($user->role && $user->role->name === 'super_admin'));

        if (!$isSuperAdmin) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses Admin Portal Taman hanya dapat diakses oleh Super Admin.');
        }

        // Generate token sekali pakai (one-time token), berlaku 60 detik
        $token = Str::random(64);

        Cache::put('portal_taman_sso_' . $token, [
            'user_id'  => $user->id,
            'username' => $user->name,
            'email'    => $user->email,
            'role'     => 'super_admin',
        ], now()->addSeconds(60));

        return redirect('/portal-taman/admin/sso.php?token=' . urlencode($token));
    }
}
