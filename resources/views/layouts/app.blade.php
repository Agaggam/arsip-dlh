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
                letter-spacing: -0.01em; /* Membuat teks terlihat lebih padat & modern */
            }

            /* Custom scrollbar yang lebih tipis dan estetik */
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { 
                background: #e2e8f0; 
                border-radius: 20px;
                border: 2px solid transparent;
            }
            ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

            /* Efek halus saat transisi halaman */
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
                        <div class="max-w-7xl mx-auto py-4 px-6 sm:px-8 lg:px-10 flex justify-between items-center">
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
                                <!-- <div class="hidden md:flex items-center bg-slate-100 rounded-full px-4 py-1.5 border border-slate-200">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    <input type="text" placeholder="Cari sesuatu..." class="bg-transparent border-none focus:ring-0 text-xs text-slate-600 placeholder-slate-400 w-32">
                                </div> TIDAK DIPERLUKAN-->
                            </div>
                        </div>
                    </header>
                @endisset

                <div class="p-6 sm:p-8 lg:p-10 flex-1 fade-in">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </div>

                <footer class="py-6 px-10 border-t border-slate-200/60 bg-white/30">
                    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
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
    </body>
</html>