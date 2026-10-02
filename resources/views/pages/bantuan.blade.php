<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusat Bantuan | {{ config('app.name', 'E-Arsip DLH') }}</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        <div class="absolute inset-0 z-0 bg-blue-900">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#60a5fa 1.5px, transparent 1.5px); background-size: 32px 32px;"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-50 via-transparent to-transparent"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100/20 border border-blue-400/30 mb-4 text-blue-100 text-xs font-bold tracking-widest uppercase">
                <i class="fas fa-headset"></i> Support
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">Pusat Bantuan</h1>
            <p class="text-blue-50 text-lg sm:text-xl max-w-2xl mx-auto drop-shadow">Butuh bantuan menggunakan E-Arsip DLH? Kami siap membantu menyelesaikan kendala Anda.</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-10 relative z-10">
        
        <div class="grid sm:grid-cols-2 gap-6 mb-10">
            <!-- FAQ 1 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Mengapa saya tidak bisa login?</h3>
                <p class="text-sm text-gray-600">Pastikan email dan password Anda diketik dengan benar. Jika akun Anda baru dibuat oleh admin, pastikan statusnya telah diaktifkan.</p>
            </div>
            <!-- FAQ 2 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Bagaimana cara mereset password?</h3>
                <p class="text-sm text-gray-600">Saat ini reset password hanya dapat dilakukan melalui Super Admin. Silakan hubungi bagian IT atau Admin pusat di dinas Anda.</p>
            </div>
            <!-- FAQ 3 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Saya tidak bisa melihat arsip Bidang lain</h3>
                <p class="text-sm text-gray-600">Ini normal. Sistem membatasi hak akses Anda hanya untuk melihat dokumen yang relevan dengan Departemen/Bidang Anda untuk menjaga kerahasiaan.</p>
            </div>
            <!-- FAQ 4 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Apa arti dari "Token Arsip"?</h3>
                <p class="text-sm text-gray-600">Token arsip adalah kode rahasia unik untuk setiap arsip. Pengguna publik bisa mengunduh arsip tertentu jika mengetahui token rahasianya.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Masih Butuh Bantuan?</h2>
            <p class="text-gray-600 mb-8 max-w-lg mx-auto">Tim IT Support kami tersedia setiap hari kerja dari jam 08:00 hingga 16:00. Hubungi kami melalui kontak di bawah ini.</p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="mailto:support@dlh-arsip.go.id" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-50 text-blue-700 font-semibold rounded-xl hover:bg-blue-100 transition w-full sm:w-auto justify-center">
                    <i class="fas fa-envelope"></i> support@dlh-arsip.go.id
                </a>
                <a href="tel:+623411234567" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-50 text-emerald-700 font-semibold rounded-xl hover:bg-emerald-100 transition w-full sm:w-auto justify-center">
                    <i class="fas fa-phone"></i> (0341) 123-4567
                </a>
            </div>
        </div>

        <div class="text-center mt-12 mb-8">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Sistem Informasi Arsip DLH. Hak Cipta Dilindungi.</p>
        </div>
    </div>

</body>
</html>
