<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Usulan Harga SSH/SBU</h2>
                <p class="text-sm text-slate-500 mt-0.5">Formulir Survei Harga Pasar untuk Standar Satuan Harga</p>
            </div>
            <a href="{{ route('survey-harga.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Usulan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        {{-- SESSION MESSAGE --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm font-medium px-5 py-3.5 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- KPI CARDS --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['label' => 'Total Usulan', 'value' => $summary['total'], 'color' => 'indigo', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['label' => 'Diajukan', 'value' => $summary['diajukan'], 'color' => 'blue', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Disetujui', 'value' => $summary['disetujui'], 'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Ditolak', 'value' => $summary['ditolak'], 'color' => 'red', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp
            @foreach($cards as $card)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">{{ $card['label'] }}</p>
                    <div class="p-2 bg-{{ $card['color'] }}-50 rounded-xl">
                        <svg class="w-4 h-4 text-{{ $card['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-slate-800">{{ $card['value'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <form method="GET" action="{{ route('survey-harga.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Cari</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Judul, Kode Komponen..."
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Kelompok</label>
                    <select name="kelompok" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua</option>
                        @foreach(['SSH', 'SBU', 'HSPK', 'ASB'] as $k)
                        <option value="{{ $k }}" {{ ($filters['kelompok'] ?? '') === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-widest">Status</label>
                    <select name="status" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua</option>
                        <option value="diajukan" {{ ($filters['status'] ?? '') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="disetujui" {{ ($filters['status'] ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ ($filters['status'] ?? '') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                @if(auth()->user()->isPureSuperAdmin())
                <div class="min-w-[160px]">
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
                            <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($surveys as $survey)
                        <tr class="hover:bg-slate-50/70 transition-colors" x-data>
                            <td class="px-5 py-4 align-top">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="font-bold text-slate-800 truncate max-w-[220px]" title="{{ $survey->judul }}">{{ $survey->judul }}</p>
                                    <span class="text-[10px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded whitespace-nowrap">{{ $survey->kode_komponen ?? 'Tanpa Kode' }}</span>
                                </div>
                                @if($survey->spesifikasi_singkat)
                                <p class="text-[11px] text-slate-400 truncate max-w-[280px]" title="{{ $survey->spesifikasi_singkat }}">{{ $survey->spesifikasi_singkat }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                @php
                                    $kColor = ['SSH' => 'indigo', 'SBU' => 'violet', 'HSPK' => 'amber', 'ASB' => 'teal'];
                                    $c = $kColor[$survey->kelompok] ?? 'slate';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $c }}-100 text-{{ $c }}-700">{{ $survey->kelompok }}</span>
                            </td>
                            <td class="px-4 py-4 align-top whitespace-nowrap font-extrabold text-slate-800">{{ $survey->harga_usulan_format }}</td>
                            <td class="px-4 py-4 align-top text-slate-600 text-xs font-medium">{{ $survey->department->name ?? '-' }}</td>
                            <td class="px-4 py-4 align-top">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $survey->status_badge }}">
                                    {{ $survey->status_label }}
                                </span>
                                @if($survey->status === 'ditolak' && $survey->catatan_admin)
                                <p class="text-[11px] text-red-500 font-medium mt-1.5 max-w-[120px] leading-tight" title="{{ $survey->catatan_admin }}">{{ Str::limit($survey->catatan_admin, 30) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top text-slate-500 text-xs font-medium">{{ $survey->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-4 align-top">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Download PDF --}}
                                    <a href="{{ route('survey-harga.export-pdf', $survey) }}" target="_blank"
                                       class="p-2 text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 rounded-lg transition-colors focus:ring-2 focus:ring-red-200" title="Download PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('survey-harga.edit', $survey) }}"
                                       class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 rounded-lg transition-colors focus:ring-2 focus:ring-indigo-200" title="Edit Usulan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- Ubah Status (Super Admin) --}}
                                    @if(auth()->user()->isPureSuperAdmin())
                                    <button @click="$dispatch('open-status-modal-{{ $survey->id }}')" type="button"
                                            class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 hover:text-amber-700 rounded-lg transition-colors focus:ring-2 focus:ring-amber-200" title="Ubah Status">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </button>
                                    @endif
                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('survey-harga.destroy', $survey) }}" onsubmit="return confirm('Yakin ingin menghapus usulan ini?')" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 rounded-lg transition-colors focus:ring-2 focus:ring-rose-200" title="Hapus Usulan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- STATUS MODAL (Super Admin) --}}
                        @if(auth()->user()->isPureSuperAdmin())
                        <div x-data="{ open: false }" @open-status-modal-{{ $survey->id }}.window="open = true">
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="open = false">
                                <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4" @click.stop>
                                    <h3 class="text-base font-bold text-slate-800 mb-4">Ubah Status — {{ Str::limit($survey->judul, 40) }}</h3>
                                    <form method="POST" action="{{ route('survey-harga.update-status', $survey) }}" class="space-y-4">
                                        @csrf @method('PATCH')
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Status</label>
                                            <select name="status" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="diajukan" {{ $survey->status === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                                <option value="disetujui" {{ $survey->status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                                <option value="ditolak" {{ $survey->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Catatan (opsional, wajib jika ditolak)</label>
                                            <textarea name="catatan_admin" rows="3" placeholder="Alasan penolakan atau catatan persetujuan..."
                                                      class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">{{ $survey->catatan_admin }}</textarea>
                                        </div>
                                        <div class="flex gap-3 justify-end pt-2">
                                            <button type="button" @click="open = false" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
                                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center text-slate-400">
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

    </div>
</x-app-layout>
