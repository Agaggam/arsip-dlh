<x-guest-layout>
    <div class="text-center mb-8">
        {{-- Icon --}}
        <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Verifikasi Email</h2>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">
            Kami telah mengirim kode <strong>6 digit</strong> ke<br>
            <span class="font-semibold text-indigo-600">{{ Auth::user()->email }}</span>
        </p>
    </div>

    {{-- Status kirim ulang --}}
    @if (session('status') === 'kode-terkirim')
        <div class="flex items-center gap-3 p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Kode baru berhasil dikirim! Periksa inbox email Anda.</span>
        </div>
    @endif

    {{-- Warning session --}}
    @if (session('warning'))
        <div class="flex items-center gap-3 p-4 mb-6 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl text-sm">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    {{-- Error OTP --}}
    @if ($errors->has('otp'))
        <div class="flex items-center gap-3 p-4 mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $errors->first('otp') }}</span>
        </div>
    @endif

    {{-- Form OTP --}}
    <form method="POST" action="{{ route('verification.otp.verify') }}" id="otpForm">
        @csrf
        <input type="hidden" name="otp" id="otpHidden">

        <p class="text-xs text-slate-400 text-center mb-4 uppercase tracking-widest font-bold">Masukkan Kode</p>

        {{-- 6 Kotak OTP --}}
        <div class="flex justify-center gap-3 mb-8" id="otpInputs">
            @for ($i = 0; $i < 6; $i++)
                <input type="text"
                       maxlength="1"
                       inputmode="numeric"
                       pattern="[0-9]"
                       class="otp-digit w-12 h-14 text-center text-2xl font-black text-slate-800 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all duration-200 caret-transparent"
                       autocomplete="off">
            @endfor
        </div>

        {{-- Timer countdown --}}
        <div class="text-center text-xs text-slate-400 mb-6">
            <span id="timerWrap">Kode kedaluwarsa dalam <span id="countdown" class="font-bold text-indigo-600">10:00</span></span>
            <span id="expiredMsg" class="hidden text-red-500 font-medium">Kode sudah kedaluwarsa.</span>
        </div>

        <button type="submit" id="submitBtn"
                class="w-full py-3 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Verifikasi Sekarang
        </button>
    </form>

    {{-- Kirim Ulang --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-slate-400 mb-2">Tidak menerima email?</p>
        <form method="POST" action="{{ route('verification.otp.resend') }}">
            @csrf
            <button type="submit"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 hover:underline transition-colors">
                Kirim Ulang Kode
            </button>
        </form>
    </div>

    {{-- Logout --}}
    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
                Ganti Akun / Log Out
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs  = document.querySelectorAll('.otp-digit');
        const hidden  = document.getElementById('otpHidden');
        const form    = document.getElementById('otpForm');
        const submitBtn = document.getElementById('submitBtn');

        // Sync input → hidden field
        function syncOtp() {
            hidden.value = [...inputs].map(i => i.value).join('');
        }

        // Auto-focus next input
        inputs.forEach((input, idx) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/\D/, '');
                input.value = val;
                syncOtp();

                if (val && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                }

                // Auto-submit jika semua terisi
                if ([...inputs].every(i => i.value)) {
                    setTimeout(() => form.submit(), 150);
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    inputs[idx - 1].focus();
                }
            });

            // Handle paste OTP sekaligus
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                [...pasted].slice(0, 6).forEach((char, i) => {
                    if (inputs[i]) inputs[i].value = char;
                });
                syncOtp();
                inputs[Math.min(pasted.length, 5)].focus();
                if (pasted.length >= 6) setTimeout(() => form.submit(), 150);
            });
        });

        // Focus input pertama
        inputs[0].focus();

        // Countdown timer 10 menit
        let seconds = 10 * 60;
        const countdownEl = document.getElementById('countdown');
        const timerWrap   = document.getElementById('timerWrap');
        const expiredMsg  = document.getElementById('expiredMsg');

        const timer = setInterval(() => {
            seconds--;
            const m = String(Math.floor(seconds / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            countdownEl.textContent = `${m}:${s}`;

            if (seconds <= 0) {
                clearInterval(timer);
                timerWrap.classList.add('hidden');
                expiredMsg.classList.remove('hidden');
                submitBtn.disabled = true;
            }
        }, 1000);
    });
    </script>
</x-guest-layout>
