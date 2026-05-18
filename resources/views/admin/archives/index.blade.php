<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Arsip') }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800">Daftar Arsip Aktif</h3>
            <div class="flex gap-2">
                <button id="bulkDeleteBtn" type="button" class="hidden bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus Terpilih (<span id="selectedCount">0</span>)
                </button>
                <button type="button" x-data x-on:click="$dispatch('open-modal', { id: 'modal-tambah-arsip' })" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Unggah Arsip
                </button>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="flex items-center justify-end mb-6">
            <x-search-input 
                route="{{ route('admin.archives.index') }}" 
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
                        'label' => 'Tipe Dokumen',
                        'options' => $fileTypes->mapWithKeys(fn($type) => [strtolower($type) => strtoupper($type)])->toArray()
                    ],
                ] + (Auth::user()->isPureSuperAdmin() ? [
                    'department_id' => [
                        'label' => 'Departemen',
                        'options' => $departments->pluck('name', 'id')->toArray()
                    ]
                ] : [])"
            />
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                        <th class="px-6 py-4 text-left">Dokumen</th>
                        <th class="px-6 py-4 text-left">Kategori</th>
                        <th class="px-6 py-4 text-left">Bidang / Departemen</th>
                        <th class="px-6 py-4 text-left">Dibuat</th>
                        <th class="px-6 py-4 text-left">Pengunggah</th>
                        <th class="px-6 py-4 text-center">Hits</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
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
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
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
                                {{ $archive->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-semibold text-slate-600">
                                {{ $archive->category->department->name ?? 'UMUM / SISTEM' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-slate-500">
                            {{ \Carbon\Carbon::parse($archive->archive_date)->format('d M Y, H:i') }}
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
                                <button type="button" x-data x-on:click="$dispatch('open-modal', { id: 'preview-modal', title: 'Preview: {{ addslashes($archive->title) }}', fileUrl: '{{ asset('storage/' . $archive->file_path) }}', fileType: '{{ strtolower($archive->file_type) }}', downloadUrl: '{{ $downloadRoute }}' })" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Pratinjau">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <a href="{{ $downloadRoute }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" title="Download">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                <button type="button" x-data x-on:click="$dispatch('open-modal', { id: 'confirm-delete', action: '{{ route('admin.archives.destroy', $archive) }}', title: 'Hapus Arsip Permanent?', warning: 'Anda akan menghapus arsip: {{ addslashes($archive->title) }}. Tindakan ini tidak dapat dibatalkan!', method: 'DELETE', withPassword: true })" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all active:scale-90" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-20 text-center text-slate-400 italic text-sm">Belum ada arsip yang diunggah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Links --}}
    @if($archives->hasPages())
        <div class="mt-6">
            {{ $archives->links() }}
        </div>
    @endif

    @include('admin.archives.modal')
</x-app-layout>