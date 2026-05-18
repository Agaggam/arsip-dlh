{{-- MODAL PREVIEW DOKUMEN --}}
<x-modal name="preview-modal" maxWidth="4xl">
    <div class="p-6" x-data="{ title: '', url: '', type: '', downloadUrl: '' }" 
         x-on:open-modal.window="if($event.detail.id === 'preview-modal') { 
            title = $event.detail.title; 
            url = $event.detail.fileUrl; 
            type = $event.detail.fileType;
            downloadUrl = $event.detail.downloadUrl;
         }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-slate-800" x-text="title"></h2>
            <button x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center min-h-[400px]">
            <template x-if="type === 'pdf'">
                <iframe :src="url" class="w-full h-[600px] border-none"></iframe>
            </template>
            <template x-if="['jpg', 'jpeg', 'png', 'webp'].includes(type)">
                <img :src="url" class="max-w-full max-h-[600px] object-contain shadow-lg">
            </template>
            <template x-if="!['pdf', 'jpg', 'jpeg', 'png', 'webp'].includes(type)">
                <div class="text-center p-10">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-slate-500 font-medium text-sm">Pratinjau tidak tersedia untuk tipe file ini.<br>Silakan unduh dokumen untuk melihat isi.</p>
                    <a :href="downloadUrl" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg transition active:scale-95">Download Sekarang</a>
                </div>
            </template>
        </div>
    </div>
</x-modal>

{{-- MODAL KONFIRMASI HAPUS SATU ARSIP --}}
<x-confirm-modal id="confirm-delete" method="DELETE" type="danger" />

{{-- MODAL TAMBAH ARSIP dengan Datepicker Flowbite yang sudah diperbaiki --}}
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
                <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" required>
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
                           class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                           placeholder="Pilih tanggal (YYYY-MM-DD)" autocomplete="off">
                </div>
                <p class="text-xs text-slate-400 mt-1">Isi jika arsip ini dibuat pada tanggal tertentu (contoh: arsip lama). <strong>Biarkan kosong untuk menggunakan tanggal hari ini.</strong></p>
            </div>

            {{-- Deskripsi (Opsional) --}}
            <div>
                <x-input-label for="description" value="Deskripsi (Opsional)" class="text-xs font-bold uppercase text-slate-500" />
                <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Tambahkan deskripsi arsip..."></textarea>
            </div>

            {{-- Upload file drag & drop (sama seperti sebelumnya) --}}
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
            <button type="submit" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4" />
                </svg>
                Simpan Arsip
            </button>
        </div>
    </form>
</x-modal>

{{-- STYLE KHUSUS UNTUK DATEPICKER (tanpa menyebabkan card bertumpuk) --}}
<style>
    /* Hanya target popup datepicker utama, jangan semua .datepicker */
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

<script>
    (function() {
        let datepickerInstance = null;
        let isDatepickerLoaded = false;

        function destroyDatepicker() {
            if (datepickerInstance) {
                // Coba panggil destroy jika ada
                if (typeof datepickerInstance.destroy === 'function') {
                    datepickerInstance.destroy();
                }
                datepickerInstance = null;
            }
            // Hapus popup yang mungkin tertinggal
            const existingPopups = document.querySelectorAll('.datepicker-picker');
            existingPopups.forEach(popup => popup.remove());
        }

        function initDatepicker() {
            const input = document.getElementById('archive_date');
            if (!input) return;
            if (typeof Datepicker === 'undefined') return;
            
            // Bersihkan instance dan popup sebelumnya
            destroyDatepicker();
            
            // Inisialisasi baru
            datepickerInstance = new Datepicker(input, {
                format: 'yyyy-mm-dd',
                autohide: true,
                todayHighlight: true,
                clearBtn: true,
                todayBtn: true
            });
        }

        // Load Flowbite Datepicker jika diperlukan
        if (typeof Datepicker === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/flowbite@2/dist/datepicker.js';
            script.onload = function() {
                isDatepickerLoaded = true;
                // Jangan inisialisasi otomatis, tunggu modal terbuka
            };
            document.head.appendChild(script);
        } else {
            isDatepickerLoaded = true;
        }

        // Saat modal tambah arsip dibuka, inisialisasi datepicker
        window.addEventListener('open-modal', function(e) {
            if (e.detail.id === 'modal-tambah-arsip') {
                // Tunggu DOM siap
                setTimeout(function() {
                    if (typeof Datepicker !== 'undefined') {
                        initDatepicker();
                    }
                }, 100);
            }
        });

        // Bersihkan saat modal ditutup (opsional)
        window.addEventListener('close-modal', function(e) {
            if (e.detail.id === 'modal-tambah-arsip') {
                destroyDatepicker();
            }
        });
    })();

    // Progress bar
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
                    if (width >= 90) {
                        clearInterval(interval);
                    } else {
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
</script>