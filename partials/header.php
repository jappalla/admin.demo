<!doctype html>
<?php
// Generate CSP nonce for inline scripts
$csp_nonce = base64_encode(random_bytes(16));

// Content-Security-Policy header
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$csp_nonce}'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
?>
<html lang="it">

<head>
    <?php
    $resolvedTitle = isset($pageTitle)
        ? (string) $pageTitle
        : (string) config('app.seo_title', 'Antonio Trapasso | Curriculum Vitae');
    $resolvedDescription = isset($pageDescription)
        ? (string) $pageDescription
        : (string) config('app.seo_description', 'Curriculum vitae professionale di Antonio Trapasso.');
    $isHomePage = isset($isHomePage) ? (bool) $isHomePage : true;
    $resolvedRobots = isset($pageRobots) ? (string) $pageRobots : 'index,follow';
    $resolvedCanonical = isset($pageCanonical)
        ? (string) $pageCanonical
        : route_url($isHomePage ? '' : trim((string) ($_GET['route'] ?? ''), '/'));
    // Ensure canonical is absolute
    if ($resolvedCanonical !== '' && !str_starts_with($resolvedCanonical, 'http')) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'developer.testscript.info';
        $resolvedCanonical = $scheme . '://' . $host . $resolvedCanonical;
    }
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($resolvedTitle); ?></title>
    <meta name="description" content="<?php echo e($resolvedDescription); ?>">
    <meta name="robots" content="<?php echo e($resolvedRobots); ?>">
    <meta name="theme-color" content="#090f18">
    <link rel="canonical" href="<?php echo e($resolvedCanonical); ?>">

    <meta property="og:title" content="<?php echo e($resolvedTitle); ?>">
    <meta property="og:description" content="<?php echo e($resolvedDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e($resolvedCanonical); ?>">
    <?php $ogImage = $scheme . '://' . $host . asset_url('assets/img/tony_2013_-600x754.webp'); ?>
    <meta property="og:image" content="<?php echo e($ogImage); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($resolvedTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($resolvedDescription); ?>">
    <meta name="twitter:image" content="<?php echo e($ogImage); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset_url('assets/img/favicon.svg')); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload"
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap">
    </noscript>

    <!-- TailwindCSS (compiled build) -->
    <link rel="stylesheet" href="<?php echo e(asset_url('assets/css/dist.css')); ?>">

    <!-- Il tuo CSS esistente (non lo tocchiamo) -->
    <link rel="stylesheet" href="<?php echo e(asset_url('assets/css/style.css')); ?>?v=<?php echo filemtime(BASE_PATH . '/assets/css/style.css') ?: '1'; ?>">

    <!-- Piccole utilità CSS (solo per header/nav + accessibilità) -->
    <style>
        /* Evita shift per anchor con header sticky */
        html {
            scroll-padding-top: 96px;
        }

        /* Focus visibile e coerente */
        :focus-visible {
            outline: 2px solid rgba(59, 130, 246, .9);
            outline-offset: 3px;
        }
    </style>
</head>

