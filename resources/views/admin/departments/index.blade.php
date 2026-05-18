<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Departemen') }}
        </h2>
    </x-slot>

    @include('admin.departments.tips')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-[1.5rem] p-4 sm:p-6 border border-gray-100">
        {{-- Header Responsive --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Departemen</h3>
                <p class="text-xs text-slate-500">Total: {{ $departments->count() }} Departemen</p>
            </div>

            <button type="button" x-data 
                x-on:click="$dispatch('open-modal', 'modal-tambah-departemen')" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Departemen
            </button>
        </div>

        {{-- Tabel Responsive --}}
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Departemen</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Anggota</th>
                            <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Arsip</th>
                            <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($departments as $dept)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-800">{{ $dept->name }}</div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-sm text-slate-500 max-w-xs truncate">
                                {{ Str::limit($dept->description, 40) ?? '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-center whitespace-nowrap">
                                <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded-full border border-indigo-100">
                                    {{ $dept->users_count }} User
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-center whitespace-nowrap">
                                <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full border border-green-100">
                                    {{ $dept->categories_count }} Kategori
                                </span>
                            <td class="px-4 sm:px-6 py-3 text-center whitespace-nowrap">
                                <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full border border-amber-100">
                                    {{ $dept->archives_count ?? 0 }} Arsip
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-center whitespace-nowrap">
                                <div class="flex justify-center gap-1 sm:gap-2">
                                    @if($dept->name !== 'System')
                                        {{-- Tombol Edit --}}
                                        <button type="button" x-data
                                            x-on:click="
                                                $dispatch('open-modal', 'modal-edit-departemen');
                                                $dispatch('set-edit-data', {
                                                    id: {{ $dept->id }},
                                                    name: '{{ addslashes($dept->name) }}',
                                                    description: '{{ addslashes($dept->description) }}'
                                                })
                                            "
                                            class="text-amber-500 hover:text-amber-700 p-1.5 sm:p-2 hover:bg-amber-50 rounded-xl transition-all">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <button type="button" x-data
                                            x-on:click.prevent="$dispatch('open-modal', { 
                                                id: 'confirm-dept-delete', 
                                                action: '{{ route('admin.departments.destroy', $dept) }}',
                                                title: 'Hapus data {{ addslashes($dept->name) }}?',
                                                warning: 'Seluruh akun User dan Arsip Dokumen terkait akan dihapus secara permanen!',
                                                withPassword: true 
                                            })"
                                            class="text-rose-500 hover:text-rose-700 p-1.5 sm:p-2 hover:bg-rose-50 rounded-xl transition-all">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
    
    @include('admin.departments.modal')
</x-app-layout>