<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Terlalu Banyak Permintaan | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white dark:bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl text-indigo-600 dark:text-indigo-500">429</h1>
        <p class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white">Terlalu Banyak Permintaan</p>
        <p class="mb-4 text-lg font-light text-gray-500 dark:text-gray-400">Anda telah mengirim terlalu banyak permintaan. Silakan coba lagi beberapa saat.</p>
        <a href="{{ route('dashboard') }}" class="inline-flex text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-indigo-900 my-4">Kembali ke Dashboard</a>
    </div>
</body>
</html>