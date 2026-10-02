<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | {{ config('app.name', 'E-Arsip DLH') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 flex items-center justify-center min-h-screen p-4 font-sans">
    <div class="text-center max-w-md mx-auto">
        <h1 class="mb-2 text-7xl lg:text-8xl tracking-tight font-extrabold text-indigo-600">404</h1>
        <p class="mb-3 text-2xl font-bold text-slate-800">Halaman Tidak Ditemukan</p>
        <p class="mb-6 text-sm text-slate-500 leading-relaxed">Halaman yang Anda cari tidak tersedia, telah dipindahkan, atau alamat URL salah.</p>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center text-white bg-indigo-600 hover:bg-indigo-700 font-semibold rounded-xl text-sm px-6 py-3 transition shadow-sm">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>