<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    /**
     * Tampilkan halaman input OTP
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->redirectTo());
        }

        return view('auth.otp-verify');
    }

    /**
     * Verifikasi kode OTP yang dimasukkan user
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus tepat 6 digit.',
        ]);

        $user = Auth::user();

        // Cek apakah sudah terverifikasi
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->redirectTo());
        }

        // Cek apakah kode cocok
        if ($user->email_otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa email Anda.']);
        }

        // Cek apakah kode sudah kedaluwarsa
        if ($user->email_otp_expires_at && now()->isAfter($user->email_otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode.']);
        }

        // Tandai email sudah terverifikasi
        $user->markEmailAsVerified();
        $user->update([
            'email_otp_code'       => null,
            'email_otp_expires_at' => null,
            'status'               => 'active', // Otomatis aktifkan user setelah verifikasi email
        ]);

        return redirect()->intended($this->redirectTo())
            ->with('success', 'Email berhasil diverifikasi! Selamat datang di E-Arsip DLH.');
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->redirectTo());
        }

        // Generate kode baru
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'email_otp_code'       => $otp,
            'email_otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->name));

        return back()->with('status', 'kode-terkirim');
    }

    /**
     * Tujuan redirect setelah verifikasi berhasil
     */
    private function redirectTo(): string
    {
        $user = Auth::user();

        if ($user->isPureSuperAdmin() || $user->isAdmin()) {
            return route('admin.dashboard');
        }

        return route('dashboard');
    }
}
