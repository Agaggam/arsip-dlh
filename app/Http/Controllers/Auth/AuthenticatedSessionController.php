<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // RateLimiting sudah dihandle di LoginRequest::authenticate()
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Jika email belum terverifikasi (OTP belum dikonfirmasi) → arahkan ke halaman OTP
        if (!$user->hasVerifiedEmail()) {
            if (!$user->email_otp_code || ($user->email_otp_expires_at && now()->isAfter($user->email_otp_expires_at))) {
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->email_otp_code = $otp;
                $user->email_otp_expires_at = now()->addMinutes(10);
                $user->save();

                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp, $user->name));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Gagal kirim OTP saat login: ' . $e->getMessage());
                }
            }

            return redirect()->route('verification.otp')
                ->with('warning', 'Akun Anda belum diverifikasi. Silakan masukkan kode OTP yang dikirim ke email Anda.');
        }

        // Redirect langsung berdasarkan role (tanpa intended() untuk hindari loop dari URL lama)
        if ($user->role && in_array($user->role->name, ['super_admin', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}