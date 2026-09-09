@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-sky-200 bg-sky-50 text-sky-800',
    ];
@endphp

<div
    x-data
    aria-live="polite"
    aria-atomic="true"
    class="pointer-events-none fixed inset-x-0 top-4 z-[300] flex flex-col items-center gap-2 px-4 sm:items-end sm:px-6"
>
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto w-full max-w-sm rounded-lg border p-4 shadow-lg"
            :class="{
                'border-emerald-200 bg-emerald-50 text-emerald-800': toast.type === 'success',
                'border-red-200 bg-red-50 text-red-800': toast.type === 'error',
                'border-amber-200 bg-amber-50 text-amber-800': toast.type === 'warning',
                'border-sky-200 bg-sky-50 text-sky-800': toast.type === 'info',
            }"
            role="status"
        >
            <div class="flex items-start gap-3">
                <svg x-show="toast.type === 'success'" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="toast.type === 'error'" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="toast.type === 'warning'" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 4.5c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <svg x-show="toast.type === 'info'" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>

                <p class="flex-1 text-sm font-medium" x-text="toast.message"></p>

                <button
                    type="button"
                    class="shrink-0 rounded text-current/60 hover:text-current focus:outline-none focus:ring-2 focus:ring-current"
                    x-on:click="$store.toasts.dismiss(toast.id)"
                    aria-label="Cerrar notificación"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
