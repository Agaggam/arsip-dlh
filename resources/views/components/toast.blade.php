@props(['type' => 'success', 'message' => '', 'duration' => 5000])

<div 
    x-data="{ 
        show: false,
        init() {
            this.$nextTick(() => {
                this.show = true;
                if ({{ $duration }} > 0) {
                    setTimeout(() => this.show = false, {{ $duration }});
                }
            });
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-400"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="fixed bottom-6 right-6 z-[9999] w-11/12 sm:w-96"
    style="display: none; will-change: transform, opacity;"
>
    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <!-- Progress bar -->
        <div class="absolute top-0 left-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full"
             x-init="
                $el.style.width = '0%';
                setTimeout(() => {
                    $el.style.transition = 'width {{ $duration }}ms linear';
                    $el.style.width = '100%';
                }, 50);
             "
             style="width: 0%; transition: none;"></div>

        <div class="flex items-start gap-3 p-4">
            <!-- Icon -->
            <div @class([
                'flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center',
                'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' => $type === 'success',
                'bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400' => $type === 'error' || $type === 'danger',
                'bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400' => $type === 'warning',
                'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400' => $type === 'info',
            ])>
                @if($type === 'success')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                @elseif($type === 'error' || $type === 'danger')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                @elseif($type === 'warning')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <p @class([
                    'text-xs font-semibold uppercase tracking-wider',
                    'text-emerald-600 dark:text-emerald-400' => $type === 'success',
                    'text-red-600 dark:text-red-400' => $type === 'error' || $type === 'danger',
                    'text-amber-600 dark:text-amber-400' => $type === 'warning',
                    'text-blue-600 dark:text-blue-400' => $type === 'info',
                ])>
                    {{ ucfirst($type) }}
                </p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 break-words">
                    {{ $message }}
                </p>
            </div>

            <!-- Close button -->
            <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>