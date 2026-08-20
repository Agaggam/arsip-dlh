<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Arsip DLH') }} | Pengelolaan Arsip Digital</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom base styles */
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.02) 90%);
        }
        @keyframes ken-burns {
            0% { transform: scale(1); }
            100% { transform: scale(1.15); }
        }
        .animate-ken-burns {
            animation: ken-burns 20s ease-in-out infinite alternate;
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.12);
        }
        .stat-number {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-glow {
            transition: all 0.2s ease;
        }
        .btn-glow:hover {
            box-shadow: 0 8px 20px -6px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }
        .animate-float-slow {
            animation: float-slow 8s ease-in-out infinite;
        }
        @keyframes float-slow {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(1.5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .animate-glow-breath {
            animation: glow-breath 4s ease-in-out infinite alternate;
        }
        @keyframes glow-breath {
            0% { opacity: 0.3; transform: scale(0.9); }
            100% { opacity: 0.65; transform: scale(1.05); }
        }
        .animate-fade-orbit {
            animation: fade-orbit 4s ease-in-out infinite alternate;
        }
        @keyframes fade-orbit {
            0% { opacity: 0.1; transform: translateY(10px); }
            100% { opacity: 0.9; transform: translateY(-10px); }
        }
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fade-in-up 1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .blur-bg {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.75);
        }
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }
        .scroll-reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 10px;
        }
        /* Mobile menu transition */
        .mobile-menu-enter {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .mobile-menu-enter-active {
            max-height: 500px;
            opacity: 1;
        }
        .mobile-menu-leave {
            max-height: 500px;
            opacity: 1;
        }
        .mobile-menu-leave-active {
            max-height: 0;
            opacity: 0;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="font-sans antialiased overflow-x-hidden" style="background-image: url('{{ asset('images/1443.jpg') }}'); background-size: 400px 400px; background-repeat: repeat;">

    <!-- Navbar dengan mobile menu -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 lg:h-24">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-dlh.png') }}" alt="Logo" class="w-10 h-10 sm:w-12 sm:h-12 object-contain drop-shadow-md">
                    <span class="font-extrabold text-gray-900 text-lg sm:text-xl tracking-tight">{{ config('app.name', 'E-Arsip DLH') }}</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8 lg:gap-10">
                    <a href="#features" class="text-gray-600 hover:text-emerald-600 hover:-translate-y-0.5 transition-all text-base font-semibold">Fitur</a>
                    <a href="#stats" class="text-gray-600 hover:text-emerald-600 hover:-translate-y-0.5 transition-all text-base font-semibold">Statistik</a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-emerald-600 hover:-translate-y-0.5 transition-all text-base font-semibold">Tentang</a>
                </div>

                <!-- Auth Buttons (Desktop) -->
                <div class="hidden md:flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 text-base font-bold bg-emerald-600 text-white rounded-xl shadow-sm hover:bg-emerald-700 transition btn-glow">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-5 py-2.5 text-base font-semibold text-gray-700 hover:text-emerald-600 transition">Masuk</a>
                            <a href="{{ route('register') }}" class="px-6 py-2.5 text-base font-bold bg-emerald-600 text-white rounded-xl shadow-md hover:bg-emerald-700 hover:-translate-y-0.5 transition-all btn-glow">
                                Daftar
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Mobile menu button -->
                <button id="mobileMenuButton" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Menu Panel -->
            <div id="mobileMenuPanel" class="md:hidden mobile-menu-enter transition-all duration-300">
                <div class="py-4 border-t border-gray-100 space-y-3">
                    <a href="#features" class="block py-2 text-gray-600 hover:text-emerald-600 transition font-medium">Fitur</a>
                    <a href="#stats" class="block py-2 text-gray-600 hover:text-emerald-600 transition font-medium">Statistik</a>
                    <a href="{{ route('about') }}" class="block py-2 text-gray-600 hover:text-emerald-600 transition font-medium">Tentang</a>
                    <div class="pt-3 flex flex-col gap-2">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="w-full text-center px-5 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-xl shadow-sm hover:bg-emerald-700 transition">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="w-full text-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-emerald-600 transition border border-gray-200 rounded-xl">Masuk</a>
                                <a href="{{ route('register') }}" class="w-full text-center px-5 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-xl shadow-sm hover:bg-emerald-700 transition">
                                    Daftar
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-24 sm:pt-28 lg:pt-36 pb-12 sm:pb-20 lg:pb-28 overflow-hidden">
        <!-- Animated Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=2000&auto=format&fit=crop" 
                 alt="Nature Background" 
                 class="w-full h-full object-cover animate-ken-burns">
            <div class="absolute inset-0 bg-emerald-950/60 mix-blend-multiply"></div>
            <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-gray-50 to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-4xl mx-auto text-center flex flex-col items-center justify-center pt-4 sm:pt-8 pb-8 sm:pb-12">
                
                <!-- Badge Terintegrasi -->
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md rounded-full px-4 py-2 border border-white/20 shadow-sm mb-6 sm:mb-8 animate-fade-in-up">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span>
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-emerald-50 tracking-wide uppercase">Sistem Arsip Digital Terintegrasi</span>
                </div>
                
                <!-- Main Headline -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-[1.15] drop-shadow-lg mb-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                    Kelola Arsip <br class="hidden sm:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-200 drop-shadow-md">Lingkungan Hidup</span> dengan Mudah
                </h1>
                
                <!-- Subheadline -->
                <p class="text-base sm:text-lg md:text-xl text-emerald-50/90 max-w-2xl mx-auto drop-shadow-md mb-10 leading-relaxed font-light animate-fade-in-up" style="animation-delay: 0.2s;">
                    Platform pengelolaan arsip digital yang aman, cepat, dan ramah lingkungan. Akses dokumen penting Anda kapan saja, di mana saja tanpa ribet.
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto animate-fade-in-up" style="animation-delay: 0.3s;">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 sm:py-4 bg-emerald-500 text-white rounded-xl font-bold shadow-xl shadow-emerald-500/30 hover:bg-emerald-400 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 text-base">
                        Mulai Sekarang <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-3.5 sm:py-4 bg-white/10 text-white rounded-xl font-bold border border-white/20 hover:bg-white/20 hover:border-white/40 hover:-translate-y-1 backdrop-blur-sm transition-all duration-300 shadow-lg text-base">
                        Pelajari Fitur
                    </a>
                </div>
                
                <!-- Feature Highlights -->
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-8 mt-10 sm:mt-16 text-xs sm:text-sm text-gray-200 bg-black/20 backdrop-blur-sm py-3 sm:py-4 px-4 sm:px-10 rounded-2xl border border-white/10 shadow-2xl animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-400 text-base"></i> <span class="font-medium">Tanpa ribet</span></div>
                    <div class="hidden sm:block text-gray-500">•</div>
                    <div class="flex items-center gap-2"><i class="fas fa-shield-alt text-emerald-400 text-base"></i> <span class="font-medium">Aman & Terenkripsi</span></div>
                    <div class="hidden sm:block text-gray-500">•</div>
                    <div class="flex items-center gap-2"><i class="fas fa-cloud-upload-alt text-emerald-400 text-base"></i> <span class="font-medium">Akses 24/7</span></div>
                </div>

            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-16 sm:py-24 overflow-hidden" style="background-image: url('{{ asset('images/1443.jpg') }}'); background-size: 400px 400px; background-repeat: repeat;">
        <!-- White overlay for readability -->
        <div class="absolute inset-0 bg-white/80 pointer-events-none"></div>
        
        <!-- Abstract Background Shapes for Eco-Digital feel -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-emerald-200/20 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-teal-200/20 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100/50 border border-emerald-200 mb-4 text-emerald-700 text-xs font-bold tracking-widest uppercase">
                    <i class="fas fa-leaf"></i> Teknologi Hijau
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">Fitur Unggulan Sistem</h2>
                <p class="text-gray-500 mt-4 text-base sm:text-lg">Dirancang khusus untuk memudahkan pengelolaan dokumen lingkungan hidup secara digital, efisien, dan modern.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-emerald-50 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-100 to-teal-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-emerald-100/50">
                        <i class="fas fa-search text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-emerald-700 transition-colors">Pencarian Cepat</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Temukan arsip berdasarkan kata kunci, kategori, departemen, atau tahun dengan fitur pencarian yang canggih.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-emerald-50 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-teal-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-teal-100 to-emerald-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-teal-100/50">
                        <i class="fas fa-folder-open text-teal-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-teal-700 transition-colors">Kategori Terstruktur</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Arsip dikelompokkan berdasarkan bidang, jenis dokumen, dan tingkat kerahasiaan untuk kemudahan akses.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-emerald-50 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-100 to-teal-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-emerald-100/50">
                        <i class="fas fa-users-cog text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-emerald-700 transition-colors">Manajemen Role</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Atur hak akses pengguna (Admin, Operator, User) dengan sistem perizinan yang fleksibel namun ketat.</p>
                </div>
                <!-- Feature 4 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-emerald-50 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-teal-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-teal-100 to-emerald-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-teal-100/50">
                        <i class="fas fa-chart-pie text-teal-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-teal-700 transition-colors">Statistik Real-time</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Pantau jumlah arsip, aktivitas pengguna, dan tren dokumen melalui dashboard analitik interaktif.</p>
                </div>
                <!-- Feature 5 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-emerald-50 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-100 to-teal-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-emerald-100/50">
                        <i class="fas fa-recycle text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-emerald-700 transition-colors">Digitalisasi Ramah</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Mendukung program Go Green DLH dengan mengurangi penggunaan kertas hingga 90% di setiap divisi.</p>
                </div>
                <!-- Feature 6 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-emerald-50 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-teal-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-teal-100 to-emerald-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-teal-100/50">
                        <i class="fas fa-shield-virus text-teal-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-teal-700 transition-colors">Keamanan Lapis Ganda</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Enkripsi data arsip dan riwayat aktivitas sistem untuk melindungi seluruh dokumen penting.</p>
                </div>
                <!-- Feature 7 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-amber-50 shadow-sm hover:shadow-xl hover:shadow-amber-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-amber-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-amber-100 to-yellow-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-amber-100/50">
                        <i class="fas fa-file-invoice-dollar text-amber-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-amber-700 transition-colors">Usulan SSH & SBU</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Kelola dan ajukan usulan Standar Satuan Harga (SSH) dan Standar Biaya Umum (SBU) secara digital lengkap dengan cetak PDF otomatis.</p>
                </div>
                <!-- Feature 8 -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 card-hover border border-cyan-50 shadow-sm hover:shadow-xl hover:shadow-cyan-900/5 group relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-cyan-100/50 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-cyan-100 to-teal-50 rounded-xl flex items-center justify-center mb-5 sm:mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 border border-cyan-100/50">
                        <i class="fas fa-clipboard-check text-cyan-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 relative z-10 group-hover:text-cyan-700 transition-colors">Pengawasan Usaha</h3>
                    <p class="text-gray-500 mt-3 text-sm leading-relaxed relative z-10">Monitoring dan pengawasan pelaku usaha & kegiatan per kecamatan dengan ekspor laporan PDF & Excel otomatis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="relative py-16 sm:py-24 bg-emerald-950 overflow-hidden text-white border-t border-emerald-900">
        <!-- Eco-Digital Pattern Background -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#34d399 1.5px, transparent 1.5px); background-size: 32px 32px;"></div>
        <div class="absolute -right-20 top-1/2 -translate-y-1/2 w-80 h-80 bg-emerald-500/20 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="absolute -left-20 bottom-0 w-64 h-64 bg-teal-500/20 blur-[100px] rounded-full pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6 text-center">
                <div class="scroll-reveal p-4 sm:p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors card-hover">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-200 drop-shadow-sm">{{ number_format($totalArsip, 0, ',', '.') }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Arsip Digital</p>
                </div>
                <div class="scroll-reveal p-4 sm:p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors card-hover" style="transition-delay: 100ms;">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-200 drop-shadow-sm">{{ $totalDepartemen }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Bidang DLH</p>
                </div>
                <div class="scroll-reveal p-4 sm:p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors card-hover" style="transition-delay: 200ms;">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-200 drop-shadow-sm">{{ $totalKategori }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Kategori</p>
                </div>
                <div class="scroll-reveal p-4 sm:p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors card-hover" style="transition-delay: 300ms;">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-200 drop-shadow-sm">{{ $totalUser }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Pengguna</p>
                </div>
                <div class="scroll-reveal p-4 sm:p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors card-hover" style="transition-delay: 400ms;">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-yellow-200 drop-shadow-sm">{{ number_format($totalUsulan, 0, ',', '.') }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Usulan Harga</p>
                </div>
                <div class="scroll-reveal p-4 sm:p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors card-hover" style="transition-delay: 500ms;">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-teal-200 drop-shadow-sm">{{ number_format($totalPengawasan ?? 0, 0, ',', '.') }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Pengawasan</p>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer class="border-t border-gray-200 py-8 sm:py-12" style="background-image: url('{{ asset('images/1443.jpg') }}'); background-size: 400px 400px; background-repeat: repeat;">
        <div class="relative">
        <!-- White overlay -->
        <div class="absolute inset-0 bg-white/85"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 sm:gap-6">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-dlh.png') }}" alt="Logo" class="w-7 h-7 object-contain grayscale opacity-80">
                    <span class="font-bold text-gray-800 text-sm sm:text-base">{{ config('app.name', 'E-Arsip DLH') }}</span>
                </div>
                <div class="flex flex-wrap justify-center gap-4 sm:gap-8 text-xs sm:text-sm text-gray-500">
                    <a href="{{ route('about') }}" class="hover:text-emerald-600 transition">Tentang Kami</a>
                    <a href="{{ route('panduan') }}" class="hover:text-emerald-600 transition">Panduan</a>
                    <a href="{{ route('bantuan') }}" class="hover:text-emerald-600 transition">Bantuan</a>
                    <a href="{{ route('kebijakan-privasi') }}" class="hover:text-emerald-600 transition">Kebijakan Privasi</a>
                </div>
                <div class="text-xs sm:text-sm text-gray-400">
                    &copy; {{ date('Y') }} Dinas Lingkungan Hidup. All rights reserved.
                </div>
            </div>
        </div>
        </div><!-- end relative wrapper -->
    </footer>

    <!-- Scroll reveal & mobile menu script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Scroll reveal
            const revealElements = document.querySelectorAll('.scroll-reveal');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, { threshold: 0.2 });
            revealElements.forEach(el => observer.observe(el));

            // Mobile menu toggle
            const menuButton = document.getElementById('mobileMenuButton');
            const menuPanel = document.getElementById('mobileMenuPanel');
            let isOpen = false;

            if (menuButton && menuPanel) {
                // Initially closed
                menuPanel.classList.add('mobile-menu-enter');
                menuPanel.style.maxHeight = '0';
                menuPanel.style.opacity = '0';
                menuPanel.style.overflow = 'hidden';

                menuButton.addEventListener('click', () => {
                    if (!isOpen) {
                        // Open
                        menuPanel.style.maxHeight = menuPanel.scrollHeight + 'px';
                        menuPanel.style.opacity = '1';
                        menuPanel.style.overflow = 'visible';
                        isOpen = true;
                    } else {
                        // Close
                        menuPanel.style.maxHeight = '0';
                        menuPanel.style.opacity = '0';
                        menuPanel.style.overflow = 'hidden';
                        isOpen = false;
                    }
                });
            }
        });
    </script>
</body>
</html>