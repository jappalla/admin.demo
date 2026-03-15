<main id="main-content" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
    <section class="panel relative overflow-hidden rounded-3xl border border-white/10 bg-base-950/50 backdrop-blur-xl shadow-2xl shadow-black/40 p-8 sm:p-12 text-center">
        <!-- Background glow -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -left-20 h-64 w-64 rounded-full bg-rose-400/15 blur-3xl"></div>
            <div class="absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-sky-400/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-base-950 via-base-950/70 to-base-950"></div>
        </div>

        <div class="relative space-y-6">
            <!-- Error code -->
            <p class="font-display text-8xl sm:text-9xl font-extrabold tracking-tight text-white/10">
                404
            </p>

            <h1 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white -mt-4">
                Pagina non trovata
            </h1>

            <p class="mx-auto max-w-md text-base text-white/60 leading-relaxed">
                La pagina che stai cercando non esiste o è stata spostata.
                Torna alla home per continuare la navigazione.
            </p>

            <!-- CTA -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a class="inline-flex items-center justify-center gap-2 rounded-2xl bg-accent-blue px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-black/30 transition hover:bg-blue-400"
                    href="<?php echo e(route_url('')); ?>">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                    Torna alla Home
                </a>
                <a class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white"
                    href="<?php echo e(route_url('cv/export')); ?>">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="currentColor"
                            d="M12 3a1 1 0 0 1 1 1v8.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.42L11 12.59V4a1 1 0 0 1 1-1z" />
                        <path fill="currentColor" d="M5 19a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1z" />
                    </svg>
                    Scarica CV
                </a>
            </div>
        </div>
    </section>
</main>