@props([
    'id', 
    'title' => 'Apakah Anda yakin?', 
    'type' => 'danger', 
    'action' => '#',
    'method' => 'DELETE'
])

{{-- Style khusus untuk menyamarkan input text menjadi tampilan password --}}
<style>
    .mask-security {
        -webkit-text-security: disc !important;
    }
</style>

<div x-data="{ 
        open: false, 
        dynamicAction: '{{ $action }}', 
        dynamicTitle: '{{ $title }}',
        dynamicMethod: '{{ $method }}',
        dynamicWarning: '', 
        needPassword: false
    }"
    x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') { 
        open = true; 
        dynamicAction = $event.detail.action || dynamicAction;
        dynamicTitle = $event.detail.title || dynamicTitle;
        dynamicMethod = $event.detail.method || '{{ $method }}';
        dynamicWarning = $event.detail.warning || '';
        needPassword = $event.detail.withPassword || false;
    }"
    x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') { open = false }"
    x-on:keydown.escape.window="open = false"
>
    <template x-teleport="body">
        <div 
            x-show="open"
            style="display: none;"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6"
        >
            {{-- Backdrop: Disamakan dengan komponen modal (gray-900/60 + backdrop-blur-sm) --}}
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                {{-- PERUBAHAN DISINI: Menggunakan bg-gray-900/60 dan backdrop-blur-sm agar identik --}}
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                @click="open = false"
            ></div>

            {{-- Modal Content --}}
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-md bg-white border border-gray-100 rounded-[2.5rem] shadow-2xl overflow-hidden z-10"
            >
                {{-- Bar Indikator Atas --}}
                <div class="h-2 w-full" :class="{
                    'bg-red-500': '{{ $type }}' === 'danger',
                    'bg-emerald-500': '{{ $type }}' === 'success',
                    'bg-indigo-500': '{{ $type }}' === 'info'
                }"></div>

                <div class="p-6 md:p-8">
                    {{-- Tombol Close --}}
                    <button type="button" @click="open = false" class="absolute top-6 end-6 text-gray-400 hover:bg-gray-100 hover:text-gray-900 rounded-2xl text-sm w-10 h-10 inline-flex justify-center items-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="text-center">
                        <div class="mx-auto mb-6 flex items-center justify-center">
                            <div class="p-4 rounded-3xl" :class="{
                                'bg-red-50 text-red-500': '{{ $type }}' === 'danger',
                                'bg-emerald-50 text-emerald-500': '{{ $type }}' === 'success',
                                'bg-indigo-50 text-indigo-500': '{{ $type }}' === 'info'
                            }">
                                <template x-if="'{{ $type }}' === 'danger'">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </template>
                                <template x-if="'{{ $type }}' === 'success'">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                                <template x-if="'{{ $type }}' === 'info'">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                            </div>
                        </div>

                        <h3 class="mb-3 text-2xl font-extrabold text-slate-800 tracking-tight" x-text="dynamicTitle"></h3>
                        
                        <template x-if="dynamicWarning">
                            <div class="mb-6 p-4 bg-amber-50/50 border border-amber-100 rounded-3xl text-sm text-amber-700 leading-relaxed text-left">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    <span x-text="dynamicWarning"></span>
                                </div>
                            </div>
                        </template>

                        <form :action="dynamicAction" method="POST" class="mt-4" autocomplete="off">
                            @csrf
                            <template x-if="dynamicMethod !== 'POST'">
                                <input type="hidden" name="_method" :value="dynamicMethod">
                            </template>

                            <div x-show="needPassword" x-transition class="mb-8 text-left">
                                <label for="password" class="text-[11px] font-black uppercase text-slate-400 ml-2 mb-2 block tracking-[0.2em]">Konfirmasi Password</label>
                                <input 
                                    id="password"
                                    type="text" 
                                    name="password" 
                                    ::required="needPassword"
                                    autocomplete="off"
                                    readonly 
                                    onfocus="this.removeAttribute('readonly');"
                                    class="mask-security mt-1 block w-full rounded-[1.5rem] border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-base p-4 bg-slate-50 transition-all"
                                    placeholder="Ketik password untuk verifikasi..."
                                >
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <button @click="open = false" type="button" class="text-slate-500 bg-slate-100 hover:bg-slate-200 font-bold rounded-[1.5rem] text-sm px-6 py-4 transition-all">
                                    Batal
                                </button>

                                <button type="submit" 
                                    class="text-white shadow-xl font-bold rounded-[1.5rem] text-sm px-6 py-4 transition-all active:scale-95"
                                    :class="{
                                        'bg-red-600 hover:bg-red-700 shadow-red-200': '{{ $type }}' === 'danger',
                                        'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200': '{{ $type }}' === 'success',
                                        'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200': '{{ $type }}' === 'info'
                                    }"
                                >
                                    Ya, Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>