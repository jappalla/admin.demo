<?php

/**
 * Configurazione progetti da mostrare nella homepage.
 * Spostato qui dalla view per rispettare il pattern MVC.
 */
return [
    [
        'name' => 'testscript.info',
        'description' => 'Piattaforma full-stack con blog multilingua, 16 demo interattive, dashboard admin e REST API con 54 endpoints.',
        'tech' => ['React 19', 'TypeScript', 'PHP 8', 'Slim 4', 'MySQL', 'TailwindCSS'],
        'url'  => 'https://testscript.info/app',
        'github' => 'https://github.com/jappalla/testscript.info',
        'metrics' => ['627 test', 'Lighthouse 95+', '6 lingue'],
        'color' => 'accent-blue',
    ],
    [
        'name' => 'Developer CV Portal',
        'description' => 'Curriculum vitae dinamico con MVC custom, admin dashboard, PDF export, sistema messaggistica istantanea.',
        'tech' => ['PHP 8', 'TailwindCSS v4', 'MySQL', 'JWT Auth', 'PDF Generator'],
        'url'  => 'https://developer.testscript.info',
        'github' => 'https://github.com/jappalla/admin.demo',
        'metrics' => ['Custom MVC', 'Zero deps PDF', 'CRUD completo'],
        'color' => 'accent-violet',
    ],
    [
        'name' => 'REST API Enterprise',
        'description' => 'API backend con Slim 4, JWT authentication, rate limiting, CORS dinamico, logging strutturato e 54 endpoints documentati.',
        'tech' => ['Slim 4', 'PHP-DI', 'Firebase JWT', 'Monolog', 'PDO/MySQL'],
        'url'  => 'https://testscript.info/API/health',
        'github' => 'https://github.com/jappalla/testscript.info',
        'metrics' => ['54 endpoints', '63 test PHPUnit', '119 assertions'],
        'color' => 'emerald',
    ],
    [
        'name' => 'Interactive Demo Hub',
        'description' => '16+ playground interattivi: Python (Pyodide), SQL (WASM), Regex, Algorithm Visualizer, CSS Grid Builder e altro.',
        'tech' => ['JavaScript', 'WebAssembly', 'Canvas API', 'Pyodide', 'SQLite'],
        'url'  => 'https://testscript.info/demo',
        'github' => 'https://github.com/jappalla/testscript.info',
        'metrics' => ['16 demo live', 'Zero backend', 'Full interactive'],
        'color' => 'sky',
    ],
    [
        'name' => 'Admin Dashboard',
        'description' => 'Pannello amministrativo React con CRUD completo per post, demo, categorie, tag, contatti e statistiche aggregate.',
        'tech' => ['React', 'TypeScript', 'react-hook-form', 'Zod', 'TanStack Query'],
        'url'  => 'https://testscript.info/dashboard',
        'github' => 'https://github.com/jappalla/testscript.info',
        'metrics' => ['333 test', '75% coverage', 'JWT protected'],
        'color' => 'fuchsia',
    ],
    [
        'name' => 'E2E Testing Suite',
        'description' => 'Suite di test end-to-end con Playwright: 96 test su 3 dispositivi (Desktop, Mobile, Tablet) con screenshot on failure.',
        'tech' => ['Playwright', 'TypeScript', 'CI/CD Ready'],
        'url'  => 'https://testscript.info/app',
        'github' => 'https://github.com/jappalla/testscript.info',
        'metrics' => ['96 test E2E', '3 dispositivi', 'Cross-browser'],
        'color' => 'amber',
    ],
];
