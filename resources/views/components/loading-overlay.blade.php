<div
    id="global-loading-overlay"
    class="fixed inset-0 z-[200] hidden flex-col items-center justify-center gap-4 bg-white/80 backdrop-blur-sm dark:bg-gray-900/80"
    role="status"
    aria-live="assertive"
>
    <div class="relative flex h-20 w-20 items-center justify-center">
        <span class="absolute inset-0 rounded-full border-[3px] border-brand-green/15"></span>
        <span class="absolute inset-0 animate-spin rounded-full border-[3px] border-transparent border-t-brand-green border-r-brand-gold [animation-duration:0.8s]"></span>
        <img
            src="{{ asset('images/favicon-48.png') }}"
            alt=""
            class="h-10 w-10 animate-pulse rounded-full shadow-sm [animation-duration:1.4s]"
            aria-hidden="true"
        >
    </div>
    <p class="text-sm font-semibold text-brand-green">Procesando…</p>
    <span class="sr-only">Procesando tu solicitud, por favor espera.</span>
</div>
