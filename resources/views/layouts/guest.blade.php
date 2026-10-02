<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans text-gray-900 antialiased selection:bg-emerald-100 selection:text-emerald-700 bg-slate-100">
        
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <img src="{{ asset('images/1443.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-15" />
            <div class="absolute inset-0 bg-slate-100/80"></div>
        </div>

        <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
            
            <div class="w-full max-w-4xl bg-white shadow-lg rounded-2xl overflow-hidden flex flex-col md:flex-row border border-slate-200">
                
                <div class="hidden md:flex md:w-1/2 bg-slate-50 flex-col items-center justify-center p-8 relative border-r border-slate-200">
                    
                    <div class="absolute top-10 left-10">
                        <a href="/" class="flex items-center gap-4 group">
                            <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                                <x-application-logo class="w-12 h-12 object-contain" />
                            </div>

                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] leading-none mb-1">
                                    Sistem Informasi Arsip
                                </span>
                                <span class="text-xl font-extrabold text-slate-800 leading-none tracking-tight">
                                    {{ config('app.name', 'Laravel') }}
                                </span>
                            </div>
                        </a>
                    </div>

                    <div class="text-center mt-12">
                        <img src="{{ asset('images/auth-vector.png') }}" 
                             alt="Authentication" 
                             class="w-full max-w-[260px] h-auto mx-auto mb-4 drop-shadow-md">
                        
                        <h2 class="text-2xl font-bold text-slate-800 mb-2 tracking-tight">Selamat Datang!</h2>
                        <p class="text-slate-500 leading-relaxed max-w-xs mx-auto text-balance">
                            Kelola data Anda dengan lebih mudah dan cepat dalam satu dashboard terintegrasi.
                        </p>
                    </div>
                </div>

                <div class="w-full md:w-1/2 px-8 py-10 flex flex-col justify-center items-center relative">
                    
                    <div class="md:hidden mb-10 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <x-application-logo class="w-16 h-16 object-contain shadow-sm p-2 bg-white rounded-2xl" />
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">Sistem Informasi</span>
                                <span class="text-lg font-extrabold text-slate-800">{{ config('app.name', 'Laravel') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="max-w-[360px] mx-auto w-full">
                        {{ $slot }}
                    </div>
                </div>

            </div>
        </div>
    </body>
</html>