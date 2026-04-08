<x-guest-layout>
    <div class="text-center lg:text-left mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-amber-50 rounded-xl mb-4 md:hidden lg:inline-flex">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0he-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7h3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h3" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Konfirmasi Password
        </h1>
        <p class="text-gray-500 mt-3 leading-relaxed">
            {{ __('Ini adalah area aman. Silakan konfirmasi password Anda sebelum melanjutkan akses ke fitur ini.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" class="font-semibold" />
            <x-text-input id="password" 
                class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center py-3.5 text-base font-bold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-lg shadow-indigo-200 transition-all">
                {{ __('Confirm Password') }}
            </x-primary-button>
        </div>
        
        <div class="text-center">
            <a href="{{ url()->previous() }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                Batal dan Kembali
            </a>
        </div>
    </form>
</x-guest-layout>