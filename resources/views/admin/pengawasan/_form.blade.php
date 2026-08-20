{{-- Shared Form Partial: _form.blade.php --}}
@php $p = $pengawasan ?? null; @endphp

{{-- Validation Errors --}}
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-4">
    <p class="font-bold mb-1">Mohon perbaiki kesalahan berikut:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

{{-- SECTION 1: Info Pengawasan --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 border-b border-indigo-500">
        <h3 class="text-sm font-bold text-white uppercase tracking-widest">1. Informasi Pengawasan</h3>
    </div>
    <div class="p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {{-- Tahun --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Tahun <span class="text-red-500">*</span></label>
                <input type="number" name="tahun" value="{{ old('tahun', $p?->tahun ?? date('Y')) }}" required min="2020" max="{{ date('Y') + 1 }}"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Nama Pengawas --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Nama Pengawas <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pengawas" value="{{ old('nama_pengawas', $p?->nama_pengawas) }}" required placeholder="Contoh: NAWANG WIRAWAN, S.Pt"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Waktu Pengawasan --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Waktu Pengawasan <span class="text-red-500">*</span></label>
                <input type="date" name="waktu_pengawasan" value="{{ old('waktu_pengawasan', $p?->waktu_pengawasan?->format('Y-m-d')) }}" required
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Kecamatan --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Kecamatan <span class="text-red-500">*</span></label>
                <select name="kecamatan" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach(\App\Models\Pengawasan::KECAMATAN_OPTIONS as $kec)
                    <option value="{{ $kec }}" {{ old('kecamatan', $p?->kecamatan) === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Jenis Pengawasan --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Jenis Pengawasan <span class="text-red-500">*</span></label>
                <select name="jenis_pengawasan" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="langsung" {{ old('jenis_pengawasan', $p?->jenis_pengawasan) === 'langsung' ? 'selected' : '' }}>Langsung</option>
                    <option value="tidak_langsung" {{ old('jenis_pengawasan', $p?->jenis_pengawasan) === 'tidak_langsung' ? 'selected' : '' }}>Tidak Langsung</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 2: Data Usaha --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 border-b border-emerald-500">
        <h3 class="text-sm font-bold text-white uppercase tracking-widest">2. Data Usaha</h3>
    </div>
    <div class="p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- Nama Usaha --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Nama Usaha <span class="text-red-500">*</span></label>
                <input type="text" name="nama_usaha" value="{{ old('nama_usaha', $p?->nama_usaha) }}" required placeholder="Contoh: Dapur Enak"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Skala Usaha --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Skala Usaha / Dokumen <span class="text-red-500">*</span></label>
                <select name="skala_usaha" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Skala --</option>
                    @foreach(\App\Models\Pengawasan::SKALA_USAHA_OPTIONS as $skala)
                    <option value="{{ $skala }}" {{ old('skala_usaha', $p?->skala_usaha) === $skala ? 'selected' : '' }}>{{ $skala }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 3: Hasil Pengawasan --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{
    scores: {
        @foreach(\App\Models\Pengawasan::HASIL_COLUMNS as $col => $label)
        '{{ $col }}': '{{ old($col, $p?->$col ?? '-') }}',
        @endforeach
    }
}">
    <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 border-b border-amber-400 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-widest">3. Hasil Pengawasan (16 Parameter)</h3>
            <p class="text-xs text-amber-100 mt-0.5">Pilih status untuk setiap indikator pengawasan</p>
        </div>
        <div class="flex items-center gap-3 text-xs font-bold text-white/90">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-white/50"></span> - = N/A</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-300"></span> V = Baik</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-300"></span> P = Proses</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-300"></span> X = Tidak Ada</span>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach(\App\Models\Pengawasan::HASIL_COLUMNS as $col => $label)
            <div class="bg-slate-50 border border-slate-200 p-2.5 rounded-xl shadow-xs hover:border-slate-300 transition">
                <input type="hidden" name="{{ $col }}" :value="scores['{{ $col }}']">
                <div class="text-[11px] font-bold text-slate-700 mb-1.5 truncate" title="{{ $label }}">{{ $label }}</div>
                
                {{-- 4 Segmented Pill Options: -, V, P, X --}}
                <div class="grid grid-cols-4 gap-1 p-0.5 bg-slate-200/70 rounded-lg text-center text-xs font-black">
                    {{-- Dash --}}
                    <button type="button" @click="scores['{{ $col }}'] = '-'"
                            :class="scores['{{ $col }}'] === '-' ? 'bg-white text-slate-700 shadow-xs ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-700'"
                            class="py-1 rounded-md transition-all">
                        -
                    </button>
                    {{-- V --}}
                    <button type="button" @click="scores['{{ $col }}'] = 'v'"
                            :class="scores['{{ $col }}'] === 'v' ? 'bg-emerald-500 text-white shadow-xs' : 'text-emerald-700 hover:bg-emerald-50'"
                            class="py-1 rounded-md transition-all">
                        V
                    </button>
                    {{-- P --}}
                    <button type="button" @click="scores['{{ $col }}'] = 'p'"
                            :class="scores['{{ $col }}'] === 'p' ? 'bg-amber-500 text-white shadow-xs' : 'text-amber-700 hover:bg-amber-50'"
                            class="py-1 rounded-md transition-all">
                        P
                    </button>
                    {{-- X --}}
                    <button type="button" @click="scores['{{ $col }}'] = 'x'"
                            :class="scores['{{ $col }}'] === 'x' ? 'bg-rose-500 text-white shadow-xs' : 'text-rose-700 hover:bg-rose-50'"
                            class="py-1 rounded-md transition-all">
                        X
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- SECTION 4: Keterangan --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-slate-600 to-slate-700 border-b border-slate-500">
        <h3 class="text-sm font-bold text-white uppercase tracking-widest">4. Keterangan</h3>
    </div>
    <div class="p-6">
        <textarea name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)..."
                  class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">{{ old('keterangan', $p?->keterangan) }}</textarea>
    </div>
</div>
