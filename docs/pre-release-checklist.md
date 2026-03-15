# Pre-Release Checklist (Locale)

Data ultimo controllo: 2026-02-23

## Setup e database

- [x] Migrazioni eseguite (`php database/migrate.php`)
- [x] Schema Fase 5 verificato (`php database/verify_phase5.php`)
- [x] Account admin locale pronto (`php database/seed_admin.php`)
- [x] Dati demo disponibili (`php database/seed_demo.php`)

## Quality gate tecnico

- [x] Lint PHP senza errori sintattici
- [x] Navigazione verificata (menu, ancore, offset navbar sticky)
- [x] Wiring verificato (controller, viste, asset CSS/JS, routing)
- [x] Funzionalita verificate (login admin, CRUD contenuti, form messaggio istantaneo)
- [x] SEO base verificata (title, meta description, OG tags, canonical, JSON-LD)

## Comando unico consigliato

- [x] Eseguito `scripts/phase6_quality_gate.ps1`

## Note rilascio locale

- Warning ambiente non bloccante: `Module "mysqli" is already loaded`
- Se necessario, rigenerare dati demo: `php database/seed_demo.php --fresh`
