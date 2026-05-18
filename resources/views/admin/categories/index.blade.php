<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kategori Arsip') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-4 sm:p-6">
        {{-- Header Responsif --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Kategori Arsip</h3>
                <p class="text-xs text-slate-500">Kategori aktif di unit kerja Anda</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'modal-tambah-kategori')" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 px-4 sm:py-2.5 sm:px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Kategori
                </button>
                <x-search-input 
                    route="{{ route('categories.index') }}" 
                    placeholder="Cari nama kategori atau departemen..."
                    searchParam="search"
                    buttonText="Cari"
                    :resetButton="true"
                />
            </div>
        </div>

        {{-- Tabel Responsif dengan overflow horizontal --}}
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Kategori</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Departemen</th>
                            <th class="px-3 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Total Arsip</th>
                            <th class="px-3 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($categories as $index => $category)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm font-bold text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="h-8 w-8 sm:h-10 sm:w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-xs sm:text-sm uppercase">
                                        {{ substr($category->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">{{ $category->name }}</div>
                                        <div class="text-xs text-slate-500">{{ Str::limit($category->description ?? 'Tidak ada deskripsi', 40) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                <span class="px-2 sm:px-3 py-1 inline-flex text-[10px] font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-100 uppercase tracking-wider">
                                    {{ $category->department->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 text-center whitespace-nowrap">
                                <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 sm:px-3 py-1 rounded-full border border-amber-100">
                                    {{ $category->archives_count ?? 0 }} Arsip
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-2 sm:py-4 text-center">
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
                                        class="text-amber-500 hover:text-amber-700 p-1 sm:p-2 hover:bg-amber-50 rounded-xl transition-all"
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
                                        class="text-rose-500 hover:text-rose-700 p-1 sm:p-2 hover:bg-rose-50 rounded-xl transition-all"
                                        title="Hapus Kategori">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-3 sm:px-6 py-8 text-center text-slate-400 italic text-sm">
                                Belum ada kategori tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.categories.modal')
</x-app-layout>