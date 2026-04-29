<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kategori Arsip') }}
        </h2>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- {{-- Alert Success --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-2xl shadow-sm border-l-4 border-green-500 transition-all">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Alert Error (PENTING: Agar pesan 'Password Salah' muncul) --}}
            @if(session('error') || $errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-2xl shadow-sm border-l-4 border-red-500 transition-all">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">{{ session('error') ?? 'Terjadi kesalahan pada data yang Anda masukkan.' }}</span>
                    </div>
                </div>
            @endif -->

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-6">
                {{-- Header Tabel --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Daftar Kategori Arsip</h3>
                        <p class="text-xs text-slate-500">Kategori aktif di unit kerja Anda</p>
                    </div>
                    
                    <button 
                        type="button"
                        x-data="" 
                        x-on:click="$dispatch('open-modal', 'modal-tambah-kategori')" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Kategori
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Departemen</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Total Arsip</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($categories as $index => $category)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold mr-3 text-sm">
                                            {{ substr($category->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ $category->name }}</div>
                                            <div class="text-xs text-slate-500">{{ Str::limit($category->description ?? 'Tidak ada deskripsi', 40) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-[10px] font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-100 uppercase tracking-wider">
                                        {{ $category->department->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-3 py-1 rounded-full border border-amber-100">
                                        {{ $category->archives_count ?? 0 }} Arsip
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        type="button"
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', { 
                                            id: 'confirm-delete', 
                                            action: '{{ route('categories.destroy', $category) }}',
                                            title: 'Hapus kategori {{ $category->name }}?',
                                            warning: 'PERHATIAN: Seluruh arsip di dalam kategori ini akan ikut terhapus secara permanen!',
                                            withPassword: true
                                        })"
                                        class="text-rose-500 hover:text-rose-700 p-2 hover:bg-rose-50 rounded-xl transition-all"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <div class="text-slate-400 italic text-sm font-medium">Belum ada kategori tersedia.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

{{-- MODAL TAMBAH KATEGORI --}}
<x-modal name="modal-tambah-kategori" focusable>
    <form method="post" action="{{ route('categories.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-bold text-slate-800 mb-4">Buat Kategori Baru</h2>
        
        <div class="space-y-4">
            <div>
                <x-input-label for="name" value="Nama Kategori" class="text-xs font-bold uppercase text-slate-500" />
                <x-text-input id="name" name="name" type="text" autocomplete="off" class="mt-1 block w-full rounded-xl" placeholder="Misal: Laporan Keuangan" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="Deskripsi" class="text-xs font-bold uppercase text-slate-500" />
                <textarea name="description" id="description" rows="3" autocomplete="off" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Berikan sedikit konteks tentang kategori ini..."></textarea>
            </div>
            
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Unit Kerja Terdeteksi</p>
                <p class="text-sm font-bold text-indigo-700">{{ Auth::user()->department->name }}</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
            <x-primary-button class="bg-indigo-600 rounded-xl">Simpan Kategori</x-primary-button>
        </div>
    </form>
</x-modal>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <x-confirm-modal id="confirm-delete" type="danger" />
</x-app-layout>