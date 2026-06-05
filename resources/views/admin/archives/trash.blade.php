<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tong Sampah Arsip') }}
            </h2>
        </div>
    </x-slot>



            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Arsip yang Dihapus</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dokumen</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Bidang</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Dihapus</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($archives as $archive)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-bold uppercase text-xs">
                                            {{ $archive->file_type }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-slate-800">{{ $archive->title }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase italic">
                                                {{ $archive->file_size }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-[10px] font-extrabold rounded-full border bg-slate-50 text-slate-500 border-slate-200 uppercase tracking-wider">
                                        {{ $archive->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-slate-600">
                                        {{ $archive->category->department->name ?? 'TANPA BIDANG' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 font-medium">
                                    {{ $archive->deleted_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        
                                        {{-- Tombol Restore (Tanpa Password) --}}
                                        <button 
                                            type="button"
                                            x-data="" 
                                            x-on:click="$dispatch('open-modal', { 
                                                id: 'confirm-restore', 
                                                action: '{{ route('admin.archives.restore', $archive->id) }}',
                                                method: 'POST',
                                                title: 'Pulihkan Arsip?',
                                                warning: 'Arsip &quot;{{ addslashes($archive->title) }}&quot; akan dikembalikan ke daftar aktif.',
                                                withPassword: false
                                            })"
                                            class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-xl transition-all active:scale-90" 
                                            title="Pulihkan Arsip">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>

                                        {{-- Tombol Hapus Permanen (Wajib Password) --}}
                                        <button 
                                            type="button"
                                            x-data="" 
                                            x-on:click="$dispatch('open-modal', { 
                                                id: 'confirm-delete', 
                                                action: '{{ route('admin.archives.force_delete', $archive->id) }}',
                                                method: 'DELETE',
                                                title: 'Hapus Permanen?',
                                                warning: 'Arsip &quot;{{ addslashes($archive->title) }}&quot; akan dihapus selamanya dari sistem. Tindakan ini tidak bisa dibatalkan!',
                                                withPassword: true 
                                            })"
                                            class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all active:scale-90" 
                                            title="Hapus Permanen">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="text-slate-400 italic text-sm font-medium">Tong sampah kosong.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


    {{-- MODAL HIJAU (RESTORE) --}}
    <x-confirm-modal id="confirm-restore" method="POST" type="success" />

    {{-- MODAL MERAH (FORCE DELETE) --}}
    <x-confirm-modal id="confirm-delete" method="DELETE" type="danger" />

</x-app-layout>