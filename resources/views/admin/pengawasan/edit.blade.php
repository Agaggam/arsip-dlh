<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pengawasan.index') }}" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Edit Sesi Pengawasan & Kelola Pelaku Usaha</h2>
                <p class="text-sm text-slate-500 mt-0.5">Sesi: <span class="font-bold text-slate-700">Pengawasan {{ $pengawasan->jenis_label }} — {{ $pengawasan->kecamatan }} ({{ $pengawasan->nama_pengawas }})</span></p>
            </div>
        </div>
    </x-slot>

    @php
        $hasilCols = \App\Models\Pengawasan::HASIL_COLUMNS;
        $kecamatans = \App\Models\Pengawasan::KECAMATAN_OPTIONS;
        $skalas = \App\Models\Pengawasan::SKALA_USAHA_OPTIONS;
    @endphp

    <style>
        /* Styling khusus cell tabel word/excel */
        .excel-table th {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 8px 6px;
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }
        .excel-table td {
            padding: 4px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            background-color: #ffffff;
        }
        .excel-input {
            width: 100%;
            font-size: 12px;
            padding: 5px 8px;
            border: 1px solid transparent;
            border-radius: 6px;
            transition: all 0.15s;
            background: transparent;
        }
        .excel-input:focus {
            background: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
            outline: none;
        }
        .excel-select {
            width: 100%;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 2px;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .excel-select.val-v { background-color: #dcfce7; color: #15803d; border-color: #86efac; }
        .excel-select.val-p { background-color: #fef3c7; color: #b45309; border-color: #fde68a; }
        .excel-select.val-x { background-color: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
        .excel-select.val-dash { background-color: #f8fafc; color: #64748b; border-color: #e2e8f0; }
    </style>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-[100rem] mx-auto"
         x-data="pengawasanSessionEditSpreadsheet()"
         x-cloak>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl px-5 py-4 mb-6">
            <p class="font-bold mb-1">Mohon perbaiki kesalahan berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('pengawasan.update', $pengawasan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- 1. INFORMASI PENGAWASAN (HEADER UMUM) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 border-b border-indigo-500">
                    <h3 class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        1. Informasi Sesi Pengawasan & Wilayah
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Tahun --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun" value="{{ old('tahun', $pengawasan->tahun) }}" required min="2020" max="{{ date('Y') + 1 }}"
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                        </div>
                        {{-- Nama Pengawas --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Nama Pengawas <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pengawas" value="{{ old('nama_pengawas', $pengawasan->nama_pengawas) }}" required
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                        </div>
                        {{-- Kecamatan --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Kecamatan <span class="text-red-500">*</span></label>
                            <select name="kecamatan" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                                @foreach($kecamatans as $kec)
                                <option value="{{ $kec }}" {{ old('kecamatan', $pengawasan->kecamatan) === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Jenis Pengawasan --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Jenis Pengawasan <span class="text-red-500">*</span></label>
                            <select name="jenis_pengawasan" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                                <option value="langsung" {{ old('jenis_pengawasan', $pengawasan->jenis_pengawasan) === 'langsung' ? 'selected' : '' }}>Langsung</option>
                                <option value="tidak_langsung" {{ old('jenis_pengawasan', $pengawasan->jenis_pengawasan) === 'tidak_langsung' ? 'selected' : '' }}>Tidak Langsung</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TABEL PELAKU USAHA (DYNAMIC INLINE SPREADSHEET WORD/EXCEL) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 border-b border-emerald-500 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                            2. Data Pelaku Usaha & Hasil Pengawasan
                        </h3>
                        <p class="text-xs text-emerald-100 mt-0.5">Edit usaha pada sesi ini atau tambahkan baris usaha baru di bawah.</p>
                    </div>

                    {{-- TOMBOL TAMBAH BARIS --}}
                    <div class="flex items-center gap-2">
                        <button type="button" @click="addRow()" 
                                class="px-3.5 py-1.5 bg-white text-emerald-700 hover:bg-emerald-50 text-xs font-black rounded-xl shadow-xs transition flex items-center gap-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            + Tambah Baris Usaha
                        </button>
                        <button type="button" @click="addMultipleRows(3)" 
                                class="px-3 py-1.5 bg-emerald-700/60 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition border border-emerald-400/40 active:scale-95">
                            +3 Baris
                        </button>
                        <button type="button" @click="addMultipleRows(5)" 
                                class="px-3 py-1.5 bg-emerald-700/60 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition border border-emerald-400/40 active:scale-95">
                            +5 Baris
                        </button>
                    </div>
                </div>

                {{-- PETUNJUK KODE --}}
                <div class="bg-slate-50 px-6 py-2.5 border-b border-slate-200 flex flex-wrap items-center justify-between text-[11px] text-slate-600">
                    <div class="flex items-center gap-4 font-semibold">
                        <span><strong>Petunjuk Nilai:</strong></span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> <strong>V</strong> = Ada/Baik</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> <strong>P</strong> = Proses</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> <strong>X</strong> = Tidak Ada</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span> <strong>-</strong> = Tidak Berlaku</span>
                    </div>
                    <div class="text-slate-500 font-medium">
                        Total: <span class="font-bold text-slate-800" x-text="items.length"></span> Baris Usaha
                    </div>
                </div>

                {{-- SPREADSHEET TABLE CONTAINER --}}
                <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="w-full border-collapse excel-table">
                        <thead class="sticky top-0 z-20 shadow-xs">
                            <tr>
                                <th rowspan="2" style="width: 35px;">No</th>
                                <th rowspan="2" style="min-width: 170px; text-align: left; padding-left: 10px;">Nama Usaha <span class="text-red-500">*</span></th>
                                <th rowspan="2" style="width: 100px;">Skala Usaha</th>
                                <th rowspan="2" style="width: 120px;">Waktu Pengawasan</th>
                                <th colspan="16" class="bg-indigo-50 text-indigo-900">Hasil Pengawasan (16 Parameter)</th>
                                <th colspan="3" class="bg-slate-100" style="width: 85px;">Akumulasi</th>
                                <th rowspan="2" style="min-width: 140px;">Keterangan</th>
                                <th rowspan="2" style="width: 45px;">Aksi</th>
                            </tr>
                            <tr>
                                @foreach($hasilCols as $col => $lbl)
                                <th title="{{ $lbl }}" style="width: 42px; font-size: 9px; padding: 4px 1px;">
                                    {{ Str::limit($lbl, 8) }}
                                </th>
                                @endforeach
                                <th style="width: 28px; color: #16a34a;" title="Jumlah V">V</th>
                                <th style="width: 28px; color: #d97706;" title="Jumlah P">P</th>
                                <th style="width: 28px; color: #dc2626;" title="Jumlah X">X</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    {{-- No --}}
                                    <td class="text-center font-bold text-slate-400 text-xs" x-text="index + 1"></td>

                                    {{-- Hidden ID (jika existing item) --}}
                                    <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id || ''">

                                    {{-- Nama Usaha --}}
                                    <td>
                                        <input type="text" 
                                               :name="'items[' + index + '][nama_usaha]'" 
                                               x-model="item.nama_usaha" 
                                               placeholder="Nama usaha..." 
                                               class="excel-input font-bold text-slate-800">
                                    </td>

                                    {{-- Skala Usaha --}}
                                    <td>
                                        <select :name="'items[' + index + '][skala_usaha]'" 
                                                x-model="item.skala_usaha" 
                                                class="excel-select bg-slate-50 text-slate-700 font-semibold">
                                            @foreach($skalas as $skala)
                                            <option value="{{ $skala }}">{{ $skala }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Waktu Pengawasan --}}
                                    <td>
                                        <input type="date" 
                                               :name="'items[' + index + '][waktu_pengawasan]'" 
                                               x-model="item.waktu_pengawasan" 
                                               class="excel-input text-xs text-slate-700">
                                    </td>

                                    {{-- 16 Parameter Select Cells --}}
                                    @foreach($hasilCols as $col => $lbl)
                                    <td>
                                        <select :name="'items[' + index + '][{{ $col }}]'" 
                                                x-model="item.{{ $col }}" 
                                                :class="'excel-select val-' + item.{{ $col }}"
                                                title="{{ $lbl }}">
                                            <option value="v">V</option>
                                            <option value="p">P</option>
                                            <option value="x">X</option>
                                            <option value="-">-</option>
                                        </select>
                                    </td>
                                    @endforeach

                                    {{-- Auto Counters --}}
                                    <td class="text-center font-black text-xs text-emerald-700 bg-emerald-50/50" x-text="countScore(index, 'v')"></td>
                                    <td class="text-center font-black text-xs text-amber-700 bg-amber-50/50" x-text="countScore(index, 'p')"></td>
                                    <td class="text-center font-black text-xs text-rose-700 bg-rose-50/50" x-text="countScore(index, 'x')"></td>

                                    {{-- Keterangan --}}
                                    <td>
                                        <input type="text" 
                                               :name="'items[' + index + '][keterangan]'" 
                                               x-model="item.keterangan" 
                                               placeholder="Keterangan..." 
                                               class="excel-input text-xs text-slate-600">
                                    </td>

                                    {{-- Hapus Baris --}}
                                    <td class="text-center">
                                        <button type="button" @click="removeRow(index)" 
                                                :disabled="items.length <= 1"
                                                class="p-1.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition disabled:opacity-30 disabled:cursor-not-allowed"
                                                title="Hapus baris ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- TOMBOL AKSI BAWAH --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="addRow()" 
                                class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            + Tambah Baris Usaha
                        </button>
                        <span class="text-xs text-slate-400 italic">Baris yang dikosongkan tidak akan disimpan.</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('pengawasan.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-8 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition flex items-center gap-2 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan Sesi</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function pengawasanSessionEditSpreadsheet() {
            return {
                items: @json($items->map(function($it) use ($hasilCols) {
                    $row = [
                        'id' => $it->id,
                        'nama_usaha' => $it->nama_usaha,
                        'skala_usaha' => $it->skala_usaha,
                        'waktu_pengawasan' => $it->waktu_pengawasan ? $it->waktu_pengawasan->format('Y-m-d') : date('Y-m-d'),
                        'keterangan' => $it->keterangan ?? '',
                    ];
                    foreach(array_keys($hasilCols) as $col) {
                        $row[$col] = $it->$col ?? '-';
                    }
                    return $row;
                })),

                createEmptyRow() {
                    return {
                        id: null,
                        nama_usaha: '',
                        skala_usaha: 'SPPL',
                        waktu_pengawasan: '{{ date('Y-m-d') }}',
                        keterangan: '',
                        @foreach($hasilCols as $col => $lbl)
                        '{{ $col }}': '-',
                        @endforeach
                    };
                },

                addRow() {
                    this.items.push(this.createEmptyRow());
                },

                addMultipleRows(count) {
                    for (let i = 0; i < count; i++) {
                        this.addRow();
                    }
                },

                removeRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                countScore(index, val) {
                    const row = this.items[index];
                    if (!row) return 0;
                    let c = 0;
                    const cols = @json(array_keys($hasilCols));
                    cols.forEach(k => {
                        if (row[k] === val) c++;
                    });
                    return c;
                }
            };
        }
    </script>
</x-app-layout>
