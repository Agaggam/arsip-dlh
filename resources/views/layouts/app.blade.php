<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { 
                font-family: 'Inter', sans-serif; 
                letter-spacing: -0.01em;
            }
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { 
                background: #e2e8f0; 
                border-radius: 20px;
                border: 2px solid transparent;
            }
            ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
            .fade-in {
                animation: fadeIn 0.5s ease-in-out;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body class="antialiased bg-[#f1f5f9] text-slate-900 selection:bg-indigo-100 selection:text-indigo-700">
        <div class="min-h-screen sm:flex">
            
            @include('layouts.navigation')

            <main class="flex-1 sm:ml-64 min-h-screen flex flex-col relative">
                
                <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-slate-200/50 to-transparent -z-10"></div>

                @isset($header)
                    <header class="bg-white/70 backdrop-blur-xl sticky top-0 z-30 border-b border-slate-200/80">
                        <div class="max-w-full mx-auto py-4 px-6 sm:px-8 lg:px-10 flex justify-between items-center">
                            <div class="flex flex-col">
                                <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                                    {{ $header }}
                                </h1>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                                    </span>
                                    <p class="text-[11px] text-slate-500 font-medium tracking-wide uppercase">
                                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                {{-- tempat untuk tambahan tombol jika perlu --}}
                            </div>
                        </div>
                    </header>
                @endisset

                <div class="p-6 sm:p-8 lg:p-10 flex-1 fade-in">
                    <div class="max-w-full mx-auto">
                        {{ $slot }}
                    </div>
                </div>

                <footer class="py-6 px-10 border-t border-slate-200/60 bg-white/30">
                    <div class="max-w-full mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
                        <p class="text-[13px] text-slate-500 font-medium">
                            &copy; {{ date('Y') }} <span class="text-indigo-600 font-bold">{{ config('app.name') }}</span>.
                        </p>
                        <div class="flex gap-6">
                            <a href="#" class="text-[12px] text-slate-400 hover:text-slate-600 transition">Panduan</a>
                            <a href="#" class="text-[12px] text-slate-400 hover:text-slate-600 transition">Bantuan</a>
                        </div>
                    </div>
                </footer>
            </main>
        </div>

        {{-- ========== TOAST NOTIFICATIONS (DITEMPATKAN DI LUAR ELEMEN DENGAN TRANSFORM) ========== --}}
        @if(session('success'))
            <x-toast type="success" message="{{ session('success') }}" :duration="5000" />
        @endif

        @if(session('error'))
            <x-toast type="error" message="{{ session('error') }}" :duration="5000" />
        @endif

        {{-- Menangkap session status dari ProfileController (profile-updated, password-updated, dll) --}}
        @if(session('status'))
            @php
                $statusMessage = match(session('status')) {
                    'profile-updated' => 'Profil berhasil diperbarui.',
                    'password-updated' => 'Kata sandi berhasil diubah.',
                    default => session('status')
                };
            @endphp
            <x-toast type="success" message="{{ $statusMessage }}" :duration="5000" />
        @endif

        {{-- Menangkap error validasi dari berbagai form (termasuk userDeletion) --}}
        @if($errors->any() && !session('success') && !session('error') && !session('status'))
            <x-toast type="error" message="Terjadi kesalahan. Silakan periksa kembali." :duration="5000" />
        @endif

        {{-- Menangkap error khusus untuk hapus akun (userDeletion) --}}
        @if($errors->userDeletion->has('password'))
            <x-toast type="error" message="Gagal menghapus akun: Kata sandi salah." :duration="5000" />
        @endif
    </body>
</html>