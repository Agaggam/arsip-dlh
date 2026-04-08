<x-guest-layout>
    <div class="text-center lg:text-left mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Reset Password
        </h1>
        <p class="text-gray-500 mt-3 leading-relaxed">
            {{ __('Lupa password? Jangan khawatir. Masukkan alamat email Anda dan kami akan mengirimkan link pengaturan ulang password.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email Address')" class="font-semibold" />
            <x-text-input id="email" 
                class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center py-3.5 text-base font-bold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-lg shadow-indigo-200 transition-all">
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-500 transition-colors group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>