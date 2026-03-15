# Engineering Standards

## Naming

- Classes: `PascalCase` (es. `ErrorHandler`, `Validator`)
- Methods and variables: `camelCase`
- Config keys: `snake_case` inside config arrays
- Table names: plural in `snake_case` (es. `experiences`, `skills`)

## Error handling

- Ogni entrypoint carica `app/bootstrap.php`
- Gli errori PHP sono convertiti in eccezioni tramite `App\Support\ErrorHandler`
- In locale (`APP_DEBUG=true`) viene mostrata una risposta dettagliata
- In produzione (`APP_DEBUG=false`) viene mostrata una risposta generica senza leak tecnici

## Input validation

- Ogni input utente deve essere validato server-side prima di usarlo
- Usare `App\Support\Validator` per stringhe obbligatorie/opzionali e interi
- Tutte le POST admin richiedono CSRF token valido (`App\Support\Csrf`)
- Anche il form contatti pubblico richiede CSRF token e validazione email server-side
- Escapare sempre output HTML con helper `e()`
- Non fidarsi mai di parametri GET/POST/sessione non validati

## Wiring quality gate

- Nessuna fase e considerata completa se non supera:
  - test navigazione
  - test wiring
  - test funzionalita
- Per i controlli tecnici locali usare `scripts/phase6_quality_gate.ps1`
- Ogni passaggio completato va marcato con checklist `- [x]` in `step_progetto.md`
