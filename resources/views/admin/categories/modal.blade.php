    {{-- MODAL TAMBAH KATEGORI --}}
    <x-modal name="modal-tambah-kategori" focusable>
        <form method="post" action="{{ route('categories.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-slate-800 mb-4">Buat Kategori Baru</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="Nama Kategori" class="text-xs font-bold uppercase text-slate-500" />
                    <x-text-input id="name" name="name" type="text" autocomplete="off" class="mt-1 block w-full rounded-xl" placeholder="Misal: Laporan Keuangan" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Deskripsi" class="text-xs font-bold uppercase text-slate-500" />
                    <textarea name="description" id="description" rows="3" autocomplete="off" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Berikan sedikit konteks tentang kategori ini..."></textarea>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Unit Kerja Terdeteksi</p>
                    <p class="text-sm font-bold text-indigo-700">{{ Auth::user()->department->name }}</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
                <x-primary-button class="bg-indigo-600 rounded-xl">Simpan Kategori</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL EDIT KATEGORI --}}
    <x-modal name="modal-edit-kategori" focusable>
        <div x-data="{ id: null, name: '', description: '' }"
             x-on:set-edit-data.window="id = $event.detail.id; name = $event.detail.name; description = $event.detail.description">
            <form :action="'{{ route('categories.update', ['category' => '__ID__']) }}'.replace('__ID__', id)" method="post" class="p-6">
                @csrf
                @method('PATCH')
                <h2 class="text-lg font-bold text-slate-800 mb-4">Edit Kategori</h2>
                <div class="space-y-4">
                    <div>
                        <x-input-label for="edit_name" value="Nama Kategori" class="text-xs font-bold uppercase text-slate-500" />
                        <x-text-input id="edit_name" name="name" type="text" x-model="name" class="mt-1 block w-full rounded-xl" required />
                    </div>
                    <div>
                        <x-input-label for="edit_description" value="Deskripsi" class="text-xs font-bold uppercase text-slate-500" />
                        <textarea id="edit_description" name="description" rows="3" x-model="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Berikan sedikit konteks tentang kategori ini..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
                    <x-primary-button class="bg-indigo-600 rounded-xl">Update Kategori</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

{{-- MODAL KONFIRMASI HAPUS --}}
<x-confirm-modal id="confirm-delete" type="danger" />