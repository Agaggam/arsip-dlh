<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Daftar Arsip') }} — <span class="text-indigo-600">{{ $currentDeptName }}</span>
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-sm border border-gray-100 sm:rounded-[2rem] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-gray-100 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-6 py-4 text-left w-16">No.</th>
                                <th class="px-6 py-4 text-left">Nama Dokumen</th>
                                <th class="px-4 py-4 text-center">Format</th>
                                <th class="px-4 py-4 text-center">Ukuran</th>
                                <th class="px-6 py-4 text-center">Tanggal Unggah</th>
                                <th class="px-6 py-4 text-center w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php 
                                $iteration = ($archives->currentPage() - 1) * $archives->perPage() + 1; 
                                $lastCategory = null;
                            @endphp
                            
                            @forelse($archives as $archive)
                                {{-- LOGIKA HEADER KATEGORI OTOMATIS --}}
                                @php 
                                    $currentCategory = $archive->category->name ?? 'Umum';
                                @endphp

                                @if($lastCategory !== $currentCategory)
                                    <tr class="bg-slate-50/80">
                                        <td colspan="6" class="px-6 py-3 border-y border-gray-100 font-black text-slate-900 uppercase text-xs tracking-wider">
                                            <div class="flex items-center gap-2">
                                                <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>
                                                {{ $currentCategory }}
                                            </div>
                                        </td>
                                    </tr>
                                    @php $lastCategory = $currentCategory; @endphp
                                @endif

                                @php
                                    $ext = strtolower($archive->file_type);
                                    $bgClass = 'bg-slate-100 text-slate-500'; 
                                    if(in_array($ext, ['pdf'])) $bgClass = 'bg-red-100 text-red-600';
                                    elseif(in_array($ext, ['xlsx', 'xls', 'csv'])) $bgClass = 'bg-emerald-100 text-emerald-600';
                                    elseif(in_array($ext, ['doc', 'docx'])) $bgClass = 'bg-blue-100 text-blue-600';
                                    elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $bgClass = 'bg-purple-100 text-purple-600';
                                    
                                    $fileUrl = asset('storage/' . $archive->file_path);
                                    $downloadRoute = route('arsip.download', $archive);
                                @endphp

                                <tr class="hover:bg-slate-50/50 transition-all group">
                                    <td class="px-6 py-5 text-sm font-medium text-slate-400">
                                        {{ $iteration++ }}.
                                    </td>
                                    <td class="px-6 py-5 text-sm font-normal text-slate-600">
                                        {{ $archive->title }}
                                    </td>
                                    <td class="px-4 py-5 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $bgClass }}">
                                            {{ $archive->file_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-5 text-center text-xs font-medium text-slate-500">
                                        {{ $archive->file_size }}
                                    </td>
                                    <td class="px-6 py-5 text-center text-xs font-medium text-slate-400">
                                        {{ $archive->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-5 text-center text-sm">
                                        <div class="flex justify-center items-center gap-3">
                                            <button 
                                                type="button"
                                                x-data=""
                                                x-on:click="$dispatch('open-modal', { 
                                                    id: 'preview-modal-user', 
                                                    title: '{{ addslashes($archive->title) }}',
                                                    fileUrl: '{{ $fileUrl }}',
                                                    fileType: '{{ $ext }}',
                                                    downloadUrl: '{{ $downloadRoute }}'
                                                })"
                                                class="text-[11px] font-black uppercase text-indigo-500 hover:text-indigo-700 underline decoration-indigo-100 decoration-2 underline-offset-4 transition-all cursor-pointer">
                                                Preview
                                            </button>
                                            <span class="text-slate-200">|</span>
                                            <a href="{{ $downloadRoute }}" 
                                               class="text-[11px] font-black uppercase text-rose-500 hover:text-rose-700 underline decoration-rose-100 decoration-2 underline-offset-4 transition-all">
                                                Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-slate-400 italic text-sm font-medium">Belum ada dokumen di bidang ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-6 bg-slate-50/50 border-t border-gray-100">
                    {{ $archives->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW FIX --}}
    <x-modal name="preview-modal-user" maxWidth="5xl" focusable>
        <div class="p-8" x-data="{ title: '', url: '', type: '', downloadUrl: '' }" 
             x-on:open-modal.window="if($event.detail.id === 'preview-modal-user') { 
                title = $event.detail.title; 
                url = $event.detail.fileUrl; 
                type = $event.detail.fileType;
                downloadUrl = $event.detail.downloadUrl;
             }">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight" x-text="title"></h2>
                    <div class="flex items-center gap-2 mt-1 font-bold">
                        <span class="text-[10px] text-indigo-500 uppercase tracking-widest">Pratinjau Dokumen</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest" x-text="type"></span>
                    </div>
                </div>
                <button x-on:click="$dispatch('close')" class="p-2 bg-slate-50 text-slate-400 hover:text-rose-500 rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="bg-slate-50 rounded-[2rem] overflow-hidden border border-slate-100 flex items-center justify-center min-h-[500px] relative shadow-inner text-center">
                {{-- PREVIEW PDF --}}
                <template x-if="type === 'pdf'">
                    <iframe :src="url" class="w-full h-[75vh] border-none rounded-xl shadow-lg"></iframe>
                </template>
                
                {{-- PREVIEW GAMBAR --}}
                <template x-if="['jpg', 'jpeg', 'png', 'webp'].includes(type)">
                    <img :src="url" class="max-w-full max-h-[75vh] object-contain p-4 drop-shadow-2xl">
                </template>

                {{-- FORMAT TIDAK DIDUKUNG --}}
                <template x-if="!['pdf', 'jpg', 'jpeg', 'png', 'webp'].includes(type)">
                    <div class="p-12">
                        <div class="w-20 h-20 bg-white rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-4 text-slate-200">
                             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-slate-800 font-bold tracking-tight">Pratinjau Tidak Tersedia</h4>
                        <p class="text-xs text-slate-400 mt-1 mb-8 italic">Format file ini tidak mendukung pratinjau langsung.</p>
                        <a :href="downloadUrl" class="inline-flex items-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-2xl shadow-lg shadow-indigo-100 transition-all uppercase tracking-widest">
                            Unduh Dokumen
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </x-modal>
</x-app-layout>