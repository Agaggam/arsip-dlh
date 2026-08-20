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
                {{-- Unit Kerja --}}
                <select name="unit_kerja" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl py-2 px-3 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Unit Kerja</option>
                    @foreach ($unitKerjaList as $uk)
                        <option value="{{ $uk }}" {{ ($filters['unit_kerja'] ?? '') === $uk ? 'selected' : '' }}>{{ $uk }}</option>
                    @endforeach
                </select>
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
                <h2 class="text-sm font-bold text-slate-700">Daftar Pegawai</h2>
                <p class="text-xs text-slate-400 mt-0.5">Total {{ $pegawais->total() }} data ditemukan</p>
            </div>
            @if(auth()->user()->isPureSuperAdmin())
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pegawai
            </button>
            @endif
        </div>

         @if ($pegawais->isEmpty())
            {{-- EMPTY STATE --}}
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-600 mb-1">Tidak Ada Data Pegawai</h3>
                <p class="text-sm text-slate-400 max-w-sm">Tidak ada data yang cocok dengan kriteria filter Anda. Coba ubah parameter pencarian atau reset filter.</p>
                @if (array_filter($filters))
                    <a href="{{ route('kepegawaian.index') }}" class="mt-4 px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                        Reset Filter
                    </a>
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
                                @if(auth()->user()->isPureSuperAdmin())
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

    // Buka otomatis modal tambah jika terdapat error validasi
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('modalTambah').classList.remove('hidden');
        });
    @endif
    </script>
    @endpush

</x-app-layout>
