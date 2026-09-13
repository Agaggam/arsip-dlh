<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Informasi Manajemen Arsip Elektronik - Dinas Lingkungan Hidup">
    <meta name="theme-color" content="#4f46e5">

    <title>{{ $title ?? config('app.name', 'E-Arsip DLH') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="shortcut icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- SweetAlert2 (Loaded early) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            overflow-y: scroll;
            scroll-behavior: smooth;
        }
        body {
            letter-spacing: -0.01em;
            zoom: 0.75;
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
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ===== LOADING BUTTON STATES ===== */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.85;
        }
        .btn-loading .btn-text { opacity: 0; }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: btnSpin 0.7s linear infinite;
        }
        @keyframes btnSpin { to { transform: translate(-50%, -50%) rotate(360deg); } }

        /* ===== GLOBAL SEARCH ===== */
        #globalSearchOverlay {
            animation: fadeInOverlay 0.2s ease;
        }
        @keyframes fadeInOverlay {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        #globalSearchPanel {
            animation: slideDown 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .search-result-item:hover {
            background: #eef2ff;
        }
        .search-result-item.active {
            background: #eef2ff;
        }
    </style>
</head>
<body class="antialiased text-slate-900 selection:bg-indigo-100 selection:text-indigo-700 relative">
    <div class="fixed inset-0 z-[-1]">
        <img src="{{ asset('images/1443.jpg') }}" class="w-full h-full object-cover" alt="Background">
        <div class="absolute inset-0 bg-white/80"></div>
    </div>
<div class="min-h-screen lg:flex relative z-0">

    @include('layouts.navigation')

    <main class="flex-1 lg:ml-64 min-h-screen flex flex-col bg-slate-50/30">

        <!-- Header Baru dengan desain lebih bersih -->
        @isset($header)
            <header class="bg-white/90 backdrop-blur-sm sticky top-0 z-30 border-b border-slate-200/80 shadow-sm">
                <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-3 lg:py-4">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Kiri: Judul Halaman --}}
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="hidden sm:block w-1 h-7 bg-indigo-500 rounded-full flex-shrink-0"></div>
                            <div class="min-w-0">
                                <h1 class="text-lg lg:text-xl font-bold text-slate-800 tracking-tight truncate">
                                    {{ $header }}
                                </h1>
                                <div class="hidden sm:flex items-center gap-2 mt-0.5">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                    <p class="text-xs text-slate-400 font-medium">
                                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} &bull; {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Tengah: Global Search Bar (Admin only) --}}
                        @if(!Auth::user()->isUser())
                        <div class="flex-1 max-w-md hidden md:block">
                            <button id="searchTrigger" onclick="openGlobalSearch()"
                                    class="w-full flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-400 text-sm transition-all duration-200 group border border-transparent hover:border-indigo-200">
                                <svg class="w-4 h-4 flex-shrink-0 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                                <span class="flex-1 text-left text-slate-400">Cari arsip, pegawai, menu...</span>
                                <kbd class="hidden lg:inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium text-slate-400 bg-white border border-slate-200 rounded-md">
                                    <span>Ctrl</span><span>K</span>
                                </kbd>
                            </button>
                        </div>
                        @endif

                        {{-- Kanan: User Profile Quick --}}
                        <div class="flex items-center gap-2">
                            {{-- Search icon mobile (Admin only) --}}
                            @if(!Auth::user()->isUser())
                            <button onclick="openGlobalSearch()" class="md:hidden w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                            </button>
                            @endif
                            {{-- User Avatar --}}
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden lg:block text-sm font-semibold text-slate-700 truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                            </a>
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

@stack('scripts')

