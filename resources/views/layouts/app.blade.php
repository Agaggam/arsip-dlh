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
        html {
            overflow-y: scroll;
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.01em;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-slate-100 to-slate-200 text-slate-900 selection:bg-indigo-100 selection:text-indigo-700">
<div class="min-h-screen lg:flex">

    @include('layouts.navigation')

    <main class="flex-1 lg:ml-64 min-h-screen flex flex-col bg-slate-50/30">

        <!-- Header Baru dengan desain lebih bersih -->
        @isset($header)
            <header class="bg-white/90 backdrop-blur-sm sticky top-0 z-30 border-b border-slate-200/80 shadow-sm">
                <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <!-- Indikator aktif -->
                            <div class="hidden sm:block w-1.5 h-8 bg-indigo-500 rounded-full"></div>
                            <div>
                                <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight">
                                    {{ $header }}
                                </h1>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        <p class="text-xs text-slate-500 font-medium">
                                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                        </p>
                                    </div>
                                    <span class="text-slate-300 text-xs">•</span>
                                    <p class="text-xs text-slate-500 font-medium">
                                        {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Area tambahan tombol (opsional) -->
                        <div class="flex items-center gap-3">
                            {{-- Bisa ditambahkan tombol aksi cepat di sini --}}
                        </div>
                    </div>
                </div>
            </header>
        @endisset

        <!-- Main Content -->
        <div class="flex-1 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <div class="fade-in">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer Minimalis -->
        <footer class="border-t border-slate-200/60 bg-white/50 backdrop-blur-sm mt-auto">
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 text-xs">
                    <p class="text-slate-500">
                        &copy; {{ date('Y') }} <span class="text-indigo-600 font-semibold">{{ config('app.name', 'E-Arsip DLH') }}</span>. 
                        All rights reserved.
                    </p>
                    <div class="flex gap-6">
                        <a href="#" class="text-slate-400 hover:text-indigo-600 transition duration-200">Panduan</a>
                        <a href="#" class="text-slate-400 hover:text-indigo-600 transition duration-200">Bantuan</a>
                        <a href="#" class="text-slate-400 hover:text-indigo-600 transition duration-200">Kebijakan Privasi</a>
                    </div>
                </div>
            </div>
        </footer>
    </main>
</div>

{{-- TOAST NOTIFICATIONS --}}
@if(session('success'))
    <x-toast type="success" message="{{ session('success') }}" :duration="5000" />
@endif

@if(session('error'))
    <x-toast type="error" message="{{ session('error') }}" :duration="5000" />
@endif

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

@if($errors->any() && !session('success') && !session('error') && !session('status'))
    <x-toast type="error" message="Terjadi kesalahan. Silakan periksa kembali." :duration="5000" />
@endif

@if($errors->userDeletion->has('password'))
    <x-toast type="error" message="Gagal menghapus akun: Kata sandi salah." :duration="5000" />
@endif

</body>
</html>