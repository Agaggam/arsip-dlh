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

    <div class="">
        <div class="">
            <div class="bg-white shadow-sm border border-gray-100 sm:rounded-[2rem] overflow-hidden">
                {{-- Search Bar di atas kanan --}}
                <div class="flex justify-end p-4 border-b border-gray-100">
                    <x-search-input 
                        route="{{ route('arsip.user') }}" 
                        placeholder="Cari judul atau deskripsi..."
                        searchParam="search"
                        buttonText="Cari"
                        :resetButton="true"
                        :filters="[
                            'category_id' => [
                                'label' => 'Kategori',
                                'options' => $categories->pluck('name', 'id')->toArray()
                            ],
                            'file_type' => [
                                'label' => 'Format',
                                'options' => $fileTypes->mapWithKeys(fn($type) => [strtolower($type) => strtoupper($type)])->toArray()
                            ]
                        ]"
                    />
                </div>

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
                                    <td class="px-6 py-5 text-sm font-medium text-slate-400">{{ $iteration++ }}. </td>
                                    <td class="px-6 py-5 text-sm font-normal text-slate-600">{{ $archive->title }}</td>
                                    <td class="px-4 py-5 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $bgClass }}">{{ $archive->file_type }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-center text-xs font-medium text-slate-500">{{ $archive->file_size }}</td>
                                    <td class="px-6 py-5 text-center text-xs font-medium text-slate-400">{{ $archive->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-5 text-center text-sm">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- CUTE BUTTON XLS --}}
                                            <a href="{{ route('admin.archives.export-excel-single', $archive) }}" 
                                               class="bg-emerald-100/80 hover:bg-emerald-200 text-emerald-800 font-extrabold text-[10px] px-2.5 py-1 rounded-lg transition-all shadow-2xs border border-emerald-300/40" 
                                               title="Unduh Data Excel Arsip Ini">
                                                XLS
                                            </a>

                                            {{-- CUTE BUTTON PDF --}}
                                            <a href="{{ route('admin.archives.export-pdf-single', $archive) }}" 
                                               class="bg-rose-100/80 hover:bg-rose-200 text-rose-800 font-extrabold text-[10px] px-2.5 py-1 rounded-lg transition-all shadow-2xs border border-rose-300/40" 
                                               title="Unduh Lembar PDF Arsip Ini">
                                                PDF
                                            </a>

                                            {{-- PRATINJAU --}}
                                            <button type="button" 
                                                    x-data 
                                                    x-on:click="$dispatch('open-modal', { 
                                                        id: 'preview-modal', 
                                                        title: 'Preview: {{ addslashes($archive->title) }}', 
                                                        fileUrl: '{{ route('arsip.preview', $archive->hash_token) }}', 
                                                        fileType: '{{ strtolower($archive->file_type) }}', 
                                                        downloadUrl: '{{ $downloadRoute }}' 
                                                    })" 
                                                    class="flex items-center justify-center gap-1 px-2.5 py-1 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 font-bold text-xs rounded-lg transition-all" 
                                                    title="Pratinjau Berkas">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Preview</span>
                                            </button>

                                            {{-- DOWNLOAD FILE ASLI --}}
                                            <a href="{{ $downloadRoute }}" 
                                               class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-xs rounded-lg transition-all"
                                               title="Unduh File Asli">
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

@include('admin.archives.preview')
</x-app-layout>