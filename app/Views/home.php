<?php
$profileHtml = isset($profileHtml) && is_string($profileHtml) ? $profileHtml : '';
$profileText = trim(preg_replace('/\s+/', ' ', strip_tags($profileHtml)) ?? '');
if ($profileText === '') {
    $profileText = (string) ($settings['profile_text'] ?? '');
}
$contactEmail = (string) ($settings['contact_email'] ?? '');
$contactLinkedinLabel = (string) ($settings['contact_linkedin_label'] ?? 'Profilo professionale');
$contactLinkedinUrl = (string) ($settings['contact_linkedin_url'] ?? '');
$contactLinkedinHref = $contactLinkedinUrl !== '' ? $contactLinkedinUrl : '#';
$contactPhone = (string) ($settings['contact_phone'] ?? '');
$contactIntro = (string) ($settings['contact_intro'] ?? '');
$contactIntroHtml = isset($contactIntroHtml) && is_string($contactIntroHtml) ? $contactIntroHtml : '';

$yearsExperience = max(1, (int) date('Y') - 2012);
$totalProjects = count($projects);
$totalTechnologies = count($skills);
// $totalTests passed from controller

$personSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => 'Antonio Trapasso',
    'jobTitle' => 'Full-Stack Developer',
    'description' => $profileText,
    'email' => $contactEmail !== '' ? 'mailto:' . $contactEmail : null,
    'telephone' => $contactPhone !== '' ? $contactPhone : null,
    'url' => route_url(''),
    'sameAs' => $contactLinkedinUrl !== '' ? [$contactLinkedinUrl] : [],
    'knowsAbout' => array_map(static fn(array $s): string => (string) ($s['name'] ?? ''), $skills),
];
$personSchema = array_filter($personSchema, static fn(mixed $value): bool => $value !== null && $value !== []);
$personSchemaJson = json_encode($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$skillCategories = [];
foreach ($skills as $skill) {
    $cat = (string) ($skill['category'] ?? 'Altro');
    if ($cat === '') $cat = 'Altro';
    $skillCategories[$cat][] = $skill;
}

$colorMap = [
    'accent-blue'   => ['border' => 'border-blue-400/20',  'bg' => 'bg-blue-400/10',  'text' => 'text-blue-300',  'ring' => 'ring-blue-400/20'],
    'accent-violet'  => ['border' => 'border-violet-400/20', 'bg' => 'bg-violet-400/10', 'text' => 'text-violet-300', 'ring' => 'ring-violet-400/20'],
    'emerald'         => ['border' => 'border-emerald-400/20', 'bg' => 'bg-emerald-400/10', 'text' => 'text-emerald-300', 'ring' => 'ring-emerald-400/20'],
    'sky'             => ['border' => 'border-sky-400/20',   'bg' => 'bg-sky-400/10',   'text' => 'text-sky-300',   'ring' => 'ring-sky-400/20'],
    'fuchsia'         => ['border' => 'border-fuchsia-400/20', 'bg' => 'bg-fuchsia-400/10', 'text' => 'text-fuchsia-300', 'ring' => 'ring-fuchsia-400/20'],
    'amber'           => ['border' => 'border-amber-400/20', 'bg' => 'bg-amber-400/10', 'text' => 'text-amber-300', 'ring' => 'ring-amber-400/20'],
];

$skillIcons = [
    'PHP' => '<path d="M7.01 10.207h-.944l-.515 2.648h.838c.556 0 .97-.105 1.242-.314.272-.21.455-.559.55-1.049.092-.47.05-.802-.124-.995-.175-.193-.523-.29-1.047-.29zM12 5.688C5.373 5.688 0 8.514 0 12s5.373 6.313 12 6.313S24 15.486 24 12c0-3.486-5.373-6.312-12-6.312zm-3.26 7.451c-.261.25-.575.438-.917.551-.336.108-.765.164-1.285.164H5.357l-.327 1.681H3.652l1.23-6.326h2.65c.797 0 1.378.209 1.744.628.366.418.476 1.002.33 1.752a2.836 2.836 0 0 1-.866 1.55zm4.266.45l.67-3.451H12.36l.126-.652h3.652l-.126.652h-1.317l-.67 3.451h-1.019zm5.053-2.201c-.092.47-.29.856-.593 1.158-.305.303-.695.514-1.176.636-.336.085-.805.128-1.405.128h-.838l-.32 1.645h-1.018l1.23-6.326h2.649c.797 0 1.378.209 1.744.628.366.418.476 1.002.33 1.752-.025.126-.056.252-.093.379h-.51zm-1.186-.17c.092-.47.05-.802-.124-.995-.175-.193-.523-.29-1.047-.29h-.944l-.515 2.648h.838c.556 0 .97-.105 1.242-.314.272-.21.455-.559.55-1.049z"/>',
    'JavaScript' => '<path d="M0 0h24v24H0V0zm22.034 18.276c-.175-1.095-.888-2.015-3.003-2.873-.736-.345-1.554-.585-1.797-1.14-.091-.33-.105-.51-.046-.705.15-.646.915-.84 1.515-.66.39.12.75.42.976.9 1.034-.676 1.034-.676 1.755-1.125-.27-.42-.405-.585-.585-.765-.63-.675-1.47-1.02-2.834-.975l-.705.089c-.676.165-1.32.525-1.71 1.005-1.14 1.275-.81 3.51.6 4.44 1.395.96 3.44 1.17 3.69 2.07.24 1.065-.795 1.41-1.8 1.29-.75-.18-1.17-.585-1.62-1.29l-1.83 1.05c.21.48.45.69.81 1.11 1.74 1.74 6.09 1.65 6.87-1.005.03-.09.24-.735.045-1.755l.001-.001zM14.054 12.44l-1.83.002-.002 5.664c0 1.17.06 2.235-.12 2.565-.29.585-1.05.51-1.395.405-.345-.165-.525-.39-.735-.69l-.015-.03-1.83 1.11c.3.63.735 1.17 1.32 1.515.855.495 2.004.66 3.21.36.78-.24 1.455-.72 1.8-1.44.495-.99.39-2.205.39-3.54l.003-5.925-.002.003z"/>',
    'TypeScript' => '<path d="M1.125 0C.502 0 0 .502 0 1.125v21.75C0 23.498.502 24 1.125 24h21.75c.623 0 1.125-.502 1.125-1.125V1.125C24 .502 23.498 0 22.875 0zm17.363 9.75c.612 0 1.154.037 1.627.111a6.38 6.38 0 0 1 1.306.34v2.458a3.95 3.95 0 0 0-.643-.361 5.093 5.093 0 0 0-.717-.26 5.453 5.453 0 0 0-1.426-.2c-.3 0-.573.028-.819.086a2.1 2.1 0 0 0-.623.242c-.17.104-.3.229-.393.374a.888.888 0 0 0-.14.49c0 .196.053.373.156.529.104.156.252.304.443.444s.423.276.696.41c.273.135.582.274.926.416.47.197.892.407 1.266.628.374.222.695.473.963.753.268.279.472.598.614.957.142.359.214.776.214 1.253 0 .657-.125 1.21-.373 1.656a3.033 3.033 0 0 1-1.012 1.085 4.38 4.38 0 0 1-1.487.596c-.566.12-1.163.18-1.79.18a9.916 9.916 0 0 1-1.84-.164 5.544 5.544 0 0 1-1.512-.493v-2.63a5.033 5.033 0 0 0 3.237 1.2c.333 0 .624-.03.872-.09.249-.06.456-.144.623-.25.166-.108.29-.234.373-.38a1.023 1.023 0 0 0-.074-1.089 2.12 2.12 0 0 0-.537-.5 5.597 5.597 0 0 0-.807-.444 27.72 27.72 0 0 0-1.007-.436c-.918-.383-1.602-.852-2.053-1.405-.45-.553-.676-1.222-.676-2.005 0-.614.123-1.141.369-1.582.246-.441.58-.804 1.004-1.089a4.494 4.494 0 0 1 1.47-.629 7.536 7.536 0 0 1 1.77-.201zm-15.113.188h9.563v2.166H9.506v9.646H6.789v-9.646H3.375z"/>',
    'React' => '<path d="M14.23 12.004a2.236 2.236 0 0 1-2.235 2.236 2.236 2.236 0 0 1-2.236-2.236 2.236 2.236 0 0 1 2.235-2.236 2.236 2.236 0 0 1 2.236 2.236zm2.648-10.69c-1.346 0-3.107.96-4.888 2.622-1.78-1.653-3.542-2.602-4.887-2.602-.31 0-.592.06-.842.174-1.58.72-1.968 3.31-.947 6.564C2.704 9.22 1.5 10.535 1.5 12c0 3.314 4.958 6 11.079 6h.842C19.542 18 24.5 15.314 24.5 12c0-1.466-1.204-2.78-3.316-3.928 1.02-3.253.633-5.844-.947-6.564a1.88 1.88 0 0 0-.842-.174zM12 16.5c-4.142 0-7.5-2.015-7.5-4.5S7.858 7.5 12 7.5s7.5 2.015 7.5 4.5-3.358 4.5-7.5 4.5z"/>',
    'MySQL' => '<path d="M16.405 5.501c-.115 0-.193.014-.274.033v.013h.014c.054.104.146.18.214.273.054.107.1.214.154.32l.014-.015c.094-.066.14-.172.14-.333-.04-.047-.046-.094-.08-.14-.04-.067-.126-.1-.18-.153zM5.77 18.695h-.927a50.854 50.854 0 0 0-.27-4.41h-.008l-1.41 4.41H2.45l-1.4-4.41h-.01a72.892 72.892 0 0 0-.195 4.41H0c.055-1.966.192-3.81.41-5.53h1.15l1.335 4.064h.008l1.347-4.064h1.095c.242 2.015.384 3.86.428 5.53zm4.017-4.08c-.378 2.045-.876 3.533-1.492 4.46-.482.73-1.01 1.095-1.58 1.095-.152 0-.34-.046-.564-.138v-.477c.106.017.227.026.362.026.282 0 .517-.087.707-.264.216-.2.324-.44.324-.72 0-.18-.075-.54-.225-1.08L6.4 14.615h.855l.714 2.615c.16.58.24.943.24 1.08.402-1.168.673-2.42.816-3.843l.84.148zm11.236 4.08h-3.905v-5.53h.884v4.768h3.02v.762zm-5.677 0h-.884v-5.53h.884v5.53z"/>',
    'HTML5' => '<path d="M1.5 0h21l-1.91 21.563L11.977 24l-8.564-2.438L1.5 0zm7.031 9.75l-.232-2.718 10.059.003.071-.747.168-1.97.012-.14H6.42l.627 7.068h7.098l-.296 3.274-.6.162-2.272.614-.633-.172-.36-4.047H8.61l.694 7.84 3.648.993 3.71-1.025L17.356 9.75H8.531z"/>',
    'CSS3' => '<path d="M1.5 0h21l-1.91 21.563L11.977 24l-8.565-2.438L1.5 0zm17.09 4.413L5.41 4.41l.213 2.622 10.125.002-.255 2.716h-6.64l.24 2.573h6.182l-.366 3.523-2.91.804-2.956-.81-.188-2.11h-2.61l.29 3.855L12 19.002l5.355-1.12.616-6.88L7.65 11.005l-.165-2.014 11.21-.003z"/>',
    'TailwindCSS' => '<path d="M12.001 4.8c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C13.666 10.618 15.027 12 18.001 12c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C16.337 6.182 14.976 4.8 12.001 4.8zm-6 7.2c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624 1.177 1.194 2.538 2.576 5.512 2.576 3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C10.337 13.382 8.976 12 6.001 12z"/>',
    'Git' => '<path d="M23.546 10.93L13.067.452c-.604-.603-1.582-.603-2.188 0L8.708 2.627l2.76 2.76c.645-.215 1.379-.07 1.889.441.516.515.658 1.258.438 1.9l2.66 2.66c.645-.222 1.387-.078 1.9.435.72.72.72 1.884 0 2.604-.72.719-1.885.719-2.604 0-.516-.515-.658-1.27-.435-1.91l-2.48-2.48v6.53c.175.087.34.2.484.344.72.72.72 1.884 0 2.604-.72.72-1.884.72-2.604 0-.72-.72-.72-1.884 0-2.604.177-.177.381-.312.602-.402V8.856a1.843 1.843 0 0 1-.602-.403c-.52-.52-.66-1.28-.436-1.918L7.928 3.785.456 11.26c-.604.603-.604 1.582 0 2.186l10.48 10.477c.604.604 1.582.604 2.186 0l10.43-10.43c.604-.604.604-1.582-.006-2.563z"/>',
    'Node.js' => '<path d="M11.998 24c-.321 0-.641-.084-.922-.247L8.14 22.016c-.438-.245-.224-.332-.08-.383.613-.213.738-.262 1.392-.633.069-.038.159-.024.23.018l2.26 1.341c.082.045.198.045.275 0l8.795-5.076c.084-.048.138-.144.138-.236V7.971c0-.098-.054-.19-.14-.24L12.138 2.66a.274.274 0 0 0-.273 0L3.079 7.732c-.087.05-.14.147-.14.241v10.075c0 .094.053.187.136.237l2.409 1.392c1.307.654 2.108-.116 2.108-.89V8.843c0-.142.114-.253.256-.253h1.115c.139 0 .255.111.255.253v9.944c0 1.74-.948 2.74-2.599 2.74-.507 0-.907 0-2.024-.55l-2.306-1.327A1.85 1.85 0 0 1 1.365 18.05V7.971c0-.648.346-1.254.91-1.578L11.07.319a1.913 1.913 0 0 1 1.856 0l8.795 5.074c.564.325.91.93.91 1.578v10.076a1.85 1.85 0 0 1-.91 1.577l-8.795 5.076a1.833 1.833 0 0 1-.928.3z"/>',
];
?>

<main id="main-content">

    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section class="hero relative overflow-hidden" id="home">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-10 -left-24 h-52 w-72 rounded-full bg-accent-blue/15 blur-3xl"></div>
            <div class="absolute -bottom-10 -right-24 h-76 w-96 rounded-full bg-accent-violet/10 blur-3xl"></div>
            <div class="absolute top-1/3 left-1/2 -translate-x-1/2 h-64 w-64 rounded-full bg-cyan-400/5 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-base-950 via-base-950/70 to-base-950"></div>
            <!-- Animated grid -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image:linear-gradient(rgba(255,255,255,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.1) 1px,transparent 1px);background-size:60px 60px"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14 lg:py-16">
            <div class="grid items-center gap-10 lg:grid-cols-12">

                <!-- Copy -->
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-4 py-2 shadow-lg shadow-black/30">
                        <span class="relative inline-block h-2.5 w-2.5">
                            <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-75"></span>
                            <span class="relative inline-block h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        </span>
                        <p class="eyebrow m-0 text-xs font-semibold tracking-wide text-emerald-200">
                            Disponibile per nuovi progetti
                        </p>
                    </div>

                    <h1 class="mt-5 font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white">
                        Antonio Trapasso
                    </h1>

                    <p class="mt-2 flex items-center gap-2 text-lg sm:text-xl font-semibold text-accent-blue">
                        <span>Full-Stack Developer</span>
                        <span class="text-white/30">|</span>
                        <span id="typing-role" class="text-accent-violet" aria-label="Specializzazioni"></span>
                        <span class="animate-pulse text-accent-violet">|</span>
                    </p>

                    <p class="intro mt-4 max-w-2xl text-base sm:text-lg text-white/70 leading-relaxed">
                        Costruisco piattaforme reali, scalabili e production-ready. API robuste, UI moderne, architetture enterprise. Dal codice alla struttura completa.
                    </p>

                    <!-- Counter animati -->
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="counter-card rounded-2xl border border-white/10 bg-white/5 p-3 text-center backdrop-blur-sm">
                            <span class="counter block text-2xl sm:text-3xl font-extrabold text-white" data-target="<?php echo $yearsExperience; ?>">0</span>
                            <span class="block text-xs text-white/50 mt-1">Anni Esperienza</span>
                        </div>
                        <div class="counter-card rounded-2xl border border-white/10 bg-white/5 p-3 text-center backdrop-blur-sm">
                            <span class="counter block text-2xl sm:text-3xl font-extrabold text-accent-blue" data-target="<?php echo $totalProjects; ?>">0</span>
                            <span class="block text-xs text-white/50 mt-1">Progetti Live</span>
                        </div>
                        <div class="counter-card rounded-2xl border border-white/10 bg-white/5 p-3 text-center backdrop-blur-sm">
                            <span class="counter block text-2xl sm:text-3xl font-extrabold text-accent-violet" data-target="<?php echo $totalTechnologies; ?>">0</span>
                            <span class="block text-xs text-white/50 mt-1">Tecnologie</span>
                        </div>
                        <div class="counter-card rounded-2xl border border-white/10 bg-white/5 p-3 text-center backdrop-blur-sm">
                            <span class="counter block text-2xl sm:text-3xl font-extrabold text-emerald-400" data-target="<?php echo $totalTests; ?>">0</span>
                            <span class="block text-xs text-white/50 mt-1">Test Passati</span>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="hero-actions mt-7 flex flex-col sm:flex-row gap-3">
                        <a class="button primary group inline-flex items-center justify-center gap-2 rounded-2xl bg-accent-blue px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-accent-blue/25 transition hover:bg-blue-400 hover:shadow-accent-blue/40"
                            href="#progetti">
                            <span>Vedi Progetti</span>
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M13.3 17.3a1 1 0 0 1-1.4-1.4L15.17 12.5H4a1 1 0 1 1 0-2h11.17l-3.3-3.3a1 1 0 0 1 1.42-1.4l5 5a1 1 0 0 1 0 1.4l-5 5z" />
                            </svg>
                        </a>
                        <a class="button secondary inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white"
                            href="<?php echo e(route_url('cv/export')); ?>">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M12 3a1 1 0 0 1 1 1v8.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.42L11 12.59V4a1 1 0 0 1 1-1z" />
                                <path fill="currentColor" d="M5 19a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1z" />
                            </svg>
                            <span>Scarica CV</span>
                        </a>
                        <a class="button secondary inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white"
                            href="#contatti">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 2v.01L12 13 4 6.01V6h16zM4 18V8.2l7.4 6.17a1 1 0 0 0 1.2 0L20 8.2V18H4z" />
                            </svg>
                            <span>Contatti</span>
                        </a>
                    </div>
                </div>

                <!-- Profile card -->
                <div class="lg:col-span-5">
                    <figure class="profile-card relative mx-auto w-full max-w-sm">
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-3 shadow-2xl shadow-black/40 backdrop-blur-sm">
                            <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/10">
                                <img class="block h-auto w-full object-cover"
                                    src="<?php echo e(asset_url('assets/img/tony_2013_-600x754.webp')); ?>"
                                    alt="Foto profilo di Antonio Trapasso" width="600" height="754" loading="eager">
                                <div class="absolute inset-0 bg-gradient-to-t from-base-950/60 via-transparent to-transparent"></div>
                                <div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-base-950/60 px-3 py-2 text-xs font-semibold text-white/80 backdrop-blur">
                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-400/90"></span>
                                    Profilo verificato
                                </div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <p class="font-display text-lg font-extrabold text-white drop-shadow-lg">Full-Stack Developer</p>
                                    <p class="text-xs text-white/70 drop-shadow-lg">Architetture Scalabili &bull; UI Moderne &bull; API Enterprise</p>
                                </div>
                            </div>
                            <figcaption class="mt-3 flex items-center justify-between gap-3 px-1">
                                <div class="flex items-center gap-2">
                                    <div class="flex -space-x-1">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-500/20 ring-2 ring-base-900 text-[10px] font-bold text-blue-300">R</span>
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-violet-500/20 ring-2 ring-base-900 text-[10px] font-bold text-violet-300">P</span>
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 ring-2 ring-base-900 text-[10px] font-bold text-emerald-300">T</span>
                                    </div>
                                    <span class="text-xs text-white/50">React &bull; PHP &bull; TypeScript</span>
                                </div>
                                <a class="inline-flex items-center justify-center gap-1.5 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-white/80 hover:bg-white/10 hover:text-white transition"
                                    href="<?php echo e($contactLinkedinHref); ?>" target="_blank"
                                    rel="noopener noreferrer" aria-label="Apri LinkedIn" title="Apri LinkedIn">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill="currentColor" d="M6.6 8.6H3.2V21h3.4V8.6zM4.9 3A2 2 0 1 0 5 7a2 2 0 0 0-.1-4zM20.8 13.9c0-3.7-2-5.4-4.7-5.4-2.2 0-3.1 1.2-3.6 2v-1.8H9.1V21h3.4v-6.1c0-1.6.3-3.2 2.3-3.2 2 0 2 1.9 2 3.3V21h3.4v-7.1z" />
                                    </svg>
                                    LinkedIn
                                </a>
                            </figcaption>
                        </div>
                    </figure>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════════════ PROFILO ═══════════════════ -->
    <section class="panel relative overflow-hidden" id="profilo">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -right-12 -top-16 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>
        </div>
        <div class="relative">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border border-cyan-300/20 bg-cyan-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-200">
                    Profilo
                </span>
                <h2 class="m-0 text-2xl font-extrabold tracking-tight text-white">Profilo Professionale</h2>
            </div>
            <div class="profile-content mt-5 rounded-2xl border border-white/10 bg-white/5 p-6 text-sm leading-relaxed text-white/80 backdrop-blur-sm">
                <?php if ($profileHtml !== ''): ?>
                    <?php echo $profileHtml; ?>
                <?php else: ?>
                    <?php echo nl2br(e($profileText), false); ?>
                <?php endif; ?>
            </div>
            <!-- Highlight cards sotto il profilo -->
            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-accent-blue/15 text-accent-blue ring-1 ring-accent-blue/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-white">Architettura Full-Stack</p>
                        <p class="text-xs text-white/55">Frontend React, Backend PHP, API REST, Database MySQL.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-300/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-white">Qualità &amp; Testing</p>
                        <p class="text-xs text-white/55">548+ test automatizzati, 75% coverage, CI/CD ready.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-sky-400/15 text-sky-300 ring-1 ring-sky-300/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-white">Multilingua &amp; SEO</p>
                        <p class="text-xs text-white/55">Piattaforme in 6 lingue, Lighthouse 95+, Schema.org.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ PROGETTI ═══════════════════ -->
    <section class="panel relative overflow-hidden" id="progetti">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-20 top-1/4 h-64 w-64 rounded-full bg-accent-violet/10 blur-3xl"></div>
            <div class="absolute -right-20 bottom-0 h-48 w-48 rounded-full bg-accent-blue/8 blur-3xl"></div>
        </div>
        <div class="relative">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border border-accent-violet/20 bg-accent-violet/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-violet-200">
                    Progetti
                </span>
                <h2 class="m-0 text-2xl font-extrabold tracking-tight text-white">Progetti Live &amp; Open Source</h2>
            </div>
            <p class="mt-2 max-w-2xl text-sm text-white/55">Ogni progetto è deployato in produzione con test automatizzati, CI/CD e documentazione completa.</p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($projects as $pi => $project):
                    $pc = $colorMap[$project['color']] ?? $colorMap['accent-blue'];
                ?>
                    <article class="group relative flex flex-col rounded-2xl border <?php echo $pc['border']; ?> bg-base-900/55 shadow-lg shadow-black/20 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-black/30 overflow-hidden">
                        <!-- Top accent bar -->
                        <div class="h-1 w-full <?php echo $pc['bg']; ?>"></div>
                        <div class="flex flex-1 flex-col p-5">
                            <!-- Header -->
                            <div class="flex items-start justify-between gap-2">
                                <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl <?php echo $pc['bg']; ?> <?php echo $pc['text']; ?> ring-1 <?php echo $pc['ring']; ?>">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill="currentColor" d="M3 3h18v18H3V3zm2 2v14h14V5H5zm2 2h10v2H7V7zm0 4h10v2H7v-2zm0 4h7v2H7v-2z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold uppercase tracking-wider <?php echo $pc['text']; ?> opacity-60">#<?php echo $pi + 1; ?></span>
                            </div>
                            <h3 class="mt-3 text-base font-bold text-white group-hover:<?php echo $pc['text']; ?> transition"><?php echo e($project['name']); ?></h3>
                            <p class="mt-2 flex-1 text-xs text-white/60 leading-relaxed"><?php echo e($project['description']); ?></p>

                            <!-- Tech stack -->
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                <?php foreach ($project['tech'] as $tech): ?>
                                    <span class="rounded-full border border-white/10 bg-white/5 px-2 py-0.5 text-[10px] font-semibold text-white/70"><?php echo e($tech); ?></span>
                                <?php endforeach; ?>
                            </div>

                            <!-- Metrics -->
                            <div class="mt-3 flex flex-wrap gap-2">
                                <?php foreach ($project['metrics'] as $metric): ?>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold <?php echo $pc['text']; ?>">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24">
                                            <path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                        </svg>
                                        <?php echo e($metric); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                            <!-- Links -->
                            <div class="mt-4 flex items-center gap-2 border-t border-white/10 pt-4">
                                <a class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 px-3 py-1.5 text-xs font-semibold text-white/70 transition hover:bg-white/10 hover:text-white"
                                    href="<?php echo e($project['url']); ?>" target="_blank" rel="noopener noreferrer">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z" />
                                        <path fill="currentColor" d="M5 5h6v2H7v10h10v-4h2v6H5V5z" />
                                    </svg>
                                    Live
                                </a>
                                <a class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 px-3 py-1.5 text-xs font-semibold text-white/70 transition hover:bg-white/10 hover:text-white"
                                    href="<?php echo e($project['github']); ?>" target="_blank" rel="noopener noreferrer">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" />
                                    </svg>
                                    GitHub
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ ESPERIENZA (Timeline) ═══════════════════ -->
    <section class="panel relative overflow-hidden" id="esperienza">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-20 top-10 h-52 w-52 rounded-full bg-accent-blue/10 blur-3xl"></div>
        </div>
        <div class="relative">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border border-accent-blue/20 bg-accent-blue/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-200">
                    Esperienza
                </span>
                <h2 class="m-0 text-2xl font-extrabold tracking-tight text-white">Percorso Professionale</h2>
            </div>

            <!-- Timeline -->
            <div class="timeline mt-8 relative pl-8 sm:pl-10 before:absolute before:left-3 sm:before:left-4 before:top-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-accent-blue before:via-accent-violet before:to-emerald-400/50">
                <?php foreach ($experiences as $ei => $experience): ?>
                    <?php
                    $descriptionHtml = (string) ($experience['description_html'] ?? '');
                    $startDate = (string) ($experience['start_date'] ?? '');
                    $endDate = (string) ($experience['end_date'] ?? '');
                    $period = '';
                    $isCurrent = false;
                    if ($startDate !== '' && $endDate !== '') {
                        $period = $startDate . ' - ' . $endDate;
                    } elseif ($startDate !== '') {
                        $period = $startDate . ' - Presente';
                        $isCurrent = true;
                    } elseif ($endDate !== '') {
                        $period = 'fino al ' . $endDate;
                    }
                    ?>
                    <div class="timeline-item relative mb-8 last:mb-0">
                        <!-- Timeline dot -->
                        <div class="absolute -left-8 sm:-left-10 top-1 flex items-center justify-center">
                            <span class="relative inline-flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-full <?php echo $isCurrent ? 'bg-accent-blue ring-4 ring-accent-blue/20' : 'bg-base-800 ring-2 ring-white/20'; ?>">
                                <?php if ($isCurrent): ?>
                                    <span class="absolute inset-0 rounded-full bg-accent-blue animate-ping opacity-30"></span>
                                <?php endif; ?>
                                <span class="relative text-[10px] font-bold text-white"><?php echo $ei + 1; ?></span>
                            </span>
                        </div>

                        <div class="rounded-2xl border <?php echo $isCurrent ? 'border-accent-blue/30 bg-accent-blue/5' : 'border-white/10 bg-base-900/55'; ?> p-5 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:border-accent-blue/35">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <h3 class="m-0 text-lg font-bold text-white"><?php echo e((string) ($experience['role'] ?? '')); ?></h3>
                                <div class="flex items-center gap-2">
                                    <?php if ($isCurrent): ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-2.5 py-0.5 text-[10px] font-bold text-emerald-300">
                                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                            Attuale
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($period !== ''): ?>
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-white/70">
                                            <?php echo e($period); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($descriptionHtml !== ''): ?>
                                <div class="mt-3 text-sm leading-relaxed text-white/70"><?php echo $descriptionHtml; ?></div>
                            <?php else: ?>
                                <p class="mt-3 text-sm leading-relaxed text-white/70"><?php echo e((string) ($experience['description'] ?? '')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ COMPETENZE ═══════════════════ -->
    <section class="panel relative overflow-hidden" id="competenze">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -right-20 bottom-0 h-56 w-56 rounded-full bg-emerald-400/10 blur-3xl"></div>
        </div>
        <div class="relative">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-200">
                    Competenze
                </span>
                <h2 class="m-0 text-2xl font-extrabold tracking-tight text-white">Stack Tecnologico</h2>
            </div>

            <?php if (count($skillCategories) > 1): ?>
                <!-- Categorized skills -->
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <?php
                    $catColors = [
                        'Frontend' => ['border-blue-400/20', 'bg-blue-400/10', 'text-blue-200', 'text-blue-300'],
                        'Backend'  => ['border-violet-400/20', 'bg-violet-400/10', 'text-violet-200', 'text-violet-300'],
                        'Database' => ['border-emerald-400/20', 'bg-emerald-400/10', 'text-emerald-200', 'text-emerald-300'],
                        'DevOps'   => ['border-amber-400/20', 'bg-amber-400/10', 'text-amber-200', 'text-amber-300'],
                        'Tools'    => ['border-sky-400/20', 'bg-sky-400/10', 'text-sky-200', 'text-sky-300'],
                    ];
                    $defaultCatColor = ['border-white/10', 'bg-white/5', 'text-white/70', 'text-white/80'];
                    ?>
                    <?php foreach ($skillCategories as $catName => $catSkills):
                        $cc = $catColors[$catName] ?? $defaultCatColor;
                    ?>
                        <div class="rounded-2xl border <?php echo $cc[0]; ?> <?php echo $cc[1]; ?> p-5">
                            <h3 class="m-0 text-sm font-bold <?php echo $cc[2]; ?> uppercase tracking-wide"><?php echo e($catName); ?></h3>
                            <ul class="mt-3 flex flex-wrap gap-2">
                                <?php foreach ($catSkills as $skill):
                                    $skillName = (string) ($skill['name'] ?? '');
                                    $skillLink = trim((string) ($skill['link_url'] ?? ''));
                                    $skillLevel = (int) ($skill['level'] ?? 0);
                                ?>
                                    <li class="group relative">
                                        <?php if ($skillLink !== ''): ?>
                                            <a class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold <?php echo $cc[3]; ?> transition hover:bg-white/10 hover:text-white"
                                                href="<?php echo e($skillLink); ?>" target="_blank" rel="noopener noreferrer"
                                                aria-label="<?php echo e('Risorsa: ' . $skillName); ?>">
                                                <?php if (isset($skillIcons[$skillName])): ?>
                                                    <svg class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" aria-hidden="true"><?php echo $skillIcons[$skillName]; ?></svg>
                                                <?php endif; ?>
                                                <?php echo e($skillName); ?>
                                                <svg class="h-3 w-3 opacity-50" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z" />
                                                </svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold <?php echo $cc[3]; ?>">
                                                <?php if (isset($skillIcons[$skillName])): ?>
                                                    <svg class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" aria-hidden="true"><?php echo $skillIcons[$skillName]; ?></svg>
                                                <?php endif; ?>
                                                <?php echo e($skillName); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($skillLevel > 0): ?>
                                            <!-- Skill level indicator -->
                                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 flex gap-0.5">
                                                <?php for ($li = 0; $li < 5; $li++): ?>
                                                    <span class="inline-block h-0.5 w-2 rounded-full <?php echo $li < $skillLevel ? $cc[1] : 'bg-white/10'; ?>"></span>
                                                <?php endfor; ?>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Flat skill list (fallback) -->
                <ul class="mt-5 flex flex-wrap gap-3">
                    <?php foreach ($skills as $skill):
                        $skillName = (string) ($skill['name'] ?? '');
                        $skillLink = trim((string) ($skill['link_url'] ?? ''));
                    ?>
                        <li class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-100 shadow-sm shadow-black/20">
                            <?php if ($skillLink !== ''): ?>
                                <a class="inline-flex items-center gap-1.5 text-emerald-100 transition hover:text-white"
                                    href="<?php echo e($skillLink); ?>" target="_blank" rel="noopener noreferrer"
                                    aria-label="<?php echo e('Apri risorsa competenza: ' . $skillName); ?>">
                                    <?php if (isset($skillIcons[$skillName])): ?>
                                        <svg class="h-4 w-4 opacity-70" viewBox="0 0 24 24" aria-hidden="true"><?php echo $skillIcons[$skillName]; ?></svg>
                                    <?php endif; ?>
                                    <?php echo e($skillName); ?>
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5">
                                    <?php if (isset($skillIcons[$skillName])): ?>
                                        <svg class="h-4 w-4 opacity-70" viewBox="0 0 24 24" aria-hidden="true"><?php echo $skillIcons[$skillName]; ?></svg>
                                    <?php endif; ?>
                                    <?php echo e($skillName); ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <!-- ═══════════════════ FORMAZIONE ═══════════════════ -->
    <section class="panel relative overflow-hidden" id="formazione">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -right-16 top-10 h-48 w-48 rounded-full bg-indigo-400/10 blur-3xl"></div>
        </div>
        <div class="relative">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border border-indigo-300/20 bg-indigo-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200">
                    Formazione
                </span>
                <h2 class="m-0 text-2xl font-extrabold tracking-tight text-white">Formazione &amp; Certificazioni</h2>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-indigo-400/20 bg-indigo-400/5 p-5 transition hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-400/15 text-indigo-300 ring-1 ring-indigo-400/20">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-white">Formazione Continua</p>
                            <p class="text-xs text-white/50">Web Development &amp; Software Engineering</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-white/60 leading-relaxed">Studio costante delle tecnologie emergenti: React, TypeScript, architetture cloud, DevOps practices e pattern enterprise.</p>
                </div>
                <div class="rounded-2xl border border-indigo-400/20 bg-indigo-400/5 p-5 transition hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-400/15 text-indigo-300 ring-1 ring-indigo-400/20">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-white">Esperienza Pratica</p>
                            <p class="text-xs text-white/50"><?php echo $yearsExperience; ?>+ anni nel settore</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-white/60 leading-relaxed">Progetti reali in produzione, collaborazione con team internazionali, gestione completa del ciclo di vita software.</p>
                </div>
                <div class="rounded-2xl border border-indigo-400/20 bg-indigo-400/5 p-5 transition hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-400/15 text-indigo-300 ring-1 ring-indigo-400/20">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-white">Open Source</p>
                            <p class="text-xs text-white/50">Contributi attivi su GitHub</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-white/60 leading-relaxed">Progetti open source, documentazione tecnica, condivisione knowledge base con la community developer.</p>
                </div>
                <div class="rounded-2xl border border-indigo-400/20 bg-indigo-400/5 p-5 transition hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-400/15 text-indigo-300 ring-1 ring-indigo-400/20">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-white">Security &amp; Best Practice</p>
                            <p class="text-xs text-white/50">OWASP, JWT, CSP, CORS</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-white/60 leading-relaxed">Implementazione security headers, autenticazione JWT, CSRF protection, rate limiting, input validation enterprise.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ TECH STACK VISUAL ═══════════════════ -->
    <section class="panel relative overflow-hidden" id="tech-stack">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/4 -top-10 h-40 w-40 rounded-full bg-sky-400/10 blur-3xl"></div>
        </div>
        <div class="relative">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border border-sky-300/20 bg-sky-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sky-200">
                    Stack
                </span>
                <h2 class="m-0 text-2xl font-extrabold tracking-tight text-white">Architettura di Riferimento</h2>
            </div>

            <div class="mt-6 rounded-2xl border border-white/10 bg-base-900/55 p-6 shadow-lg shadow-black/20">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="text-center">
                        <div class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-400/10 text-blue-300 ring-1 ring-blue-400/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M11.572 0c-.176 0-.31.001-.358.007a19.76 19.76 0 0 1-.364.033C7.443.346 4.25 2.185 2.228 5.012a11.875 11.875 0 0 0-2.119 5.243c-.096.659-.108.854-.108 1.747s.012 1.089.108 1.748c.652 4.506 3.86 8.292 8.209 9.695.779.25 1.6.422 2.534.525.363.04 1.935.04 2.299 0 1.611-.178 2.977-.577 4.323-1.264.207-.106.247-.134.219-.158-.02-.013-.9-1.193-1.955-2.62l-1.919-2.592-2.404-3.558a338.739 338.739 0 0 0-2.422-3.556c-.009-.002-.018 1.579-.023 3.51-.007 3.38-.01 3.515-.052 3.595a.426.426 0 0 1-.206.214c-.075.037-.14.044-.495.044H7.81l-.108-.068a.438.438 0 0 1-.157-.171l-.05-.106.006-4.703.007-4.705.072-.092a.645.645 0 0 1 .174-.143c.096-.047.134-.051.54-.051.478 0 .558.018.682.154.035.038 1.337 1.999 2.895 4.361a10760.433 10760.433 0 0 0 4.735 7.17l1.9 2.879.096-.063a12.317 12.317 0 0 0 2.466-2.163 11.944 11.944 0 0 0 2.824-6.134c.096-.66.108-.854.108-1.748 0-.893-.012-1.088-.108-1.747-.652-4.506-3.859-8.292-8.208-9.695a12.597 12.597 0 0 0-2.499-.523A33.119 33.119 0 0 0 11.572 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-white">Frontend</p>
                        <p class="mt-1 text-[10px] text-white/50">React 19 &bull; TypeScript &bull; TailwindCSS &bull; Vite</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-400/10 text-violet-300 ring-1 ring-violet-400/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M7.01 10.207h-.944l-.515 2.648h.838c.556 0 .97-.105 1.242-.314.272-.21.455-.559.55-1.049.092-.47.05-.802-.124-.995-.175-.193-.523-.29-1.047-.29zM12 5.688C5.373 5.688 0 8.514 0 12s5.373 6.313 12 6.313S24 15.486 24 12c0-3.486-5.373-6.312-12-6.312z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-white">Backend</p>
                        <p class="mt-1 text-[10px] text-white/50">PHP 8 &bull; Slim 4 &bull; JWT &bull; PDO</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-400/10 text-emerald-300 ring-1 ring-emerald-400/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 3C7.58 3 4 4.79 4 7v10c0 2.21 3.59 4 8 4s8-1.79 8-4V7c0-2.21-3.58-4-8-4zm0 2c3.87 0 6 1.5 6 2s-2.13 2-6 2-6-1.5-6-2 2.13-2 6-2zM6 17v-2.42c1.23.8 3.39 1.42 6 1.42s4.77-.62 6-1.42V17c0 .5-2.13 2-6 2s-6-1.5-6-2zm0-5v-2.42c1.23.8 3.39 1.42 6 1.42s4.77-.62 6-1.42V12c0 .5-2.13 2-6 2s-6-1.5-6-2z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-white">Database</p>
                        <p class="mt-1 text-[10px] text-white/50">MySQL &bull; SQLite &bull; PDO &bull; Migrations</p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-400/10 text-amber-300 ring-1 ring-amber-400/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M22.67 12l-4.49-4.47a1 1 0 0 0-1.41 0 1 1 0 0 0 0 1.41L20.59 12l-3.8 3.47a1 1 0 0 0 0 1.41 1 1 0 0 0 1.41 0L22.67 12zM1.33 12l4.49 4.47a1 1 0 0 0 1.41 0 1 1 0 0 0 0-1.41L3.41 12l3.8-3.47a1 1 0 0 0 0-1.41 1 1 0 0 0-1.41 0L1.33 12zm6.18 6.16l3.1-12.54a1 1 0 0 1 1.22-.73 1 1 0 0 1 .73 1.22l-3.1 12.54a1 1 0 0 1-1.22.73 1 1 0 0 1-.73-1.22z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-white">DevOps &amp; Test</p>
                        <p class="mt-1 text-[10px] text-white/50">Vitest &bull; PHPUnit &bull; Playwright &bull; Git</p>
                    </div>
                </div>

                <!-- Architecture flow -->
                <div class="mt-6 flex items-center justify-center gap-2 text-xs text-white/40">
                    <span class="rounded-lg bg-blue-400/10 px-2 py-1 text-blue-300 font-semibold">React SPA</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z" />
                    </svg>
                    <span class="rounded-lg bg-violet-400/10 px-2 py-1 text-violet-300 font-semibold">REST API</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z" />
                    </svg>
                    <span class="rounded-lg bg-emerald-400/10 px-2 py-1 text-emerald-300 font-semibold">MySQL</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z" />
                    </svg>
                    <span class="rounded-lg bg-amber-400/10 px-2 py-1 text-amber-300 font-semibold">CI/CD</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ CONTATTI ═══════════════════ -->
    <section class="panel relative overflow-hidden" id="contatti">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-16 -bottom-20 h-56 w-56 rounded-full bg-fuchsia-400/10 blur-3xl"></div>
        </div>
        <div class="relative">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border border-fuchsia-300/20 bg-fuchsia-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-fuchsia-200">
                    Contatti
                </span>
                <h2 class="m-0 text-2xl font-extrabold tracking-tight text-white">Parliamo Del Tuo Progetto</h2>
            </div>
            <p class="mt-2 max-w-xl text-sm text-white/55">Hai un progetto in mente? Cerchi un developer full-stack? Scrivimi e rispondo entro 24 ore.</p>

            <div class="mt-5 grid gap-5 lg:grid-cols-5">
                <div class="space-y-3 lg:col-span-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-white/20">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                                <svg class="h-4 w-4" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 2v.01L12 13 4 6.01V6h16zM4 18V8.2l7.4 6.17a1 1 0 0 0 1.2 0L20 8.2V18H4z" />
                                </svg>
                            </span>
                            <div>
                                <p class="m-0 text-xs uppercase tracking-wide text-white/40">Email</p>
                                <a class="mt-0.5 inline-block text-sm font-semibold text-cyan-200 hover:text-white transition" href="mailto:<?php echo e($contactEmail); ?>">
                                    <?php echo e($contactEmail); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if ($contactPhone !== ''): ?>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-white/20">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="m-0 text-xs uppercase tracking-wide text-white/40">Telefono</p>
                                    <a class="mt-0.5 inline-block text-sm font-semibold text-cyan-200 hover:text-white transition" href="tel:<?php echo e($contactPhone); ?>">
                                        <?php echo e($contactPhone); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-white/20">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                                <svg class="h-4 w-4" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M6.6 8.6H3.2V21h3.4V8.6zM4.9 3A2 2 0 1 0 5 7a2 2 0 0 0-.1-4zM20.8 13.9c0-3.7-2-5.4-4.7-5.4-2.2 0-3.1 1.2-3.6 2v-1.8H9.1V21h3.4v-6.1c0-1.6.3-3.2 2.3-3.2 2 0 2 1.9 2 3.3V21h3.4v-7.1z" />
                                </svg>
                            </span>
                            <div>
                                <p class="m-0 text-xs uppercase tracking-wide text-white/40">LinkedIn</p>
                                <a class="mt-0.5 inline-block text-sm font-semibold text-cyan-200 hover:text-white transition"
                                    href="<?php echo e($contactLinkedinHref); ?>" target="_blank" rel="noopener noreferrer"
                                    aria-label="Profilo LinkedIn di Antonio Trapasso">
                                    <?php echo e($contactLinkedinLabel); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-white/20">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                                <svg class="h-4 w-4" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" />
                                </svg>
                            </span>
                            <div>
                                <p class="m-0 text-xs uppercase tracking-wide text-white/40">GitHub</p>
                                <a class="mt-0.5 inline-block text-sm font-semibold text-cyan-200 hover:text-white transition"
                                    href="https://github.com/jappalla" target="_blank" rel="noopener noreferrer">
                                    github.com/jappalla
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if ($contactIntroHtml !== ''): ?>
                        <div class="contact-intro rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/70"><?php echo $contactIntroHtml; ?></div>
                    <?php elseif ($contactIntro !== ''): ?>
                        <p class="contact-intro rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/70"><?php echo e($contactIntro); ?></p>
                    <?php endif; ?>
                </div>

                <div class="lg:col-span-3 rounded-2xl border border-white/10 bg-base-900/55 p-5 shadow-lg shadow-black/20">
                    <?php if (is_string($contactSuccess) && $contactSuccess !== ''): ?>
                        <div class="mb-4 rounded-xl border border-emerald-400/20 bg-emerald-400/10 p-3 text-sm font-semibold text-emerald-200"><?php echo e($contactSuccess); ?></div>
                    <?php endif; ?>

                    <?php if (is_string($contactError) && $contactError !== ''): ?>
                        <div class="mb-4 rounded-xl border border-red-400/20 bg-red-400/10 p-3 text-sm font-semibold text-red-200"><?php echo e($contactError); ?></div>
                    <?php endif; ?>

                    <form class="form-grid contact-form mt-0" method="post" action="<?php echo e(route_url('contact/send')); ?>">
                        <?php echo csrf_field(); ?>
                        <label>
                            <span>Nome</span>
                            <input type="text" name="full_name" maxlength="120" required placeholder="Il tuo nome completo">
                        </label>
                        <label>
                            <span>Email</span>
                            <input type="email" name="email" maxlength="190" required placeholder="la-tua@email.com">
                        </label>
                        <label>
                            <span>Oggetto</span>
                            <input type="text" name="subject" maxlength="190" placeholder="Di cosa vorresti parlare?">
                        </label>
                        <label class="wide">
                            <span>Messaggio istantaneo</span>
                            <textarea name="message" rows="4" maxlength="5000" required placeholder="Descrivi il tuo progetto o la tua richiesta..."></textarea>
                        </label>
                        <label class="wide flex items-center gap-2 cursor-pointer select-none" style="display:flex;">
                            <input type="checkbox" name="consent" value="1" checked required
                                class="h-3.5 w-3.5 shrink-0 rounded" style="accent-color:#e879f9;">
                            <span style="font-size:11px;line-height:1;white-space:nowrap;color:rgba(255,255,255,.55);">
                                Acconsento al trattamento dei miei dati personali esclusivamente per rispondere alla presente richiesta.
                                <span class="text-red-400">*</span>
                            </span>
                        </label>
                        <button class="button primary group inline-flex items-center justify-center gap-2" type="submit">
                            <span>Invia Messaggio</span>
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php if (is_string($personSchemaJson) && $personSchemaJson !== ''): ?>
    <script type="application/ld+json">
        <?php echo $personSchemaJson; ?>
    </script>
<?php endif; ?>