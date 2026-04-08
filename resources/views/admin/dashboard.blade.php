<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Super Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Card Total Pengguna --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-l-4 border-indigo-500 transition-transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-indigo-100 text-indigo-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h4>
                                <div class="text-gray-500 text-sm font-medium">Total Pengguna</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Total Arsip --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-l-4 border-green-500 transition-transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-green-100 text-green-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-bold text-gray-800">{{ $totalArchives }}</h4>
                                <div class="text-gray-500 text-sm font-medium">Total Arsip</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Pending / Log Aktivitas Terkini --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-l-4 border-yellow-500 transition-transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-yellow-100 text-yellow-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-bold text-gray-800">{{ $pendingArchives }}</h4>
                                <div class="text-gray-500 text-sm font-medium">Menunggu Persetujuan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-gray-100">
                <div class="flex items-center gap-4 text-gray-900">
                    <div class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xl">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Selamat Datang, {{ Auth::user()->name }}!</h3>
                        <p class="text-slate-500 text-sm">Anda memiliki akses penuh sebagai Super Admin aplikasi ini.</p>
                    </div>
                </div>
                
                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <h3 class="text-slate-800 font-bold mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Aksi Cepat
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('admin.archives.index') }}" class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm hover:border-indigo-300 border border-transparent transition-all group">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600">Lihat Semua Arsip</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('users.index') }}" class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm hover:border-indigo-300 border border-transparent transition-all group">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600">Kelola Pengguna</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <h3 class="text-slate-800 font-bold mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Konfigurasi
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('categories.index') }}" class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm hover:border-emerald-300 border border-transparent transition-all group">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-emerald-600">Master Kategori</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>