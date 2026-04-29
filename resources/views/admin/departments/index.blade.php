<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Departemen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[1.5rem] p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Daftar Departemen</h3>
                        <p class="text-xs text-slate-500">Total: {{ $departments->count() }} Departemen</p>
                    </div>

                    <button 
                        type="button"
                        x-data="" 
                        x-on:click="$dispatch('open-modal', 'modal-tambah-departemen')" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Departemen
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Departemen</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Anggota</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Arsip</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($departments as $dept)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-800">{{ $dept->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ Str::limit($dept->description, 50) ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-3 py-1 rounded-full border border-indigo-100">
                                        {{ $dept->users_count }} User
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-3 py-1 rounded-full border border-amber-100">
                                        {{ $dept->archives_count ?? 0 }} Arsip
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        @if($dept->name !== 'System')
                                        <button 
                                            type="button"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', { 
                                                id: 'confirm-dept-delete', 
                                                action: '{{ route('admin.departments.destroy', $dept) }}',
                                                title: 'Hapus data {{ addslashes($dept->name) }}?',
                                                warning: 'Seluruh akun User dan Arsip Dokumen terkait akan dihapus secara permanen!',
                                                withPassword: true 
                                            })"
                                            class="text-rose-500 hover:text-rose-700 p-2 hover:bg-rose-50 rounded-xl transition-all"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

{{-- MODAL TAMBAH DEPARTEMEN --}}
<x-modal name="modal-tambah-departemen" focusable>
    <form method="post" action="{{ route('admin.departments.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-bold text-slate-800 mb-4">Tambah Departemen Baru</h2>
        <div class="space-y-4">
            <div>
                <x-input-label for="name" value="Nama Departemen" class="text-xs font-bold uppercase text-slate-500" />
                <x-text-input id="name" name="name" type="text" autocomplete="off" class="mt-1 block w-full rounded-xl" placeholder="Contoh: Bidang Infrastruktur" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="description" value="Deskripsi / Keterangan" class="text-xs font-bold uppercase text-slate-500" />
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Tambahkan keterangan singkat..."></textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
            <x-primary-button class="bg-indigo-600 rounded-xl shadow-indigo-200 shadow-lg">Simpan Departemen</x-primary-button>
        </div>
    </form>
</x-modal>

    {{-- Modal Konfirmasi Hapus --}}
    <x-confirm-modal id="confirm-dept-delete" type="danger" />
</x-app-layout>