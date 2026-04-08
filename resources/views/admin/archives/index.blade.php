<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Arsip') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl border-l-4 border-green-500 shadow-sm">
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Arsip Aktif</h3>         
                        <button 
                            type="button"
                            x-data="" 
                            x-on:click="$dispatch('open-modal', 'modal-tambah-arsip')" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Unggah Arsip
                        </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                                <th class="px-6 py-4 text-left">Dokumen</th>
                                <th class="px-6 py-4 text-left">Kategori</th>
                                <th class="px-6 py-4 text-left">Dibuat</th>
                                <th class="px-6 py-4 text-left">Pengunggah</th>
                                <th class="px-6 py-4 text-center">Hits</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($archives as $archive)
                            @php
                                // LOGIKA PEWARNAAN BERDASARKAN FORMAT
                                $ext = strtolower($archive->file_type);
                                $bgClass = 'bg-slate-100 text-slate-600'; // Default
                                if(in_array($ext, ['pdf'])) $bgClass = 'bg-red-100 text-red-600';
                                elseif(in_array($ext, ['xlsx', 'xls', 'csv'])) $bgClass = 'bg-emerald-100 text-emerald-600';
                                elseif(in_array($ext, ['doc', 'docx'])) $bgClass = 'bg-blue-100 text-blue-600';
                                elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $bgClass = 'bg-purple-100 text-purple-600';
                                
                                $downloadRoute = route('admin.archives.download', $archive);
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        {{-- Kotak Ikon yang Berubah Warna --}}
                                        <div class="h-10 w-10 {{ $bgClass }} rounded-xl flex items-center justify-center font-bold uppercase text-[10px]">
                                            {{ $archive->file_type }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-slate-800">{{ $archive->title }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                                {{ $archive->file_size }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-[10px] font-extrabold rounded-full border bg-slate-50 text-slate-600 border-slate-200 uppercase tracking-wider">
                                        {{ $archive->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-500">
                                    {{ $archive->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500 uppercase">
                                            {{ substr($archive->user->name ?? 'A', 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-700">{{ $archive->user->name ?? 'Admin' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-slate-700">{{ $archive->download_count }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-1">
                                        {{-- Tombol Preview --}}
                                        <button 
                                            type="button"
                                            x-data="" 
                                            x-on:click="$dispatch('open-modal', { 
                                                id: 'preview-modal', 
                                                title: 'Preview: {{ addslashes($archive->title) }}',
                                                fileUrl: '{{ asset('storage/' . $archive->file_path) }}',
                                                fileType: '{{ strtolower($archive->file_type) }}',
                                                downloadUrl: '{{ $downloadRoute }}'
                                            })"
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" 
                                            title="Pratinjau">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        {{-- Tombol Download --}}
                                        <a href="{{ $downloadRoute }}" 
                                           class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" 
                                           title="Download">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>

                                        {{-- Tombol Hapus --}}
                                        <button 
                                            type="button"
                                            x-data="" 
                                            x-on:click="$dispatch('open-modal', { 
                                                id: 'confirm-delete', 
                                                action: '{{ route('admin.archives.destroy', $archive) }}',
                                                title: 'Hapus arsip {{ addslashes($archive->title) }}?' 
                                            })"
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all active:scale-90" 
                                            title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center text-slate-400 italic text-sm">Belum ada arsip yang diunggah.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW DOKUMEN --}}
    <x-modal name="preview-modal" maxWidth="4xl">
        <div class="p-6" x-data="{ title: '', url: '', type: '', downloadUrl: '' }" 
             x-on:open-modal.window="if($event.detail.id === 'preview-modal') { 
                title = $event.detail.title; 
                url = $event.detail.fileUrl; 
                type = $event.detail.fileType;
                downloadUrl = $event.detail.downloadUrl;
             }">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-slate-800" x-text="title"></h2>
                <button x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center min-h-[400px]">
                <template x-if="type === 'pdf'">
                    <iframe :src="url" class="w-full h-[600px] border-none"></iframe>
                </template>
                
                <template x-if="['jpg', 'jpeg', 'png', 'webp'].includes(type)">
                    <img :src="url" class="max-w-full max-h-[600px] object-contain shadow-lg">
                </template>

                <template x-if="!['pdf', 'jpg', 'jpeg', 'png', 'webp'].includes(type)">
                    <div class="text-center p-10">
                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-slate-500 font-medium text-sm">Pratinjau tidak tersedia untuk tipe file ini.<br>Silakan unduh dokumen untuk melihat isi.</p>
                        
                        {{-- GANTI :href="url" menjadi :href="downloadUrl" dan hapus atribut download --}}
                        <a :href="downloadUrl" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg transition active:scale-95">Download Sekarang</a>
                    </div>
                </template>
            </div>
        </div>
    </x-modal>

    {{-- Sisanya tetap sama --}}
    <x-confirm-modal id="confirm-delete" method="DELETE" type="danger" />

    <x-modal name="modal-tambah-arsip" focusable>
        <form method="post" action="{{ route('admin.archives.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf
            <h2 class="text-lg font-bold text-slate-800 mb-4">Unggah Arsip Baru</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="title" value="Judul Arsip" class="text-xs font-bold uppercase text-slate-500" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full rounded-xl" placeholder="Masukkan judul dokumen" required />
                </div>
                <div>
                    <x-input-label for="category_id" value="Kategori" class="text-xs font-bold uppercase text-slate-500" />
                    <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="file" value="Pilih File (PDF/Gambar/Doc)" class="text-xs font-bold uppercase text-slate-500" />
                    <input type="file" name="file" id="file" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                </div>
                <div>
                    <x-input-label for="description" value="Deskripsi" class="text-xs font-bold uppercase text-slate-500" />
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Tambahkan keterangan singkat..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
                <x-primary-button class="bg-indigo-600 rounded-xl shadow-indigo-200 shadow-lg">Simpan Arsip</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>