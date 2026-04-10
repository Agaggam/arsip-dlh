<x-guest-layout>
    <div class="text-center lg:text-left mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Create Account
        </h1>
        <p class="text-gray-500 mt-2">
            Daftar sekarang untuk mulai mengelola data Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Full Name')" class="font-semibold" />
            <x-text-input id="name" class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" 
                type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Address')" class="font-semibold" />
            <x-text-input id="email" class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" 
                type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Input Department --}}
        <div>
            <x-input-label for="department_id" :value="__('Bidang / Bagian')" class="font-semibold" />
            <select id="department_id" name="department_id" 
                class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm" 
                required>
                <option value="" disabled selected>Pilih Bidang Anda</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="font-semibold" />
            <x-text-input id="password" class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" 
                type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-semibold" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" 
                type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full justify-center py-3.5 text-base font-bold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-lg shadow-indigo-200 transition-all">
                {{ __('Register Account') }}
            </x-primary-button>
        </div>

        <p class="text-center text-sm text-gray-600 mt-6">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 hover:underline transition">
                Sign in
            </a>
        </p>
    </form>
</x-guest-layout>