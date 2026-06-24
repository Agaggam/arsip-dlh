<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tong Sampah Arsip') }}
            </h2>
        </div>
    </x-slot>

    {{-- --- CARD STATISTIK TRASH --- --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group border border-gray-100">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Berkas (Non-aktif)</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalTrash) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-100 text-amber-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-amber-600 font-medium">Berkas Tersedia Di Storage</span>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group border border-gray-100">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Berkas (Hilang)</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalFileHilang) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-rose-100 text-rose-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-rose-600 font-medium">Berkas Tidak Ditemukan Di Storage</span>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group border border-gray-100">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Dengan Alasan</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($withReason) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-indigo-600 font-medium">Arsip Yang Dihapus Memiliki Alasan</span>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group border border-gray-100">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tanpa Alasan</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($withoutReason) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-100 text-slate-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-slate-600 font-medium">Arsip Yang Dihapus Tidak Melampirkan Alasan</span>
                </div>
            </div>
        </div>
    </div>

    {{-- --- TABEL DATA & KOMPONEN FILTER BAR --- --}}
    {{-- Menambahkan kelas relative dan z-10 pada pembungkus utama --}}
    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-[1.5rem] p-6 relative z-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 relative z-20">
            <h3 class="text-lg font-bold text-slate-800">Daftar Arsip Non-Aktif</h3>
            
{{-- Bagian pembungkus Search Input diberikan relative z-50 --}}
<div class="flex items-center justify-end w-full md:w-auto relative z-50">
    <x-search-input 
        route="{{ route('admin.archives.trash') }}" 
        placeholder="Cari judul arsip..."
        searchParam="search"
        buttonText="Cari"
        :resetButton="true"
        :filters="
            (Auth::user()->isPureSuperAdmin() ? [
                'department_id' => [
                    'label' => 'Departemen',
                    'fullWidth' => true,
                    'options' => $departments->pluck('name', 'id')->toArray()
                ]
            ] : []) + [
                'category_id' => [
                    'label' => 'Kategori',
                    'options' => $categories->mapWithKeys(fn($category) => [
                        $category->id => (Auth::user()->isPureSuperAdmin() && $category->department) 
                            ? $category->name . ' (' . $category->department->name . ')' 
                            : $category->name
                    ])->toArray()
                ],
                'file_type' => [
                    'label' => 'Tipe Dokumen',
                    'options' => $fileTypes->mapWithKeys(fn($type) => [strtolower($type) => strtoupper($type)])->toArray()
                ],
                'status_file' => [
                    'label' => 'Status Berkas',
                    'options' => [
                        'tersedia' => '✔️ TERSEDIA',
                        'hilang' => '❌ HILANG'
                    ]
                ],
                'filter_reason' => [
                    'label' => 'Keterangan Alasan',
                    'options' => [
                        'dengan_alasan' => '📄 DENGAN ALASAN',
                        'tanpa_alasan' => '📭 TANPA ALASAN'
                    ]
                ],
                'month' => [
                    'label' => 'Bulan',
                    'options' => $months
                ],
                'year' => [
                    'label' => 'Tahun',
                    'options' => $years->mapWithKeys(fn($yr) => [(string)$yr => (string)$yr])->toArray()
                ]
            ]
        "
    />
</div>
        </div>

        {{-- Menambahkan pt-4 (padding top) untuk memberi jarak jika dropdown mengarah ke bawah --}}
        <div class="overflow-x-auto relative z-10 pt-4">
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
                        @php
                            $fileExists = $archive->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($archive->file_path);
                            $ext = strtolower($archive->file_type);
                            
                            if (!$fileExists) {
                                $bgClass = 'bg-gray-100 text-gray-400 border border-gray-200';
                            } else {
                                $bgClass = 'bg-slate-100 text-slate-600'; 
                                if(in_array($ext, ['pdf'])) $bgClass = 'bg-red-100 text-red-600';
                                elseif(in_array($ext, ['xlsx', 'xls', 'csv'])) $bgClass = 'bg-emerald-100 text-emerald-600';
                                elseif(in_array($ext, ['doc', 'docx'])) $bgClass = 'bg-blue-100 text-blue-600';
                                elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $bgClass = 'bg-purple-100 text-purple-600';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 {{ $bgClass }} rounded-xl flex items-center justify-center font-bold uppercase text-[10px] select-none">
                                        {{ $archive->file_type }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold {{ !$fileExists ? 'line-through text-gray-400' : 'text-slate-800' }}">
                                            {{ $archive->title }}
                                            @if(!$fileExists)
                                                <span class="text-[11px] text-red-500 font-bold tracking-normal normal-case ml-1 bg-red-50 px-1.5 py-0.5 rounded-md border border-red-100 inline-block">
                                                    [File Hilang]
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">
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
                                <div class="flex justify-center items-center gap-3">
                                    <button type="button" 
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', { 
                                            id: 'view-delete-reason', 
                                            title: '{{ addslashes($archive->title) }}', 
                                            reason: '{{ addslashes($archive->delete_reason ?? __('Tidak dicantumkan alasan spesifik.')) }}' 
                                        })"
                                        class="text-[11px] text-amber-600 hover:text-amber-700 font-bold underline block">
                                        Lihat Alasan Dihapus
                                    </button>
                                    
                                    {{-- Tombol Restore --}}
                                    <button 
                                        type="button"
                                        x-data="" 
                                        x-on:click="$dispatch('open-modal', { 
                                            id: 'confirm-restore', 
                                            action: '{{ route('admin.archives.restore', $archive->id) }}',
                                            method: 'POST',
                                            title: 'Pulihkan Arsip?',
                                            warning: '{{ !$fileExists ? "Peringatan: Berkas fisik tidak ditemukan di storage. " : "" }}Arsip &quot;{{ addslashes($archive->title) }}&quot; akan dikembalikan ke daftar arsip aktif.',
                                            withPassword: false
                                        })"
                                        class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-xl transition-all active:scale-90" 
                                        title="Pulihkan Arsip">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    
                                    {{-- Tombol Hapus Permanen --}}
                                    <button 
                                        type="button"
                                        x-data="" 
                                        x-on:click="$dispatch('open-modal', { 
                                            id: 'confirm-delete', 
                                            action: '{{ route('admin.archives.force-delete', $archive->id) }}',
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
                            <td colspan="5" class="px-6 py-40 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="text-slate-400 italic text-sm font-medium">Tong sampah kosong atau tidak ada arsip yang cocok dengan filter.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-4">
            {{ $archives->links() }}
        </div>
    </div>

    {{-- MODAL HIJAU (RESTORE) --}}
    <x-confirm-modal id="confirm-restore" method="POST" type="success" />

    {{-- MODAL MERAH (FORCE DELETE) --}}
    <x-confirm-modal id="confirm-delete" method="DELETE" type="danger" />

    @include('admin.archives.reason')
</x-app-layout>