# Database locale

Questa cartella ospita migrazioni SQL e asset per il database locale.

## Obiettivo

- Supportare sviluppo locale per dashboard admin e frontend dinamico
- Tenere traccia delle evoluzioni schema in file versionati
- Gestire persistenza messaggi contatto (`contact_messages`)
- Gestire link esterni opzionali per competenze (`skills.link_url`)

## Convenzioni

- Ogni migration usa prefisso timestamp: `YYYYMMDD_HHMMSS_nome.sql`
- Le migration sono idempotenti (`IF NOT EXISTS` quando possibile)
- Tabelle e colonne usano `snake_case`

## Comandi utili

- Eseguire migration locali: `php database/migrate.php`
- Verifica schema fase 2: `php database/verify_phase2.php`
- Verifica schema fase 5: `php database/verify_phase5.php`
- Seed/aggiornamento admin locale: `php database/seed_admin.php`
- Seed dati demo locali: `php database/seed_demo.php`
