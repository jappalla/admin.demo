# step progetto da eseguire

Nota: aggiornare gli step con spunta di successo (`- [x]`) quando un'operazione e' stata completata.

## vincoli per il progetto

utilizzando i seguenti criteri

# vincoli:

1 - Standar minimo Enterprise
2 - Productin grade
3 - uix/ui first dark mode

# Requisiti per il successo:

# eseguire test di

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 01

- [x] loading page (al caricamento)
- [x] pagina home, con header / footer / css / js separati
- [x] contenuto del progetto curriculum vitae personale

# step 02 - roadmap fase 1

- [x] struttura cartelle base: public/app/config/database/partials/assets
- [x] front controller `public/index.php` + compatibilita da `index.php`
- [x] configurazione locale centralizzata con `.env` e `config/`
- [x] bootstrap applicazione con autoload, helper e gestione errori
- [x] convenzioni progetto (naming, error handling, validazione input)
- [x] UI/UX dark mode moderno e accattivante mantenuto

# test gate fase 1

- [x] Navigazione
- [x] wiring
- [x] funzionalita

# step 03 - roadmap fase 2

- [x] database locale MySQL creato (`cv_portal_local`)
- [x] schema iniziale creato (`experiences`, `skills`, `users`, `settings`)
- [x] migration SQL versionate attive in `database/migrations`
- [x] migrator CLI implementato (`php database/migrate.php`)
- [x] verifica schema fase 2 implementata (`php database/verify_phase2.php`)

# test gate fase 2

- [x] Navigazione
- [x] wiring
- [x] funzionalita

# step 04 - roadmap fase 3

- [x] layer dati separato (repository/service) implementato
- [x] autenticazione admin implementata (login/logout/session/protezione route)
- [x] CRUD admin Esperienze completo (crea/modifica/elimina/pubblica)
- [x] CRUD admin Competenze completo (crea/modifica/elimina/pubblica)
- [x] validazioni server-side + protezione CSRF + escaping output applicati
- [x] API backend protette per listing contenuti admin (`admin/api/experiences`, `admin/api/skills`)
- [x] foto profilo integrata nella home

# test gate fase 3

- [x] Navigazione
- [x] wiring
- [x] funzionalita

# step 05 - fix routing home

- [x] risolto errore 404 della home in esecuzione da sottocartella (`/info.antonio.trapasso/`)
- [x] verificata compatibilita route home/admin sia in root sia in sottocartella

# step 06 - roadmap fase 4 + ui refinement home

- [x] area admin `/admin` attiva con layout dark mode coerente
- [x] pagina login admin operativa
- [x] lista Esperienze + form aggiunta operativi
- [x] lista Competenze + form aggiunta operativi
- [x] feedback utente successo/errore e controlli conferma eliminazione operativi
- [x] foto profilo ridotta di dimensione
- [x] rimossi riferimenti admin dalla home (accesso solo via URL)
- [x] corretto offset ancoraggi per evitare contenuti nascosti sotto navbar sticky

# test gate fase 4

- [x] Navigazione
- [x] wiring
- [x] funzionalita

# step 07 - roadmap fase 5

- [x] home resa dinamica da database (`experiences`, `skills`, `settings`)
- [x] fallback automatico mantenuto se contenuti DB non disponibili
- [x] admin estesa con modifica `Profilo` e `Contatti`
- [x] form contatti in home per invio messaggio istantaneo
- [x] persistenza messaggi in tabella locale `contact_messages`
- [x] visualizzazione messaggi ricevuti nella dashboard admin

# test gate fase 5

- [x] Navigazione
- [x] wiring
- [x] funzionalita

# step 08 - roadmap fase 6

- [x] quality gate locale automatizzato (`scripts/phase6_quality_gate.ps1`)
- [x] checklist pre-release locale creata (`docs/pre-release-checklist.md`)
- [x] script seed dati demo creato (`php database/seed_demo.php`)
- [x] miglioramenti SEO tecnici (robots, canonical, og tags, JSON-LD)
- [x] documentazione `.md` aggiornata (README, roadmap, docs, database)

# test gate fase 6

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 09 - fix visibilita admin

- [x] corretto CSS reveal: i pannelli admin non vengono piu nascosti dal comportamento animato della home

