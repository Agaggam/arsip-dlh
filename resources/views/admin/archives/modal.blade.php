{{-- MODAL KONFIRMASI HAPUS SATU ARSIP --}}
<x-confirm-modal id="confirm-delete" method="DELETE" type="danger" />

{{-- MODAL TAMBAH ARSIP dengan Datepicker Flowbite --}}
<x-modal name="modal-tambah-arsip" focusable>
    <form method="post" action="{{ route('admin.archives.store') }}" class="p-6" enctype="multipart/form-data" id="uploadForm">
        @csrf
        <h2 class="text-lg font-bold text-slate-800 mb-4">Unggah Arsip Baru</h2>
        <div class="space-y-4">
            <div>
                <x-input-label for="title" value="Judul Arsip" class="text-xs font-bold uppercase text-slate-500" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full rounded-xl" placeholder="Masukkan judul dokumen" required />
            </div>
            <div>
                <x-input-label for="category_id" value="Kategori" class="text-xs font-bold uppercase text-slate-500" />
                <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">
                            @if(Auth::user()->isPureSuperAdmin())
                                {{ $cat->name }} ({{ $cat->department->name ?? 'System' }})
                            @else
                                {{ $cat->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Datepicker dengan ikon kalender --}}
            <div>
                <x-input-label for="archive_date" value="Tanggal Arsip (Opsional)" class="text-xs font-bold uppercase text-slate-500" />
                <div class="relative max-w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z"/>
                        </svg>
                    </div>
                    <input id="archive_date" name="archive_date" type="text" 
                           class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                           placeholder="Pilih tanggal (YYYY-MM-DD)" autocomplete="off">
                </div>
                <p class="text-xs text-slate-400 mt-1">Isi jika arsip ini dibuat pada tanggal tertentu (contoh: arsip lama). <strong>Biarkan kosong untuk menggunakan tanggal hari ini.</strong></p>
            </div>

            {{-- Deskripsi (Opsional) --}}
            <div>
                <x-input-label for="description" value="Deskripsi (Opsional)" class="text-xs font-bold uppercase text-slate-500" />
                <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm" placeholder="Tambahkan deskripsi arsip..."></textarea>
            </div>

            {{-- Upload file drag & drop --}}
            <div>
                <x-input-label for="file" value="Pilih File" class="text-xs font-bold uppercase text-slate-500" />
                <div class="mt-1 flex justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-4 transition hover:bg-gray-100 cursor-pointer" 
                     x-data="{ fileName: '', fileExtension: '' }"
                     @click="$refs.fileInput.click()">
                    <div class="text-center w-full">
                        <template x-if="!fileName">
                            <div>
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <div class="mt-2 flex text-sm leading-6 text-gray-600 justify-center">
                                    <span class="rounded-md bg-gray-50 font-semibold text-indigo-600 hover:text-indigo-500">Unggah file</span>
                                    <span class="pl-1">atau seret & lepas ke sini</span>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG, DOCX, XLSX (Max 10MB)</p>
                            </div>
                        </template>
                        <template x-if="fileName">
                            <div>
                                <img :src="'/file-icons/' + fileExtension + '.png'" class="mx-auto h-12 w-12" x-on:error="fileExtension = 'image'">
                                <p class="mt-2 text-sm font-medium text-indigo-600 break-all" x-text="fileName"></p>
                                <p class="text-xs text-gray-400 mt-1">Klik area untuk ganti file</p>
                            </div>
                        </template>
                    </div>
                    <input id="file" name="file" type="file" class="hidden" x-ref="fileInput" 
                           @change="
                               const file = $event.target.files[0];
                               if(file) {
                                   fileName = file.name;
                                   let ext = file.name.split('.').pop().toLowerCase();
                                   if(['pdf'].includes(ext)) fileExtension = 'pdf';
                                   else if(['doc', 'docx'].includes(ext)) fileExtension = 'doc';
                                   else if(['xls', 'xlsx'].includes(ext)) fileExtension = 'xls';
                                   else if(['jpg', 'jpeg', 'png', 'webp'].includes(ext)) fileExtension = 'image';
                                   else fileExtension = 'image';
                               } else {
                                   fileName = '';
                                   fileExtension = '';
                               }
                           " required>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div id="progressContainer" class="mt-6 hidden">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-slate-700">Mengunggah...</span>
                <span class="text-sm font-medium text-slate-700" id="progressPercent">0%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div id="progressBar" class="bg-indigo-600 h-2 rounded-full" style="width: 0%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-2">Harap tunggu, jangan tutup halaman ini.</p>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
            <button type="submit" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4" />
                </svg>
                Simpan Arsip
            </button>
        </div>
    </form>
</x-modal>

