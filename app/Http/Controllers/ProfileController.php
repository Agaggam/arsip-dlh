<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldEmail = $user->email;
        $oldName = $user->name;
        $newEmail = strtolower(trim($request->email));

        // 1. Simpan perubahan nama bila ada
        if ($request->name !== $oldName) {
            $user->name = $request->name;
            $user->save();
            log_activity($user, 'update_profile', "Nama profil diperbarui dari '{$oldName}' menjadi '{$user->name}'");
        }

        // 2. Cek apakah email diubah
        if ($newEmail === strtolower($oldEmail)) {
            // Email tidak berubah, hanya nama atau tidak ada perubahan
            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        }

        // 3. Email berubah -> Butuh verifikasi OTP ke alamat email baru
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'pending_email_change' => [
                'new_email'  => $newEmail,
                'otp'        => $otp,
                'expires_at' => now()->addMinutes(15),
            ]
        ]);

        try {
            Mail::to($newEmail)->send(new OtpVerificationMail($otp, $user->name, 'change_email'));
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim OTP perubahan email ke {$newEmail}: " . $e->getMessage());
            return Redirect::route('profile.edit')->withErrors([
                'email' => 'Gagal mengirim email verifikasi ke alamat baru. Periksa koneksi email SMTP atau hubungi administrator.',
            ]);
        }

        return Redirect::route('profile.edit')
            ->with('status', 'email-otp-sent')
            ->with('info', "Kode verifikasi 6-digit telah dikirim ke {$newEmail}. Silakan masukkan kode OTP untuk menyelesaikan perubahan email.");
    }

    /**
     * Verifikasi kode OTP untuk mengonfirmasi perubahan email akun.
     */
    public function verifyEmailChange(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus tepat 6 digit angka.',
        ]);

        $pending = session('pending_email_change');

        if (!$pending || empty($pending['new_email']) || empty($pending['otp'])) {
            return Redirect::route('profile.edit')->withErrors(['otp' => 'Tidak ada permohonan perubahan email yang aktif.']);
        }

        if (now()->isAfter($pending['expires_at'])) {
            return Redirect::route('profile.edit')->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan klik kirim ulang kode.']);
        }

        if ($request->otp !== $pending['otp']) {
            return Redirect::route('profile.edit')->withErrors(['otp' => 'Kode OTP tidak cocok. Silakan periksa kembali email Anda.']);
        }

        $user = $request->user();
        $newEmail = $pending['new_email'];

        // Pastikan email baru belum digunakan oleh akun lain
        if (User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            session()->forget('pending_email_change');
            return Redirect::route('profile.edit')->withErrors(['email' => 'Email tersebut sudah digunakan oleh akun lain.']);
        }

        $oldEmail = $user->email;
        $user->email = $newEmail;
        $user->email_verified_at = now();
        $user->save();

        session()->forget('pending_email_change');

        log_activity($user, 'update_email', "Email akun berhasil diubah dari '{$oldEmail}' menjadi '{$newEmail}' melalui verifikasi OTP.");

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('success', "Email Anda berhasil diverifikasi dan diperbarui menjadi {$newEmail}!");
    }

    /**
     * Kirim ulang kode OTP untuk perubahan email.
     */
    public function resendEmailChangeOtp(Request $request): RedirectResponse
    {
        $pending = session('pending_email_change');

        if (!$pending || empty($pending['new_email'])) {
            return Redirect::route('profile.edit')->withErrors(['email' => 'Tidak ada permohonan perubahan email yang aktif.']);
        }

        $user = $request->user();
        $newEmail = $pending['new_email'];
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'pending_email_change' => [
                'new_email'  => $newEmail,
                'otp'        => $otp,
                'expires_at' => now()->addMinutes(15),
            ]
        ]);

        try {
            Mail::to($newEmail)->send(new OtpVerificationMail($otp, $user->name, 'change_email'));
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim ulang OTP perubahan email ke {$newEmail}: " . $e->getMessage());
            return Redirect::route('profile.edit')->withErrors([
                'otp' => 'Gagal mengirim ulang kode OTP. Silakan coba sesaat lagi.',
            ]);
        }

        return Redirect::route('profile.edit')
            ->with('status', 'email-otp-sent')
            ->with('info', "Kode OTP baru telah dikirimkan ke {$newEmail}.");
    }

    /**
     * Batalkan permintaan perubahan email.
     */
    public function cancelEmailChange(Request $request): RedirectResponse
    {
        session()->forget('pending_email_change');
        return Redirect::route('profile.edit')->with('info', 'Permintaan perubahan email telah dibatalkan.');
    }

    /**
     * Delete the user's account without password confirmation.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $userName = $user->name;
        $userEmail = $user->email;

        // Catat log penghapusan akun mandiri
        log_activity($user, 'delete_account', "Akun {$userName} ({$userEmail}) dihapus sendiri tanpa konfirmasi password");

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}