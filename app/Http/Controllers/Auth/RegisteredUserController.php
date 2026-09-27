<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\Department;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Ambil semua bidang kecuali 'System' untuk ditampilkan di dropdown
        $departments = Department::where('name', '!=', 'System')->get();

        return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate OTP 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => 3,
            'status'    => 'pending',
            'department_id' => null,
        ]);

        // [LOW-02] Set OTP fields via direct assignment (bukan mass-assignment)
        $user->email_otp_code       = $otp;
        $user->email_otp_expires_at = now()->addMinutes(10);
        $user->save();

        event(new Registered($user));
        Auth::login($user);

        // Kirim email OTP
        Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->name));

        return redirect()->route('verification.otp');
    }
}