# step 10 - footer social + export pdf + html profilo

- [x] footer home aggiornato con icone social dedicate e hover effect per condivisione contenuti
- [x] export curriculum vitae in PDF funzionante via route `GET /cv/pdf`
- [x] admin `Profilo (Home)` aggiornata con supporto inserimento HTML in textarea
- [x] sanitizzazione HTML server-side applicata al contenuto profilo
- [x] rendering HTML profilo attivo in home con output sicuro

# test gate step 10

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 11 - html textarea admin + social share + link style

- [x] tutte le `textarea` admin abilitate a contenuto HTML (profilo, intro contatti, descrizioni esperienza)
- [x] sanitizzazione server-side HTML estesa ai campi textarea admin
- [x] rendering HTML sicuro in home per descrizioni esperienza e intro contatti
- [x] fix condivisione social: link share basati su URL/titolo/descrizione pagina corrente
- [x] rimossa sottolineatura da tutti i link globalmente

# test gate step 11

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 12 - export pdf inline browser

- [x] aggiornato export CV PDF: apertura inline nel browser (non download forzato)
- [x] link footer `Esporta CV in PDF` aperto in nuova tab

# step 13 - export hub ux/ui first

- [x] creata pagina dedicata export (`GET /cv/export`) con UX orientata ad anteprima + azioni
- [x] aggiunte azioni chiare: apri anteprima PDF, scarica PDF, copia link PDF
- [x] integrata anteprima embedded PDF nella pagina export
- [x] mantenuto endpoint PDF con doppio comportamento `inline` e `download=1`
- [x] aggiornato footer per aprire l'export hub

# test gate step 13

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 14 - stile pdf migliorato

- [x] migliorata presentazione PDF con layout grafico (header, sezioni, gerarchie tipografiche)
- [x] introdotti font differenziati e accenti visivi per leggibilita professionale
- [x] mantenuta compatibilita export inline/download e validita file PDF

# test gate step 14

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 15 - riduzione altezza hero home

- [x] ridotta altezza sezione hero in home (padding/gap ottimizzati in classi Tailwind)
- [x] aggiunto fallback CSS su `#home.hero` per garantire riduzione altezza anche senza utility Tailwind complete
- [x] mantenuta coerenza UI/UX dark mode e leggibilita above-the-fold

# test gate step 15

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 16 - restyling tailwind sezioni home

- [x] restyling Tailwind applicato alle sezioni `Profilo`, `Esperienza`, `Competenze`, `Contatti`
- [x] mantenuti id ancora e wiring esistente (`#profilo`, `#esperienza`, `#competenze`, `#contatti`)
- [x] migliorata gerarchia visiva con card, badge, spacing e layout responsive
- [x] allineato stile nuove sezioni al linguaggio UX/UI della hero

# test gate step 16

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 17 - restyling tailwind area admin

- [x] restyling Tailwind applicato a login admin (`/admin`)
- [x] restyling Tailwind applicato a dashboard admin (`/admin/panel`)
- [x] mantenute funzionalita CRUD, API link, messaggi flash e protezioni esistenti
- [x] mantenuti marker test gate (`Profilo e Contatti`, `Aggiungi Esperienza`, `Aggiungi Competenza`, `Messaggi istantanei ricevuti`)

# test gate step 17

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 18 - preservazione integrale html textarea admin

- [x] rimosso stripping HTML nei campi textarea admin per mantenere classi Tailwind e attributi inseriti
- [x] mantenuto salvataggio/rendering del markup completo senza alterazioni lato server
- [x] mantenuti controlli di presenza e lunghezza dei campi per stabilita applicativa

# test gate step 18

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo

# step 19 - link competenze gestibile da admin

- [x] aggiunto campo `link_url` su tabella `skills` tramite migration
- [x] form admin `Aggiungi Competenza` esteso con input URL link competenza
- [x] tabella admin competenze estesa con modifica inline del link competenza
- [x] validazione server-side URL (`http/https`) su create/update competenza
- [x] sezione home `Competenze` aggiornata: le skill con link aprono risorsa in nuova scheda

# test gate step 19

- [x] Navigazione
- [x] wiring
- [x] funzionalita
- [x] codice
- [x] seo