{{-- ===================================================== --}}
{{-- GLOBAL SEARCH OVERLAY --}}
{{-- ===================================================== --}}
<div id="globalSearchOverlay" class="hidden fixed inset-0 z-[9999] flex items-start justify-center pt-[10vh] px-4" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeGlobalSearch()"></div>
    {{-- Panel --}}
    <div id="globalSearchPanel" class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200 overflow-hidden">
        {{-- Search Input --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
            <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input id="globalSearchInput" type="text" placeholder="Cari arsip, pegawai, menu, atau fitur..."
                   class="flex-1 text-base text-slate-800 placeholder-slate-400 bg-transparent border-none outline-none focus:ring-0"
                   autocomplete="off">
            <kbd class="hidden sm:flex items-center gap-1 px-2 py-1 text-[10px] font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-md cursor-pointer" onclick="closeGlobalSearch()">
                ESC
            </kbd>
        </div>
        {{-- Quick Navigation Links (Role-Aware) --}}
        <div id="searchQuickNav" class="px-3 py-3">
            <p class="px-2 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Navigasi Cepat</p>
            <div class="space-y-0.5" id="navLinks">
                @php
                    $user = Auth::user();
                    $isSuperAdmin = $user->isPureSuperAdmin();
                    $isAdminRole  = $user->isAdmin();
                    $isUserRole   = $user->isUser();

                    // Susun menu berdasarkan role
                    $navItems = [];

                    if ($isSuperAdmin || $isAdminRole) {
                        $navItems[] = ['label' => 'Dashboard',       'route' => 'admin.dashboard',        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color' => 'text-indigo-600 bg-indigo-100'];
                        $navItems[] = ['label' => 'Data Arsip',      'route' => 'admin.archives.index',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-emerald-600 bg-emerald-100'];
                        $navItems[] = ['label' => 'Tong Sampah',     'route' => 'admin.trash.index',   'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', 'color' => 'text-rose-600 bg-rose-100'];
                        $navItems[] = ['label' => 'Kategori Arsip',  'route' => 'categories.index',       'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'color' => 'text-pink-600 bg-pink-100'];
                    }

                    if ($isSuperAdmin) {
                        $navItems[] = ['label' => 'Data Pegawai',    'route' => 'kepegawaian.index',      'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'text-orange-600 bg-orange-100'];
                        $navItems[] = ['label' => 'Manajemen User',  'route' => 'users.index',            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'text-blue-600 bg-blue-100'];
                        $navItems[] = ['label' => 'Departemen',      'route' => 'admin.departments.index','icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'text-teal-600 bg-teal-100'];
                        $navItems[] = ['label' => 'Log Aktivitas',   'route' => 'activity-logs.index',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'text-amber-600 bg-amber-100'];
                        $navItems[] = ['label' => 'Backup Database', 'route' => 'admin.backup.index',     'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'color' => 'text-violet-600 bg-violet-100'];
                    }

                    if ($isUserRole) {
                        $navItems[] = ['label' => 'Dashboard Saya',  'route' => 'dashboard',              'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color' => 'text-indigo-600 bg-indigo-100'];
                        $navItems[] = ['label' => 'Daftar Arsip',    'route' => 'arsip.user',             'icon' => 'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5M5 19v-2a2 2 0 00-2-2m14 4h2a2 2 0 002-2v-5m-2 3h.01', 'color' => 'text-emerald-600 bg-emerald-100'];
                        $navItems[] = ['label' => 'Profil Saya',     'route' => 'profile.edit',           'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'text-slate-600 bg-slate-100'];
                    }
                @endphp
                @foreach($navItems as $navItem)
                <a href="{{ route($navItem['route']) }}" 
                   class="search-result-item nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer transition-colors"
                   data-label="{{ strtolower($navItem['label']) }}">
                    <div class="w-8 h-8 rounded-lg {{ $navItem['color'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $navItem['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-700">{{ $navItem['label'] }}</span>
                    <svg class="w-3 h-3 text-slate-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
        </div>
        {{-- Empty State --}}
        <div id="searchEmpty" class="hidden px-5 py-10 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <p class="text-sm text-slate-400">Tidak ada hasil untuk "<span id="emptyQuery" class="font-semibold text-slate-600"></span>"</p>
        </div>
        {{-- Footer hint --}}
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <p class="text-[11px] text-slate-400">Ketik untuk mencari menu atau fitur</p>
            <div class="flex items-center gap-3 text-[11px] text-slate-400">
                <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-[10px]">↑↓</kbd> navigasi</span>
                <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-[10px]">↵</kbd> buka</span>
                <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-[10px]">ESC</kbd> tutup</span>
            </div>
        </div>
    </div>
</div>

{{-- ===================================================== --}}
{{-- GLOBAL JS (Loading Buttons + Search Logic) --}}
{{-- ===================================================== --}}
<script>
// ===== GLOBAL SEARCH =====
function openGlobalSearch() {
    document.getElementById('globalSearchOverlay').classList.remove('hidden');
    setTimeout(() => document.getElementById('globalSearchInput').focus(), 50);
}
function closeGlobalSearch() {
    document.getElementById('globalSearchOverlay').classList.add('hidden');
    document.getElementById('globalSearchInput').value = '';
    filterSearch('');
}

document.addEventListener('DOMContentLoaded', () => {
    const input    = document.getElementById('globalSearchInput');
    const overlay  = document.getElementById('globalSearchOverlay');
    const items    = document.querySelectorAll('.nav-item');
    const empty    = document.getElementById('searchEmpty');
    const emptyQ   = document.getElementById('emptyQuery');
    let activeIdx  = -1;

    // Ctrl+K / Cmd+K to open
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (overlay.classList.contains('hidden')) openGlobalSearch();
            else closeGlobalSearch();
        }
        if (e.key === 'Escape') closeGlobalSearch();
    });

    // Filter on type
    input?.addEventListener('input', () => {
        filterSearch(input.value.trim().toLowerCase());
        activeIdx = -1;
    });

    // Arrow key navigation
    input?.addEventListener('keydown', (e) => {
        const visible = [...items].filter(i => i.style.display !== 'none');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, visible.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, 0);
        } else if (e.key === 'Enter' && activeIdx >= 0) {
            e.preventDefault();
            visible[activeIdx]?.click();
        }
        visible.forEach((el, i) => el.classList.toggle('active', i === activeIdx));
    });
});

function filterSearch(q) {
    const items  = document.querySelectorAll('.nav-item');
    const empty  = document.getElementById('searchEmpty');
    const emptyQ = document.getElementById('emptyQuery');
    let found = 0;
    items.forEach(item => {
        const match = item.dataset.label.includes(q);
        item.style.display = match ? '' : 'none';
        if (match) found++;
    });
    if (empty) {
        empty.classList.toggle('hidden', found > 0 || !q);
        if (emptyQ) emptyQ.textContent = q;
    }
}

// ===== LOADING BUTTON STATE =====
document.addEventListener('DOMContentLoaded', () => {
    // Apply to all submit buttons inside forms (except DELETE)
    document.querySelectorAll('form:not([data-no-loading]) button[type="submit"]').forEach(btn => {
        const form = btn.closest('form');
        form?.addEventListener('submit', () => {
            btn.classList.add('btn-loading');
            btn.disabled = true;
            // Auto-recover after 8s as failsafe
            setTimeout(() => {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
            }, 8000);
        });
    });
});
// ===== GLOBAL SWEETALERT CONFIRMATION & ALERT OVERRIDE =====
window.alert = function(message, type = 'warning', title = 'Pemberitahuan') {
    return Swal.fire({
        title: title,
        text: message,
        icon: type,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Mengerti',
        customClass: {
            popup: 'rounded-2xl shadow-2xl border border-slate-100',
            title: 'text-base font-bold text-slate-800',
            htmlContainer: 'text-xs text-slate-600',
            confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs shadow-md'
        }
    });
};

window.showToast = function(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    Toast.fire({
        icon: type,
        title: message
    });
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. Intercept all elements with onclick="return confirm(...)"
    const confirmElements = document.querySelectorAll('[onclick*="confirm("]');
    confirmElements.forEach(el => {
        const attr = el.getAttribute('onclick') || '';
        const match = attr.match(/confirm\(['"]([^'"]+)['"]\)/);
        const msg = match ? match[1] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        
        el.removeAttribute('onclick');
        
        el.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isDelete = el.innerText.toLowerCase().includes('hapus') || 
                             (el.closest('form') && el.closest('form').querySelector('input[name="_method"][value="DELETE"]')) ||
                             (el.href && (el.href.includes('destroy') || el.href.includes('delete')));
            
            Swal.fire({
                title: 'Konfirmasi',
                text: msg,
                icon: isDelete ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDelete ? '#ef4444' : '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: isDelete ? 'Ya, Hapus!' : 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-100',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (el.tagName === 'BUTTON' && el.type === 'submit' && el.closest('form')) {
                        el.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;
                        el.classList.add('opacity-80', 'cursor-not-allowed');
                        el.closest('form').submit();
                    } else if (el.tagName === 'A' && el.href) {
                        window.location.href = el.href;
                    } else if (el.closest('form')) {
                        el.closest('form').submit();
                    }
                }
            });
        });
    });

    // 2. Intercept all forms with onsubmit="return confirm(...)"
    const confirmForms = document.querySelectorAll('form[onsubmit*="confirm("]');
    confirmForms.forEach(form => {
        const attr = form.getAttribute('onsubmit') || '';
        const match = attr.match(/confirm\(['"]([^'"]+)['"]\)/);
        const msg = match ? match[1] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        
        form.removeAttribute('onsubmit');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') ||
                             form.action.includes('destroy') || form.action.includes('delete');
            
            Swal.fire({
                title: 'Konfirmasi',
                text: msg,
                icon: isDelete ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDelete ? '#ef4444' : '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: isDelete ? 'Ya, Hapus!' : 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-100',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

</body>
</html>