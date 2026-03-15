# Roadmap Progetto

Obiettivo: evolvere il progetto CV in una piattaforma con database locale e dashboard admin per aggiornare i contenuti frontend (es. aggiungi Esperienza, aggiungi Competenze), mantenendo i vincoli definiti in `step_progetto.md`.

## Vincoli da rispettare in ogni fase

1. Standard minimo Enterprise
2. Production grade
3. UI/UX first dark mode
4. Test obbligatori (gate fase): Navigazione, wiring, funzionalita, codice, SEO

## Fase 1 - Fondamenta tecniche

- [x] Definire struttura cartelle: `public/`, `app/`, `config/`, `database/`, `partials/`, `assets/`
- [x] Centralizzare configurazione ambiente (`.env` locale) e bootstrap applicazione
- [x] Definire convenzioni codice (naming, error handling, validazione input)
- [x] Output atteso: base progetto ordinata e mantenibile

## Fase 2 - Database locale

- [x] Creare database locale MySQL (es. `cv_portal_local`)
- [x] Definire schema iniziale:
  - [x] tabella `experiences` (ruolo, descrizione, data_inizio, data_fine, ordinamento, visibile)
  - [x] tabella `skills` (nome, categoria, livello, ordinamento, visibile)
  - [x] tabella `users` (admin)
  - [x] tabella `settings` (configurazioni sito)
- [x] Aggiungere migration SQL versionate in `database/migrations/`
- [x] Output atteso: schema pronto per CRUD dashboard e rendering frontend

## Fase 3 - Backend applicativo (CRUD + sicurezza)

- [x] Implementare layer dati (repository/service) separato dal rendering
- [x] Creare CRUD admin per:
  - [x] Esperienza: crea/modifica/elimina/pubblica
  - [x] Competenze: crea/modifica/elimina/pubblica
- [x] Implementare autenticazione admin (sessione, logout, protezione route)
- [x] Aggiungere validazioni server-side e protezioni base (CSRF, escaping output)
- [x] Output atteso: API/azioni backend affidabili e sicure

## Fase 4 - Dashboard admin

- [x] Creare area `/admin` con layout dark-mode coerente ai vincoli
- [x] Pagine principali:
  - [x] Login admin
  - [x] Lista Esperienze + form "Aggiungi Esperienza"
  - [x] Lista Competenze + form "Aggiungi Competenze"
- [x] Inserire feedback utente (messaggi successo/errore) e controlli di conferma
- [x] Output atteso: contenuti gestibili senza toccare codice frontend

## Fase 5 - Integrazione frontend dinamica

- [x] Sostituire contenuti statici di home con dati dal database (`esperienze`, `skills`, `settings`)
- [x] Aggiungere gestione admin per modifica `Profilo` e `Contatti`
- [x] Aggiungere form contatti per invio messaggio istantaneo con persistenza locale (`contact_messages`)
- [x] Mantenere separazione header/footer/css/js gia implementata
- [x] Gestire fallback: se DB vuoto mostrare sezioni con stato iniziale guidato
- [x] Output atteso: frontend aggiornato automaticamente dai dati admin

## Fase 6 - Qualita, test e rilascio locale

- [x] Test Navigazione: menu, ancore, flussi admin
- [x] Test wiring: collegamenti DB, controller, viste, asset
- [x] Test codice: lint PHP/JS, controlli errori, log
- [x] Test SEO: title/meta dinamici, heading structure, performance base
- [x] Preparare checklist pre-release locale e script di seed dati demo
- [x] Footer home con icone social share e hover effect
- [x] Export CV in PDF funzionante (`GET /cv/pdf`)
- [x] Admin Profilo (Home): supporto inserimento HTML con sanitizzazione server-side
- [x] Supporto HTML esteso a tutte le textarea admin (incluse descrizioni esperienza e intro contatti)
- [x] Link social share allineati al contenuto pagina corrente (URL + metadati)
- [x] Uniformato stile link: nessuna sottolineatura in tutta la piattaforma
- [x] Introduzione export hub UX/UI first (`/cv/export`) con anteprima PDF e azioni rapide
- [x] Restyling documento PDF con presentazione visiva migliorata (header, sezioni, tipografia)
- [x] Home hero ottimizzata: altezza ridotta (Tailwind + fallback CSS) per migliorare la prima impressione
- [x] Restyling Tailwind esteso alle altre sezioni home (Profilo, Esperienza, Competenze, Contatti)
- [x] Restyling Tailwind area admin (login + dashboard) con UX coerente
- [x] Preservazione completa del codice HTML inserito in textarea admin (classi Tailwind incluse)
- [x] Competenze con link esterno opzionale gestibile da admin e apertura in nuova scheda dalla home
- [x] Output atteso: versione locale stabile e pronta per ambienti successivi

## Definition of Done

- Dashboard admin operativa su locale
- Esperienze, Competenze, Profilo e Contatti modificabili da pannello e visibili in home
- Ogni competenza puo includere un link esterno configurabile da admin (apertura in nuova scheda)
- Messaggi istantanei da form contatti salvati e consultabili da admin
- Footer con condivisione social e download PDF CV disponibile lato utente
- Esperienza export CV migliorata con hub dedicato e preview immediata
- Vincoli `step_progetto.md` rispettati e verificati con test documentati
