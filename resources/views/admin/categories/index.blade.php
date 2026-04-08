<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kategori Arsip') }}
        </h2>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-2xl shadow-sm border-l-4 border-green-500 transition-all">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-6 h-fit">
                    <div class="flex items-center mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Tambah Kategori</h3>
                    </div>
                    
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Kategori</label>
                            <input type="text" name="name" id="name" class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Contoh: Surat Masuk" required>
                            @error('name') <span class="text-rose-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi (Opsional)</label>
                            <textarea name="description" id="description" rows="3" class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Keterangan singkat..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all active:scale-95">
                            Simpan Kategori
                        </button>
                    </form>
                </div>

                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Daftar Kategori</h3>
                        <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Total: {{ $categories->count() }} Item
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-50 rounded-xl overflow-hidden">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($categories as $index => $category)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold mr-3 text-sm">
                                                {{ substr($category->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800">{{ $category->name }}</div>
                                                <div class="text-xs text-slate-500">{{ Str::limit($category->description ?? 'Tidak ada deskripsi', 30) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-extrabold rounded-full border bg-slate-50 text-slate-600 border-slate-200 uppercase tracking-wider">
                                            {{ $category->slug }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button 
                                            type="button"
                                            x-data=""
                                            x-on:click="$dispatch('open-modal', { 
                                                id: 'confirm-delete', 
                                                action: '{{ route('categories.destroy', $category) }}',
                                                title: 'Hapus kategori {{ $category->name }} secara permanen?' 
                                            })"
                                            class="text-rose-500 hover:text-rose-700 p-2 hover:bg-rose-50 rounded-xl transition-all active:scale-90"
                                            title="Hapus Kategori"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center">
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
    </div>

    <x-confirm-modal id="confirm-delete" />
</x-app-layout>