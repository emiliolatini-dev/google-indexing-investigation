# E-013 — Sessione live GSC + Bing + server, diagnosi P0 sitemap

- **Date:** 2026-07-26 (sera, ~21:30 CEST)
- **Method:** accesso diretto via browser autenticato a Google Search Console (proprietà
  dominio `sc-domain:fotomoto.click`) e Bing Webmaster Tools (`fotomoto.click/`), più test
  HTTP dal server di produzione con `curl` (user-agent Googlebot desktop/mobile) dalla rete
  dell'utente.
- **Demonstrates:** F-012, F-013, F-014, O-006; falsifica H-005; supporta H-006.

## 1. Googlebot recupera l'HTML in tempo reale (GSC URL Inspection — TEST LIVE)
- `https://fotomoto.click/foto/bocca-serriola/11-07-2026/` → **"L'URL è disponibile per Google
  — La pagina può essere indicizzata"** (test live eseguito 26/07 21:28). La stessa pagina, che
  al mattino del 26/07 risultava tra le "scansionate ma non indicizzate", ora risulta
  **indicizzata**.
- Test live sull'XML `sitemap_index.xml` → **"Si è verificato un problema"**, ripetibile: è un
  **limite noto di URL Inspection** (progettato per pagine HTML, non per file sitemap). NON è
  prova di blocco.

## 2. Server sano per Googlebot (curl dalla rete utente)
- `robots.txt`, `/`, `sitemap_index.xml`, `product-sitemap1.xml`, `page-sitemap1.xml` →
  **HTTP 200** con UA Googlebot desktop e mobile, 0,3–0,4 s.
- Header sitemap: `Cache-Control: no-cache, must-revalidate, max-age=0, no-store, private`;
  `cf-cache-status: DYNAMIC`; `x-turbo-charged-by: LiteSpeed`; `Content-Encoding: gzip`.
- Validazione XML: **nessun BOM** (primi byte `3C 3F 78 6D` = `<?xm`), UTF-8, root `sitemapindex`,
  **244 `<sitemap>` / 244 `<loc>`**, ben formato (parser .NET: OK), `lastmod` ISO validi.
- Il 403 osservato in sessioni precedenti dal fetcher in cloud era per **IP datacenter**
  (Cloudflare challenge su IP non verificati), **non applicabile al Googlebot verificato**.

## 3. Bing legge la STESSA sitemap senza problemi (cross-check decisivo)
Bing Webmaster Tools → Sitemaps:
- `https://fotomoto.click/sitemap_index.xml` → **Last crawl 25/07/2026, Status: Success,
  58.400 URL scoperti.**
- Totale URL scoperti da Bing su tutte le sitemap: 175,6K. Sitemaps note: 6 (inclusi duplicati
  `sitemap.xml`, `www.fotomoto.click/sitemap_index.xml`).
- **IndexNow attivo:** 156,2K URL inviati (60 nelle ultime 19 h), sorgenti **Rank Math +
  WordPress** → canale di notifica istantanea a Bing/Yandex già operativo.

→ Un secondo motore processa con successo l'identico file che Google dichiara "Impossibile
leggere". La causa è **lato Google**, non nel contenuto della sitemap.

## 4. Azione eseguita in GSC (con consenso dell'utente)
- **Re-inviata** `sitemap_index.xml` (26/07). Esito immediato nella pagina di dettaglio:
  **"Ultima lettura: 26/07/26"** (prima vuota) + stato **"Impossibile leggere la Sitemap"**,
  0 pagine. Transizione da "Impossibile *recuperare*" (mai letta) a "Google la *raggiunge* ma non
  la interpreta".
- **Rimossa** la ridondante `page-sitemap1.xml` (coperta dall'index; anch'essa mostrava in
  dettaglio "Impossibile leggere", Ultima lettura 25/06).

## Note di metodo
Accesso in lettura ai report; le uniche scritture su GSC sono state il re-invio di
`sitemap_index.xml` e la rimozione di `page-sitemap1.xml`, autorizzate dall'utente. Nessuna
modifica su Bing. Screenshot disponibili nella sessione.
