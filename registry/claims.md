# Claims Registry — Facts & Observations

> **Status:** empty — no claims recorded yet.

This registry holds every **FACT** (`F-`) and **OBSERVATION** (`O-`) in the
investigation. Each entry is the single source of truth for that claim and is
referenced from report chapters via relative links.

**Rule:** every `F-` and `O-` must cite at least one evidence item (`E-`).
See [CONTRIBUTING.md §1](../CONTRIBUTING.md#1-epistemic-rules-non-negotiable).

---

## Facts (F-)

| ID | Statement | Evidence | Status | Added |
|----|-----------|----------|--------|-------|
| F-008 | `lastmod` generato da Rank Math è coerente con `wp_posts.post_modified` per la sessione 21-06-2026. | E-008 | active | 2026-06-26 |
| F-009 | Il 24/06/2026 Googlebot ha eseguito GET di `sitemap_index.xml` e `product-sitemap1.xml` durante una finestra in cui le sitemap erano stale. | E-009, E-003 | active | 2026-06-26 |
| F-010 | Per la sessione 20-06-2026 la pipeline di crawl è completa (landing → prodotto → immagini). Per la sessione 21-06-2026 nessun crawl spontaneo è stato osservato nei log disponibili prima della richiesta manuale GSC del 26/06. | E-010, E-011 | active | 2026-06-26 |
| F-011 | La pipeline post-patch (flush automatico cache Rank Math + rigenerazione sitemap) è validata: `sitemap-check.sh` restituisce 8 PASS / 0 FAIL. | E-012 | active | 2026-06-26 |
| F-012 | Googlebot recupera l'HTML del sito in tempo reale: il test live GSC su una landing sessione restituisce "URL disponibile per Google"; curl con UA Googlebot riceve HTTP 200 su home e sitemap. | E-013 | active | 2026-07-26 |
| F-013 | `sitemap_index.xml` è XML valido e leggibile (no BOM, UTF-8, 244 voci): Bing l'ha crawlata con Status "Success" il 25/07/2026 scoprendo 58.400 URL. Il "Impossibile leggere" di Google è lato Google, non un difetto del file. | E-013 | active | 2026-07-26 |
| F-014 | Il 403 osservato dal fetcher in cloud era per IP datacenter (Cloudflare), non applicabile al Googlebot verificato. Dopo il re-invio del 26/07 lo stato GSC è passato da "Impossibile recuperare/mai letta" a "Ultima lettura 26/07 / Impossibile leggere". | E-013 | active | 2026-07-26 |
| F-015 | Il fix dell'header sitemap (`no-store, private` → `public, max-age=300` via filtro `nocache_headers`) è stato applicato e verificato via curl, ma la lettura GSC resta "Impossibile leggere" (0 pagine) su lettura fresca del 27/07. Header ESCLUSO come causa. Cloudflare ESCLUSO (problema antecedente a CF, per il proprietario). Bing legge la stessa sitemap (F-013). Causa ancora aperta. | E-014 | active | 2026-07-27 |

---

### F-008

- **Statement:** Il valore `<lastmod>` emesso da Rank Math in `product-sitemap1.xml` è coerente con `wp_posts.post_modified` per i prodotti della sessione Bocca Serriola 21-06-2026. Nessuna anomalia rilevata. L'ipotesi "lastmod errato" è da considerarsi chiusa.
- **Evidence:** [E-008](../evidence/rank-math/README.md#e-008)
- **Status:** active
- **Added:** 2026-06-26

---

### F-009

- **Statement:** Il 24/06/2026 Googlebot ha eseguito GET completi di `sitemap_index.xml` e `product-sitemap1.xml`. L'indagine ha dimostrato che in quella data le sitemap Rank Math servite erano stale e non includevano sessioni presenti nel database (E-003). Googlebot ha quindi letto le sitemap durante una finestra in cui queste non erano coerenti con il database. Questo esclude l'ipotesi che Google non avesse mai visto le sitemap; non dimostra causalità sulla mancata indicizzazione.
- **Evidence:** [E-009](../evidence/crawl/README.md#e-009), [E-003](../evidence/rank-math/README.md#e-003)
- **Status:** active
- **Added:** 2026-06-26

---

### F-010

- **Statement:** Per la sessione 20-06-2026: Googlebot ha crawlato la landing il 21/06 alle 15:50 UTC, il primo prodotto alle 18:49 UTC, e successivamente numerose immagini — pipeline completa e coerente. Per la sessione 21-06-2026: nei log LiteSpeed disponibili non è stato osservato alcun crawl spontaneo prima della richiesta manuale GSC del 26/06; il primo GET della landing è avvenuto subito dopo tale richiesta. Limitazione: i log potrebbero non coprire l'intera finestra per rotazione o lacune di conservazione.
- **Evidence:** [E-010](../evidence/crawl/README.md#e-010), [E-011](../evidence/crawl/README.md#e-011)
- **Status:** active
- **Added:** 2026-06-26

---

### F-011

- **Statement:** La pipeline post-patch è validata in produzione. Dopo `generate-json`: eliminazione file XML fisici Rank Math, eliminazione option `rank_math_sitemap_cache_files`, purge LiteSpeed, `wp_cache_flush()`. `sitemap-check.sh` restituisce 8 PASS / 0 FAIL: sitemap index HTTP 200, CF-Cache-Status non HIT, loc entries presenti, URL prodotto sessione trovati in product-sitemap, landing HTTP 200, nessun noindex, canonical corretto.
- **Evidence:** [E-012](../evidence/rank-math/README.md#e-012)
- **Status:** active
- **Added:** 2026-06-26

---

### F-012

- **Statement:** Googlebot recupera l'HTML del sito in tempo reale. GSC URL Inspection "Testa URL pubblicato" su `https://fotomoto.click/foto/bocca-serriola/11-07-2026/` restituisce "L'URL è disponibile per Google — La pagina può essere indicizzata" (26/07 21:28). `curl` con UA Googlebot desktop/mobile riceve HTTP 200 su `/`, `sitemap_index.xml`, sotto-sitemap (0,3–0,4 s). Il test live sull'XML restituisce "Si è verificato un problema", ma è un limite noto di URL Inspection sugli XML, non prova di blocco.
- **Evidence:** [E-013](../evidence/gsc/E-013_2026-07-26_gsc_live-session-p0-diagnosis.md)
- **Status:** active
- **Added:** 2026-07-26

---

### F-013

- **Statement:** `sitemap_index.xml` è XML ben formato e leggibile: nessun BOM (primi byte `<?xm`), UTF-8, root `sitemapindex`, 244 `<sitemap>`/244 `<loc>`, parser .NET OK, `Content-Encoding: gzip`. Bing Webmaster Tools ha crawlato la stessa sitemap con Status "Success" il 25/07/2026, scoprendo 58.400 URL. Poiché un secondo motore la processa senza problemi, il "Impossibile leggere" di Google è attribuibile a Google (stato stale + header), non al contenuto del file.
- **Evidence:** [E-013](../evidence/gsc/E-013_2026-07-26_gsc_live-session-p0-diagnosis.md)
- **Status:** active
- **Added:** 2026-07-26

---

### F-014

- **Statement:** Il 403 osservato in sessioni precedenti dal fetcher in cloud proveniva da un IP datacenter e riflette una challenge Cloudflare sugli IP non verificati; non si applica al Googlebot verificato. Dopo il re-invio manuale di `sitemap_index.xml` in GSC (26/07), la pagina di dettaglio è passata da "Impossibile recuperare / Ultima lettura vuota" a "Ultima lettura 26/07/26 / Impossibile leggere la Sitemap" — cioè Google ora la raggiunge ma non la interpreta. Header sitemap misurato: `Cache-Control: no-cache, must-revalidate, max-age=0, no-store, private`.
- **Evidence:** [E-013](../evidence/gsc/E-013_2026-07-26_gsc_live-session-p0-diagnosis.md)
- **Status:** active
- **Added:** 2026-07-26

---

### F-015

- **Statement:** L'header sitemap è stato corretto nel mu-plugin `fotomotorankmathsitemapheaders.php` aggiungendo un filtro `nocache_headers` (vera fonte del Cache-Control in WP 6.x) condizionato alle richieste sitemap: da `no-cache, must-revalidate, max-age=0, no-store, private` a `public, max-age=300, must-revalidate`, verificato via curl su index e sotto-sitemap. Ciononostante, dopo re-invio, GSC mostra "Ultima lettura 27/07/26 / 0 pagine / Impossibile leggere la Sitemap" su lettura fresca. **Header rigettato come causa** (H-006). **Cloudflare escluso** come causa: il proprietario conferma che il problema è antecedente all'introduzione di Cloudflare. Contenuto XML già escluso (F-013, Bing legge 58.400 URL). Causa di "Impossibile leggere" ancora aperta.
- **Evidence:** [E-014](../evidence/gsc/E-014_2026-07-27_sitemap-header-fix.md)
- **Status:** active
- **Added:** 2026-07-27

---

## Observations (O-)

| ID | Statement | Evidence | Status | Added |
|----|-----------|----------|--------|-------|
| O-001 | Rank Math era configurato per ordinare per `post_modified DESC` ma continuava a servire sitemap fisiche obsolete. | E-003, E-004 | active | 2026-06-26 |
| O-002 | La pipeline WP-CLI di FotoMoto.Click non triggherava l'invalidazione della cache fisica di Rank Math prima della patch. | E-004 | active | 2026-06-26 |
| O-003 | La funzione `flush_rank_math_sitemap_cache()` aggiunta al mu-plugin rimuove file fisici, option, transient, esegue `wp_cache_flush()` e LiteSpeed purge. | E-012 | active | 2026-06-26 |
| O-004 | Il 21-06-2026 è stata eseguita una richiesta di indicizzazione manuale via GSC URL Inspection per la landing della sessione Bocca Serriola 21-06-2026. | E-007 | active | 2026-06-26 |
| O-005 | A circa 7 giorni dalla pubblicazione, GSC riportava la landing della sessione 21-06-2026 come non presente nell'indice di Google. È stata eseguita una seconda richiesta di indicizzazione. | E-007 | active | 2026-06-26 |
| O-006 | IndexNow è già attivo su fotomoto.click (sorgenti Rank Math + WordPress): 156.200 URL inviati totali, 60 nelle ultime 19 h. Canale di notifica istantanea a Bing/Yandex operativo. | E-013 | active | 2026-07-26 |

---

### O-001

- **Statement:** Rank Math era configurato per ordinare per `post_modified DESC` ma continuava a servire sitemap fisiche obsolete, ignorando le mutazioni più recenti del database.
- **Evidence:** [E-003](../evidence/rank-math/README.md#e-003), [E-004](../evidence/rank-math/README.md#e-004)
- **Status:** active
- **Added:** 2026-06-26

---

### O-002

- **Statement:** La pipeline WP-CLI di FotoMoto.Click (import-session, repair-session, generate-json) non triggherava l'invalidazione della cache fisica di Rank Math prima della patch.
- **Evidence:** [E-004](../evidence/rank-math/README.md#e-004)
- **Status:** active
- **Added:** 2026-06-26

---

### O-003

- **Statement:** La funzione `flush_rank_math_sitemap_cache()` aggiunta al mu-plugin rimuove i file fisici `rank_math_*.xml`, l'option `rank_math_sitemap_cache_files`, i transient sitemap, esegue `wp_cache_flush()` e LiteSpeed purge. Viene richiamata automaticamente da `flush_cache()` dopo import-session, repair-session e generate-json.
- **Evidence:** [E-012](../evidence/rank-math/README.md#e-012)
- **Status:** active
- **Added:** 2026-06-26

---

### O-004

- **Statement:** Il 21-06-2026, giorno stesso della pubblicazione della sessione Bocca Serriola 21-06-2026, è stata eseguita una richiesta di indicizzazione manuale via Google Search Console URL Inspection per l'URL `https://fotomoto.click/foto/bocca-serriola/21-06-2026/`.
- **Evidence:** E-007 *(da registrare in evidence/gsc/)*
- **Status:** active
- **Added:** 2026-06-26

---

### O-005

- **Statement:** A circa 7 giorni dalla pubblicazione (e dalla prima richiesta di indicizzazione), Google Search Console riportava l'URL `https://fotomoto.click/foto/bocca-serriola/21-06-2026/` come non presente nell'indice di Google. È stata eseguita una seconda richiesta di indicizzazione.
- **Evidence:** E-007 *(da registrare in evidence/gsc/)*
- **Status:** active
- **Added:** 2026-06-26

---

### O-006

- **Statement:** IndexNow risulta già attivo su fotomoto.click (Bing Webmaster Tools → IndexNow): 156.200 URL inviati in totale, 60 nelle ultime 19 ore, con sorgenti "Rankmath" e "Wordpress". Il canale di notifica istantanea degli URL a Bing/Yandex è quindi già operativo; non richiede setup. Rilevante come contesto: la scoperta/segnalazione degli URL verso Bing funziona, isolando ulteriormente il problema di indicizzazione al solo Google.
- **Evidence:** [E-013](../evidence/gsc/E-013_2026-07-26_gsc_live-session-p0-diagnosis.md)
- **Status:** active
- **Added:** 2026-07-26
