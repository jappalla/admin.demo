<main id="main-content" class="export-shell mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <!-- HERO EXPORT (upgrade moderno) -->
    <section
        class="panel export-hero-panel relative overflow-hidden rounded-3xl border border-white/10 bg-base-950/50 backdrop-blur-xl shadow-2xl shadow-black/40 p-6 sm:p-8">
        <!-- Background glow -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-accent-blue/15 blur-3xl"></div>
            <div class="absolute -bottom-28 -right-24 h-96 w-96 rounded-full bg-accent-violet/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-base-950 via-base-950/70 to-base-950"></div>
        </div>

        <div class="relative grid gap-8 lg:grid-cols-12 lg:items-center">
            <!-- Copy -->
            <div class="lg:col-span-7">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-400/90"></span>
                    <p class="eyebrow m-0 text-xs font-semibold tracking-wide text-white/70">
                        UX/UI First Export
                    </p>
                </div>

                <h1 class="mt-5 font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
                    Export Curriculum in PDF
                </h1>

                <p class="intro mt-4 max-w-2xl text-base sm:text-lg text-white/75 leading-relaxed">
                    Anteprima immediata nel browser, opzione download e link diretto condivisibile.
                </p>

                <!-- Actions -->
                <div class="export-actions mt-7 flex flex-col sm:flex-row gap-3">
                    <a class="button primary inline-flex items-center justify-center gap-2 rounded-2xl bg-accent-blue px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-black/30 transition hover:bg-blue-400"
                        href="<?php echo e($pdfInlineUrl); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path fill="currentColor"
                                d="M12 5c4.5 0 8.2 2.7 10 7-1.8 4.3-5.5 7-10 7S3.8 16.3 2 12c1.8-4.3 5.5-7 10-7zm0 2C8.7 7 5.8 8.9 4.2 12 5.8 15.1 8.7 17 12 17s6.2-1.9 7.8-5C18.2 8.9 15.3 7 12 7zm0 2.5A2.5 2.5 0 1 1 12 14a2.5 2.5 0 0 1 0-5z" />
                        </svg>
                        Apri Anteprima PDF
                    </a>

                    <a class="button secondary inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white"
                        href="<?php echo e($pdfDownloadUrl); ?>">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path fill="currentColor"
                                d="M12 3a1 1 0 0 1 1 1v8.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.42L11 12.59V4a1 1 0 0 1 1-1z" />
                            <path fill="currentColor" d="M5 19a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1z" />
                        </svg>
                        Scarica PDF
                    </a>

                    <button
                        class="button tertiary inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-transparent px-5 py-3 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white"
                        type="button" data-copy-target="<?php echo e($pdfInlineUrl); ?>"
                        data-copy-label="Link PDF copiato">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path fill="currentColor"
                                d="M16 1H6a2 2 0 0 0-2 2v12h2V3h10V1zm3 4H10a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H10V7h9v14z" />
                        </svg>
                        Copia Link PDF
                    </button>
                </div>

                <!-- Tiny note -->
                <p class="mt-4 text-xs text-white/45">
                    Suggerimento: usa “Copia link” per condividere l’anteprima con recruiter o clienti.
                </p>
            </div>

            <!-- Profile photo + Metrics -->
            <div class="lg:col-span-5">
                <div class="flex flex-col items-center gap-5 lg:items-start">
                    <img
                        src="<?php echo e(asset_url('assets/img/tony_2013_-600x754.webp')); ?>"
                        alt="Antonio Trapasso — foto profilo"
                        class="h-28 w-28 rounded-3xl object-cover ring-2 ring-white/10 shadow-xl shadow-black/40"
                        loading="lazy">
                </div>

                <ul class="export-metrics m-0 mt-5 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                    <li class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-xl shadow-black/30">
                        <span class="block text-xs uppercase tracking-wider text-white/55">Esperienze</span>
                        <strong class="mt-2 block text-2xl font-extrabold text-white">
                            <?php echo e((string) $experiencesCount); ?>
                        </strong>
                    </li>
                    <li class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-xl shadow-black/30">
                        <span class="block text-xs uppercase tracking-wider text-white/55">Competenze</span>
                        <strong class="mt-2 block text-2xl font-extrabold text-white">
                            <?php echo e((string) $skillsCount); ?>
                        </strong>
                    </li>
                    <li class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-xl shadow-black/30">
                        <span class="block text-xs uppercase tracking-wider text-white/55">Ultimo aggiornamento</span>
                        <strong class="mt-2 block text-base sm:text-lg font-extrabold text-white">
                            <?php echo e(date('Y-m-d H:i')); ?>
                        </strong>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- PREVIEW -->
    <section
        class="panel export-preview-panel mt-8 rounded-3xl border border-white/10 bg-white/5 p-6 sm:p-8 shadow-2xl shadow-black/30">
        <div class="export-preview-top flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="m-0 font-display text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                    Anteprima Documento
                </h2>
                <p class="mt-2 text-sm text-white/60">
                    La preview è interattiva: puoi cercare e zoomare (toolbar attiva).
                </p>
            </div>

            <a class="button tertiary inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-transparent px-4 py-2.5 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white"
                href="<?php echo e($pdfInlineUrl); ?>" target="_blank" rel="noopener noreferrer">
                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z" />
                    <path fill="currentColor" d="M5 5h6v2H7v10h10v-4h2v6H5V5z" />
                </svg>
                Apri a Schermo Intero
            </a>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-base-950/50">
            <iframe class="export-preview-frame block w-full" style="height: min(72vh, 760px);"
                src="<?php echo e($pdfInlineUrl); ?>#toolbar=1&navpanes=0" title="Anteprima PDF Curriculum Vitae"
                loading="lazy"></iframe>
        </div>
    </section>

    <!-- SUMMARY -->
    <section
        class="panel export-summary-panel mt-8 rounded-3xl border border-white/10 bg-white/5 p-6 sm:p-8 shadow-2xl shadow-black/30">
        <div class="flex items-start justify-between gap-6 flex-wrap">
            <div>
                <h2 class="m-0 font-display text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                    Sintesi Profilo
                </h2>
                <p class="mt-2 text-sm text-white/60">
                    Una breve descrizione del profilo, utile come introduzione in mail o candidature.
                </p>
            </div>

            <span
                class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-white/70">
                <span class="inline-block h-2 w-2 rounded-full bg-accent-blue/80"></span>
                Preview generata
            </span>
        </div>

        <p class="mt-5 text-sm sm:text-base leading-relaxed text-white/75">
            <?php echo e($profilePreview); ?>
        </p>
    </section>
</main>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Export CV PDF | Antonio Trapasso",
        "description": "Anteprima e strumenti di export del curriculum in PDF.",
        "url": "<?php echo e(route_url('cv/export')); ?>",
        "mainEntity": {
            "@type": "Person",
            "name": "Antonio Trapasso",
            "url": "<?php echo e(route_url('')); ?>"
        }
    }
</script>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "DigitalDocument",
        "name": "CV Antonio Trapasso — PDF",
        "description": "Curriculum Vitae di Antonio Trapasso — Full-Stack Developer.",
        "url": "<?php echo e(route_url('cv/export')); ?>",
        "author": {
            "@type": "Person",
            "name": "Antonio Trapasso",
            "url": "https://developer.testscript.info"
        },
        "encodingFormat": "application/pdf",
        "hasDigitalDocumentPermission": {
            "@type": "DigitalDocumentPermission",
            "permissionType": "https://schema.org/ReadPermission",
            "grantee": {
                "@type": "Audience",
                "audienceType": "public"
            }
        }
    }
</script>