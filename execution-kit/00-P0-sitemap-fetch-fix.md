# P0 — "Impossibile recuperare" sulle sitemap: diagnosi rifinita + fix

> Aggiornato 26-07-2026 con **prove fresche dal server** (curl multi-user-agent dalla macchina di Emilio).
> Rif. `seo-audit/2026-07-26-gsc-ga-evidence.md §1`.

## Cosa dicevano le evidenze precedenti
GSC → Indicizzazione → Sitemap: `sitemap_index.xml` e `page-sitemap1.xml` entrambe
**"Impossibile recuperare", 0 pagine, "Ultima lettura" vuota**. Ipotesi principale registrata:
un filtro bot (Cloudflare/WAF) restituisce **403 al fetcher di Google**.

## Nuove prove (26-07-2026, dalla rete di Emilio)
Test con `curl` variando lo user-agent, contro il server di produzione:

| Risorsa | UA browser | UA Googlebot desktop | UA Googlebot mobile |
|---|---|---|---|
| `/robots.txt` | 200 | 200 | 200 |
| `/` (home) | 200 | 200 | 200 |
| `/sitemap_index.xml` | 200 (31,6 KB, 0,37 s) | **200** | **200** |
| `/product-sitemap1.xml` | — | **200** (0,35 s) | — |
| `/page-sitemap1.xml` | — | **200** (0,34 s) | — |

**Conclusione tecnica:** il server NON discrimina Googlebot per user-agent. Tutto risponde
200, veloce, con corpo pieno. Il **403 osservato in precedenza era per IP/ASN** (Cloudflare che
sfida gli IP datacenter/bot non verificati) e **non si applica al vero Googlebot**, che rientra
nella allowlist "Verified Bots" di Cloudflare. L'ipotesi "il WAF blocca Googlebot per user-agent"
e' quindi **da declassare**.

### Header reali della sitemap (Googlebot UA)
```
HTTP/1.1 200 OK
Content-Type: text/xml; charset=UTF-8
Transfer-Encoding: chunked
x-litespeed-cache-control: no-cache
Cache-Control: no-cache, must-revalidate, max-age=0, no-store, private   <-- problema
Server: cloudflare
cf-cache-status: DYNAMIC
x-turbo-charged-by: LiteSpeed
```

## Cause residue plausibili, in ordine
1. **Stato GSC stale dall'incidente Rank Math di giugno.** "Impossibile recuperare" puo'
   persistere da un tentativo fallito durante la finestra in cui le sitemap erano stale
   (F-009/F-011 del registry). Google non re-tenta a ogni ora. **Un re-invio pulito spesso
   azzera lo stato.** — probabilita' alta, costo fix ~0.
2. **`Cache-Control: no-store, private` sulla sitemap.** Non blocca il fetch, ma e' l'estremo
   sbagliato: un file che cambia 1-2 volte/settimana servito come "mai cacheabile, privato".
   Da correggere in `max-age=300, must-revalidate` (rimuovere `no-store` e `private`).
3. **Verified Bots di Cloudflare non attivo / regola WAF custom sull'IP di Google.** Se per
   qualsiasi motivo Cloudflare non riconosce l'allowlist dei bot verificati, il fetcher di
   Google (che arriva da IP Google, non datacenter generici) potrebbe ricevere challenge.
   **Va verificato nel pannello Cloudflare.** — probabilita' media.
4. **244 sotto-sitemap in un index no-cache generato dinamicamente.** L'index e' piccolo
   (31 KB) e veloce, quindi timeout sull'index improbabile; ma la pulizia di §3.1 (→ ~10 file)
   riduce comunque il rischio.

## Azioni — nell'ordine, subito

### A. Rimuovere `no-store, private` dall'header della sitemap  (Emilio / dev — mu-plugin)
Aggiungi al mu-plugin (o dove gia' vive `flush_rank_math_sitemap_cache`):
```php
add_action('template_redirect', function () {
    if (preg_match('#sitemap.*\.xml$#i', $_SERVER['REQUEST_URI'] ?? '')) {
        header_remove('Pragma');
        header('Cache-Control: public, max-age=300, must-revalidate', true);
        header('X-Robots-Tag: noindex', true); // le sitemap non vanno indicizzate come pagine
    }
}, 1);
```
Verifica dopo il deploy:
```bash
curl -sI -A 'Googlebot' https://fotomoto.click/sitemap_index.xml | grep -i cache-control
# atteso: Cache-Control: public, max-age=300, must-revalidate
```

### B. Cloudflare → confermare che Googlebot e' verificato e non sfidato  (Emilio)
- Security → Bots: se "Bot Fight Mode" / "Super Bot Fight Mode" e' attivo, assicurarsi che
  **"Verified bots"** sia impostato su *Allow* (non challenge/block).
- Security → Events: filtrare per `User agent` contenente `Googlebot` e Action = Block/Challenge
  negli ultimi 30 giorni. Se compaiono blocchi → aggiungere una **regola WAF di skip** per i
  bot verificati (`cf.client.bot` = true → Skip).
- (Facoltativo ma pulito) aggiungere una regola: se `http.request.uri.path contains "sitemap"`
  e `cf.client.bot` → **Skip all remaining custom rules + Security Level Essentially Off**.

### C. Re-inviare le sitemap e forzare la lettura  (Emilio — in GSC)
1. GSC → Indicizzazione → Sitemap: **rimuovere** `page-sitemap1.xml` (obsoleta, verra' coperta
   dall'index) e lasciare solo `sitemap_index.xml`.
2. **Controllo URL** su `https://fotomoto.click/sitemap_index.xml` → **"Testa URL pubblicato"**:
   leggere la risposta ESATTA che vede Google (e' la prova definitiva). Screenshot in
   `evidence/gsc/`.
3. Re-inviare `sitemap_index.xml`. Aggiungere anche `news-sitemap.xml` (vedi
   `12-recent-sessions-sitemap.md`) quando pronta.
4. Attendere 48-72 h e verificare che **"Ultima lettura"** si popoli e "Pagine rilevate" > 0.

### D. Registrare l'esito nel repo
Aprire un `F-` in `registry/claims.md` con l'esito del test "URL pubblicato" e degli Security
Events di Cloudflare. Questo chiude (o conferma) l'ipotesi H1.

## Criterio di successo
`sitemap_index.xml` in GSC passa da "Impossibile recuperare / 0" a **"Riuscito"** con
"Ultima lettura" valorizzata e "Pagine rilevate" coerente col numero di URL post-pulizia.
