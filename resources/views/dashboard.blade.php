<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12 space-y-6">
        
        {{-- Quick Access & Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Data Arsip --}}
            <a href="{{ route('arsip.user') }}" 
               class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Lihat Arsip</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800" data-stat-user="totalArsip">{{ number_format($totalArsip ?? 0) }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Daftar Arsip Digital</p>
            </a>

            {{-- Card 2: Usulan Harga --}}
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm cursor-default">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">SSH/SBU</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800" data-stat-user="totalUsulan">{{ number_format($totalUsulan ?? 0) }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Usulan Harga</p>
            </div>

            {{-- Card 3: Data Pengawasan --}}
            <a href="{{ route('pengawasan.index') }}" 
               class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <span class="text-xs font-bold text-cyan-600 bg-cyan-50 px-2 py-0.5 rounded-full">Pengawasan</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800" data-stat-user="totalPengawasan">{{ number_format($totalPengawasan ?? 0) }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Data Pengawasan</p>
            </a>

            {{-- Card 4: Data Kepegawaian --}}
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm cursor-default">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Kepegawaian</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-800" data-stat-user="totalPegawai">{{ number_format($totalPegawai ?? 0) }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Data Pegawai</p>
            </div>
        </div>


        {{-- PORTAL & SISTEM TERINTEGRASI DLH --}}
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl shadow-xl p-6 text-white mb-6 border border-indigo-900/50">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold flex items-center gap-2 text-white">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Portal & Aplikasi Terintegrasi DLH
                    </h3>
                    <p class="text-xs text-slate-300 mt-0.5">Akses cepat ke sistem eksternal dan portal layanan Dinas Lingkungan Hidup</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Card 1: Portal Taman Kota --}}
                <a href="/portal-taman/" target="_blank" rel="noopener noreferrer" 
                   class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl p-4 transition-all duration-200 flex items-center justify-between group backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-emerald-500/20 text-emerald-400 rounded-xl group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white group-hover:text-emerald-300 transition-colors">Portal Taman Kota</h4>
                            <p class="text-xs text-slate-300 mt-0.5">Sistem Informasi Pengelolaan & Peta Informasi Taman Kota</p>
                        </div>
                    </div>
                    <span class="p-2 text-slate-400 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>

                {{-- Card 2: Bank Sampah --}}
                <a href="https://mybanksampah.netlify.app/" target="_blank" rel="noopener noreferrer" 
                   class="bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl p-4 transition-all duration-200 flex items-center justify-between group backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-teal-500/20 text-teal-400 rounded-xl group-hover:bg-teal-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white group-hover:text-teal-300 transition-colors">Bank Sampah Kota</h4>
                            <p class="text-xs text-slate-300 mt-0.5">Sistem Informasi & Manajemen Bank Sampah DLH</p>
                        </div>
                    </div>
                    <span class="p-2 text-slate-400 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>
            </div>
        </div>
        
        {{-- Form Pencarian Simpel (Responsif) --}}
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-[1.25rem] sm:rounded-[1.5rem] p-4 sm:p-6 mb-6">
            <div class="max-w-xl mx-auto text-center">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1 sm:mb-2">Search File Via Token</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mb-4 px-2">File hanya akan muncul jika Anda memasukkan kode token pengaman yang valid.</p>
                
                {{-- Form berubah menjadi kolom pada mobile, row pada tablet ke atas --}}
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Masukkan kode..." 
                        class="w-full text-sm border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm px-4 py-2.5"
                        required
                    >
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center shrink-0">
                            Cari Berkas
                        </button>
                        @if(request('search'))
                            <a href="{{ route('dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold py-2.5 px-3.5 rounded-xl transition-all flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Hasil Pencarian Berbentuk Card (Sangat Responsif) --}}
        <div class="space-y-4">
            @forelse($archives as $archive)
                @php
                    $ext = strtolower($archive->file_type);
                    $bgClass = 'bg-slate-100 text-slate-600'; 
                    if(in_array($ext, ['pdf'])) $bgClass = 'bg-red-100 text-red-600';
                    elseif(in_array($ext, ['xlsx', 'xls', 'csv'])) $bgClass = 'bg-emerald-100 text-emerald-600';
                    elseif(in_array($ext, ['doc', 'docx'])) $bgClass = 'bg-blue-100 text-blue-600';
                    elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $bgClass = 'bg-purple-100 text-purple-600';
                    
                    $downloadRoute = route('admin.archives.download', $archive); 
                @endphp

                {{-- Tampilan Card Hasil Berkas --}}
                <div class="bg-white border border-slate-100 rounded-[1.25rem] sm:rounded-[1.5rem] p-4 sm:p-6 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 md:gap-6 group hover:shadow-md transition-all">
                    
                    {{-- Bagian Info File (Icon + Deskripsi) --}}
                    <div class="flex items-center gap-4 min-w-0">
                        {{-- Badge Ekstensi File Besar --}}
                        <div class="h-14 w-14 sm:h-16 sm:w-16 {{ $bgClass }} rounded-xl sm:rounded-2xl flex flex-col items-center justify-center shadow-inner tracking-wide font-black uppercase text-[10px] sm:text-xs shrink-0">
                            <span>{{ $archive->file_type }}</span>
                        </div>
                        
                        {{-- Informasi Arsip (Gunakan min-w-0 dan truncate agar teks panjang tidak merusak layout) --}}
                        <div class="min-w-0 flex-1">
                            <span class="inline-block px-2 py-0.5 text-[9px] sm:text-[10px] font-extrabold rounded-md border bg-slate-50 text-slate-600 border-slate-200 uppercase tracking-wider mb-1">
                                {{ $archive->category->name ?? 'N/A' }}
                            </span>
                            <h4 class="text-sm sm:text-base font-bold text-slate-800 leading-snug group-hover:text-indigo-600 transition-colors truncate break-all" title="{{ $archive->title }}">
                                {{ $archive->title }}
                            </h4>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5 text-[11px] sm:text-xs font-medium text-slate-400">
                                <span class="whitespace-nowrap">
                                    Ukuran: <strong class="text-slate-600 font-semibold">{{ $archive->file_size }}</strong>
                                </span>
                                <span class="hidden sm:inline">•</span>
                                <span class="whitespace-nowrap">{{ \Carbon\Carbon::parse($archive->archive_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Tindakan / Aksi (Sejajar horizontal di mobile, rapi di kanan saat tablet/laptop) --}}
                    <div class="flex items-center gap-2 w-full md:w-auto border-t md:border-t-0 pt-3 md:pt-0 justify-end shrink-0">
                        {{-- CUTE BUTTON XLS --}}
                        <a href="{{ route('admin.archives.export-excel-single', $archive) }}" 
                           class="bg-emerald-100/80 hover:bg-emerald-200 text-emerald-800 font-extrabold text-[10px] px-2.5 py-2 md:py-1.5 rounded-xl transition-all shadow-2xs border border-emerald-300/40" 
                           title="Unduh Data Excel">
                            XLS
                        </a>

                        {{-- CUTE BUTTON PDF --}}
                        <a href="{{ route('admin.archives.export-pdf-single', $archive) }}" 
                           class="bg-rose-100/80 hover:bg-rose-200 text-rose-800 font-extrabold text-[10px] px-2.5 py-2 md:py-1.5 rounded-xl transition-all shadow-2xs border border-rose-300/40" 
                           title="Unduh Lembar PDF">
                            PDF
                        </a>

                        {{-- PRATINJAU --}}
                        <button type="button" 
                                x-data 
                                x-on:click="$dispatch('open-modal', { 
                                    id: 'preview-modal', 
                                    title: 'Preview: {{ addslashes($archive->title) }}', 
                                    fileUrl: '{{ route('arsip.preview', $archive->hash_token) }}', 
                                    fileType: '{{ strtolower($archive->file_type) }}', 
                                    downloadUrl: '{{ $downloadRoute }}' 
                                })" 
                                class="flex-1 md:flex-none flex items-center justify-center gap-1.5 px-3.5 py-2.5 md:py-2 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 font-bold text-xs rounded-xl transition-all" 
                                title="Pratinjau Berkas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Pratinjau</span>
                        </button>
                        
                        {{-- DOWNLOAD --}}
                        <a href="{{ $downloadRoute }}" class="flex-1 md:flex-none flex items-center justify-center gap-1.5 px-3.5 py-2.5 md:py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition-all" title="Unduh Berkas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Unduh File</span>
                        </a>
                    </div>
                </div>

            @empty
                {{-- State Kosong / Belum Cari --}}
                <div class="bg-white border border-dashed border-slate-200 rounded-[1.25rem] sm:rounded-[1.5rem] p-8 sm:p-12 text-center shadow-sm">
                    <div class="h-11 w-11 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium italic max-w-md mx-auto leading-relaxed">
                        {{ request('search') ? 'Maaf, berkas tidak ditemukan. Pastikan token yang di masukkan sudah benar.' : 'Silakan masukkan kode token akses di atas untuk menampilkan berkas dengan cepat.' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

{{-- MODAL PREVIEW DOKUMEN --}}
@include('admin.archives.preview')

{{-- Real-time stats polling --}}
<script>
    (function() {
        const STATS_URL = '{{ route('dashboard.stats') }}';
        const INTERVAL_MS = 30000;

        function animateCounter(el, newVal) {
            const current = parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
            const target  = parseInt(newVal) || 0;
            if (current === target) return;
            const duration = 500, start = performance.now(), diff = target - current;
            function step(now) {
                const p = Math.min((now - start) / duration, 1);
                el.textContent = Math.round(current + diff * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        async function fetchStats() {
            try {
                const resp = await fetch(STATS_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                if (!resp.ok) return;
                const data = await resp.json();
                document.querySelectorAll('[data-stat-user]').forEach(el => {
                    const key = el.dataset.statUser;
                    if (data.stats && data.stats[key] !== undefined) animateCounter(el, data.stats[key]);
                });
            } catch (e) { /* offline */ }
        }

        setTimeout(fetchStats, 5000);
        setInterval(fetchStats, INTERVAL_MS);
    })();
</script>

</x-app-layout>