# Email Deliverability DNS Plan (testscript.info)

Data verifica: 2026-02-23

## Stato attuale rilevato

- SPF presente su `testscript.info`:
  - `v=spf1 +a +mx +ip4:155.138.239.6 include:relay.mailbaby.net ~all`
- DKIM presente su `default._domainkey.testscript.info`
- DMARC assente (`_dmarc.testscript.info` non trovato)

## Record da impostare subito (step 1)

1. Aggiungi record DMARC:
   - `Type`: `TXT`
   - `Host/Name`: `_dmarc`
   - `Value`: `v=DMARC1; p=none; adkim=s; aspf=s; pct=100`
   - `TTL`: `3600` (o default provider)

2. Verifica che SPF resti unico (un solo TXT SPF sul root):
   - `v=spf1 +a +mx +ip4:155.138.239.6 include:relay.mailbaby.net ~all`

3. Verifica DKIM attivo (già presente):
   - `Host/Name`: `default._domainkey`
   - `Type`: `TXT`
   - `Value`: chiave pubblica DKIM già pubblicata

## Hardening consigliato dopo monitoraggio (step 2 DNS)

Dopo 7-14 giorni senza problemi di recapito:

1. Aggiorna DMARC a quarantena:
   - `v=DMARC1; p=quarantine; adkim=s; aspf=s; pct=100`

2. Poi, se tutto stabile, valuta:
   - `v=DMARC1; p=reject; adkim=s; aspf=s; pct=100`

## Note operative

- I record DNS non possono essere applicati dal codice locale: vanno inseriti nel pannello DNS del dominio.
- Il sistema contatti ora usa SMTP Gmail dedicato (`CONTACT_SMTP_*`) e mittente allineato Gmail per migliorare l'inbox placement.
