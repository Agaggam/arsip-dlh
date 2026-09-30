<section>
    <header>
        <h2 class="text-lg font-bold text-slate-800">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            {{ __('Perbarui informasi profil dan alamat email akun Anda. Perubahan email memerlukan verifikasi OTP.') }}
        </p>
    </header>

    {{-- ALERT SUKSES / INFO --}}
    @if (session('success'))
        <div class="mt-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('info'))
        <div class="mt-4 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    {{-- KOTAK VERIFIKASI OTP PERUBAHAN EMAIL --}}
    @if(session('pending_email_change'))
        <div class="mt-6 p-6 rounded-2xl bg-gradient-to-br from-indigo-50/90 via-white to-violet-50/80 border-2 border-indigo-200 text-slate-800 shadow-md">
            <div class="flex items-start gap-3.5">
                <div class="p-2.5 bg-indigo-600 text-white rounded-xl shrink-0 mt-0.5 shadow-md shadow-indigo-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-sm text-indigo-950">Verifikasi Email Baru Diperlukan</h4>
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full border border-amber-200">Menunggu OTP</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                        Kode OTP 6-digit telah dikirimkan ke alamat email baru: <span class="font-bold text-indigo-700 underline">{{ session('pending_email_change.new_email') }}</span>. 
                        Masukkan kode untuk menyelesaikan dan mengaktifkan email baru Anda.
                    </p>

                    <form method="POST" action="{{ route('profile.email.verify') }}" class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        @csrf
                        <div class="relative flex-1 sm:max-w-xs">
                            <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" required autofocus
                                   class="w-full text-center tracking-[0.4em] font-mono font-bold text-lg px-4 py-2.5 bg-white border-2 border-indigo-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-inner"
                                   placeholder="••••••">
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Verifikasi & Simpan Email
                        </button>
                    </form>
                    <x-input-error class="mt-2" :messages="$errors->get('otp')" />

                    <div class="mt-3.5 flex flex-wrap items-center gap-4 text-xs pt-2 border-t border-indigo-100">
                        <form method="POST" action="{{ route('profile.email.resend') }}">
                            @csrf
                            <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-semibold underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Kirim ulang kode OTP
                            </button>
                        </form>
                        <span class="text-slate-300">•</span>
                        <form method="POST" action="{{ route('profile.email.cancel') }}">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-rose-600 font-medium">
                                Batalkan perubahan email
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl" :value="old('email', $user->email)" required autocomplete="username" />
            
            <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Mengubah email akan mengirimkan kode verifikasi OTP ke alamat email baru sebelum diperbarui.
            </p>

            @if(session('pending_email_change'))
                <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu verifikasi untuk: {{ session('pending_email_change.new_email') }}
                </div>
            @endif

            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="rounded-xl px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700">{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-semibold text-emerald-600 flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Profil berhasil disimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>

