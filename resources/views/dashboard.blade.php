<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-12">
        
        {{-- Form Pencarian Simpel (Responsif) --}}
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-[1.25rem] sm:rounded-[1.5rem] p-4 sm:p-6 mb-6">
            <div class="max-w-xl mx-auto text-center">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1 sm:mb-2">Search File Via Token</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mb-4 px-2">File hanya akan muncul jika Anda memasukkan kode token pengaman yang valid.</p>
                
                {{-- Form berubah menjadi kolom pada mobile, row pada tablet ke atas --}}
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Masukkan kode..." 
                        class="w-full text-sm border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm px-4 py-2.5"
                        required
                    >
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center shrink-0">
                            Cari Berkas
                        </button>
                        @if(request('search'))
                            <a href="{{ route('dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold py-2.5 px-3.5 rounded-xl transition-all flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Hasil Pencarian Berbentuk Card (Sangat Responsif) --}}
        <div class="space-y-4">
            @forelse($archives as $archive)
                @php
                    $ext = strtolower($archive->file_type);
                    $bgClass = 'bg-slate-100 text-slate-600'; 
                    if(in_array($ext, ['pdf'])) $bgClass = 'bg-red-100 text-red-600';
                    elseif(in_array($ext, ['xlsx', 'xls', 'csv'])) $bgClass = 'bg-emerald-100 text-emerald-600';
                    elseif(in_array($ext, ['doc', 'docx'])) $bgClass = 'bg-blue-100 text-blue-600';
                    elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $bgClass = 'bg-purple-100 text-purple-600';
                    
                    $downloadRoute = route('admin.archives.download', $archive); 
                @endphp

                {{-- Tampilan Card Hasil Berkas --}}
                <div class="bg-white border border-slate-100 rounded-[1.25rem] sm:rounded-[1.5rem] p-4 sm:p-6 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 md:gap-6 group hover:shadow-md transition-all">
                    
                    {{-- Bagian Info File (Icon + Deskripsi) --}}
                    <div class="flex items-center gap-4 min-w-0">
                        {{-- Badge Ekstensi File Besar --}}
                        <div class="h-14 w-14 sm:h-16 sm:w-16 {{ $bgClass }} rounded-xl sm:rounded-2xl flex flex-col items-center justify-center shadow-inner tracking-wide font-black uppercase text-[10px] sm:text-xs shrink-0">
                            <span>{{ $archive->file_type }}</span>
                        </div>
                        
                        {{-- Informasi Arsip (Gunakan min-w-0 dan truncate agar teks panjang tidak merusak layout) --}}
                        <div class="min-w-0 flex-1">
                            <span class="inline-block px-2 py-0.5 text-[9px] sm:text-[10px] font-extrabold rounded-md border bg-slate-50 text-slate-600 border-slate-200 uppercase tracking-wider mb-1">
                                {{ $archive->category->name ?? 'N/A' }}
                            </span>
                            <h4 class="text-sm sm:text-base font-bold text-slate-800 leading-snug group-hover:text-indigo-600 transition-colors truncate break-all" title="{{ $archive->title }}">
                                {{ $archive->title }}
                            </h4>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5 text-[11px] sm:text-xs font-medium text-slate-400">
                                <span class="whitespace-nowrap">
                                    Ukuran: <strong class="text-slate-600 font-semibold">{{ $archive->file_size }}</strong>
                                </span>
                                <span class="hidden sm:inline">•</span>
                                <span class="whitespace-nowrap">{{ \Carbon\Carbon::parse($archive->archive_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Tindakan / Aksi (Sejajar horizontal di mobile, rapi di kanan saat tablet/laptop) --}}
                    <div class="flex items-center gap-2 w-full md:w-auto border-t md:border-t-0 pt-3 md:pt-0 justify-end shrink-0">
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
                                class="flex-1 md:flex-none flex items-center justify-center gap-1.5 px-3.5 py-2.5 md:py-2 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 font-bold text-xs rounded-xl transition-all" 
                                title="Pratinjau Berkas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Pratinjau</span>
                        </button>
                        
                        {{-- DOWNLOAD --}}
                        <a href="{{ $downloadRoute }}" class="flex-1 md:flex-none flex items-center justify-center gap-1.5 px-3.5 py-2.5 md:py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition-all" title="Unduh Berkas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Unduh File</span>
                        </a>
                    </div>
                </div>

            @empty
                {{-- State Kosong / Belum Cari --}}
                <div class="bg-white border border-dashed border-slate-200 rounded-[1.25rem] sm:rounded-[1.5rem] p-8 sm:p-12 text-center shadow-sm">
                    <div class="h-11 w-11 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium italic max-w-md mx-auto leading-relaxed">
                        {{ request('search') ? 'Maaf, berkas tidak ditemukan. Pastikan token yang di masukkan sudah benar.' : 'Silakan masukkan kode token akses di atas untuk menampilkan berkas dengan cepat.' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

{{-- MODAL PREVIEW DOKUMEN --}}
@include('admin.archives.preview')

</x-app-layout>