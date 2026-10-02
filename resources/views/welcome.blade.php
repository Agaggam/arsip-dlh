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
        .card-hover {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.1);
        }
        .stat-number {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .scroll-reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
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
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=2000&auto=format&fit=crop" 
                 alt="Nature Background" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-emerald-950/75 mix-blend-multiply"></div>
            <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-gray-50 to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-4xl mx-auto text-center flex flex-col items-center justify-center pt-4 sm:pt-8 pb-8 sm:pb-12">
                
                <!-- Badge Terintegrasi -->
                <div class="inline-flex items-center gap-2 bg-emerald-900/60 rounded-full px-4 py-2 border border-emerald-400/30 shadow-sm mb-6 sm:mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="text-xs sm:text-sm font-semibold text-emerald-100 tracking-wide uppercase">Sistem Arsip Digital Terintegrasi</span>
                </div>
                
                <!-- Main Headline -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-[1.15] drop-shadow-md mb-6">
                    Kelola Arsip <br class="hidden sm:block" />
                    <span class="text-emerald-300">Lingkungan Hidup</span> dengan Mudah
                </h1>
                
                <!-- Subheadline -->
                <p class="text-base sm:text-lg md:text-xl text-emerald-50/90 max-w-2xl mx-auto mb-10 leading-relaxed font-light">
                    Platform pengelolaan arsip digital yang aman, cepat, dan terstruktur untuk Dinas Lingkungan Hidup. Akses dokumen kearsipan kapan saja secara efisien.
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 sm:py-4 bg-emerald-600 text-white rounded-xl font-bold shadow-md hover:bg-emerald-700 transition-all duration-200 flex items-center justify-center text-base">
                        Mulai Sekarang
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-3.5 sm:py-4 bg-white/10 text-white rounded-xl font-semibold border border-white/20 hover:bg-white/20 transition-all duration-200 text-base">
                        Pelajari Fitur
                    </a>
                </div>
                
                <!-- Feature Highlights -->
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-8 mt-10 sm:mt-16 text-xs sm:text-sm text-emerald-100 bg-emerald-950/60 py-3 sm:py-4 px-6 sm:px-10 rounded-2xl border border-emerald-800/60 shadow-lg">
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-400 text-base"></i> <span class="font-medium">Paperless</span></div>
                    <div class="hidden sm:block text-emerald-700">•</div>
                    <div class="flex items-center gap-2"><i class="fas fa-shield-alt text-emerald-400 text-base"></i> <span class="font-medium">Aman & Terenkripsi</span></div>
                    <div class="hidden sm:block text-emerald-700">•</div>
                    <div class="flex items-center gap-2"><i class="fas fa-clock text-emerald-400 text-base"></i> <span class="font-medium">Akses Kapan Saja</span></div>
                </div>

            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-16 sm:py-24 overflow-hidden bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100/50 border border-emerald-200 mb-4 text-emerald-700 text-xs font-bold tracking-widest uppercase">
                    <i class="fas fa-leaf"></i> Teknologi Hijau
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">Fitur Unggulan Sistem</h2>
                <p class="text-gray-500 mt-4 text-base sm:text-lg">Dirancang khusus untuk memudahkan pengelolaan dokumen lingkungan hidup secara digital, efisien, dan modern.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Pencarian Cepat</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Temukan dokumen berdasarkan kata kunci, bidang, atau tahun dengan filter yang responsif.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-folder-open text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Kategori Terstruktur</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Pengelompokan dokumen berdasarkan bidang kerja dan tingkat kerahasiaan kearsipan.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-users-cog text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Manajemen Hak Akses</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Otorisasi akses berjenjang untuk Super Admin, Admin Bidang, dan Pengguna internal.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-chart-pie text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Statistik Real-time</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Monitoring jumlah arsip dan riwayat aktivitas melalui dasbor analitik terpusat.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-leaf text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Gerakan Paperless</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Mendukung program Go Green DLH dengan mereduksi penggunaan berkas fisik secara signifikan.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Keamanan Terjamin</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Penyimpanan terenkripsi dengan log audit aktivitas sistem untuk menjaga integritas data.</p>
                </div>

                <!-- Feature 7 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Usulan SSH &amp; SBU</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Kelola usulan Standar Satuan Harga dan Standar Biaya Umum dengan cetak PDF resmi.</p>
                </div>

                <!-- Feature 8 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-5 text-emerald-600">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Pengawasan Usaha</h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Monitoring ketaatan lingkungan pelaku usaha per wilayah dengan ekspor berkas rekapitulasi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="relative py-16 sm:py-20 bg-emerald-950 text-white border-t border-emerald-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6 text-center">
                <div class="p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-extrabold text-emerald-300">{{ number_format($totalArsip, 0, ',', '.') }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Arsip Digital</p>
                </div>
                <div class="p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-extrabold text-emerald-300">{{ $totalDepartemen }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Bidang DLH</p>
                </div>
                <div class="p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-extrabold text-emerald-300">{{ $totalKategori }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Kategori</p>
                </div>
                <div class="p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-extrabold text-emerald-300">{{ $totalUser }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Pengguna</p>
                </div>
                <div class="p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-extrabold text-emerald-300">{{ number_format($totalUsulan, 0, ',', '.') }}</div>
                    <p class="text-emerald-100/80 mt-2 font-medium text-xs sm:text-sm tracking-wide uppercase">Usulan Harga</p>
                </div>
                <div class="p-4 sm:p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="stat-number text-2xl sm:text-3xl lg:text-4xl font-extrabold text-emerald-300">{{ number_format($totalPengawasan ?? 0, 0, ',', '.') }}</div>
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