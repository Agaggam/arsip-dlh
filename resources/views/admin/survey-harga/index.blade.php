<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Usulan Standar Harga (SSH/SBU)') }}
            </h2>
        </div>
    </x-slot>

    {{-- GRID KPI & AKSI KONSISTEN SEPERTI DATA ARSIP --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- CARD 1: Total Usulan --}}
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Usulan Komponen</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($summary['total']) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-600 font-semibold">{{ $summary['ssh'] }} SSH &bull; {{ $summary['sbu'] }} SBU &bull; {{ $summary['hspk'] }} HSPK/ASB</span>
                </div>
            </div>
        </div>

        {{-- CARD 2: Total Nilai Estimasi Usulan --}}
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 pr-2">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Estimasi Nilai Total</p>
                        <p class="text-2xl font-extrabold text-indigo-600 mt-2 truncate">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-indigo-600 font-medium">Akumulasi Harga Usulan Survei</span>
                </div>
            </div>
        </div>

        {{-- CARD 3 & 4 (DIGABUNG): AKSI USULAN STANDAR HARGA --}}
        <div class="md:col-span-2 relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10 h-full flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                
                {{-- Bagian Teks Info Kiri --}}
                <div class="max-w-md">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi Usulan Standar Harga</p>
                    <p class="text-xs text-gray-400 mt-1">Gunakan tombol aksi di samping untuk mengekspor rekapitulasi data usulan harga pasar atau menambahkan usulan survei baru.</p>
                </div>
                
                {{-- Bagian Tombol Aksi Kanan --}}
                <div class="flex items-center gap-2 flex-wrap flex-1 w-full md:w-auto md:justify-end">
                    {{-- TOMBOL EKSPOR EXCEL --}}
                    <a href="{{ route('survey-harga.export-excel', request()->query()) }}" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold py-2.5 px-3 rounded-xl shadow-2xs transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <span>Excel Rekap</span>
                    </a>

                    @if(!auth()->user()->isUser())
                    {{-- Tombol Unggah / Buat Baru --}}
                    <a href="{{ route('survey-harga.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 px-3.5 rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ Buat Usulan Baru</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <form method="GET" action="{{ route('survey-harga.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Cari Usulan</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Judul, Kode Komponen..."
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Kelompok</label>
                    <select name="kelompok" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Kelompok</option>
                        @foreach(['SSH', 'SBU', 'HSPK', 'ASB'] as $k)
                        <option value="{{ $k }}" {{ ($filters['kelompok'] ?? '') === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                @if(auth()->user()->isPureSuperAdmin())
                <div class="min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Departemen</label>
                    <select name="department_id" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ ($filters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">Filter</button>
                <a href="{{ route('survey-harga.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul & Kode Komponen</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kelompok</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Harga Usulan</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Departemen</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi & Unduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($surveys as $survey)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-4 align-middle">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="font-bold text-slate-800 truncate max-w-[240px]" title="{{ $survey->judul }}">{{ $survey->judul }}</p>
                                    <span class="text-[10px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded whitespace-nowrap">{{ $survey->kode_komponen ?? 'Tanpa Kode' }}</span>
                                </div>
                                @if($survey->spesifikasi_singkat)
                                <p class="text-[11px] text-slate-400 truncate max-w-[300px]" title="{{ $survey->spesifikasi_singkat }}">{{ $survey->spesifikasi_singkat }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-middle">
                                @php
                                    $kColor = ['SSH' => 'indigo', 'SBU' => 'violet', 'HSPK' => 'amber', 'ASB' => 'teal'];
                                    $c = $kColor[$survey->kelompok] ?? 'slate';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $c }}-100 text-{{ $c }}-700">{{ $survey->kelompok }}</span>
                            </td>
                            <td class="px-4 py-4 align-middle whitespace-nowrap font-extrabold text-slate-800">{{ $survey->harga_usulan_format }}</td>
                            <td class="px-4 py-4 align-middle text-slate-600 text-xs font-medium">{{ $survey->department->name ?? '-' }}</td>
                            <td class="px-4 py-4 align-middle text-slate-500 text-xs font-medium whitespace-nowrap">{{ $survey->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-4 align-middle text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    {{-- CUTE BUTTON XLS --}}
                                    <a href="{{ route('survey-harga.export-excel-single', $survey) }}"
                                       class="bg-emerald-100/80 hover:bg-emerald-200 text-emerald-800 font-extrabold text-[10px] px-2.5 py-1 rounded-lg transition-all shadow-2xs border border-emerald-300/40" 
                                       title="Unduh Excel Usulan Ini">
                                        XLS
                                    </a>

                                    {{-- CUTE BUTTON PDF --}}
                                    <a href="{{ route('survey-harga.export-pdf', $survey) }}" target="_blank"
                                       class="bg-rose-100/80 hover:bg-rose-200 text-rose-800 font-extrabold text-[10px] px-2.5 py-1 rounded-lg transition-all shadow-2xs border border-rose-300/40" 
                                       title="Unduh PDF Berita Acara Survei">
                                        PDF
                                    </a>

                                    @if(!auth()->user()->isUser())
                                    {{-- Edit --}}
                                    <a href="{{ route('survey-harga.edit', $survey) }}"
                                       class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Usulan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('survey-harga.destroy', $survey) }}" onsubmit="return confirm('Yakin ingin menghapus usulan ini?')" class="m-0 inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Usulan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium">Belum ada usulan harga</p>
                                <a href="{{ route('survey-harga.create') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-600 hover:underline">+ Buat Usulan Baru</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($surveys->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $surveys->links() }}</div>
            @endif
        </div>
</x-app-layout>
