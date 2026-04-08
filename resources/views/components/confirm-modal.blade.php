@props([
    'id', 
    'title' => 'Apakah Anda yakin?', 
    'type' => 'danger', 
    'action' => '#',
    'method' => 'DELETE' {{-- Default tetap DELETE --}}
])

<div 
    id="{{ $id }}" 
    x-data="{ 
        open: false, 
        dynamicAction: '{{ $action }}', 
        dynamicTitle: '{{ $title }}',
        dynamicMethod: '{{ $method }}'
    }"
    x-show="open"
    x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') { 
        open = true; 
        dynamicAction = $event.detail.action || dynamicAction;
        dynamicTitle = $event.detail.title || dynamicTitle;
        dynamicMethod = $event.detail.method || '{{ $method }}';
    }"
    x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') { open = false }"
    x-on:keydown.escape.window="open = false"
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm"
>
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="relative p-4 w-full max-w-md max-h-full"
    >
        <div class="relative bg-white border border-gray-200 rounded-[2rem] shadow-2xl p-4 md:p-6">
            <button type="button" @click="open = false" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-xl text-sm w-9 h-9 ms-auto inline-flex justify-center items-center transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="p-4 md:p-5 text-center">
                {{-- Icon Dinamis Berdasarkan Type --}}
                @if($type == 'danger')
                    <svg class="mx-auto mb-4 text-red-500 w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @elseif($type == 'success')
                    <svg class="mx-auto mb-4 text-emerald-500 w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="mx-auto mb-4 text-indigo-500 w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif

                <h3 class="mb-6 text-lg font-semibold text-gray-700 leading-tight" x-text="dynamicTitle"></h3>
                
                <div class="flex items-center space-x-3 justify-center">
                    <form :action="dynamicAction" method="POST">
                        @csrf
                        {{-- Method dinamis: Jika POST tidak perlu input hidden, jika DELETE/PUT perlu --}}
                        <template x-if="dynamicMethod !== 'POST'">
                            <input type="hidden" name="_method" :value="dynamicMethod">
                        </template>

                        <button type="submit" 
                            class="text-white shadow-lg font-bold rounded-xl text-sm px-6 py-2.5 transition-all active:scale-95"
                            :class="{
                                'bg-red-600 hover:bg-red-700': '{{ $type }}' === 'danger',
                                'bg-emerald-600 hover:bg-emerald-700': '{{ $type }}' === 'success',
                                'bg-indigo-600 hover:bg-indigo-700': '{{ $type }}' === 'info'
                            }"
                        >
                            Ya, Lanjutkan
                        </button>
                    </form>

                    <button @click="open = false" type="button" class="text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 font-bold rounded-xl text-sm px-6 py-2.5 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>