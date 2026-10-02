<section class="space-y-6">
    <header>
        <div class="flex items-center gap-3 mb-2">
            <h2 class="text-lg font-bold text-slate-800">
                {{ __('Hapus Akun') }}
            </h2>
            <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-black uppercase rounded-md tracking-wider">
                Permanen
            </span>
        </div>

        <p class="text-sm text-slate-500 leading-relaxed max-w-2xl">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum melanjutkan, harap cadangkan data penting Anda.') }}
        </p>
    </header>

    <div class="pt-2">
        {{-- Tombol Pemicu Modal --}}
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', { 
                id: 'confirm-deletion-profile', 
                title: 'Konfirmasi Penghapusan Akun',
                action: '{{ route('profile.destroy') }}',
                method: 'DELETE',
                withPassword: false,
                warning: 'Tindakan ini permanen. Seluruh data profil dan akses Anda akan dihapus dari sistem.'
            })"
            class="rounded-2xl px-8 py-3.5 shadow-xl shadow-red-200 hover:shadow-red-300 transition-all active:scale-95 flex items-center gap-2 group"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            {{ __('Ya, Hapus Akun Saya') }}
        </x-danger-button>
    </div>
    
    <x-confirm-modal id="confirm-deletion-profile" type="danger" />
</section>