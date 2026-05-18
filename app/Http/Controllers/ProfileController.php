<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Catat log perubahan
        $changes = [];
        if ($user->name != $oldName) {
            $changes[] = "nama dari '{$oldName}' menjadi '{$user->name}'";
        }
        if ($user->email != $oldEmail) {
            $changes[] = "email dari '{$oldEmail}' menjadi '{$user->email}'";
        }

        if (!empty($changes)) {
            log_activity($user, 'update_profile', 'Profil diperbarui: ' . implode(', ', $changes));
        } else {
            // Jika tidak ada perubahan (optional, bisa dihilangkan)
            log_activity($user, 'update_profile', 'Profil diperbarui tanpa perubahan data');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userName = $user->name;
        $userEmail = $user->email;

        // Catat log dengan menyimpan nama dan email ke description
        log_activity($user, 'delete_account', "Akun {$userName} ({$userEmail}) dihapus sendiri");

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}