{{-- MODAL TAMBAH ARSIP VIA ZIP --}}
<x-modal name="modal-tambah-zip" focusable>
    <form method="post" action="{{ route('admin.archives.store-zip') }}" class="p-6" enctype="multipart/form-data" id="uploadZipForm">
        @csrf
        <h2 class="text-lg font-bold text-slate-800 mb-2">Unggah Masal via ZIP</h2>
        <p class="text-xs text-slate-500 mb-4">Ekstrak otomatis file dokumen di dalam berkas ZIP menjadi arsip individu sesuai kategori pilihan.</p>
        
        <div class="space-y-4">
            <div>
                <x-input-label for="zip_category_id" value="Kategori Target" class="text-xs font-bold uppercase text-slate-500" />
                <select name="category_id" id="zip_category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm">
                    <option value="">-- Otomatis: Belum Dikategorikan --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">
                            @if(Auth::user()->isPureSuperAdmin())
                                {{ $cat->name }} ({{ $cat->department->name ?? 'System' }})
                            @else
                                {{ $cat->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Pilih kategori target, atau <strong>biarkan default</strong> untuk otomatis memasukkannya ke kategori "Belum Dikategorikan" milik departemen Anda.</p>
            </div>

            {{-- Upload file ZIP drag & drop --}}
            <div>
                <x-input-label for="zip_file" value="Pilih Berkas ZIP" class="text-xs font-bold uppercase text-slate-500" />
                <div class="mt-1 flex justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-5 transition hover:bg-gray-100 cursor-pointer" 
                     x-data="{ zipName: '' }"
                     @click="$refs.zipInput.click()">
                    <div class="text-center w-full">
                        <template x-if="!zipName">
                            <div>
                                <svg class="mx-auto h-12 w-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19V4a1 1 0 011-1h5l2 2h6a1 1 0 011 1v13a1 1 0 01-1 1H6a1 1 0 01-1-1z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m-3-3h6" />
                                </svg>
                                <div class="mt-2 flex text-sm leading-6 text-gray-600 justify-center">
                                    <span class="rounded-md bg-gray-50 font-semibold text-indigo-600 hover:text-indigo-500">Unggah berkas ZIP</span>
                                    <span class="pl-1">atau seret ke sini</span>
                                </div>
                                <p class="text-xs text-gray-500">Hanya menerima format berkas kompresi .ZIP (Max 50MB)</p>
                            </div>
                        </template>
                        <template x-if="zipName">
                            <div>
                                <svg class="mx-auto h-12 w-12 text-indigo-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm font-medium text-emerald-600 break-all" x-text="zipName"></p>
                                <p class="text-xs text-gray-400 mt-1">Klik area untuk mengganti berkas ZIP</p>
                            </div>
                        </template>
                    </div>
                    <input id="zip_file" name="zip_file" type="file" class="hidden" x-ref="zipInput" accept=".zip"
                           @change="
                               const file = $event.target.files[0];
                               zipName = file ? file.name : '';
                           " required>
                </div>
            </div>
        </div>

        {{-- Zip Progress Bar --}}
        <div id="zipProgressContainer" class="mt-6 hidden">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-slate-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 animate-spin text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Mengekstrak & Mengunggah...
                </span>
                <span class="text-sm font-medium text-slate-700" id="zipProgressPercent">0%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div id="zipProgressBar" class="bg-indigo-600 h-2 rounded-full" style="width: 0%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-2">Sistem sedang mendekompresi arsip. Jangan menutup tab halaman ini.</p>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
            <button type="submit" id="zipSubmitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                </svg>
                Ekstrak & Simpan
            </button>
        </div>
    </form>
</x-modal>

{{-- MODAL EDIT / UBAH DATA ARSIP (SEKARANG SAMA DENGAN FORM TAMBAH ARSIP) --}}
<x-modal name="modal-edit-arsip" focusable>
    <form method="post" action="" class="p-6" enctype="multipart/form-data" id="editForm"
          x-data="{ 
              actionUrl: '', 
              archiveTitle: '', 
              categoryId: '',
              archiveDate: '',
              archiveDesc: ''
          }"
          {{-- Menangkap payload data data arsip terpilih ketika tombol edit di baris tabel diklik --}}
          @open-modal.window="
              if ($event.detail.id === 'modal-edit-arsip') {
                  actionUrl = $event.detail.action;
                  archiveTitle = $event.detail.title;
                  categoryId = $event.detail.category_id;
                  
                  // Menangkap variabel tambahan opsional jika dikirim dari controller / dataset table
                  archiveDate = $event.detail.archive_date || '';
                  archiveDesc = $event.detail.description || '';
                  
                  // Menyetel ulang value mentah ke dalam native input element
                  $nextTick(() => {
                      const dateInput = document.getElementById('edit_archive_date');
                      if(dateInput) {
                          dateInput.value = archiveDate;
                      }
                  });
              }
          "
          x-bind:action="actionUrl">
        
        @csrf
        @method('PUT') {{-- Spoofing method PUT penting untuk update data di Laravel --}}

        <h2 class="text-lg font-bold text-slate-800 mb-4">Ubah Data Arsip</h2>
        
        <div class="space-y-4">
            {{-- Input Judul --}}
            <div>
                <x-input-label for="edit_title" value="Judul Arsip" class="text-xs font-bold uppercase text-slate-500" />
                <x-text-input id="edit_title" name="title" type="text" class="mt-1 block w-full rounded-xl text-sm" 
                              x-model="archiveTitle" placeholder="Masukkan judul dokumen" required />
            </div>

            {{-- Pilihan Kategori --}}
            <div>
                <x-input-label for="edit_category_id" value="Kategori" class="text-xs font-bold uppercase text-slate-500" />
                <select name="category_id" id="edit_category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm" 
                        x-model="categoryId" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">
                            @if(Auth::user()->isPureSuperAdmin())
                                {{ $cat->name }} ({{ $cat->department->name ?? 'System' }})
                            @else
                                {{ $cat->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Datepicker Edit dengan ikon kalender --}}
            <div>
                <x-input-label for="edit_archive_date" value="Tanggal Arsip (Opsional)" class="text-xs font-bold uppercase text-slate-500" />
                <div class="relative max-w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z"/>
                        </svg>
                    </div>
                    <input id="edit_archive_date" name="archive_date" type="text" 
                           class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                           placeholder="Pilih tanggal (YYYY-MM-DD)" autocomplete="off">
                </div>
                <p class="text-xs text-slate-400 mt-1">Kosongkan untuk tetap mempertahankan tanggal penyimpanan saat ini.</p>
            </div>

            {{-- Deskripsi Edit --}}
            <div>
                <x-input-label for="edit_description" value="Deskripsi (Opsional)" class="text-xs font-bold uppercase text-slate-500" />
                <textarea id="edit_description" name="description" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm" 
                          x-model="archiveDesc" placeholder="Tambahkan deskripsi arsip..."></textarea>
            </div>

            {{-- Ganti File/Berkas Fisik (Opsional) dengan interaktivitas Drag & Drop identik --}}
            <div>
                <x-input-label for="edit_file" value="Ganti File Dokumen (Opsional)" class="text-xs font-bold uppercase text-slate-500" />
                <div class="mt-1 flex justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-4 transition hover:bg-gray-100 cursor-pointer" 
                     x-data="{ editFileName: '', editFileExtension: '' }"
                     @click="$refs.editFileInput.click()">
                    <div class="text-center w-full">
                        <template x-if="!editFileName">
                            <div>
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <div class="mt-2 flex text-sm leading-6 text-gray-600 justify-center">
                                    <span class="rounded-md bg-gray-50 font-semibold text-indigo-600 hover:text-indigo-500">Pilih berkas baru</span>
                                    <span class="pl-1">atau seret ke sini</span>
                                </div>
                                <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengganti file lama (Max 10MB)</p>
                            </div>
                        </template>
                        <template x-if="editFileName">
                            <div>
                                <img :src="'/file-icons/' + editFileExtension + '.png'" class="mx-auto h-12 w-12" x-on:error="editFileExtension = 'image'">
                                <p class="mt-2 text-sm font-medium text-emerald-600 break-all" x-text="editFileName"></p>
                                <p class="text-xs text-gray-400 mt-1">Klik area untuk mengganti file baru</p>
                            </div>
                        </template>
                    </div>
                    <input id="edit_file" name="file" type="file" class="hidden" x-ref="editFileInput"
                           @change="
                               const file = $event.target.files[0];
                               if(file) {
                                   editFileName = file.name;
                                   let ext = file.name.split('.').pop().toLowerCase();
                                   if(['pdf'].includes(ext)) editFileExtension = 'pdf';
                                   else if(['doc', 'docx'].includes(ext)) editFileExtension = 'doc';
                                   else if(['xls', 'xlsx'].includes(ext)) editFileExtension = 'xls';
                                   else if(['jpg', 'jpeg', 'png', 'webp'].includes(ext)) editFileExtension = 'image';
                                   else editFileExtension = 'image';
                               } else {
                                   editFileName = '';
                                   editFileExtension = '';
                               }
                           ">
                </div>
            </div>
        </div>

        {{-- Edit Progress Bar --}}
        <div id="editProgressContainer" class="mt-6 hidden">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-slate-700">Menyimpan Perubahan...</span>
                <span class="text-sm font-medium text-slate-700" id="editProgressPercent">0%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div id="editProgressBar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">Batal</x-secondary-button>
            <button type="submit" id="editSubmitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-modal>

{{-- STYLE KHUSUS UNTUK DATEPICKER --}}
<style>
    .datepicker-picker {
        background-color: white !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important;
        padding: 0.5rem !important;
        width: auto !important;
    }
    .datepicker-cell.selected {
        background-color: #4f46e5 !important;
        color: white !important;
    }
    .datepicker-cell:hover:not(.selected) {
        background-color: #eef2ff !important;
    }
    .datepicker-nav-btn {
        color: #1e293b !important;
    }
    .datepicker-nav-btn:hover {
        background-color: #f1f5f9 !important;
    }
</style>

{{-- JAVASCRIPT LOGIC --}}
<script>
    (function() {
        let datepickerInstance = null;
        let editDatepickerInstance = null;

        function destroyDatepickers() {
            if (datepickerInstance) {
                if (typeof datepickerInstance.destroy === 'function') datepickerInstance.destroy();
                datepickerInstance = null;
            }
            if (editDatepickerInstance) {
                if (typeof editDatepickerInstance.destroy === 'function') editDatepickerInstance.destroy();
                editDatepickerInstance = null;
            }
            document.querySelectorAll('.datepicker-picker').forEach(popup => popup.remove());
        }

        function initDatepicker(elementId, isEdit = false) {
            const input = document.getElementById(elementId);
            if (!input || typeof Datepicker === 'undefined') return;
            
            const instance = new Datepicker(input, {
                format: 'yyyy-mm-dd',
                autohide: true,
                todayHighlight: true,
                clearBtn: true,
                todayBtn: true
            });

            if (isEdit) editDatepickerInstance = instance;
            else datepickerInstance = instance;
        }

        if (typeof Datepicker === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/flowbite@2/dist/datepicker.js';
            document.head.appendChild(script);
        }

        window.addEventListener('open-modal', function(e) {
            if (e.detail.id === 'modal-tambah-arsip') {
                setTimeout(() => initDatepicker('archive_date', false), 100);
            }
            if (e.detail.id === 'modal-edit-arsip') {
                setTimeout(() => initDatepicker('edit_archive_date', true), 100);
            }
        });

        window.addEventListener('close-modal', function(e) {
            if (e.detail.id === 'modal-tambah-arsip' || e.detail.id === 'modal-edit-arsip') {
                destroyDatepickers();
            }
        });
    })();

    // Progress bar form tambah arsip tunggal
    (function() {
        const form = document.getElementById('uploadForm');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const submitBtn = document.getElementById('submitBtn');
            if (progressContainer) {
                progressContainer.classList.remove('hidden');
                let width = 0;
                const interval = setInterval(() => {
                    if (width >= 90) clearInterval(interval);
                    else {
                        width += 10;
                        progressBar.style.width = width + '%';
                        progressPercent.innerText = width + '%';
                    }
                }, 150);
            }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mengunggah...';
            }
        });
    })();

    // Progress bar form tambah ZIP masal
    (function() {
        const zipForm = document.getElementById('uploadZipForm');
        if (!zipForm) return;
        zipForm.addEventListener('submit', function(e) {
            const zipProgressContainer = document.getElementById('zipProgressContainer');
            const zipProgressBar = document.getElementById('zipProgressBar');
            const zipProgressPercent = document.getElementById('zipProgressPercent');
            const zipSubmitBtn = document.getElementById('zipSubmitBtn');
            if (zipProgressContainer) {
                zipProgressContainer.classList.remove('hidden');
                let width = 0;
                const interval = setInterval(() => {
                    if (width >= 95) clearInterval(interval);
                    else {
                        width += 5;
                        zipProgressBar.style.width = width + '%';
                        zipProgressPercent.innerText = width + '%';
                    }
                }, 200);
            }
            if (zipSubmitBtn) {
                zipSubmitBtn.disabled = true;
                zipSubmitBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Memproses ZIP...';
            }
        });
    })();

    // Progress bar form edit arsip tunggal
    (function() {
        const editForm = document.getElementById('editForm');
        if (!editForm) return;
        editForm.addEventListener('submit', function(e) {
            const editProgressContainer = document.getElementById('editProgressContainer');
            const editProgressBar = document.getElementById('editProgressBar');
            const editProgressPercent = document.getElementById('editProgressPercent');
            const editSubmitBtn = document.getElementById('editSubmitBtn');
            if (editProgressContainer) {
                editProgressContainer.classList.remove('hidden');
                let width = 0;
                const interval = setInterval(() => {
                    if (width >= 90) clearInterval(interval);
                    else {
                        width += 10;
                        editProgressBar.style.width = width + '%';
                        editProgressPercent.innerText = width + '%';
                    }
                }, 100);
            }
            if (editSubmitBtn) {
                editSubmitBtn.disabled = true;
                editSubmitBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Menyimpan...';
            }
        });
    })();
</script>