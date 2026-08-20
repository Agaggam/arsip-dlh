<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pengawasan.index') }}" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Tambah Data Pengawasan</h2>
                <p class="text-sm text-slate-500 mt-0.5">Input data pengawasan dan tabel pelaku usaha (tambah/hapus baris seperti Word / Excel)</p>
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
         x-data="pengawasanSpreadsheet()" 
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

        <form method="POST" action="{{ route('pengawasan.store') }}" @submit="handleSubmit($event)" class="space-y-6">
            @csrf

            {{-- 1. INFORMASI PENGAWASAN (HEADER) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 border-b border-indigo-500 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        1. Informasi Pengawasan (Kop Laporan)
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Tahun --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun" x-model="header.tahun" required min="2020" max="{{ date('Y') + 1 }}"
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                        </div>
                        {{-- Nama Pengawas --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Nama Pengawas <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pengawas" x-model="header.nama_pengawas" required placeholder="Contoh: NAWANG WIRAWAN, S.Pt"
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                        </div>
                        {{-- Kecamatan --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Kecamatan <span class="text-red-500">*</span></label>
                            <select name="kecamatan" x-model="header.kecamatan" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach($kecamatans as $kec)
                                <option value="{{ $kec }}">{{ $kec }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Jenis Pengawasan --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase tracking-wider">Jenis Pengawasan <span class="text-red-500">*</span></label>
                            <select name="jenis_pengawasan" x-model="header.jenis_pengawasan" required class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="langsung">Langsung</option>
                                <option value="tidak_langsung">Tidak Langsung</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TABEL PELAKU USAHA (INLINE SPREADSHEET WORD/EXCEL STYLE) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 border-b border-emerald-500 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                            2. Tabel Pelaku Usaha & Hasil Pengawasan
                        </h3>
                        <p class="text-xs text-emerald-100 mt-0.5">Isi langsung nama usaha pada baris tabel di bawah. Klik tombol <strong class="text-white">+ Tambah Baris Usaha</strong> untuk menambah baris ke bawah.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="addRow()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white text-emerald-700 font-bold text-xs rounded-xl shadow-sm hover:bg-emerald-50 active:scale-95 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            + Tambah Baris Usaha
                        </button>
                        <button type="button" @click="addMultipleRows(3)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-700/60 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all" title="Tambah 3 baris sekaligus">
                            +3 Baris
                        </button>
                    </div>
                </div>

                {{-- PETUNJUK KODE --}}
                <div class="bg-slate-50 px-6 py-2 border-b border-slate-200 flex flex-wrap items-center justify-between text-[11px] text-slate-600">
                    <div class="flex items-center gap-4 font-semibold">
                        <span><strong>Petunjuk Pengisian:</strong></span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> <strong>V</strong> = Ada/Baik</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> <strong>P</strong> = Proses</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> <strong>X</strong> = Tidak Ada</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span> <strong>-</strong> = Tidak Berlaku</span>
                    </div>
                    <div class="text-slate-500 font-bold">
                        Total Baris: <span class="text-emerald-600 font-black" x-text="items.length"></span> Usaha
                    </div>
                </div>

                {{-- SPREADSHEET TABLE CONTAINER --}}
                <div class="overflow-x-auto p-4 max-h-[600px] overflow-y-auto">
                    <table class="excel-table w-full border-collapse">
                        <thead class="sticky top-0 z-10 shadow-xs">
                            <tr>
                                <th rowspan="2" class="w-10">NO</th>
                                <th rowspan="2" class="min-w-[180px] text-left px-3">NAMA USAHA <span class="text-red-500">*</span></th>
                                <th rowspan="2" class="w-28">SKALA USAHA</th>
                                <th rowspan="2" class="w-32">WAKTU PENGAWASAN</th>
                                <th colspan="{{ count($hasilCols) }}" class="bg-slate-200 text-slate-700">HASIL PENGAWASAN (V / P / X / -)</th>
                                <th rowspan="2" class="w-24">TOTAL (V/P/X)</th>
                                <th rowspan="2" class="min-w-[140px] text-left px-3">KETERANGAN</th>
                                <th rowspan="2" class="w-12">HAPUS</th>
                            </tr>
                            <tr>
                                @foreach($hasilCols as $col => $label)
                                <th class="w-11 text-[9px] px-1 py-1 bg-slate-100" title="{{ $label }}">
                                    {{ $label }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="item.uid">
                                <tr class="hover:bg-indigo-50/20 transition-colors">
                                    {{-- No --}}
                                    <td class="text-center font-bold text-slate-400 text-xs" x-text="index + 1"></td>
                                    
                                    {{-- Nama Usaha --}}
                                    <td>
                                        <input type="text" :name="'items[' + index + '][nama_usaha]'" 
                                               x-model="item.nama_usaha" required 
                                               placeholder="Ketik Nama Usaha..."
                                               class="excel-input font-bold text-slate-800 focus:bg-white placeholder-slate-300">
                                    </td>
                                    
                                    {{-- Skala Usaha --}}
                                    <td>
                                        <select :name="'items[' + index + '][skala_usaha]'" 
                                                x-model="item.skala_usaha" required 
                                                class="excel-input text-xs font-semibold text-slate-700 bg-white">
                                            @foreach($skalas as $skala)
                                            <option value="{{ $skala }}">{{ $skala }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Waktu Pengawasan --}}
                                    <td>
                                        <input type="date" :name="'items[' + index + '][waktu_pengawasan]'" 
                                               x-model="item.waktu_pengawasan" required 
                                               class="excel-input text-xs text-slate-700">
                                    </td>

                                    {{-- 16 Kolom Hasil Pengawasan (Mini Colored Select) --}}
                                    @foreach($hasilCols as $col => $label)
                                    <td class="text-center p-0.5">
                                        <select :name="'items[' + index + '][{{ $col }}]'" 
                                                x-model="item.{{ $col }}"
                                                :class="{
                                                    'val-v': item.{{ $col }} === 'v',
                                                    'val-p': item.{{ $col }} === 'p',
                                                    'val-x': item.{{ $col }} === 'x',
                                                    'val-dash': item.{{ $col }} === '-'
                                                }"
                                                class="excel-select">
                                            <option value="-">-</option>
                                            <option value="v">V</option>
                                            <option value="p">P</option>
                                            <option value="x">X</option>
                                        </select>
                                    </td>
                                    @endforeach

                                    {{-- Counter Ringkasan V/P/X --}}
                                    <td class="text-center whitespace-nowrap text-[11px] font-bold">
                                        <span class="text-emerald-600" x-text="countScore(item, 'v') + 'V'"></span>
                                        <span class="text-slate-300">/</span>
                                        <span class="text-amber-600" x-text="countScore(item, 'p') + 'P'"></span>
                                        <span class="text-slate-300">/</span>
                                        <span class="text-red-600" x-text="countScore(item, 'x') + 'X'"></span>
                                    </td>

                                    {{-- Keterangan --}}
                                    <td>
                                        <input type="text" :name="'items[' + index + '][keterangan]'" 
                                               x-model="item.keterangan" 
                                               placeholder="Keterangan..."
                                               class="excel-input text-xs text-slate-600 placeholder-slate-300">
                                    </td>

                                    {{-- Hapus Baris --}}
                                    <td class="text-center">
                                        <button type="button" @click="removeRow(index)" 
                                                class="p-1.5 text-rose-500 hover:text-white hover:bg-rose-600 rounded-lg transition-all" 
                                                title="Hapus Baris Ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- TOMBOL TAMBAH BARIS BAWAH & SIMPAN --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="addRow()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            + Tambah Baris Usaha Baru
                        </button>
                        <button type="button" @click="addMultipleRows(5)"
                                class="inline-flex items-center gap-1 px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition" title="Tambah 5 baris">
                            +5 Baris
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('pengawasan.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-8 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Semua Data (<span x-text="items.length"></span> Usaha)</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function pengawasanSpreadsheet() {
            let rowCounter = 0;
            const createNewRow = () => {
                rowCounter++;
                const row = {
                    uid: 'row_' + rowCounter + '_' + Date.now(),
                    nama_usaha: '',
                    skala_usaha: 'SPPL',
                    waktu_pengawasan: new Date().toISOString().split('T')[0],
                    keterangan: ''
                };
                @foreach($hasilCols as $col => $lbl)
                row['{{ $col }}'] = '-';
                @endforeach
                return row;
            };

            return {
                header: {
                    tahun: '{{ date('Y') }}',
                    nama_pengawas: 'NAWANG WIRAWAN, S.Pt',
                    kecamatan: 'Kecamatan Batu',
                    jenis_pengawasan: 'langsung'
                },
                // Default sediakan 2 baris awal siap isi
                items: [createNewRow(), createNewRow()],

                addRow() {
                    this.items.push(createNewRow());
                },

                addMultipleRows(count) {
                    for (let i = 0; i < count; i++) {
                        this.items.push(createNewRow());
                    }
                },

                removeRow(index) {
                    if (this.items.length <= 1) {
                        alert('Minimal harus ada 1 baris usaha!');
                        return;
                    }
                    this.items.splice(index, 1);
                },

                countScore(item, val) {
                    let c = 0;
                    const cols = @json(array_keys($hasilCols));
                    cols.forEach(k => {
                        if (item[k] === val) c++;
                    });
                    return c;
                },

                handleSubmit(e) {
                    // Validasi setidaknya 1 nama usaha terisi
                    const filled = this.items.filter(i => i.nama_usaha && i.nama_usaha.trim() !== '');
                    if (filled.length === 0) {
                        e.preventDefault();
                        alert('Harap isi Nama Usaha setidaknya pada 1 baris sebelum menyimpan!');
                    }
                }
            };
        }
    </script>
</x-app-layout>
