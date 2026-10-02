<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panduan Penggunaan | {{ config('app.name', 'E-Arsip DLH') }}</title>
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
        <div class="absolute inset-0 z-0 bg-emerald-900">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#34d399 1.5px, transparent 1.5px); background-size: 32px 32px;"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-50 via-transparent to-transparent"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100/20 border border-emerald-400/30 mb-4 text-emerald-100 text-xs font-bold tracking-widest uppercase">
                <i class="fas fa-book"></i> Dokumentasi
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">Panduan Penggunaan</h1>
            <p class="text-emerald-50 text-lg sm:text-xl max-w-2xl mx-auto drop-shadow">Pelajari cara menggunakan sistem E-Arsip DLH dengan langkah-langkah yang mudah dipahami.</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-10 relative z-10">
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-12 border border-gray-100">
            
            <div class="prose prose-emerald max-w-none prose-lg">
                <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-100 pb-4 mb-6"><i class="fas fa-sign-in-alt text-emerald-500 mr-2"></i> 1. Cara Masuk (Login)</h2>
                <ul class="text-gray-600 mb-8 space-y-2">
                    <li>Buka halaman utama E-Arsip DLH.</li>
                    <li>Klik tombol <strong>Masuk</strong> di pojok kanan atas.</li>
                    <li>Masukkan Email dan Password yang telah didaftarkan atau diberikan oleh administrator.</li>
                    <li>Jika Anda lupa password, Anda bisa menghubungi Super Admin departemen Anda untuk mereset kata sandi.</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-100 pb-4 mb-6"><i class="fas fa-search text-emerald-500 mr-2"></i> 2. Mencari Dokumen</h2>
                <ul class="text-gray-600 mb-8 space-y-2">
                    <li>Setelah login, Anda akan diarahkan ke Dashboard utama.</li>
                    <li>Gunakan <strong>Kotak Pencarian Token</strong> jika Anda memiliki token atau nomor spesifik dari sebuah arsip.</li>
                    <li>Untuk pencarian arsip umum, buka menu <strong>Data Arsip</strong> pada sidebar kiri.</li>
                    <li>Gunakan filter <em>Departemen</em>, <em>Kategori</em>, atau <em>Tanggal</em> untuk mempersempit hasil pencarian Anda.</li>
                </ul>

                <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-100 pb-4 mb-6"><i class="fas fa-upload text-emerald-500 mr-2"></i> 3. Mengunggah Arsip (Khusus Admin)</h2>
                <p class="text-gray-600 mb-4">Fitur ini hanya tersedia bagi pengguna dengan hak akses <strong>Admin Bidang</strong> dan <strong>Super Admin</strong>.</p>
                <ol class="text-gray-600 mb-8 space-y-2 list-decimal list-inside">
                    <li>Buka menu <strong>Data Arsip</strong> dari sidebar.</li>
                    <li>Klik tombol <strong>+ Tambah Arsip Baru</strong>.</li>
                    <li>Isi formulir dengan lengkap (Judul, Kategori, Departemen, dan Deskripsi).</li>
                    <li>Pilih file dokumen (.pdf, .doc, dsb) atau foto untuk diunggah. Maksimal ukuran file 10MB.</li>
                    <li>Klik <strong>Simpan</strong>. Sistem akan mengunggah dan mengenkripsi data secara otomatis.</li>
                </ol>

                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mt-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lightbulb text-amber-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Tips Tambahan</h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <p>Jika Anda mengalami kendala saat mengunggah arsip besar, pastikan koneksi internet Anda stabil atau hubungi pihak IT/Administrator jaringan Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-12 mb-8">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Sistem Informasi Arsip DLH. Hak Cipta Dilindungi.</p>
        </div>
    </div>

</body>
</html>
