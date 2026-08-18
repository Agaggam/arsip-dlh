<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi | {{ config('app.name', 'E-Arsip DLH') }}</title>
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
        <div class="absolute inset-0 z-0 bg-slate-900">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#94a3b8 1.5px, transparent 1.5px); background-size: 32px 32px;"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-50 via-transparent to-transparent"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800 border border-slate-600 mb-4 text-slate-300 text-xs font-bold tracking-widest uppercase">
                <i class="fas fa-shield-alt"></i> Legal & Privasi
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">Kebijakan Privasi</h1>
            <p class="text-slate-300 text-lg sm:text-xl max-w-2xl mx-auto drop-shadow">Komitmen kami dalam melindungi data dan privasi Anda dalam sistem kearsipan.</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-10 relative z-10">
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-12 border border-gray-100">
            
            <div class="prose prose-slate max-w-none prose-lg">
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Dinas Lingkungan Hidup (DLH) menghargai privasi Anda. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan sistem E-Arsip DLH.
                </p>

                <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">1. Pengumpulan Informasi</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Kami mengumpulkan informasi identitas pribadi, seperti Nama, Alamat Email, dan Departemen tempat Anda bekerja, yang diperlukan untuk keperluan otentikasi akun dan pembagian hak akses (Role-Based Access Control) dalam sistem.
                </p>

                <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">2. Penggunaan Informasi</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Informasi yang kami kumpulkan digunakan secara khusus untuk:
                </p>
                <ul class="text-gray-600 mb-6 space-y-2 list-disc list-inside">
                    <li>Memverifikasi akses log-in ke dalam sistem.</li>
                    <li>Mencatat riwayat aktivitas (audit log) pada setiap unggahan atau perubahan dokumen.</li>
                    <li>Berkomunikasi terkait pemeliharaan dan pembaruan sistem.</li>
                </ul>

                <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">3. Keamanan Data Arsip</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Seluruh dokumen arsip yang diunggah dilindungi dengan enkripsi dan pengamanan server berlapis. Arsip bersifat tertutup antar bidang sesuai kebijakan DLH, kecuali bagi Super Admin atau pengakses yang memiliki "Token Arsip" khusus.
                </p>

                <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">4. Kebijakan Berbagi Data</h2>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Kami <strong>tidak pernah</strong> menjual, menukar, atau membagikan informasi pribadi atau arsip rahasia Anda ke pihak ketiga di luar kepentingan institusi Dinas Lingkungan Hidup tanpa persetujuan sah secara hukum.
                </p>

                <div class="bg-slate-50 border-l-4 border-slate-500 p-4 rounded-r-lg mt-8">
                    <p class="text-sm text-slate-700 italic">
                        Terakhir diperbarui: {{ date('d F Y') }} <br>
                        Kebijakan privasi ini dapat berubah sewaktu-waktu sesuai dengan regulasi pemerintah dan kebijakan internal DLH.
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center mt-12 mb-8">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Sistem Informasi Arsip DLH. Hak Cipta Dilindungi.</p>
        </div>
    </div>

</body>
</html>
