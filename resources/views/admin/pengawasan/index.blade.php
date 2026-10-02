<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Pengawasan Pelaku Usaha') }}
            </h2>
        </div>
    </x-slot>

    {{-- GRID KPI & AKSI KONSISTEN SEPERTI DATA ARSIP --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- CARD 1: Total Sesi --}}
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Sesi Pengawasan</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($summary['total_sesi']) }} <span class="text-xs font-semibold text-slate-400">Sesi</span></p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-600 font-medium">Sesi Ketaatan Lingkungan Hidup</span>
                </div>
            </div>
        </div>

        {{-- CARD 2: Total Pelaku Usaha & Sebaran Wilayah --}}
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Usaha Diawasi</p>
                        <p class="text-3xl font-extrabold text-cyan-600 mt-2">{{ number_format($summary['total_usaha']) }} <span class="text-xs font-semibold text-slate-400">Usaha</span></p>
                    </div>
                    <div class="p-3 rounded-xl bg-cyan-100 text-cyan-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-cyan-600 font-semibold">Batu: {{ $summary['batu'] }} &bull; Bmj: {{ $summary['bumiaji'] }} &bull; Jnr: {{ $summary['junrejo'] }}</span>
                </div>
            </div>
        </div>

        {{-- CARD 3 & 4 (DIGABUNG): AKSI LAPORAN PENGAWASAN --}}
        <div class="md:col-span-2 relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10 h-full flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                
                {{-- Bagian Teks Info Kiri --}}
                <div class="max-w-md">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi Laporan Pengawasan</p>
                    <p class="text-xs text-gray-400 mt-1">Gunakan tombol aksi di samping untuk mengekspor rekapitulasi data pengawasan atau menambahkan sesi pengawasan baru.</p>
                </div>
                
                {{-- Bagian Tombol Aksi Kanan --}}
                <div class="flex items-center gap-2 flex-wrap flex-1 w-full md:w-auto md:justify-end">
                    {{-- TOMBOL EKSPOR PDF --}}
                    <a href="{{ route('pengawasan.export-pdf', request()->query()) }}" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold py-2.5 px-3 rounded-xl shadow-2xs transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>PDF Laporan</span>
                    </a>

                    {{-- TOMBOL EKSPOR EXCEL --}}
                    <a href="{{ route('pengawasan.export-excel', request()->query()) }}" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold py-2.5 px-3 rounded-xl shadow-2xs transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <span>Excel Laporan</span>
                    </a>

                    @if(!auth()->user()->isUser())
                    {{-- Tombol Tambah Sesi Pengawasan --}}
                    <a href="{{ route('pengawasan.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 px-3.5 rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ Tambah Sesi</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

        {{-- FILTER BAR --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <form method="GET" action="{{ route('pengawasan.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Cari Sesi / Usaha / Pengawas</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama pengawas, usaha, atau wilayah..."
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Kecamatan</label>
                    <select name="kecamatan" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Kecamatan</option>
                        @foreach(\App\Models\Pengawasan::KECAMATAN_OPTIONS as $kec)
                        <option value="{{ $kec }}" {{ ($filters['kecamatan'] ?? '') === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Jenis</label>
                    <select name="jenis_pengawasan" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua</option>
                        <option value="langsung" {{ ($filters['jenis_pengawasan'] ?? '') === 'langsung' ? 'selected' : '' }}>Langsung</option>
                        <option value="tidak_langsung" {{ ($filters['jenis_pengawasan'] ?? '') === 'tidak_langsung' ? 'selected' : '' }}>Tidak Langsung</option>
                    </select>
                </div>
                <div class="min-w-[100px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Tahun</label>
                    <select name="tahun" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua</option>
                        @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ ($filters['tahun'] ?? '') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                        @if($tahunList->isEmpty())
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                        @endif
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">Filter</button>
                <a href="{{ route('pengawasan.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
            </form>
        </div>

        {{-- TABLE UNIVERSAL SESI PENGAWASAN --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi Pengawasan & Pengawas</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Usaha</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kecamatan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-slate-500 uppercase" title="Total Akumulasi V/P/X Sesi Ini">Total V/P/X</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi & Unduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sessions as $i => $session)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- No --}}
                            <td class="px-4 py-3.5 align-middle text-slate-600 font-medium">{{ $paginatedBatches->firstItem() + $i }}</td>

                            {{-- Sesi Pengawasan & Pengawas --}}
                            <td class="px-4 py-3.5 align-middle">
                                <p class="font-bold text-slate-800 text-sm">
                                    Pengawasan {{ $session->jenis_label }} ({{ $session->kecamatan }})
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <span class="font-medium text-slate-600">{{ $session->nama_pengawas }}</span>
                                    <span>•</span>
                                    <span class="text-slate-400 font-semibold">Tahun {{ $session->tahun }}</span>
                                </p>
                            </td>

                            {{-- Daftar Usaha --}}
                            <td class="px-4 py-3.5 align-middle">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100/60">
                                    {{ $session->total_usaha }} Pelaku Usaha
                                </span>
                                <p class="text-[11px] text-slate-400 mt-1 truncate max-w-[200px]" title="{{ $session->nama_usaha_list->implode(', ') }}">
                                    {{ $session->nama_usaha_list->take(2)->implode(', ') }}{{ $session->total_usaha > 2 ? ' + ' . ($session->total_usaha - 2) . ' lainnya' : '' }}
                                </p>
                            </td>

                            {{-- Kecamatan --}}
                            <td class="px-4 py-3.5 align-middle text-slate-700 text-xs font-semibold whitespace-nowrap">
                                {{ str_replace('Kecamatan ', '', $session->kecamatan) }}
                            </td>

                            {{-- Jenis --}}
                            <td class="px-4 py-3.5 align-middle">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold {{ $session->jenis_pengawasan === 'langsung' ? 'bg-teal-100 text-teal-700' : 'bg-violet-100 text-violet-700' }}">
                                    {{ $session->jenis_label }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3.5 align-middle text-slate-500 text-xs font-medium whitespace-nowrap">
                                {{ $session->waktu_pengawasan ? \Carbon\Carbon::parse($session->waktu_pengawasan)->format('d/m/Y') : '-' }}
                            </td>

                            {{-- Total V/P/X --}}
                            <td class="px-3 py-3.5 align-middle text-center whitespace-nowrap">
                                <span class="text-emerald-600 font-bold">{{ $session->jml_v }}</span>/<span class="text-amber-600 font-bold">{{ $session->jml_p }}</span>/<span class="text-red-600 font-bold">{{ $session->jml_x }}</span>
                            </td>

                            {{-- Aksi & Unduh --}}
                            <td class="px-4 py-3.5 align-middle text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    {{-- CUTE BUTTON XLS --}}
                                    <a href="{{ route('pengawasan.export-excel-single', $session->first_item) }}"
                                       class="bg-emerald-100/80 hover:bg-emerald-200 text-emerald-800 font-extrabold text-[10px] px-2.5 py-1 rounded-lg transition-all shadow-2xs border border-emerald-300/40" 
                                       title="Unduh Excel Laporan Sesi Ini (Semua Usaha)">
                                        XLS
                                    </a>

                                    {{-- CUTE BUTTON PDF --}}
                                    <a href="{{ route('pengawasan.export-pdf-single', $session->first_item) }}"
                                       class="bg-rose-100/80 hover:bg-rose-200 text-rose-800 font-extrabold text-[10px] px-2.5 py-1 rounded-lg transition-all shadow-2xs border border-rose-300/40" 
                                       title="Unduh PDF Laporan Sesi Ini (Semua Usaha)">
                                        PDF
                                    </a>

                                    @if(!auth()->user()->isUser())
                                    {{-- Edit Sesi --}}
                                    <a href="{{ route('pengawasan.edit', $session->first_item) }}"
                                       class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Sesi & Tambah Usaha Baru">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    {{-- Hapus Sesi --}}
                                    <form method="POST" action="{{ route('pengawasan.destroy', $session->first_item) }}" onsubmit="return confirm('Yakin ingin menghapus seluruh data pada sesi pengawasan ini ({{ $session->total_usaha }} usaha)?')" class="m-0 inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Seluruh Sesi Ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm font-medium">Belum ada laporan sesi pengawasan</p>
                                <a href="{{ route('pengawasan.create') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-600 hover:underline">+ Tambah Sesi Pengawasan Baru</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($paginatedBatches->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $paginatedBatches->links() }}</div>
            @endif
        </div>
</x-app-layout>
