{{-- File: resources/views/admin/archives/reason.blade.php --}}
<x-modal name="view-delete-reason" focusable>
    <div class="p-6">
        {{-- Header Modal --}}
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Alasan Arsip Di Non-Aktifkan / Dihapus Sementara
            </h3>
            <button x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Konten Utama (Reactive via Alpine.js) --}}
        <div x-data="{ title: '', reason: '' }" 
             x-on:open-modal.window="if ($event.detail.id === 'view-delete-reason') { title = $event.detail.title; reason = $event.detail.reason; }">
            
            <div class="space-y-4">
                {{-- Nama Dokumen --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Arsip</label>
                    <div class="text-sm font-semibold text-slate-700 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-100" x-text="title"></div>
                </div>

                {{-- Detail Alasan Penghapusan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Alasan Dihapus / Non-Aktif</label>
                    <div class="text-sm text-slate-600 bg-amber-50/40 border border-amber-100/70 p-4 rounded-xl min-h-[80px] whitespace-pre-line italic" 
                         x-text="reason || 'Tidak ada alasan spesifik yang dicantumkan.'">
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl px-5">
                {{ __('Tutup') }}
            </x-secondary-button>
        </div>
    </div>
</x-modal>