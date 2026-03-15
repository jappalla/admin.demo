<?php
$isHomePage = isset($isHomePage) ? (bool) $isHomePage : false;
$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '');
$currentUrl = $requestUri !== '' ? $requestUri : route_url('');
$isAbsoluteCurrent = preg_match('#^https?://#i', $currentUrl) === 1;
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : 'localhost';
$shareUrl = $isAbsoluteCurrent ? $currentUrl : $protocol . '://' . $host . $currentUrl;
$shareUrl = preg_replace('/#.*/', '', $shareUrl) ?? $shareUrl;
$shareTitle = (string) config('app.seo_title', 'Curriculum Vitae Antonio Trapasso');
$shareDescription = (string) config('app.seo_description', '');
$shareText = trim($shareTitle . ($shareDescription !== '' ? ' - ' . $shareDescription : ''));
$encodedShareUrl = rawurlencode($shareUrl);
$encodedShareText = rawurlencode($shareText);
?>

<footer class="site-footer border-t border-white/10 bg-base-950/70 backdrop-blur-xl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">

            <!-- Left: copyright + micro info -->
            <div class="space-y-2">
                <p class="text-sm text-white/70">
                    &copy; <?php echo date('Y'); ?> <span class="font-semibold text-white">Antonio Trapasso</span>.
                    Tutti i diritti riservati.
                </p>
                <p class="text-xs text-white/45">
                    <?php echo e($shareTitle); ?>
                </p>
            </div>

            <?php if ($isHomePage): ?>
                <!-- Right: actions -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-end">

                    <!-- Export PDF -->
                    <a class="inline-flex items-center justify-center gap-2 rounded-2xl bg-accent-blue px-4 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-black/30 transition hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-accent-blue/70 focus:ring-offset-0"
                        href="<?php echo e(route_url('cv/export')); ?>">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path fill="currentColor"
                                d="M12 3a1 1 0 0 1 1 1v8.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.42L11 12.59V4a1 1 0 0 1 1-1z" />
                            <path fill="currentColor" d="M5 19a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1z" />
                        </svg>
                        Esporta CV in PDF
                    </a>

                    <!-- Share -->
                    <div class="social-share flex items-center gap-2 rounded-3xl border border-white/10 bg-white/5 p-2 shadow-xl shadow-black/30"
                        aria-label="Condivisione social" data-share-url="<?php echo e($shareUrl); ?>"
                        data-share-title="<?php echo e($shareTitle); ?>"
                        data-share-description="<?php echo e($shareDescription); ?>">
                        <span class="hidden sm:inline-block px-2 text-xs font-semibold text-white/60">
                            Condividi
                        </span>

                        <a class="social-link inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/80 transition hover:bg-white/10 hover:text-white"
                            data-share="facebook"
                            href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e($encodedShareUrl); ?>"
                            target="_blank" rel="noopener noreferrer" aria-label="Condividi su Facebook"
                            title="Condividi su Facebook">
                            <svg class="social-icon h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path fill="currentColor"
                                    d="M13.6 22v-8.9h3l.4-3.5h-3.4V7.4c0-1 .3-1.6 1.8-1.6h1.8V2.7c-.3 0-1.4-.1-2.7-.1-2.7 0-4.5 1.6-4.5 4.7v2.3H7v3.5h3.1V22h3.5z">
                                </path>
                            </svg>
                        </a>

                        <a class="social-link inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/80 transition hover:bg-white/10 hover:text-white"
                            data-share="x"
                            href="https://twitter.com/intent/tweet?url=<?php echo e($encodedShareUrl); ?>&text=<?php echo e($encodedShareText); ?>"
                            target="_blank" rel="noopener noreferrer" aria-label="Condividi su X" title="Condividi su X">
                            <svg class="social-icon h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path fill="currentColor"
                                    d="M18.8 2H22l-7 8 8.2 12h-6.4l-5-7.2L5.6 22H2.4l7.5-8.6L2 2h6.6l4.6 6.7L18.8 2zm-1.1 18h1.8L7.7 4H5.8L17.7 20z">
                                </path>
                            </svg>
                        </a>

                        <a class="social-link inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/80 transition hover:bg-white/10 hover:text-white"
                            data-share="linkedin"
                            href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo e($encodedShareUrl); ?>"
                            target="_blank" rel="noopener noreferrer" aria-label="Condividi su LinkedIn"
                            title="Condividi su LinkedIn">
                            <svg class="social-icon h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path fill="currentColor"
                                    d="M6.6 8.6H3.2V21h3.4V8.6zM4.9 3A2 2 0 1 0 5 7a2 2 0 0 0-.1-4zM20.8 13.9c0-3.7-2-5.4-4.7-5.4-2.2 0-3.1 1.2-3.6 2v-1.8H9.1V21h3.4v-6.1c0-1.6.3-3.2 2.3-3.2 2 0 2 1.9 2 3.3V21h3.4v-7.1z">
                                </path>
                            </svg>
                        </a>

                        <a class="social-link inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/80 transition hover:bg-white/10 hover:text-white"
                            data-share="whatsapp"
                            href="https://api.whatsapp.com/send?text=<?php echo e(rawurlencode($shareText . ' ' . $shareUrl)); ?>"
                            target="_blank" rel="noopener noreferrer" aria-label="Condividi su WhatsApp"
                            title="Condividi su WhatsApp">
                            <svg class="social-icon h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path fill="currentColor"
                                    d="M12 2A10 10 0 0 0 3.5 17.4L2 22l4.8-1.4A10 10 0 1 0 12 2zm0 18.1a8 8 0 0 1-4.1-1.1l-.3-.2-2.8.8.9-2.7-.2-.3A8 8 0 1 1 12 20.1zm4.4-5.8c-.2-.1-1.2-.6-1.4-.7-.2-.1-.3-.1-.5.1l-.7.8c-.1.1-.2.1-.4.1-.2-.1-.9-.3-1.6-1-.6-.6-1-1.3-1.1-1.5-.1-.2 0-.3.1-.4l.3-.4c.1-.1.1-.2.2-.3.1-.1.1-.2.2-.4 0-.1 0-.3 0-.4 0-.1-.5-1.3-.7-1.8-.2-.5-.4-.4-.5-.4h-.4c-.1 0-.4 0-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.4c.1.1 1.5 2.4 3.6 3.3 2.1.9 2.1.6 2.5.6.4-.1 1.2-.5 1.3-.9.2-.4.2-.8.2-.9 0-.1-.2-.2-.4-.3z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bottom micro-row -->
        <div
            class="mt-8 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p class="inline-flex items-center gap-2">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400/80"></span>
                Online • <?php echo e($host); ?>
            </p>
            <p>
                <a class="hover:text-white transition" href="<?php echo e(route_url('')); ?>">Torna alla home</a>
                <span class="mx-2 text-white/20">•</span>
                <a class="hover:text-white transition"
                    href="<?php echo e($isHomePage ? '#contatti' : route_url('') . '#contatti'); ?>">Contatti</a>
                <span class="mx-2 text-white/20">•</span>
                <a class="hover:text-white transition"
                    href="https://testscript.info/app">
                    Portfolio
                </a>
            </p>
        </div>
    </div>

    <!-- Micro CSS per SVG (non rompe il tuo CSS) -->
    <style>
        .social-icon {
            display: block;
        }
    </style>
</footer>

<script src="<?php echo e(asset_url('assets/js/app.js')); ?>" defer></script>
</body>

</html>