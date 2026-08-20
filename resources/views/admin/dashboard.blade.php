<x-app-layout>
    <x-slot name="header">
        @if(Auth::user()->isPureSuperAdmin())
            Dashboard Super Admin
        @elseif(Auth::user()->isAdmin())
            Dashboard — {{ Auth::user()->department->name ?? 'Admin' }}
        @endif
    </x-slot>

    <!-- CDN Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Stats Cards -->
    @php $isSuperAdmin = Auth::user()->isPureSuperAdmin(); @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 {{ $isSuperAdmin ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-6 mb-8">
                <!-- Total Pengguna (Super Admin saja) -->
                @if($isSuperAdmin)
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Pengguna</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalUsers) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            <span class="text-green-600 font-medium">{{ $usersThisMonth }} baru</span>
                            <span class="text-gray-400 ml-1">bulan ini</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Total Arsip -->
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Arsip</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalArchives) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            <span class="text-green-600 font-medium">{{ $archivesThisMonth }} baru</span>
                            <span class="text-gray-400 ml-1">bulan ini</span>
                        </div>
                    </div>
                </div>

                <!-- Total Download -->
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Download</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalDownloads) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <svg class="w-3 h-3 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span class="text-blue-600 font-medium">Akumulasi</span>
                            <span class="text-gray-400 ml-1">dari semua arsip</span>
                        </div>
                    </div>
                </div>

                {{-- Total Sampah (Trash) --}}
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-rose-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tempat Sampah</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalTrashed ?? 0) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-rose-100 text-rose-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <svg class="w-3 h-3 mr-1 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-rose-600 font-medium">Menunggu pemulihan</span>
                            <span class="text-gray-400 ml-1">atau hapus permanen</span>
                        </div>
                    </div>
                </div>

                {{-- Total Pegawai --}}
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Pegawai</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalPegawai ?? 0) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-orange-100 text-orange-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <svg class="w-3 h-3 mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-orange-600 font-medium">Terdaftar</span>
                            <span class="text-gray-400 ml-1">di semua unit kerja</span>
                        </div>
                    </div>
                </div>

                {{-- Total Usulan Harga --}}
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Usulan Harga</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalUsulan ?? 0) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-amber-100 text-amber-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <svg class="w-3 h-3 mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-amber-600 font-medium">SSH & SBU</span>
                            <span class="text-gray-400 ml-1">di departemen Anda</span>
                        </div>
                    </div>
                </div>

                {{-- Total Pengawasan --}}
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pengawasan</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalPengawasan ?? 0) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-cyan-100 text-cyan-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <svg class="w-3 h-3 mr-1 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-cyan-600 font-medium">Ketaatan Lingkungan</span>
                            <span class="text-gray-400 ml-1">Pelaku Usaha Kota Batu</span>
                        </div>
                    </div>
                </div>


                {{-- Total Unit Kerja --}}
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-teal-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalDepartments ?? 0) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-teal-100 text-teal-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <span class="text-teal-600 font-medium">Struktur</span>
                            <span class="text-gray-400 ml-1">Departemen & Bidang</span>
                        </div>
                    </div>
                </div>

                {{-- Total Kategori Arsip --}}
                <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Kategori Arsip</p>
                                <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalCategories ?? 0) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-pink-100 text-pink-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs">
                            <span class="text-pink-600 font-medium">Klasifikasi</span>
                            <span class="text-gray-400 ml-1">Penamaan Dokumen</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Grafik (70%) dan Aktivitas (30%) - menggunakan grid 12 kolom -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
                <!-- Grafik Tren Arsip - 70% (9 dari 12 kolom) -->
                <div class="lg:col-span-9 bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-md font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Tren Arsip (12 Bulan Terakhir)
                        </h3>
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">Data per bulan</span>
                    </div>
                    <canvas id="archiveChart" height="120"></canvas>
                </div>

                <!-- Aktivitas Terkini - 30% (3 dari 12 kolom) -->
