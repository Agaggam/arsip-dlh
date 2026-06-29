<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kategori Arsip') }}
        </h2>
    </x-slot>

    {{-- Utama: Semua elemen yang butuh reactive state dibungkus di dalam x-data ini --}}
    <div x-data="{ sourceCat: '', targetCat: '' }" class="space-y-6">
        
        {{-- Layout Atas: Card Migrasi di Kiri & Tombol Tambah di Kanan --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
            
            {{-- Card Migrasi Arsip (Mengambil porsi flex-1 agar melebar) --}}
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-2xl p-4 sm:p-5 border border-indigo-100 shadow-sm flex-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 bg-indigo-100 text-indigo-600 rounded-xl mt-0.5 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-gray-800">Migrasi Arsip Antar Kategori</h4>
                            <p class="text-[11px] sm:text-xs text-gray-500 max-w-sm">Pindahkan seluruh berkas ke kategori tujuan secara massal.</p>
                        </div>
                    </div>
                    
                    {{-- Form Kontrol Selektor --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto">
                        <div class="w-full sm:w-44">
                            <select x-model="sourceCat" class="w-full px-2.5 py-2 border border-gray-300 rounded-xl text-xs bg-white focus:border-indigo-500 focus:ring-indigo-500 transition shadow-sm">
                                <option value="">Kategori Sumber</option>
                                @foreach($categories as $cat)
                                    @if($cat->archives_count > 0)
                                        <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->archives_count }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-center rotate-90 sm:rotate-0">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>

                        <div class="w-full sm:w-44">
                            <select x-model="targetCat" class="w-full px-2.5 py-2 border border-gray-300 rounded-xl text-xs bg-white focus:border-indigo-500 focus:ring-indigo-500 transition shadow-sm">
                                <option value="">Kategori Tujuan</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button"
                            @click="
                                if(!sourceCat || !targetCat) {
                                    alert('Pilih kategori sumber dan tujuan terlebih dahulu!');
                                    return;
                                }
                                if(sourceCat === targetCat) {
                                    alert('Kategori sumber dan tujuan tidak boleh sama!');
                                    return;
                                }

                                $dispatch('open-modal', { 
                                    id: 'confirm-delete', 
                                    action: `{{ route('categories.migrate') }}?source_category_id=${sourceCat}&target_category_id=${targetCat}`,
                                    method: 'POST',
                                    title: 'Konfirmasi Migrasi Arsip?',
                                    warning: 'Seluruh arsip dari kategori sumber akan dipindahkan ke kategori tujuan secara permanen.',
                                    withPassword: true
                                });
                            "
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 flex items-center justify-center gap-1.5 shadow-sm shadow-indigo-100 whitespace-nowrap">
                            Migrasikan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tombol Tambah Kategori --}}
            <div class="flex items-stretch md:items-center">
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'modal-tambah-kategori')" 
                    class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-4 px-5 md:py-6 md:px-6 rounded-2xl shadow-sm transition-all active:scale-95 flex flex-row md:flex-col justify-center items-center gap-2 border border-indigo-700 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Kategori</span>
                </button>
            </div>

        </div>

        {{-- Section Tabel Utama menggunakan Komponen Global <x-table> --}}
        <x-table :isEmpty="$categories->isEmpty()" emptyMessage="Belum ada kategori tersedia.">
            
            {{-- Slot Judul & Informasi Tabel --}}
            <x-slot name="title">
                Daftar Kategori Arsip
            </x-slot>

            {{-- Slot Form Search & Filter Dropdown --}}
            <x-slot name="actions">
                    @php
                        $filters = [];
                        if (Auth::user()->isPureSuperAdmin()) {
                            $filters['department_id'] = [
                                'label' => 'Departemen',
                                'options' => $departments->pluck('name', 'id')->toArray()
                            ];
                        }
                        
                        $filters['archive_filter'] = [
                            'label' => 'Status Arsip',
                            'options' => [
                                'has' => '📄 Terisi Arsip',
                                'empty' => '📭 Kosong'
                            ]
                        ];
                    @endphp
                    
                    <x-search-input 
                        route="{{ route('categories.index') }}" 
                        placeholder="Cari nama kategori..."
                        searchParam="search"
                        buttonText="Cari"
                        :resetButton="true"
                        :filters="$filters"
                    />
            </x-slot>

            {{-- Slot Bagian Atas / Header Kolom Tabel --}}
            <x-slot name="thead">
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-16">No</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Kategori</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Departemen</th>
                <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Total Arsip</th>
                <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
            </x-slot>

            {{-- Slot Bagian Konten Data Baris Tabel --}}
            <x-slot name="tbody">
                @foreach($categories as $index => $category)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-xs font-bold text-slate-400">
                        {{ $categories->firstItem() + $index }}
                    </td>
                    <td class="px-4 sm:px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 flex-shrink-0 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                {{ substr($category->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">{{ $category->name }}</div>
                                <div class="text-xs text-slate-500 max-w-xs truncate">{{ $category->description ?? 'Tidak ada deskripsi' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-3 whitespace-nowrap">
                        <span class="px-2.5 py-0.5 inline-flex text-[10px] font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-100 uppercase tracking-wider">
                            {{ $category->department->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-3 text-center whitespace-nowrap">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-bold rounded-full 
                            {{ $category->archives_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">
                            @if($category->archives_count > 0)
                                📄 {{ $category->archives_count }} Arsip
                            @else
                                Kosong
                            @endif
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-3 text-center whitespace-nowrap">
                        <div class="flex justify-center gap-1 sm:gap-2">
                            {{-- Tombol Edit --}}
                            <button type="button" x-data
                                x-on:click="
                                    $dispatch('open-modal', 'modal-edit-kategori');
                                    $dispatch('set-edit-data', {
                                        id: {{ $category->id }},
                                        name: '{{ addslashes($category->name) }}',
                                        description: '{{ addslashes($category->description) }}'
                                    })
                                "
                                class="text-amber-500 hover:text-amber-700 p-1.5 sm:p-2 hover:bg-amber-50 rounded-xl transition-all"
                                title="Edit Kategori">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            
                            {{-- Tombol Hapus --}}
                            <button type="button" x-data
                                x-on:click="$dispatch('open-modal', { 
                                    id: 'confirm-delete', 
                                    action: '{{ route('categories.destroy', $category) }}',
                                    title: 'Hapus kategori {{ $category->name }}?',
                                    warning: 'PERHATIAN: Seluruh arsip di dalam kategori ini akan ikut terhapus secara permanen!',
                                    withPassword: true
                                })"
                                class="text-rose-500 hover:text-rose-700 p-1.5 sm:p-2 hover:bg-rose-50 rounded-xl transition-all"
                                title="Hapus Kategori">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-slot>
        </x-table>

        {{-- Pagination berada di luar box agar konsisten layoutnya --}}
        @if($categories->hasPages())
            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    @include('admin.categories.modal')
</x-app-layout>