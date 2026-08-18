<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Arsip') }}
            </h2>
        </div>
    </x-slot>

    {{-- GRID SEKARANG MENGGUNAKAN 4 KOLOM PADA UKURAN MD --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- CARD 1: Berkas Tersedia --}}
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Berkas (Aktif)</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalAktif) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-600 font-medium">Berkas Tersedia Di Storage</span>
                </div>
            </div>
        </div>

        {{-- CARD 2: Berkas Hilang --}}
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Berkas (Hilang)</p>
                        <p class="text-3xl font-extrabold text-rose-600 mt-2">{{ number_format($totalHilang) }}</p>
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

        {{-- CARD 3 & 4 (DIGABUNG): AKSI UNGHAH ARSIP LEBIH PANJANG --}}
        <div class="md:col-span-2 relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10 h-full flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                
                {{-- Bagian Teks Info Kiri --}}
                <div class="max-w-md">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi Pengunggahan Berkas</p>
                    <p class="text-xs text-gray-400 mt-1">Gunakan tombol aksi di samping untuk menambahkan berkas arsip baru ke dalam sistem, baik secara tunggal maupun massal (*.zip).</p>
                </div>
                
                {{-- Bagian Tombol Aksi Kanan --}}
                <div class="flex items-center gap-3 flex-1 w-full md:w-auto md:justify-end">
                    {{-- TOMBOL SINKRON BULK ZIP --}}
                    <button type="button" x-data x-on:click="$dispatch('open-modal', { id: 'modal-tambah-zip' })" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap flex-1 md:flex-initial">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5M5 19v-4a2 2 0 012-2h11a2 2 0 012 2v4m-11-4V9m0 0l3-3m-3 3L9 6" />
                        </svg>
                        <span>Unggah Via ZIP</span>
                    </button>
            
                    {{-- Tombol Unggah Arsip Tunggal --}}
                    <button type="button" x-data x-on:click="$dispatch('open-modal', { id: 'modal-tambah-arsip' })" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap flex-1 md:flex-initial">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Unggah Arsip Baru</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA DIBUNGKUS OLEH KOMPONEN GLOBAL <x-table> --}}
    <x-table :isEmpty="$archives->isEmpty()" emptyMessage="Belum ada arsip yang diunggah atau tidak sesuai dengan filter.">
        
        {{-- Slot Judul Tabel --}}
        <x-slot name="title">
            Daftar Arsip Aktif
        </x-slot>

        {{-- Slot Search & Filter Bar Dropdown --}}
        <x-slot name="actions">
                <x-search-input 
                    route="{{ route('admin.archives.index') }}" 
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
                            'sort_download' => [
                                'label' => 'Urutan Unduhan',
                                'options' => [
                                    'terbanyak' => '🔥 UNDUHAN TERBANYAK',
                                    'tersedikit' => '📉 UNDUHAN TERSEDIKIT'
                                ]
                            ],
                            'month' => [
                                'label' => 'Bulan',
                                'options' => $months
                            ],
                            'year' => [
                                'label' => 'Tahun',
                                'options' => $years->mapWithKeys(fn($y) => [$y => $y])->toArray()
                            ]
                        ]
                    "
                />

                {{-- Filter Rentang Tanggal Tambahan --}}
                <form action="{{ route('admin.archives.index') }}" method="GET" class="flex items-center gap-2 mt-2 md:mt-0">
                    @foreach(request()->except(['date_from', 'date_to', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-600">
                        <span class="font-semibold text-slate-500">Tgl:</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-transparent border-0 p-0 text-xs focus:ring-0">
                        <span>s/d</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-transparent border-0 p-0 text-xs focus:ring-0">
                        <button type="submit" class="bg-indigo-600 text-white px-2 py-1 rounded-lg text-xs font-semibold hover:bg-indigo-700">Filter</button>
                    </div>
                </form>
        </x-slot>

        {{-- Slot Kolom Header Tabel (Thead) --}}
        <x-slot name="thead">
            <th class="px-6 py-4 text-left">Dokumen</th>
            <th class="px-6 py-4 text-left">Kategori</th>
            <th class="px-6 py-4 text-left">Bidang</th>
            <th class="px-6 py-4 text-left">Dibuat</th>
            <th class="px-6 py-4 text-left">Pengunggah</th>
            <th class="px-6 py-4 text-center">Hits</th>
            <th class="px-6 py-4 text-center">Aksi</th>
        </x-slot>

        {{-- Slot Konten Data Baris Tabel (Tbody) --}}
        <x-slot name="tbody">
            @foreach($archives as $archive)
                @php
                    $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($archive->file_path);
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
                    
                    $downloadRoute = route('admin.archives.download', $archive);
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
                            <span class="text-xs font-bold text-slate-700">{{ $archive->user->name ?? 'User Terhapus' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-slate-700">{{ $archive->download_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-1">
                            {{-- BUTTON SALIN TOKEN AKSES --}}
                            <div x-data="{ 
                                copied: false,
                                copyToken() {
                                    const textToCopy = '{{ $archive->hash_token ?? $archive->token ?? '' }}';
                                    if (!textToCopy) return;
                                    
                                    if (navigator.clipboard && window.isSecureContext) {
                                        navigator.clipboard.writeText(textToCopy).then(() => {
                                            this.copied = true;
                                            setTimeout(() => this.copied = false, 1500);
                                        });
                                    } else {
                                        let textArea = document.createElement('textarea');
                                        textArea.value = textToCopy;
                                        textArea.style.position = 'fixed';
                                        textArea.style.left = '-999999px';
                                        document.body.appendChild(textArea);
                                        textArea.focus();
                                        textArea.select();
                                        try {
                                            document.execCommand('copy');
                                            this.copied = true;
                                            setTimeout(() => this.copied = false, 1500);
                                        } catch (error) {
                                            console.error('Gagal menyalin token', error);
                                        }
                                        document.body.removeChild(textArea);
                                    }
                                }
                            }" class="relative flex items-center">
                                <button type="button" 
                                        x-on:click="copyToken()" 
                                        class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all" 
                                        title="Salin Token Akses">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                </button>
                                
                                <div x-show="copied" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-1"
                                     class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 z-30 px-2.5 py-1 text-[10px] font-bold text-white bg-slate-800 rounded-lg shadow-sm whitespace-nowrap"
                                     x-cloak>
                                    Token Disalin!
                                </div>
                            </div>

                            {{-- BUTTON DOWNLOAD --}}
                            <a href="{{ $downloadRoute }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" title="Download">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>

                            {{-- BUTTON PREVIEW --}}
                            <button type="button" 
                                    x-data 
                                    x-on:click="$dispatch('open-modal', { 
                                        id: 'preview-modal', 
                                        title: 'Preview: {{ addslashes($archive->title) }}', 
                                        fileUrl: '{{ route('admin.archives.preview', $archive->hash_token) }}', 
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

                            {{-- BUTTON EDIT / UPDATE --}}
                            <button type="button" 
                                    x-data
                                    x-on:click="$dispatch('open-modal', { 
                                        id: 'modal-edit-arsip', 
                                        action: '{{ route('admin.archives.update', $archive) }}',
                                        title: '{{ addslashes($archive->title) }}',
                                        category_id: '{{ $archive->category_id }}'
                                    })"
                                    class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                                    title="Edit Arsip">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            {{-- BUTTON DESTROY --}}
                            <button type="button" 
                                    x-data 
                                    x-on:click="$dispatch('open-modal', { 
                                        id: 'confirm-delete', 
                                        action: '{{ route('admin.archives.destroy', $archive) }}', 
                                        title: 'Pindahkan ke Tempat Sampah?', 
                                        warning: 'Anda akan menonaktifkan arsip: {{ addslashes($archive->title) }}. Berkas ini akan dipindahkan ke tempat sampah.', 
                                        method: 'DELETE', 
                                        withPassword: false,
                                        withReason: true
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
            @endforeach
        </x-slot>
    </x-table>

    {{-- Link Pagination diletakkan rapi di luar komponen tabel --}}
    @if($archives->hasPages())
        <div class="mt-6">
            {{ $archives->links() }}
        </div>
    @endif

    @include('admin.archives.preview')
    @include('admin.archives.modal')
</x-app-layout>