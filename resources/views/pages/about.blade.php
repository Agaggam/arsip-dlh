<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang Kami | {{ config('app.name', 'E-Arsip DLH') }}</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes ken-burns {
            0% { transform: scale(1); }
            100% { transform: scale(1.15); }
        }
        .animate-ken-burns {
            animation: ken-burns 20s ease-in-out infinite alternate;
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-800 font-sans">
    
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-700 hover:text-emerald-600 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span class="font-semibold text-sm">Kembali ke Beranda</span>
                </a>
                <div class="font-bold text-lg text-emerald-700 flex items-center gap-2">
                    <img src="{{ asset('images/logo-dlh.png') }}" alt="Logo" class="w-7 h-7 object-contain"> {{ config('app.name') }}
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="relative py-24 sm:py-32 text-center px-4 overflow-hidden">
        <!-- Animated Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1511497584788-876760111969?q=80&w=2000&auto=format&fit=crop" 
                 alt="Forest Background" 
                 class="w-full h-full object-cover animate-ken-burns">
            <div class="absolute inset-0 bg-emerald-900/75 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-50 via-transparent to-transparent"></div>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">Tentang E-Arsip DLH</h1>
            <p class="text-emerald-50 text-lg sm:text-xl max-w-2xl mx-auto drop-shadow">Mewujudkan tata kelola kearsipan lingkungan hidup yang modern, aman, dan ramah lingkungan.</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-10 relative z-10">
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-12 border border-gray-100">
            
            <div class="prose prose-emerald max-w-none prose-lg">
                <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-100 pb-4 mb-6"><i class="fas fa-bullseye text-emerald-500 mr-2"></i> Visi Kami</h2>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Sistem <strong>E-Arsip Dinas Lingkungan Hidup (DLH)</strong> dibangun dengan visi untuk mendigitalisasi seluruh dokumen penting pemerintahan, guna mengurangi penggunaan kertas (paperless) serta memastikan jejak rekam lingkungan tersimpan dengan aman dan dapat diakses dengan sangat cepat ketika dibutuhkan.
                </p>

                <div class="grid sm:grid-cols-2 gap-8 mb-10">
                    <div class="bg-emerald-50 rounded-xl p-6 border border-emerald-100">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-emerald-600 text-xl shadow-sm mb-4">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Aman & Rahasia</h3>
                        <p class="text-sm text-gray-600">Seluruh dokumen disimpan dalam server terenkripsi dengan manajemen hak akses (Role-Based Access) yang ketat.</p>
                    </div>
                    <div class="bg-teal-50 rounded-xl p-6 border border-teal-100">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-teal-600 text-xl shadow-sm mb-4">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Cepat & Efisien</h3>
                        <p class="text-sm text-gray-600">Pencarian dokumen memakan waktu kurang dari 1 detik. Selamat tinggal pencarian arsip fisik berjam-jam.</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-100 pb-4 mb-6"><i class="fas fa-users text-emerald-500 mr-2"></i> Siapa yang Menggunakan Sistem Ini?</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Sistem ini dirancang khusus untuk internal Dinas Lingkungan Hidup. Setiap Bidang / Bagian memiliki ruang kerja masing-masing yang dipimpin oleh Admin Bidang, sementara arsip secara keseluruhan dikelola oleh Super Admin.
                </p>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex gap-3"><i class="fas fa-check-circle text-emerald-500 mt-1"></i> <strong>Super Admin:</strong> Mengelola seluruh konfigurasi, pengguna, dan memantau log aktivitas.</li>
                    <li class="flex gap-3"><i class="fas fa-check-circle text-emerald-500 mt-1"></i> <strong>Admin Bidang:</strong> Mengelola, mengupload, dan mengedit arsip di bidangnya masing-masing.</li>
                    <li class="flex gap-3"><i class="fas fa-check-circle text-emerald-500 mt-1"></i> <strong>User Biasa:</strong> Dapat mencari, melihat, dan mengunduh arsip tanpa bisa mengubah data.</li>
                </ul>

            </div>
        </div>

        <div class="text-center mt-12 mb-8">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Sistem Informasi Arsip DLH. Hak Cipta Dilindungi.</p>
        </div>
    </div>

</body>
</html>
