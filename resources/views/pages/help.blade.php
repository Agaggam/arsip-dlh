<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusat Bantuan | {{ config('app.name', 'E-Arsip DLH') }}</title>
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
                    <img src="{{ asset('images/logo-dlh.png') }}" alt="Logo" class="w-7 h-7 object-contain"> Pusat Bantuan
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="relative py-24 sm:py-32 text-center px-4 overflow-hidden">
        <!-- Animated Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2000&auto=format&fit=crop" 
                 alt="Abstract Tech Background" 
                 class="w-full h-full object-cover animate-ken-burns">
            <div class="absolute inset-0 bg-slate-900/80 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-50 via-transparent to-transparent"></div>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">Ada yang bisa kami bantu?</h1>
            <p class="text-gray-200 text-lg sm:text-xl max-w-2xl mx-auto drop-shadow">Temukan jawaban untuk pertanyaan umum seputar E-Arsip DLH di bawah ini.</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-10 relative z-10">
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-12 border border-gray-100">
            
            <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                <i class="fas fa-question-circle text-emerald-500"></i> FAQ (Pertanyaan yang Sering Diajukan)
            </h2>

            <div class="space-y-6">
                <!-- FAQ Item 1 -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:border-emerald-200 transition">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">1. Bagaimana cara mengunduh Arsip?</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Anda harus mendaftar dan memverifikasi akun Anda terlebih dahulu. Setelah berhasil login, cari dokumen yang Anda perlukan di menu <strong>Data Arsip</strong>, lalu klik ikon <strong>Download</strong> atau tombol Preview untuk melihatnya terlebih dahulu.
                    </p>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:border-emerald-200 transition">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">2. Mengapa saya tidak bisa mengupload dokumen?</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Hanya pengguna dengan hak akses <strong>Admin</strong> dan <strong>Super Admin</strong> yang diperbolehkan mengupload atau mengedit dokumen. Jika Anda adalah Admin Bidang tetapi tidak bisa mengupload, silakan hubungi Super Admin untuk penyesuaian Role Anda.
                    </p>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:border-emerald-200 transition">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">3. Apa yang terjadi jika arsip terhapus?</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Jangan khawatir, E-Arsip DLH dilengkapi fitur <em>Soft Delete</em>. Dokumen yang dihapus akan masuk ke menu <strong>Tong Sampah (Trash)</strong> dan dapat dipulihkan kembali oleh Super Admin. Dokumen baru benar-benar hilang jika di-hapus permanen dari menu Trash.
                    </p>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:border-emerald-200 transition">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">4. Bagaimana jika saya lupa password?</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Anda bisa menggunakan fitur <strong>Lupa Password</strong> di halaman Login. Sistem akan mengirimkan link reset password ke email yang terdaftar.
                    </p>
                </div>
            </div>

            <!-- Contact Box -->
            <div class="mt-12 bg-emerald-50 rounded-xl p-6 sm:p-8 text-center border border-emerald-100">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-emerald-600 text-2xl shadow-sm mx-auto mb-4">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Masih Butuh Bantuan?</h3>
                <p class="text-gray-600 text-sm mb-6">Hubungi administrator sistem (Super Admin) di instansi Anda atau kirimkan tiket bantuan melalui email resmi IT Support DLH.</p>
                <a href="mailto:support@dlh.go.id" class="inline-block bg-emerald-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-emerald-700 transition">
                    Hubungi Support
                </a>
            </div>

        </div>

        <div class="text-center mt-12 mb-8">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Sistem Informasi Arsip DLH. Hak Cipta Dilindungi.</p>
        </div>
    </div>

</body>
</html>
