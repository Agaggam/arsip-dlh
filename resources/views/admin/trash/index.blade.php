<x-app-layout>
@php
    $title = 'Tong Sampah Universal';
    $user = Auth::user();
@endphp

    <x-slot name="header">
        Tong Sampah Universal
    </x-slot>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tong Sampah</h1>
            <p class="text-sm text-slate-500 mt-1">Data yang dihapus (soft-delete) akan masuk ke sini sebelum dihapus permanen.</p>
        </div>
    </div>

    <!-- BANNER KEBIJAKAN RETENSI 30 HARI -->
    <div class="bg-amber-50/90 border border-amber-200/90 rounded-2xl p-4 flex items-start gap-3.5 text-amber-900 shadow-sm">
        <div class="p-2 bg-amber-100 rounded-xl text-amber-700 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="text-sm space-y-1">
            <p class="font-semibold text-amber-900">Kebijakan Pembersihan Otomatis 30 Hari</p>
            <p class="text-amber-800/90 leading-relaxed text-xs">
                Data di tong sampah disimpan sementara selama <strong>30 hari</strong> sejak tanggal penghapusan. 
                Setelah lewat 30 hari, sistem akan menghapus data dan berkas fisik terkait secara <strong>permanen</strong> otomatis agar tidak membebani ruang penyimpanan basis data.
            </p>
        </div>
    </div>

    <!-- TABS -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-2 flex flex-wrap gap-2">
        <a href="{{ route('admin.trash.index', ['type' => 'archive']) }}" 
           class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $type === 'archive' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50' }}">
           📄 Arsip Terhapus
        </a>
        <a href="{{ route('admin.trash.index', ['type' => 'survey']) }}" 
           class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $type === 'survey' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50' }}">
           💰 Usulan Harga
        </a>
        <a href="{{ route('admin.trash.index', ['type' => 'pengawasan']) }}" 
           class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $type === 'pengawasan' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50' }}">
           🔍 Pengawasan
        </a>
    </div>

    <!-- CONTENT -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <form method="GET" class="flex gap-4">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl transition-all" 
                           placeholder="Cari data terhapus...">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-2xl transition-all">
                    Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        @if($type === 'archive')
                            <th class="px-6 py-4">JUDUL ARSIP</th>
                            <th class="px-6 py-4">KATEGORI</th>
                            <th class="px-6 py-4">TANGGAL HAPUS</th>
                            <th class="px-6 py-4">SISA WAKTU</th>
                            <th class="px-6 py-4 text-right">AKSI</th>
                        @elseif($type === 'survey')
                            <th class="px-6 py-4">NAMA BARANG</th>
                            <th class="px-6 py-4">SPESIFIKASI</th>
                            <th class="px-6 py-4">TANGGAL HAPUS</th>
                            <th class="px-6 py-4">SISA WAKTU</th>
                            <th class="px-6 py-4 text-right">AKSI</th>
                        @elseif($type === 'pengawasan')
                            <th class="px-6 py-4">NAMA USAHA</th>
                            <th class="px-6 py-4">KECAMATAN / JENIS</th>
                            <th class="px-6 py-4">TANGGAL HAPUS</th>
                            <th class="px-6 py-4">SISA WAKTU</th>
                            <th class="px-6 py-4 text-right">AKSI</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $items = ${$type.'s'}; @endphp
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        @if($type === 'archive')
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $item->title }}</td>
                            <td class="px-6 py-4"><span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-semibold">{{ $item->category->name ?? '-' }}</span></td>
                        @elseif($type === 'survey')
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $item->judul }}</td>
                            <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 mr-2">{{ $item->kelompok }}</span> {{ Str::limit($item->spesifikasi_singkat, 40) }}</td>
                        @elseif($type === 'pengawasan')
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $item->nama_usaha }}</td>
                            <td class="px-6 py-4">{{ $item->kecamatan }} ({{ $item->jenis_label }})</td>
                        @endif
                        
                        <td class="px-6 py-4 text-slate-600">{{ $item->deleted_at->translatedFormat('d M Y, H:i') }}</td>
                        
                        {{-- Kolom Sisa Waktu Retensi --}}
                        <td class="px-6 py-4">
                            @php
                                $daysLeft = max(0, 30 - (int) $item->deleted_at->diffInDays(now()));
                            @endphp
                            @if($daysLeft <= 3)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Akan dihapus permanen pada {{ $item->deleted_at->addDays(30)->translatedFormat('d M Y') }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    Sisa {{ $daysLeft }} hari
                                </span>
                            @elseif($daysLeft <= 10)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="Akan dihapus permanen pada {{ $item->deleted_at->addDays(30)->translatedFormat('d M Y') }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Sisa {{ $daysLeft }} hari
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700" title="Akan dihapus permanen pada {{ $item->deleted_at->addDays(30)->translatedFormat('d M Y') }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Sisa {{ $daysLeft }} hari
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Tombol Restore -->
                                <form action="{{ route('admin.trash.restore', ['type' => $type, 'id' => $item->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg font-semibold transition-colors flex items-center gap-2" onclick="return confirm('Kembalikan data ini ke tempat semula?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Restore
                                    </button>
                                </form>

                                <!-- Tombol Hapus Permanen -->
                                <form action="{{ route('admin.trash.force-delete', ['type' => $type, 'id' => $item->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="password" value="skipped_for_simplicity_or_handled_differently">
                                    <button type="button" onclick="confirmForceDelete(this, '{{ $item->title ?? $item->judul ?? $item->nama_usaha ?? $item->name }}')" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg font-semibold transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <p class="text-lg font-medium text-slate-600">Tong Sampah Kosong</p>
                                <p class="text-sm">Tidak ada data {{ $type }} yang terhapus.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($items->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/50">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function confirmForceDelete(btn, itemName) {
    Swal.fire({
        title: 'Verifikasi Password',
        html: `
            <p class="text-sm text-slate-500 mb-4">Menghapus permanen <b>${itemName}</b> tidak bisa dibatalkan. Masukkan password Anda untuk melanjutkan.</p>
            <input type="password" id="swal-input-password" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="Password Anda...">
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Hapus Permanen',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const pwd = document.getElementById('swal-input-password').value;
            if (!pwd) {
                Swal.showValidationMessage('Password wajib diisi!');
            }
            return pwd;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = btn.closest('form');
            form.querySelector('input[name="password"]').value = result.value;
            
            btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-rose-600 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;
            btn.disabled = true;
            form.submit();
        }
    });
}
</script>
</x-app-layout>
