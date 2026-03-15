# Developer Portal — testscript.info

Portfolio MVC framework per il developer portal di testscript.info.

## Stack

- **PHP 8.2+** — MVC custom (Controllers, Repositories, Services, Support)
- **TailwindCSS v4** — Compilato con `@tailwindcss/cli`
- **MySQL/MariaDB** — Migrations in `database/migrations/`

## Struttura

```
app/
  Controllers/   — Route handlers
  Repositories/  — Data access layer
  Services/      — Business logic
  Support/       — Helpers (Env, Database, Session, Csrf, etc.)
  Views/         — PHP templates
config/          — App & database configuration
database/        — Migrations, seeders
partials/        — Shared header/footer
public/          — Web entry point
assets/          — CSS, JS
router.php       — Route definitions
```

## Setup locale

```bash
cp .env.example .env     # Configura credenziali DB
composer install          # (se vendor/ necessario)
npm install               # TailwindCSS
npx @tailwindcss/cli -i assets/css/tailwind.css -o assets/css/dist.css
```

## Deploy

SCP su Hostinger (vedi `/deploy/DEPLOY.md` nel repo principale).
