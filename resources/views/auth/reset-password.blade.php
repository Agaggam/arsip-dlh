<x-guest-layout>
    <div class="text-center lg:text-left mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Update Password
        </h1>
        <p class="text-gray-500 mt-2">
            Hampir selesai! Silakan buat password baru yang kuat untuk akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email Address')" class="font-semibold" />
            <x-text-input id="email" 
                class="block mt-1.5 w-full border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed rounded-xl shadow-sm" 
                type="email" 
                name="email" 
                :value="old('email', $request->email)" 
                required 
                readonly 
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')" class="font-semibold" />
            <x-text-input id="password" 
                class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" 
                type="password" 
                name="password" 
                required 
                autofocus
                autocomplete="new-password" 
                placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="font-semibold" />
            <x-text-input id="password_confirmation" 
                class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password" 
                placeholder="Ulangi password baru" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3.5 text-base font-bold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-lg shadow-indigo-200 transition-all">
                {{ __('Reset Password Sekarang') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>