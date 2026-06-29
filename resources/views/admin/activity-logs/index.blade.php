<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Aktivitas Log') }}
            </h2>
        </div>
    </x-slot>

    @include('admin.activity-logs.info')

    {{-- --- CARD STATISTIK LOG --- --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Log Aktivitas</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($logs->total()) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    <span class="text-green-600 font-medium">Seluruh waktu</span>
                    <span class="text-gray-400 ml-1">aktivitas tercatat</span>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Unique User</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($logs->getCollection()->unique('user_id')->count()) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    <span class="text-green-600 font-medium">{{ $logs->getCollection()->unique('user_id')->count() }} user</span>
                    <span class="text-gray-400 ml-1">dari halaman ini</span>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Hari Ini</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($logs->where('created_at', '>=', now()->startOfDay())->count()) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-100 text-amber-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <svg class="w-3 h-3 mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-amber-600 font-medium">Aktivitas hari ini</span>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Arsip Excel</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ count($exportFiles) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-100 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <svg class="w-3 h-3 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span class="text-blue-600 font-medium">File tersedia</span>
                    <span class="text-gray-400 ml-1">untuk diunduh</span>
                </div>
            </div>
        </div>
    </div>

    {{-- --- TABEL DATA LOG AKTIVITAS MENGGUNAKAN KOMPONEN REUSABLE --- --}}
    <x-table :isEmpty="$logs->isEmpty()" emptyMessage="Belum ada log aktivitas yang tercatat.">
        
        {{-- Slot Judul Tabel Kiri Atas --}}
        <x-slot name="title">
            Log Aktivitas Sistem
        </x-slot>

        {{-- Slot Input Pencarian Kanan Atas --}}
        <x-slot name="actions">
            <x-search-input 
                route="{{ route('activity-logs.index') }}" 
                placeholder="Cari aktivitas, user, atau IP..."
                searchParam="search"
                buttonText="Cari"
                :resetButton="true"
            />
        </x-slot>

        {{-- Slot Kolom Header Tabel (Thead polos, style warna diwarisi otomatis dari komponen) --}}
        <x-slot name="thead">
            <th class="px-6 py-4 text-left">User</th>
            <th class="px-6 py-4 text-left">Aktivitas</th>
            <th class="px-6 py-4 text-left w-80">Deskripsi</th>
            <th class="px-6 py-4 text-left">IP Address</th>
            <th class="px-6 py-4 text-left min-w-[200px]">Perangkat</th>
            <th class="px-6 py-4 text-left">Waktu</th>
        </x-slot>

        {{-- Slot Isi Data Baris Tabel (Tbody) --}}
        <x-slot name="tbody">
            @foreach($logs as $log)
                @php
                    $displayName = $log->causer_name ?? ($log->user->name ?? 'Guest');
                    $displayEmail = $log->causer_email ?? ($log->user->email ?? '');
                    $isDeleted = is_null($log->user_id) && $log->causer_name;
                    $avatarColor = $isDeleted ? 'bg-red-100 text-red-600' : 'bg-indigo-100 text-indigo-800';
                    
                    $activity = $log->activity;
                    $badgeClass = 'bg-gray-100 text-gray-800';
                    if (str_contains($activity, 'tambah')) {
                        $badgeClass = 'bg-blue-100 text-blue-800';
                    } elseif (str_contains($activity, 'ubah')) {
                        $badgeClass = 'bg-amber-100 text-amber-800';
                    } elseif (str_contains($activity, 'hapus')) {
                        $badgeClass = 'bg-rose-100 text-rose-800';
                    } elseif (str_contains($activity, 'login')) {
                        $badgeClass = 'bg-gray-100 text-gray-800';
                    } elseif (str_contains($activity, 'logout')) {
                        $badgeClass = 'bg-gray-100 text-gray-800';
                    } elseif (str_contains($activity, 'pulihkan')) {
                        $badgeClass = 'bg-green-100 text-green-800';
                    } elseif (str_contains($activity, 'unduh')) {
                        $badgeClass = 'bg-purple-200 text-purple-800';
                    }
                @endphp
                <tr class="hover:bg-slate-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full {{ $avatarColor }} flex items-center justify-center font-semibold text-sm shrink-0">
                                {{ strtoupper(substr($displayName, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $displayName }}</p>
                                <p class="text-xs text-gray-500">{{ $displayEmail }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                            {{ $activity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 break-words max-w-md">
                        {{ $log->description ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                        {{ $log->ip_address ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-normal break-words max-w-xs md:max-w-sm" title="{{ $log->user_agent }}">
                        <div class="flex items-start gap-1">
                            @php
                                $ua = $log->user_agent ?? '';
                                $icon = '🌐';
                                if (str_contains($ua, 'Windows')) $icon = '🪟';
                                elseif (str_contains($ua, 'Mac')) $icon = '🍎';
                                elseif (str_contains($ua, 'Linux')) $icon = '🐧';
                                elseif (str_contains($ua, 'Android')) $icon = '📱';
                                elseif (str_contains($ua, 'iPhone')) $icon = '📱';
                            @endphp
                            <span class="text-base shrink-0">{{ $icon }}</span>
                            <span class="break-words">{{ $ua ? Str::limit($ua, 50) : '-' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $log->created_at->translatedFormat('d M Y, H:i:s') }}
                    </td>
                </tr>
            @endforeach
        </x-slot>
    </x-table>

    {{-- Pagination links diletakkan di luar komponen tabel --}}
    <div class="mt-6">
        {{ $logs->withQueryString()->links() }}
    </div>

    {{-- --- BLOK BAGIAN BAWAH: ARSIP BULANAN (EXCEL CARD GRID) --- --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mt-8">
        <div class="p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Arsip Log Bulanan (Excel)
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($exportFiles as $file)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-200 group">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800 break-words">{{ $file['name'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ round($file['size'] / 1024) }} KB • {{ date('d M Y', $file['last_modified']) }}
                                </p>
                            </div>
                            <a href="{{ route('activity-logs.download', $file['name']) }}" 
                               class="text-indigo-600 hover:text-indigo-800 transition p-1 rounded-lg hover:bg-indigo-50"
                               title="Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-sm">
                        <div class="flex items-start gap-2">
                            <span class="text-base">⚠️</span>
                            <div>
                                <p class="font-semibold">Data file kosong / Tidak lolos filter nama!</p>
                                <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                                    Sistem mendeteksi folder fisik ada, tetapi tidak ada file yang memenuhi kriteria pengiriman Controller. 
                                    Pastikan nama file di dalam folder <code>storage/app/private/exports/</code> mengandung format kata pencarian (seperti kata 'logs_') jika Anda menerapkan filter regex di Controller Anda.
                                </p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>