<body class="is-loading bg-base-950 text-white antialiased">
    <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-2xl focus:bg-accent-blue focus:px-4 focus:py-2 focus:font-bold focus:text-white"
        href="#main-content">
        Salta al contenuto principale
    </a>

    <div class="page-loader" id="page-loader" aria-hidden="true">
        <div class="spinner"></div>
        <p>Caricamento pagina...</p>
    </div>

    <!-- HEADER: moderno, glass, sticky -->
    <header class="site-header sticky top-0 z-50 border-b border-white/10 bg-base-950/60 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route_url('')); ?>"
                        class="group inline-flex items-center gap-3 rounded-2xl px-2 py-1 transition hover:bg-white/5">
                        <span
                            class="grid h-9 w-9 place-items-center rounded-2xl bg-white/5 ring-1 ring-white/10 shadow-lg shadow-black/30">
                            <span class="font-display text-sm font-extrabold tracking-tight text-accent-blue">AT</span>
                        </span>
                        <span class="leading-tight">
                            <span class="block font-display text-base font-extrabold tracking-tight">
                                Antonio Trapasso
                            </span>
                            <span class="block text-xs text-white/60 -mt-0.5">
                                Curriculum Vitae
                            </span>
                        </span>
                    </a>
                </div>

                <!-- Desktop nav -->
                <nav class="hidden md:block" aria-label="Menu principale">
                    <ul class="flex items-center gap-1">
                        <?php
                        $homeHref = $isHomePage ? '#home' : route_url('') . '#home';
                        $profiloHref = $isHomePage ? '#profilo' : route_url('') . '#profilo';
                        $progettiHref = $isHomePage ? '#progetti' : route_url('') . '#progetti';
                        $esperienzaHref = $isHomePage ? '#esperienza' : route_url('') . '#esperienza';
                        $competenzeHref = $isHomePage ? '#competenze' : route_url('') . '#competenze';
                        $contattiHref = $isHomePage ? '#contatti' : route_url('') . '#contatti';
                        ?>
                        <li>
                            <a href="<?php echo e($homeHref); ?>"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/5 transition">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e($progettiHref); ?>"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/5 transition">
                                Progetti
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e($esperienzaHref); ?>"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/5 transition">
                                Esperienza
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e($competenzeHref); ?>"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/5 transition">
                                Competenze
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e($contattiHref); ?>"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/5 transition">
                                Contatti
                            </a>
                        </li>
                        <li>
                            <a href="https://testscript.info/app"
                                class="rounded-2xl px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/5 transition">
                                Portfolio
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Mobile menu button -->
                <button type="button"
                    class="md:hidden inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-semibold hover:bg-white/10 transition"
                    aria-label="Apri menu" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-btn">
                    <span class="mr-2 text-white/80">Menu</span>
                    <svg class="h-5 w-5 text-white/80" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <!-- Mobile panel -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <div class="mt-2 rounded-3xl border border-white/10 bg-white/5 p-2 shadow-xl shadow-black/30">
                    <a href="<?php echo e($homeHref); ?>"
                        class="block rounded-2xl px-4 py-3 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/10 transition">
                        Home
                    </a>
                    <a href="<?php echo e($progettiHref); ?>"
                        class="block rounded-2xl px-4 py-3 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/10 transition">
                        Progetti
                    </a>
                    <a href="<?php echo e($esperienzaHref); ?>"
                        class="block rounded-2xl px-4 py-3 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/10 transition">
                        Esperienza
                    </a>
                    <a href="<?php echo e($competenzeHref); ?>"
                        class="block rounded-2xl px-4 py-3 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/10 transition">
                        Competenze
                    </a>
                    <a href="<?php echo e($contattiHref); ?>"
                        class="block rounded-2xl px-4 py-3 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/10 transition">
                        Contatti
                    </a>
                    <a href="https://testscript.info/app"
                        class="block rounded-2xl px-4 py-3 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/10 transition">
                        Portfolio
                    </a>
                </div>
            </div>
        </div>

        <script nonce="<?php echo htmlspecialchars($csp_nonce, ENT_QUOTES, 'UTF-8'); ?>">
            (function() {
                var btn = document.getElementById('mobile-menu-btn');
                var menu = document.getElementById('mobile-menu');
                if (!btn || !menu) return;

                btn.addEventListener('click', function() {
                    var isOpen = !menu.classList.contains('hidden');
                    if (isOpen) {
                        menu.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                    } else {
                        menu.classList.remove('hidden');
                        btn.setAttribute('aria-expanded', 'true');
                    }
                });

                // Chiudi menu quando clicchi un link
                menu.querySelectorAll('a').forEach(function(a) {
                    a.addEventListener('click', function() {
                        menu.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                    });
                });
            })();
        </script>
    </header>