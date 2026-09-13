<x-app-layout>
    <x-slot name="header">Manajemen Kepegawaian</x-slot>

    {{-- ======================================================= --}}
    {{-- STYLES TAMBAHAN --}}
    {{-- ======================================================= --}}
    <style>
        .kpi-card { transition: all .2s ease; }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px -6px rgba(99,102,241,.25); }
        .badge-aktif        { background:#d1fae5; color:#065f46; }
        .badge-cuti         { background:#fef3c7; color:#92400e; }
        .badge-tugas-belajar{ background:#dbeafe; color:#1e40af; }
        .badge-non-aktif    { background:#f1f5f9; color:#475569; }
        .badge-asn          { background:#e0e7ff; color:#3730a3; }
        .badge-p3k-penuh    { background:#ede9fe; color:#5b21b6; }
        .badge-p3k-paruh    { background:#f3e8ff; color:#6b21a8; }
        .badge-bsn          { background:#ffedd5; color:#9a3412; }
        [x-cloak] { display: none !important; }
    </style>

    {{-- ======================================================= --}}
    {{-- KPI SUMMARY CARDS --}}
    {{-- ======================================================= --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        {{-- Total --}}
        <div class="kpi-card col-span-2 sm:col-span-1 lg:col-span-1 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-xs font-semibold uppercase tracking-wider opacity-80">Total Pegawai</p>
            <p class="text-4xl font-extrabold mt-1">{{ number_format($summary['total']) }}</p>
            <p class="text-xs opacity-70 mt-1">Semua Kategori</p>
        </div>
        {{-- ASN PNS --}}
        <div class="kpi-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">ASN (PNS)</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $summary['asn_pns'] }}</p>
        </div>
        {{-- P3K Penuh --}}
        <div class="kpi-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">P3K Penuh</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $summary['p3k_penuh'] }}</p>
        </div>
        {{-- P3K Paruh --}}
        <div class="kpi-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">P3K Paruh</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $summary['p3k_paruh'] }}</p>
        </div>
        {{-- BSN --}}
        <div class="kpi-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">BSN / Honorer</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $summary['bsn'] }}</p>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- STATUS DISTRIBUSI BAR --}}
    {{-- ======================================================= --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 mb-6">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Distribusi Status Keaktifan</p>
        <div class="flex flex-wrap gap-4">
            @foreach ($statusDistribusi as $st => $jml)
                @php
                    $pct = $summary['total'] > 0 ? round(($jml / $summary['total']) * 100) : 0;
                    $cls = match($st) {
                        'Aktif'         => 'bg-emerald-500',
                        'Cuti'          => 'bg-amber-400',
                        'Tugas Belajar' => 'bg-blue-500',
                        default         => 'bg-slate-400',
                    };
                @endphp
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full {{ $cls }}"></span>
                    <span class="font-semibold text-slate-700">{{ $st }}</span>
                    <span class="text-slate-400 text-xs">({{ $jml }} / {{ $pct }}%)</span>
                </div>
            @endforeach
        </div>
        @if ($summary['total'] > 0)
        <div class="mt-3 flex rounded-full overflow-hidden h-2.5 gap-0.5">
            @foreach ($statusDistribusi as $st => $jml)
                @php $pct = round(($jml / $summary['total']) * 100); $cls = match($st) { 'Aktif' => 'bg-emerald-500', 'Cuti' => 'bg-amber-400', 'Tugas Belajar' => 'bg-blue-500', default => 'bg-slate-300' }; @endphp
                @if ($pct > 0)
                <div class="{{ $cls }} transition-all duration-500" style="width:{{ $pct }}%"></div>
                @endif
            @endforeach
        </div>
        @endif
    </div>

    {{-- ======================================================= --}}
    {{-- SCOPE BADGE — tampil jika user terbatas ke 1 bidang --}}
    {{-- ======================================================= --}}
    @if($scopedDepartment)
    <div class="flex items-center gap-3 mb-4 px-4 py-3 bg-indigo-50 border border-indigo-200 rounded-2xl">
        <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-indigo-800">Akses Terbatas — Bidang: {{ $scopedDepartment->name }}</p>
            <p class="text-xs text-indigo-600 mt-0.5">Anda hanya dapat melihat data pegawai dari bidang Anda. Hubungi Super Admin untuk akses penuh.</p>
        </div>
    </div>
    @endif

    {{-- ======================================================= --}}
    {{-- FILTER & SEARCH BAR --}}
    {{-- ======================================================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-6">
        <form method="GET" action="{{ route('kepegawaian.index') }}" id="filterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                {{-- Global Search --}}
                <div class="lg:col-span-2 relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Cari NIK, NIP, Nama, Jabatan..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50">
                </div>
                {{-- Filter Kategori --}}
                <select name="kategori" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl py-2.5 px-3 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach (\App\Models\Pegawai::kategoriList() as $kat)
                        <option value="{{ $kat }}" {{ ($filters['kategori'] ?? '') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                {{-- Filter Status --}}
                <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl py-2.5 px-3 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    @foreach (\App\Models\Pegawai::statusList() as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Unit Kerja — hanya tampil untuk Super Admin --}}
                @if(auth()->user()->isPureSuperAdmin())
                <select name="unit_kerja" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl py-2 px-3 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Unit Kerja</option>
                    @foreach ($unitKerjaList as $uk)
                        <option value="{{ $uk }}" {{ ($filters['unit_kerja'] ?? '') === $uk ? 'selected' : '' }}>{{ $uk }}</option>
                    @endforeach
                </select>
                @else
                {{-- Untuk admin bidang: tampilkan label saja, filter unit_kerja disembunyikan --}}
                <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                    Bidang: {{ auth()->user()->department->name ?? 'Tidak Diketahui' }}
                </span>
                @endif
                {{-- TMT Dari --}}
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <span class="font-medium">TMT:</span>
                    <input type="date" name="tmt_dari" value="{{ $filters['tmt_dari'] ?? '' }}" onchange="this.form.submit()"
                           class="border border-slate-200 rounded-xl py-2 px-3 text-sm bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                    <span>–</span>
                    <input type="date" name="tmt_sampai" value="{{ $filters['tmt_sampai'] ?? '' }}" onchange="this.form.submit()"
                           class="border border-slate-200 rounded-xl py-2 px-3 text-sm bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                </div>
                {{-- Tombol Search & Reset --}}
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Cari
                </button>
                @if (array_filter($filters))
                    <a href="{{ route('kepegawaian.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition-colors">
                        Reset
                    </a>
                @endif

                {{-- EXPORT BUTTONS --}}
                <div class="ml-auto flex items-center gap-2" x-data="{ showQuick: false }">
                    {{-- Export aktif filter --}}
                    <a href="{{ route('kepegawaian.export.excel', $filters) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                    <a href="{{ route('kepegawaian.export.pdf', $filters) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    {{-- Quick Export Hub --}}
                    <div class="relative">
                        <button @click="showQuick = !showQuick" type="button"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Quick Export
                            <svg :class="showQuick ? 'rotate-180' : ''" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="showQuick" @click.outside="showQuick = false" x-cloak x-transition
                             class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2 mb-2">Download Per Kategori</p>
                            @foreach (['asn'=>'ASN (PNS)','p3k-penuh'=>'P3K Penuh Waktu','p3k-paruh'=>'P3K Paruh Waktu','bsn'=>'BSN / Non-ASN'] as $key => $label)
                            <div class="flex items-center justify-between px-2 py-2 hover:bg-slate-50 rounded-xl">
                                <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                                <div class="flex gap-1.5">
                                    <a href="{{ route('kepegawaian.quick.export', [$key, 'excel']) }}" class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-200 transition">XLS</a>
                                    <a href="{{ route('kepegawaian.quick.export', [$key, 'pdf']) }}" class="px-2.5 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded-lg hover:bg-rose-200 transition">PDF</a>
                                </div>
                            </div>
                            @endforeach
                            <div class="border-t border-slate-100 mt-2 pt-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2 mb-2">Download Per Status</p>
                                @foreach (['aktif'=>'Status Aktif','non-aktif'=>'Status Non-Aktif','semua'=>'Semua Pegawai'] as $key => $label)
                                <div class="flex items-center justify-between px-2 py-2 hover:bg-slate-50 rounded-xl">
                                    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                                    <div class="flex gap-1.5">
                                        <a href="{{ route('kepegawaian.quick.export', [$key, 'excel']) }}" class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-200 transition">XLS</a>
                                        <a href="{{ route('kepegawaian.quick.export', [$key, 'pdf']) }}" class="px-2.5 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded-lg hover:bg-rose-200 transition">PDF</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================================================= --}}
    {{-- TABEL PEGAWAI --}}
    {{-- ======================================================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-bold text-slate-700">Daftar Pegawai
                    @if($scopedDepartment)
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold">{{ $scopedDepartment->name }}</span>
                    @endif
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Total {{ $pegawais->total() }} data ditemukan</p>
            </div>
            @if(auth()->user()->isPureSuperAdmin() || auth()->user()->isAdmin())
            <div class="flex items-center gap-2">
                <button onclick="document.getElementById('modalImportExcel').classList.remove('hidden')"
                        style="background-color: #0d9488; color: #ffffff;"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import Excel</span>
                </button>
                <button onclick="document.getElementById('modalTambahSekaligus').classList.remove('hidden')"
                        style="background-color: #059669; color: #ffffff;"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                    <span>Tambah Sekaligus</span>
                </button>
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                        style="background-color: #4f46e5; color: #ffffff;"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Single</span>
                </button>
            </div>
            @endif
        </div>

         @if ($pegawais->isEmpty())
            {{-- EMPTY STATE --}}
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-700 mb-1">Belum Ada Data Pegawai</h3>
                <p class="text-sm text-slate-400 max-w-md">Data pegawai masih kosong atau tidak cocok dengan parameter pencarian. Anda dapat mengimpor data langsung dari file Excel atau menambahkan secara manual.</p>
                
                @if (array_filter($filters))
                    <a href="{{ route('kepegawaian.index') }}" class="mt-4 px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                        Reset Filter
                    </a>
                @else
                    @if(auth()->user()->isPureSuperAdmin() || auth()->user()->isAdmin())
                    <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                        <button onclick="document.getElementById('modalImportExcel').classList.remove('hidden')"
                                style="background-color: #0d9488; color: #ffffff;"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Import Data dari Excel</span>
                        </button>
                        <button onclick="document.getElementById('modalTambahSekaligus').classList.remove('hidden')"
                                style="background-color: #059669; color: #ffffff;"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                            <span>Tambah Sekaligus (Massal)</span>
                        </button>
                        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                                style="background-color: #4f46e5; color: #ffffff;"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Single</span>
                        </button>
                    </div>
                    @endif
                @endif
            </div>
        @else
            {{-- SCROLLABLE TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3 text-left sticky left-0 bg-slate-50 z-10">No.</th>
                            <th class="px-5 py-3 text-left sticky left-10 bg-slate-50 z-10 min-w-[200px]">Nama Pegawai</th>
                            <th class="px-5 py-3 text-left">Kategori</th>
                            <th class="px-5 py-3 text-left min-w-[180px]">Jabatan</th>
                            <th class="px-5 py-3 text-left min-w-[160px]">Unit Kerja</th>
                            <th class="px-5 py-3 text-left">Gol.</th>
                            <th class="px-5 py-3 text-left">TMT SK</th>
                            <th class="px-5 py-3 text-left">Masa Kontrak</th>
                            <th class="px-5 py-3 text-center">Jam/Minggu</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pegawais as $i => $p)
                        @php
                            $katBadge = match($p->kategori) {
                                'ASN (PNS)'       => 'badge-asn',
                                'P3K Penuh Waktu' => 'badge-p3k-penuh',
                                'P3K Paruh Waktu' => 'badge-p3k-paruh',
                                default           => 'badge-bsn',
                            };
                            $stBadge = match($p->status) {
                                'Aktif'         => 'badge-aktif',
                                'Cuti'          => 'badge-cuti',
                                'Tugas Belajar' => 'badge-tugas-belajar',
                                default         => 'badge-non-aktif',
                            };
                        @endphp
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-5 py-4 text-slate-400 text-xs font-bold sticky left-0 bg-white group-hover:bg-indigo-50/30">
                                {{ $pegawais->firstItem() + $loop->index }}
                            </td>
                            <td class="px-5 py-4 sticky left-10 bg-white group-hover:bg-indigo-50/30 z-10">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $p->jenis_kelamin === 'L' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center font-bold text-sm {{ $p->jenis_kelamin === 'L' ? 'text-blue-600' : 'text-pink-600' }} flex-shrink-0">
                                        {{ mb_substr($p->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm leading-tight">{{ $p->nama_lengkap }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5 font-mono">{{ $p->nik }}</div>
                                        @if($p->nip_nrk)
                                        <div class="text-xs text-slate-400 font-mono">{{ $p->nip_nrk }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $katBadge }}">
                                    {{ $p->kategori }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-700 font-medium">{{ $p->jabatan }}</td>
                            <td class="px-5 py-4 text-sm text-slate-500">{{ $p->unit_kerja }}</td>
                            <td class="px-5 py-4 text-xs text-slate-500 font-mono whitespace-nowrap">{{ $p->pangkat_golongan ?? '—' }}</td>
                            <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $p->tmt_sk ? $p->tmt_sk->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">{{ $p->masa_kontrak ?? '—' }}</td>
                            <td class="px-5 py-4 text-center text-xs font-bold text-slate-600">{{ $p->jam_kerja_mingguan }} jam</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $stBadge }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if(auth()->user()->isPureSuperAdmin() || auth()->user()->isAdmin())
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Edit --}}
                                    <button type="button" 
                                            data-pegawai="{{ $p->toJson() }}"
                                            onclick="openEditModal(JSON.parse(this.dataset.pegawai))"
                                            class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    {{-- Hapus --}}
                                    <button type="button" onclick="openDeleteModal({{ $p->id }}, '{{ addslashes($p->nama_lengkap) }}')"
                                            class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                @else
                                <span class="text-slate-400 text-xs font-semibold">Lihat Saja</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $pegawais->links() }}
            </div>
        @endif
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL IMPORT EXCEL PEGAWAI --}}
    {{-- ======================================================= --}}
    <div id="modalImportExcel" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modalImportExcel').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            
            {{-- Header Modal --}}
            <div class="bg-gradient-to-r from-teal-600 to-emerald-700 px-6 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-white/15 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold">Import Data Pegawai dari Excel</h3>
                        <p class="text-xs text-emerald-100 mt-0.5">Mendukung format Multi-Sheet BKN/DLH & format standar single-sheet</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalImportExcel').classList.add('hidden')"
                        class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('kepegawaian.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                @if ($errors->has('import_error'))
                    <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $errors->first('import_error') }}</span>
                    </div>
                @endif

                {{-- KARTU PINTAS FILE SERVER (Jika PNS LENGKAP 2026.xlsx ada di server) --}}
                @if (file_exists(base_path('PNS LENGKAP 2026.xlsx')))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-emerald-900">Berkas Siap Impor Ditemukan di Server!</h4>
                                <p class="text-xs text-emerald-700 mt-0.5">File <strong>PNS LENGKAP 2026.xlsx</strong> (444 pegawai multi-sheet) siap diproses langsung ke database.</p>
                                <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                                    <input type="checkbox" name="server_file" value="PNS LENGKAP 2026.xlsx" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                    <span class="text-xs font-bold text-emerald-800">Gunakan berkas server ini (PNS LENGKAP 2026.xlsx)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Area Upload File --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Berkas Excel (.xlsx, .xls, .csv)</label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-6 text-center transition-colors bg-slate-50/50 hover:bg-emerald-50/20">
                        <input type="file" name="file_excel" id="fileExcelInput" accept=".xlsx,.xls,.csv" class="hidden" onchange="document.getElementById('fileNameDisplay').textContent = this.files[0] ? this.files[0].name : 'Belum ada file dipilih'">
                        <label for="fileExcelInput" class="cursor-pointer flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-700">Klik untuk memilih berkas Excel</span>
                            <span class="text-xs text-slate-400 mt-1">Maksimal ukuran berkas 15 MB</span>
                            <span id="fileNameDisplay" class="mt-3 inline-block px-3 py-1 bg-white border border-slate-200 rounded-lg text-xs font-mono text-emerald-700 font-semibold shadow-sm">Belum ada file dipilih</span>
                        </label>
                    </div>
                </div>

                {{-- Opsi Duplikasi --}}
                <div class="mb-5 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Jika NIP / NIK Sudah Terdaftar di Sistem:</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                            <input type="radio" name="duplicate_strategy" value="update" checked class="text-emerald-600 focus:ring-emerald-500">
                            <span>Perbarui (Update) data pegawai lama</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                            <input type="radio" name="duplicate_strategy" value="skip" class="text-emerald-600 focus:ring-emerald-500">
                            <span>Lewati (Skip) data yang sudah ada</span>
                        </label>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <a href="{{ route('kepegawaian.template') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Download Template Excel</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('modalImportExcel').classList.add('hidden')"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow-sm hover:shadow flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Mulai Impor Data</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL TAMBAH SEKALIGUS (BULK PEGAWAI SPREADSHEET) --}}
    {{-- ======================================================= --}}
    <div id="modalTambahSekaligus" class="hidden fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4"
         x-data="pegawaiBulkForm({{ json_encode($unitKerjaList) }})" x-cloak>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modalTambahSekaligus').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] max-h-[92vh] flex flex-col overflow-hidden border border-slate-100">
            {{-- Header Modal --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white px-6 py-4 flex items-center justify-between shadow-md">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/10 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Input Data Pegawai Sekaligus (Massal)</h3>
                        <p class="text-xs text-emerald-100 mt-0.5">Tambah beberapa pegawai dalam bentuk tabel dinamis (spreadsheet)</p>
                    </div>
                </div>
                <button onclick="document.getElementById('modalTambahSekaligus').classList.add('hidden')" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Bulk Errors Alert --}}
            @if ($errors->has('bulk_error') || $errors->has('pegawai.*'))
            <div class="bg-rose-50 border-b border-rose-200 p-4 px-6 text-rose-700 text-xs font-semibold">
                <p class="font-bold mb-1">⚠️ Terdapat Kesalahan Input Data Massal:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @if($errors->has('bulk_error'))
                        <li>{{ $errors->first('bulk_error') }}</li>
                    @endif
                    @foreach($errors->get('pegawai.*') as $errList)
                        @foreach($errList as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('kepegawaian.storeBulk') }}" method="POST" class="flex-1 flex flex-col min-h-0">
                @csrf
                
                {{-- Toolbar Atas --}}
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-3 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="addRow()" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah 1 Baris
                        </button>
                        <button type="button" @click="add5Rows()" class="px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            + 5 Baris
                        </button>
                        <button type="button" @click="resetForm()" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition-all">
                            Reset Form
                        </button>
                    </div>

                    {{-- Global Unit Kerja Applicator --}}
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                        <span class="text-slate-500 font-medium">Set Unit Kerja ke Semua:</span>
                        <select x-model="selectedGlobalUnit" class="text-xs py-1 px-2 border border-slate-200 rounded-lg bg-slate-50 focus:ring-1 focus:ring-emerald-500">
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach ($unitKerjaList as $uk)
                                <option value="{{ $uk }}">{{ $uk }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="applyGlobalUnit()" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition-colors">
                            Terapkan
                        </button>
                    </div>

                    <div class="text-slate-500 font-bold bg-slate-200 px-3 py-1.5 rounded-xl">
                        Total: <span x-text="rows.length" class="text-emerald-700 font-extrabold"></span> Baris Pegawai
                    </div>
                </div>

                {{-- Container Tabel Dynamic --}}
                <div class="flex-1 overflow-auto p-4 bg-slate-100">
                    <table class="w-full text-xs border-collapse bg-white shadow-sm rounded-xl overflow-hidden min-w-[1350px]">
                        <thead>
                            <tr class="bg-slate-200 text-slate-700 uppercase font-extrabold text-[11px] tracking-wider border-b border-slate-300">
                                <th class="p-2 text-center w-10 border-r border-slate-300">#</th>
                                <th class="p-2 text-left min-w-[180px] border-r border-slate-300">Nama Lengkap <span class="text-rose-500">*</span></th>
                                <th class="p-2 text-left min-w-[150px] border-r border-slate-300">NIK (16 Digit) <span class="text-rose-500">*</span></th>
                                <th class="p-2 text-left min-w-[140px] border-r border-slate-300">Kategori <span class="text-rose-500">*</span></th>
                                <th class="p-2 text-left min-w-[160px] border-r border-slate-300">Jabatan <span class="text-rose-500">*</span></th>
                                <th class="p-2 text-left min-w-[170px] border-r border-slate-300">Unit Kerja <span class="text-rose-500">*</span></th>
                                <th class="p-2 text-center w-20 border-r border-slate-300">JK</th>
                                <th class="p-2 text-left min-w-[130px] border-r border-slate-300">NIP / NRK</th>
                                <th class="p-2 text-left min-w-[100px] border-r border-slate-300">Gol.</th>
                                <th class="p-2 text-left min-w-[120px] border-r border-slate-300">TMT SK</th>
                                <th class="p-2 text-center w-24 border-r border-slate-300">Jam/Mgg</th>
                                <th class="p-2 text-left min-w-[110px] border-r border-slate-300">Status</th>
                                <th class="p-2 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="hover:bg-amber-50/50 transition-colors border-b border-slate-200">
                                    <td class="p-2 text-center font-bold text-slate-500 border-r border-slate-200" x-text="index + 1"></td>
                                    
                                    {{-- Nama Lengkap --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="text" :name="`pegawai[${index}][nama_lengkap]`" x-model="row.nama_lengkap" required placeholder="Nama & Gelar..."
                                               class="w-full text-xs py-1.5 px-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>
                                    
                                    {{-- NIK --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="text" :name="`pegawai[${index}][nik]`" x-model="row.nik" maxlength="16" required placeholder="16 digit NIK..."
                                               class="w-full text-xs font-mono py-1.5 px-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>
                                    
                                    {{-- Kategori --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <select :name="`pegawai[${index}][kategori]`" x-model="row.kategori" required class="w-full text-xs py-1.5 px-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white font-semibold">
                                            @foreach (\App\Models\Pegawai::kategoriList() as $kat)
                                                <option value="{{ $kat }}">{{ $kat }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Jabatan --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="text" :name="`pegawai[${index}][jabatan]`" x-model="row.jabatan" required placeholder="Jabatan..."
                                               class="w-full text-xs py-1.5 px-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>

                                    {{-- Unit Kerja --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="text" :name="`pegawai[${index}][unit_kerja]`" x-model="row.unit_kerja" list="unitKerjaDataList" required placeholder="Pilih/ketik unit..."
                                               class="w-full text-xs py-1.5 px-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>

                                    {{-- Jenis Kelamin --}}
                                    <td class="p-1 border-r border-slate-200 text-center">
                                        <select :name="`pegawai[${index}][jenis_kelamin]`" x-model="row.jenis_kelamin" required class="w-full text-xs py-1.5 px-1 border border-slate-200 rounded-lg text-center font-bold bg-white">
                                            <option value="L">L</option>
                                            <option value="P">P</option>
                                        </select>
                                    </td>

                                    {{-- NIP / NRK --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="text" :name="`pegawai[${index}][nip_nrk]`" x-model="row.nip_nrk" placeholder="Opsional NIP..."
                                               class="w-full text-xs font-mono py-1.5 px-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>

                                    {{-- Pangkat / Golongan --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="text" :name="`pegawai[${index}][pangkat_golongan]`" x-model="row.pangkat_golongan" placeholder="Mis: IV/a..."
                                               class="w-full text-xs py-1.5 px-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>

                                    {{-- TMT SK --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="date" :name="`pegawai[${index}][tmt_sk]`" x-model="row.tmt_sk"
                                               class="w-full text-xs py-1.5 px-1 border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>

                                    {{-- Jam Kerja Mingguan --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <input type="number" step="0.5" :name="`pegawai[${index}][jam_kerja_mingguan]`" x-model="row.jam_kerja_mingguan" required placeholder="37.5"
                                               class="w-full text-xs py-1.5 px-1 border border-slate-200 rounded-lg text-center focus:ring-1 focus:ring-emerald-500 bg-white">
                                    </td>

                                    {{-- Status --}}
                                    <td class="p-1 border-r border-slate-200">
                                        <select :name="`pegawai[${index}][status]`" x-model="row.status" required class="w-full text-xs py-1.5 px-1 border border-slate-200 rounded-lg font-semibold bg-white">
                                            @foreach (\App\Models\Pegawai::statusList() as $st)
                                                <option value="{{ $st }}">{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Aksi (Duplicate & Delete) --}}
                                    <td class="p-1 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" @click="duplicateRow(index)" title="Salin Baris Ini" class="p-1 text-indigo-600 hover:bg-indigo-50 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                            <button type="button" @click="removeRow(index)" title="Hapus Baris Ini" class="p-1 text-rose-500 hover:bg-rose-50 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Datalist Unit Kerja --}}
                <datalist id="unitKerjaDataList">
                    @foreach ($unitKerjaList as $uk)
                        <option value="{{ $uk }}">
                    @endforeach
                </datalist>

                {{-- Footer Modal --}}
                <div class="bg-white border-t border-slate-200 px-6 py-4 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        <span class="font-bold text-rose-500">*</span> Kolom Wajib Diisi (Nama, NIK, Kategori, Jabatan, Unit Kerja)
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="document.getElementById('modalTambahSekaligus').classList.add('hidden')"
                                class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Semua Pegawai
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL TAMBAH PEGAWAI --}}
    {{-- ======================================================= --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalTambah').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white rounded-t-2xl flex items-center justify-between px-6 py-4 border-b border-slate-100 z-10">
                <div>
                    <h3 class="font-bold text-slate-800">Tambah Pegawai Baru</h3>
                    <p class="text-xs text-slate-400">Lengkapi seluruh data pegawai</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('kepegawaian.store') }}" method="POST" class="p-6">
                @csrf
                @include('admin.kepegawaian._form')
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
                        Simpan Pegawai
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL EDIT PEGAWAI --}}
    {{-- ======================================================= --}}
    <div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalEdit').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white rounded-t-2xl flex items-center justify-between px-6 py-4 border-b border-slate-100 z-10">
                <div>
                    <h3 class="font-bold text-slate-800">Edit Data Pegawai</h3>
                    <p class="text-xs text-slate-400" id="editSubtitle"></p>
                </div>
                <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="formEdit" action="" method="POST" class="p-6">
                @csrf @method('PUT')
                @include('admin.kepegawaian._form', ['isEdit' => true])
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL HAPUS PEGAWAI --}}
    {{-- ======================================================= --}}
    <div id="modalHapus" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalHapus').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus Data Pegawai?</h3>
                <p class="text-sm text-slate-500 mb-6">Anda yakin ingin menghapus <span id="hapusName" class="font-bold text-slate-700"></span>? Tindakan ini tidak dapat dibatalkan.</p>
                <form id="formHapus" action="" method="POST" class="flex items-center justify-center gap-3">
                    @csrf @method('DELETE')
                    <button type="button" onclick="document.getElementById('modalHapus').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors w-full">Batal</button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors w-full">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function pegawaiBulkForm(unitKerjaOptions) {
        return {
            unitKerjaOptions: unitKerjaOptions || [],
            selectedGlobalUnit: '',
            rows: [
                { nama_lengkap: '', nik: '', kategori: 'ASN (PNS)', nip_nrk: '', jabatan: '', unit_kerja: '', jenis_kelamin: 'L', pangkat_golongan: '', tmt_sk: '', masa_kontrak: '', jam_kerja_mingguan: 37.5, status: 'Aktif' },
                { nama_lengkap: '', nik: '', kategori: 'ASN (PNS)', nip_nrk: '', jabatan: '', unit_kerja: '', jenis_kelamin: 'L', pangkat_golongan: '', tmt_sk: '', masa_kontrak: '', jam_kerja_mingguan: 37.5, status: 'Aktif' },
                { nama_lengkap: '', nik: '', kategori: 'ASN (PNS)', nip_nrk: '', jabatan: '', unit_kerja: '', jenis_kelamin: 'L', pangkat_golongan: '', tmt_sk: '', masa_kontrak: '', jam_kerja_mingguan: 37.5, status: 'Aktif' }
            ],
            createEmptyRow() {
                return {
                    nama_lengkap: '',
                    nik: '',
                    kategori: 'ASN (PNS)',
                    nip_nrk: '',
                    jabatan: '',
                    unit_kerja: '',
                    jenis_kelamin: 'L',
                    pangkat_golongan: '',
                    tmt_sk: '',
                    masa_kontrak: '',
                    jam_kerja_mingguan: 37.5,
                    status: 'Aktif'
                };
            },
            addRow() {
                this.rows.push(this.createEmptyRow());
            },
            add5Rows() {
                for (let i = 0; i < 5; i++) {
                    this.rows.push(this.createEmptyRow());
                }
            },
            duplicateRow(index) {
                const rowCopy = JSON.parse(JSON.stringify(this.rows[index]));
                rowCopy.nik = '';
                this.rows.splice(index + 1, 0, rowCopy);
            },
            removeRow(index) {
                if (this.rows.length <= 1) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Batas Minimum',
                        text: 'Minimal harus ada 1 baris pegawai dalam form!',
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'Mengerti',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl border border-slate-100',
                            confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs shadow-md'
                        }
                    });
                    return;
                }
                this.rows.splice(index, 1);
            },
            applyGlobalUnit() {
                if (!this.selectedGlobalUnit) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Unit Kerja Belum Dipilih',
                        text: 'Silakan pilih unit kerja terlebih dahulu pada dropdown sebelum menerapkan!',
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'Mengerti',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl border border-slate-100',
                            confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs shadow-md'
                        }
                    });
                    return;
                }
                this.rows.forEach(r => {
                    r.unit_kerja = this.selectedGlobalUnit;
                });
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diterapkan',
                    text: `Unit Kerja "${this.selectedGlobalUnit}" telah diterapkan ke seluruh baris.`,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl shadow-2xl' }
                });
            },
            resetForm() {
                Swal.fire({
                    title: 'Reset Isian Form?',
                    text: 'Seluruh isian data yang sudah Anda ketik di tabel ini akan dikosongkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Reset Form',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-100',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs shadow-md',
                        cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.rows = [this.createEmptyRow(), this.createEmptyRow(), this.createEmptyRow()];
                    }
                });
            }
        };
    }

    function openEditModal(p) {
        const form = document.getElementById('formEdit');
        form.action = `/admin/kepegawaian/${p.id}`;
        document.getElementById('editSubtitle').textContent = p.nama_lengkap;

        const f = (name, val) => {
            const el = form.querySelector(`[name="${name}"]`);
            if (el) el.value = val ?? '';
        };

        f('nik', p.nik);
        f('nip_nrk', p.nip_nrk);
        f('nama_lengkap', p.nama_lengkap);
        f('jenis_kelamin', p.jenis_kelamin);
        f('kategori', p.kategori);
        f('pangkat_golongan', p.pangkat_golongan);
        f('jabatan', p.jabatan);
        f('unit_kerja', p.unit_kerja);
        f('tmt_sk', p.tmt_sk ? p.tmt_sk.split('T')[0] : '');
        f('masa_kontrak', p.masa_kontrak);
        f('jam_kerja_mingguan', p.jam_kerja_mingguan);
        f('pendidikan_terakhir', p.pendidikan_terakhir);
        f('no_hp', p.no_hp);
        f('email', p.email);
        f('status', p.status);

        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function openDeleteModal(id, name) {
        document.getElementById('formHapus').action = `/admin/kepegawaian/${id}`;
        document.getElementById('hapusName').textContent = name;
        document.getElementById('modalHapus').classList.remove('hidden');
    }

    // Buka otomatis modal yang sesuai jika terdapat error validasi
    @if ($errors->has('import_error'))
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('modalImportExcel').classList.remove('hidden');
        });
    @elseif ($errors->has('bulk_error') || $errors->has('pegawai.*'))
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('modalTambahSekaligus').classList.remove('hidden');
        });
    @elseif ($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('modalTambah').classList.remove('hidden');
        });
    @endif
    </script>
    @endpush

</x-app-layout>
