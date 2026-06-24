{{-- MODAL PREVIEW DOKUMEN --}}
<x-modal name="preview-modal" maxWidth="4xl">
    <div class="p-6" x-data="{ title: '', url: '', type: '', downloadUrl: '', errorMessage: '', loading: false }" 
         x-on:open-modal.window="if($event.detail.id === 'preview-modal') { 
            title = $event.detail.title; 
            url = $event.detail.fileUrl; 
            type = $event.detail.fileType;
            downloadUrl = $event.detail.downloadUrl;
            errorMessage = '';
            loading = true;

            // Lakukan cek validitas file fisik ke controller via fetch
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 404) {
                            throw new Error('Gagal memuat dokumen: File fisik tidak ditemukan atau telah dihapus dari server.');
                        } else if (response.status === 403) {
                            throw new Error('Akses Ditolak: Anda tidak memiliki hak akses untuk dokumen ini.');
                        } else {
                            throw new Error('Terjadi kesalahan saat memuat dokumen.');
                        }
                    }
                    return response.blob();
                })
                .then(() => {
                    loading = false;
                })
                .catch(error => {
                    errorMessage = error.message;
                    loading = false;
                });
         }">
         
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-slate-800" x-text="title"></h2>
            <button x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center min-h-[400px] relative">
            
            {{-- Keadaan Loading Cek File --}}
            <template x-if="loading">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mx-auto mb-2"></div>
                    <p class="text-xs text-slate-500">Memverifikasi dokumen...</p>
                </div>
            </template>

            {{-- Keadaan Jika Ada Error (File Hilang / 404 / 403) --}}
            <template x-if="!loading && errorMessage">
                <div class="text-center p-10 max-w-md">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <p class="text-slate-700 font-semibold text-sm mb-1">Pratinjau Gagal</p>
                    <p class="text-slate-500 text-xs leading-relaxed" x-text="errorMessage"></p>
                </div>
            </template>

            {{-- Keadaan Normal: Jika File Ada dan Sesuai Format --}}
            <template x-if="!loading && !errorMessage && type === 'pdf'">
                <iframe :src="url" class="w-full h-[600px] border-none"></iframe>
            </template>

            <template x-if="!loading && !errorMessage && ['jpg', 'jpeg', 'png', 'webp'].includes(type)">
                <img :src="url" class="max-w-full max-h-[600px] object-contain shadow-lg">
            </template>

            {{-- Keadaan Format File Tidak Didukung Preview (seperti docx, xlsx) namun file fisiknya ADA --}}
            <template x-if="!loading && !errorMessage && !['pdf', 'jpg', 'jpeg', 'png', 'webp'].includes(type)">
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