<div class="lg:col-span-3 bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden flex flex-col">
    <div class="p-6 flex-1">
        <h3 class="text-md font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Aktivitas Terkini
            @if($recentActivities && $recentActivities->count() > 0)
                <span class="ml-auto text-xs text-gray-400">{{ $recentActivities->count() }} terbaru</span>
            @endif
        </h3>
        
        <div class="space-y-4">
            @forelse($recentActivities ?? [] as $activity)
                @php
                    $activityType = $activity->activity ?? '';
                    $avatarColor = 'bg-gray-100 text-gray-600';
                    $badgeColor = 'bg-gray-100 text-gray-600';
                    
                    if (str_contains($activityType, 'tambah')) {
                        $avatarColor = 'bg-emerald-100 text-emerald-600';
                        $badgeColor = 'bg-emerald-50 text-emerald-600';
                    } elseif (str_contains($activityType, 'edit')) {
                        $avatarColor = 'bg-amber-100 text-amber-600';
                        $badgeColor = 'bg-amber-50 text-amber-600';
                    } elseif (str_contains($activityType, 'hapus')) {
                        $avatarColor = 'bg-rose-100 text-rose-600';
                        $badgeColor = 'bg-rose-50 text-rose-600';
                    } elseif (str_contains($activityType, 'login')) {
                        $avatarColor = 'bg-blue-100 text-blue-600';
                        $badgeColor = 'bg-blue-50 text-blue-600';
                    } elseif (str_contains($activityType, 'download')) {
                        $avatarColor = 'bg-purple-100 text-purple-600';
                        $badgeColor = 'bg-purple-50 text-purple-600';
                    } elseif (str_contains($activityType, 'status')) {
                        $avatarColor = 'bg-indigo-100 text-indigo-600';
                        $badgeColor = 'bg-indigo-50 text-indigo-600';
                    }
                    
                    $userName = $activity->causer_name ?? ($activity->user->name ?? 'Sistem');
                    $userInitial = strtoupper(substr($userName, 0, 1));
                    $description = $activity->description ?? $activity->activity ?? 'Aktivitas';
                    $timeAgo = $activity->created_at ? $activity->created_at->diffForHumans() : 'Baru saja';
                @endphp
                <div class="flex items-start gap-3 group hover:bg-gray-50 p-2 rounded-xl transition-colors duration-150">
                    <div class="flex-shrink-0">
                        <div class="w-9 h-9 rounded-full {{ $avatarColor }} flex items-center justify-center font-bold text-sm">
                            {{ $userInitial }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center flex-wrap gap-2 mb-0.5">
                            <p class="text-sm font-semibold text-gray-800 truncate max-w-[150px]">{{ $userName }}</p>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium whitespace-nowrap {{ $badgeColor }}">
                                {{ str_replace('_', ' ', ucfirst($activityType)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mt-0.5 break-words">{{ $description }}</p>
                        <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="whitespace-nowrap">{{ $timeAgo }}</span>
                        </p>
                    </div>
                </div>
            @empty
    <div class="flex flex-col items-center justify-center py-12 text-center" style="min-height: 300px;">
        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-500 text-sm">Belum ada aktivitas</p>
        <p class="text-gray-400 text-xs mt-1">Aktivitas akan muncul saat ada interaksi</p>
    </div>
@endempty
        </div>
    </div>
</div>
            </div>

            <!-- Tabel Arsip Terbaru dan Pengguna Terbaru (2 kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Arsip Terbaru -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <h3 class="text-md font-bold text-gray-800">📄 Arsip Terbaru</h3>
                        <a href="{{ route('admin.archives.index') }}" class="text-xs font-medium text-indigo-600 hover:underline flex items-center gap-1">Lihat semua <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentArchives ?? [] as $archive)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $archive->title }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $archive->category->name ?? '-' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $archive->created_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-14 h-14 text-indigo-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-indigo-400 text-sm font-medium mt-1">Belum ada arsip yang diunggah</p>
                                                <p class="text-gray-400 text-xs mt-1">Silakan klik tombol "Unggah Arsip" untuk menambah dokumen</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endempty
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pengguna Terbaru (Super Admin saja) -->
                @if($isSuperAdmin)
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <h3 class="text-md font-bold text-gray-800">👥 Pengguna Terbaru</h3>
                        <a href="{{ route('users.index') }}" class="text-xs font-medium text-indigo-600 hover:underline flex items-center gap-1">Lihat semua <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers ?? [] as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $user->name }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $user->role->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-14 h-14 text-indigo-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        <p class="text-indigo-400 text-sm font-medium">Belum ada pengguna baru</p>
                                    </div>
                                </td></tr>
                                @endempty
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                {{-- Admin Biasa: tampilkan ringkasan info departemennya --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100 p-6 flex flex-col justify-center">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-bold">Unit Kerja Anda</p>
                            <p class="text-lg font-bold text-gray-800">{{ Auth::user()->department->name ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">Anda mengelola arsip dan data khusus untuk unit kerja ini. Gunakan menu navigasi di sebelah kiri untuk mengakses fitur yang tersedia.</p>
                    <a href="{{ route('admin.archives.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                        Lihat Data Arsip
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endif
            </div>

            <!-- Selamat Datang Card (Role-Aware) -->
            <div class="bg-gradient-to-r from-indigo-50 via-white to-emerald-50 rounded-2xl shadow-md border border-indigo-100 p-6">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-600 to-indigo-400 flex items-center justify-center text-white font-bold text-2xl shadow-lg ring-4 ring-white">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h3>
                        @if($isSuperAdmin)
                            <p class="text-gray-500 text-sm">Anda memiliki akses penuh sebagai Super Admin. Kelola pengguna, arsip, data kepegawaian, dan pantau seluruh aktivitas sistem.</p>
                        @else
                            <p class="text-gray-500 text-sm">Anda login sebagai Admin Unit Kerja <strong>{{ Auth::user()->department->name ?? '' }}</strong>. Kelola arsip dan data kepegawaian di unit Anda.</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-end">
                        @if($isSuperAdmin)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">Super Admin</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Admin</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800">{{ Auth::user()->department->name ?? '' }}</span>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                    </div>
                </div>
            </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('archiveChart').getContext('2d');
            // Pastikan data 12 bulan dari controller
            const months = @json($months ?? []);
            const archiveData = @json($archiveTrend ?? []);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Jumlah Arsip',
                        data: archiveData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#e2e8f0' } },
                        x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } }
                    }
                }
            });
        });
    </script>
</x-app-layout>