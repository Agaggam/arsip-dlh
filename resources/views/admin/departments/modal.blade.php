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

    {{-- MODAL EDIT DEPARTEMEN (diperbaiki action) --}}
    <x-modal name="modal-edit-departemen" focusable>
        <div x-data="{ id: null, name: '', description: '' }"
             x-on:set-edit-data.window="id = $event.detail.id; name = $event.detail.name; description = $event.detail.description">
            <form :action="'{{ route('admin.departments.update', ['department' => '__ID__']) }}'.replace('__ID__', id)" method="post" class="p-6">
                @csrf
                @method('PATCH')
                <h2 class="text-lg font-bold text-slate-800 mb-4">Edit Departemen</h2>
                <div class="space-y-4">
                    <div>
                        <x-input-label for="edit_name" value="Nama Departemen" class="text-xs font-bold uppercase text-slate-500" />
                        <x-text-input id="edit_name" name="name" type="text" x-model="name" class="mt-1 block w-full rounded-xl" required />
                    </div>
                    <div>
                        <x-input-label for="edit_description" value="Deskripsi / Keterangan" class="text-xs font-bold uppercase text-slate-500" />
                        <textarea id="edit_description" name="description" rows="3" x-model="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Tambahkan keterangan singkat..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
                    <x-primary-button class="bg-indigo-600 rounded-xl shadow-indigo-200 shadow-lg">Update Departemen</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Modal Konfirmasi Hapus --}}
    <x-confirm-modal id="confirm-dept-delete" type="danger" />