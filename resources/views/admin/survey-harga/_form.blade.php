{{-- Shared Form Partial: _form.blade.php --}}
@php $s = $survey ?? null; @endphp

{{-- Validation Errors --}}
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-4">
    <p class="font-bold mb-1">Mohon perbaiki kesalahan berikut:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

{{-- SECTION 1: Identitas Usulan --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 border-b border-indigo-500">
        <h3 class="text-sm font-bold text-white uppercase tracking-widest">1. Identitas Usulan</h3>
    </div>
    <div class="p-6 space-y-5">

        {{-- Pilih Kelompok --}}
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Kelompok <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-data="{ selectedKelompok: '{{ old('kelompok', $s?->kelompok ?? '') }}' }">
                @foreach(['SSH' => 'Standar Satuan Harga', 'SBU' => 'Standar Biaya Umum', 'HSPK' => 'Harga Satuan Pokok Kegiatan', 'ASB' => 'Analisis Standar Belanja'] as $val => $desc)
                <label class="cursor-pointer relative block">
                    <input type="radio" name="kelompok" value="{{ $val }}" class="sr-only"
                           x-model="selectedKelompok" required>
                    <div class="flex flex-col items-center justify-center gap-1.5 py-4 px-3 rounded-xl border-2 text-center transition-all"
                         :class="selectedKelompok === '{{ $val }}' ? 'border-indigo-500 bg-indigo-50 shadow-md' : 'border-slate-200 hover:border-indigo-300'">
                        <span class="text-lg font-extrabold transition-colors"
                              :class="selectedKelompok === '{{ $val }}' ? 'text-indigo-700' : 'text-slate-700'">{{ $val }}</span>
                        <span class="text-[10px] text-slate-400 leading-tight">{{ $desc }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- Judul --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Judul / Nama Barang atau Jasa <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $s?->judul) }}" required placeholder="Contoh: Laptop 14 inch Core i5"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Kode Komponen --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Kode Komponen</label>
                <input type="text" name="kode_komponen" value="{{ old('kode_komponen', $s?->kode_komponen) }}" placeholder="Contoh: 5.2.02.01"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Kode Rekening --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Kode Rekening</label>
                <input type="text" name="kode_rekening" value="{{ old('kode_rekening', $s?->kode_rekening) }}" placeholder="Contoh: 5.2.02.01.0001"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Satuan --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="satuan" value="{{ old('satuan', $s?->satuan) }}" required placeholder="Unit, Buah, Meter, Jam, OB..."
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Harga Usulan --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Harga Usulan (Rp) <span class="text-red-500">*</span></label>
                <input type="text" name="harga_usulan" id="harga_usulan"
                       value="{{ old('harga_usulan', $s ? number_format($s->harga_usulan, 0, ',', '.') : '') }}"
                       required placeholder="Contoh: 12.500.000"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Spesifikasi Singkat --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Spesifikasi Singkat</label>
                <input type="text" name="spesifikasi_singkat" value="{{ old('spesifikasi_singkat', $s?->spesifikasi_singkat) }}" placeholder="Ringkasan spesifikasi 1 baris"
                       class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            {{-- Spesifikasi Detail --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Spesifikasi Detail</label>
                <textarea name="spesifikasi_detail" rows="4" placeholder="Uraian spesifikasi lengkap (processor, RAM, kapasitas, dll)..."
                          class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">{{ old('spesifikasi_detail', $s?->spesifikasi_detail) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 2: Survei Toko --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 border-b border-emerald-500">
        <h3 class="text-sm font-bold text-white uppercase tracking-widest">2. Data Survei Toko (Minimal 2 Toko)</h3>
    </div>
    <div class="p-6 space-y-6">
        @foreach([1, 2, 3] as $i)
        @php
            $optional = $i === 3 ? ' (Opsional)' : ' <span class="text-red-500">*</span>';
        @endphp
        <div class="p-5 bg-slate-50/70 rounded-xl border border-slate-200 space-y-4">
            <h4 class="text-sm font-bold text-slate-600 flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-extrabold">{{ $i }}</span>
                Survey {{ $i }}{!! $i < 3 ? ' <span class="text-red-500 text-xs ml-1">*</span>' : ' <span class="text-slate-400 text-xs font-normal ml-1">(Opsional)</span>' !!}
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Nama Toko</label>
                    <input type="text" name="nama_toko_{{ $i }}" value="{{ old("nama_toko_{$i}", $s?->{"nama_toko_{$i}"}) }}"
                           placeholder="Contoh: Tokopedia / Toko ABC"
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Harga di Toko (Rp)</label>
                    <input type="text" name="harga_toko_{{ $i }}" id="harga_toko_{{ $i }}"
                           value="{{ old("harga_toko_{$i}", $s?->{"harga_toko_{$i}"} ? number_format($s->{"harga_toko_{$i}"}, 0, ',', '.') : '') }}"
                           placeholder="Contoh: 12.000.000"
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Link Belanja</label>
                    <input type="url" name="link_belanja_{{ $i }}" value="{{ old("link_belanja_{$i}", $s?->{"link_belanja_{$i}"}) }}"
                           placeholder="https://..."
                           class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-widest">Foto Barang / Screenshot Toko</label>
                    <input type="file" name="gambar_toko_{{ $i }}" accept="image/*" id="img_toko_{{ $i }}"
                           class="w-full text-sm file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:text-xs file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 text-slate-500 border border-slate-200 rounded-xl p-1"
                           onchange="previewImage(this, 'preview_toko_{{ $i }}')">
                    {{-- Preview gambar baru --}}
                    <div id="preview_toko_{{ $i }}" class="mt-2 hidden">
                        <img src="" alt="Preview" class="w-28 h-28 rounded-xl border border-slate-200 object-cover shadow-xs">
                    </div>
                    {{-- Preview gambar lama (edit mode) --}}
                    @if($s && $s->{"gambar_toko_{$i}"})
                    <div class="mt-2">
                        <p class="text-xs text-slate-400 mb-1">Foto saat ini:</p>
                        <img src="{{ asset('storage/' . $s->{"gambar_toko_{$i}"}) }}" alt="Gambar Toko {{ $i }}" class="w-28 h-28 rounded-xl border border-slate-200 object-cover shadow-xs">
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Live currency formatting
document.addEventListener('DOMContentLoaded', () => {
    const inputs = ['harga_usulan', 'harga_toko_1', 'harga_toko_2', 'harga_toko_3'];
    
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            if (el.value) {
                let val = el.value.replace(/\D/g, '');
                if (val) {
                    el.value = new Intl.NumberFormat('id-ID').format(val);
                }
            }

            el.addEventListener('input', function(e) {
                let val = this.value.replace(/\D/g, '');
                if (val) {
                    this.value = new Intl.NumberFormat('id-ID').format(val);
                } else {
                    this.value = '';
                }
            });
        }
    });
});
</script>

