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
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #eef2f5 100%);
        }
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.02) 90%);
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
<body class="antialiased overflow-x-hidden">

    <!-- Navbar dengan mobile menu -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-600 to-teal-500 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-leaf text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-base sm:text-lg tracking-tight">{{ config('app.name', 'E-Arsip DLH') }}</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-6 lg:gap-8">
                    <a href="#features" class="text-gray-600 hover:text-emerald-600 transition text-sm font-medium">Fitur</a>
                    <a href="#stats" class="text-gray-600 hover:text-emerald-600 transition text-sm font-medium">Statistik</a>
                    <a href="#about" class="text-gray-600 hover:text-emerald-600 transition text-sm font-medium">Tentang</a>
                </div>

                <!-- Auth Buttons (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-xl shadow-sm hover:bg-emerald-700 transition btn-glow">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-emerald-600 transition">Masuk</a>
                            <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-xl shadow-sm hover:bg-emerald-700 transition btn-glow">
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
                    <a href="#about" class="block py-2 text-gray-600 hover:text-emerald-600 transition font-medium">Tentang</a>
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
    <section class="relative pt-24 sm:pt-28 lg:pt-36 pb-12 sm:pb-20 lg:pb-28 overflow-hidden hero-gradient">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-200/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-teal-200/20 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-8 md:gap-12">
                <div class="flex-1 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-sm rounded-full px-3 py-1.5 border border-gray-200 shadow-sm mb-4 sm:mb-6">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[11px] sm:text-xs font-semibold text-emerald-700">Sistem Arsip Digital Terintegrasi</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold tracking-tight text-gray-900 leading-tight">
                        Kelola Arsip <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Dinas Lingkungan Hidup</span> dengan Mudah
                    </h1>
                    <p class="text-base sm:text-lg text-gray-600 mt-4 sm:mt-6 max-w-xl mx-auto lg:mx-0">
                        Platform pengelolaan arsip digital yang aman, cepat, dan ramah lingkungan. Akses dokumen kapan saja, di mana saja.
                    </p>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-3 sm:gap-4 mt-6 sm:mt-8">
                        <a href="{{ route('register') }}" class="px-5 sm:px-6 py-2.5 sm:py-3 bg-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:bg-emerald-700 transition btn-glow flex items-center gap-2 text-sm sm:text-base">
                            Mulai Sekarang <i class="fas fa-arrow-right text-xs sm:text-sm"></i>
                        </a>
                        <a href="#features" class="px-5 sm:px-6 py-2.5 sm:py-3 bg-white text-gray-700 rounded-xl font-semibold border border-gray-300 hover:border-emerald-300 hover:text-emerald-600 transition shadow-sm text-sm sm:text-base">
                            Pelajari Fitur
                        </a>
                    </div>
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 sm:gap-6 mt-6 sm:mt-8 text-xs sm:text-sm text-gray-500">
                        <div class="flex items-center gap-1"><i class="fas fa-check-circle text-emerald-500 text-xs sm:text-sm"></i> <span>Tanpa ribet</span></div>
                        <div class="flex items-center gap-1"><i class="fas fa-shield-alt text-emerald-500 text-xs sm:text-sm"></i> <span>Aman & Terenkripsi</span></div>
                        <div class="flex items-center gap-1"><i class="fas fa-cloud-upload-alt text-emerald-500 text-xs sm:text-sm"></i> <span>Akses 24/7</span></div>
                    </div>
                </div>
                <div class="flex-1 relative mt-8 lg:mt-0">
                    <div class="relative animate-float">
                        <img src="https://cdn-icons-png.flaticon.com/512/921/921490.png" alt="Arsip Digital" class="w-48 sm:w-64 md:w-72 lg:w-80 mx-auto drop-shadow-2xl opacity-90">
                        <!-- Floating badges (responsive position) -->
                        <div class="absolute -bottom-4 -left-2 sm:-bottom-6 sm:-left-6 bg-white rounded-xl sm:rounded-2xl shadow-xl p-2 sm:p-4 w-32 sm:w-44 backdrop-blur-sm border border-gray-100">
                            <div class="flex items-center gap-1 sm:gap-2">
                                <i class="fas fa-file-alt text-emerald-600 text-xs sm:text-base"></i>
                                <span class="text-[10px] sm:text-xs font-semibold">+1.250 Arsip</span>
                            </div>
                            <div class="text-[8px] sm:text-[10px] text-gray-500 mt-0.5 sm:mt-1">Tersimpan aman</div>
                        </div>
                        <div class="absolute -top-2 -right-2 sm:-top-4 sm:-right-4 bg-white rounded-xl sm:rounded-2xl shadow-xl p-2 sm:p-3 w-28 sm:w-36 backdrop-blur-sm border border-gray-100">
                            <div class="flex items-center gap-1 sm:gap-2">
                                <i class="fas fa-clock text-teal-500 text-xs sm:text-base"></i>
                                <span class="text-[10px] sm:text-xs font-semibold">Akses cepat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 sm:py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-16">
                <span class="text-emerald-600 font-semibold text-xs sm:text-sm uppercase tracking-wide">Keunggulan</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mt-2">Fitur Unggulan Sistem Arsip</h2>
                <p class="text-gray-600 mt-3 sm:mt-4 text-sm sm:text-base">Dirancang untuk memudahkan pengelolaan dokumen lingkungan hidup secara digital dan efisien.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 card-hover border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-5">
                        <i class="fas fa-search text-emerald-600 text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Pencarian Cepat</h3>
                    <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">Temukan arsip berdasarkan kata kunci, kategori, departemen, atau tahun dengan fitur pencarian canggih.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 card-hover border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-5">
                        <i class="fas fa-folder-open text-emerald-600 text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Kategori Terstruktur</h3>
                    <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">Arsip dikelompokkan berdasarkan bidang, jenis dokumen, dan tingkat kerahasiaan untuk kemudahan akses.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 card-hover border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-5">
                        <i class="fas fa-users text-emerald-600 text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Manajemen Role</h3>
                    <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">Atur hak akses pengguna (Admin, Operator, User) dengan sistem perizinan yang fleksibel.</p>
                </div>
                <!-- Feature 4 -->
                <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 card-hover border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-5">
                        <i class="fas fa-chart-line text-emerald-600 text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Statistik Real-time</h3>
                    <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">Pantau jumlah arsip, aktivitas pengguna, dan tren dokumen melalui dashboard interaktif.</p>
                </div>
                <!-- Feature 5 -->
                <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 card-hover border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-5">
                        <i class="fas fa-trash-alt text-emerald-600 text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Tempat Sampah Digital</h3>
                    <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">Arsip yang dihapus sementara disimpan di trash, dapat dipulihkan kapan saja.</p>
                </div>
                <!-- Feature 6 -->
                <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 card-hover border border-gray-100">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-4 sm:mb-5">
                        <i class="fas fa-lock text-emerald-600 text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Keamanan Terjamin</h3>
                    <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">Enkripsi data dan autentikasi dua faktor untuk melindungi dokumen penting lingkungan hidup.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-12 sm:py-16 bg-gradient-to-br from-emerald-50 to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 text-center">
                <div class="scroll-reveal">
                    <div class="stat-number text-3xl sm:text-4xl lg:text-5xl font-black text-emerald-700">2.500+</div>
                    <p class="text-gray-600 mt-1 sm:mt-2 font-medium text-sm sm:text-base">Arsip Tersimpan</p>
                </div>
                <div class="scroll-reveal">
                    <div class="stat-number text-3xl sm:text-4xl lg:text-5xl font-black text-emerald-700">45+</div>
                    <p class="text-gray-600 mt-1 sm:mt-2 font-medium text-sm sm:text-base">Instansi Terhubung</p>
                </div>
                <div class="scroll-reveal">
                    <div class="stat-number text-3xl sm:text-4xl lg:text-5xl font-black text-emerald-700">98%</div>
                    <p class="text-gray-600 mt-1 sm:mt-2 font-medium text-sm sm:text-base">Kepuasan Pengguna</p>
                </div>
                <div class="scroll-reveal">
                    <div class="stat-number text-3xl sm:text-4xl lg:text-5xl font-black text-emerald-700">24/7</div>
                    <p class="text-gray-600 mt-1 sm:mt-2 font-medium text-sm sm:text-base">Akses Layanan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 lg:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-gray-900 rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 sm:w-64 h-48 sm:h-64 bg-emerald-500/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 bg-teal-500/10 rounded-full blur-2xl"></div>
                <h2 class="text-xl sm:text-2xl lg:text-4xl font-bold text-white relative z-10">Siap Mengelola Arsip Lebih Efisien?</h2>
                <p class="text-gray-300 mt-2 sm:mt-3 max-w-md mx-auto relative z-10 text-sm sm:text-base">Bergabunglah sekarang dan rasakan kemudahan sistem arsip digital Dinas Lingkungan Hidup.</p>
                <div class="mt-6 sm:mt-8 relative z-10">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-gray-900 px-6 sm:px-8 py-2.5 sm:py-3 rounded-xl font-semibold shadow-lg hover:bg-gray-100 transition transform hover:-translate-y-1 text-sm sm:text-base">
                        Daftar Akun Gratis <i class="fas fa-arrow-right text-xs sm:text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 sm:gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 sm:w-7 sm:h-7 bg-emerald-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-leaf text-white text-[10px] sm:text-xs"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-sm sm:text-base">{{ config('app.name', 'E-Arsip DLH') }}</span>
                </div>
                <div class="flex flex-wrap justify-center gap-4 sm:gap-8 text-xs sm:text-sm text-gray-500">
                    <a href="#" class="hover:text-emerald-600 transition">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-emerald-600 transition">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-emerald-600 transition">Bantuan</a>
                </div>
                <div class="text-xs sm:text-sm text-gray-400">
                    &copy; {{ date('Y') }} Dinas Lingkungan Hidup. All rights reserved.
                </div>
            </div>
        </div>
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