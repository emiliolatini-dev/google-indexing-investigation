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

## Observations (O-)

| ID | Statement | Evidence | Status | Added |
|----|-----------|----------|--------|-------|
| O-001 | Rank Math era configurato per ordinare per `post_modified DESC` ma continuava a servire sitemap fisiche obsolete. | E-003, E-004 | active | 2026-06-26 |
| O-002 | La pipeline WP-CLI di FotoMoto.Click non triggherava l'invalidazione della cache fisica di Rank Math prima della patch. | E-004 | active | 2026-06-26 |
| O-003 | La funzione `flush_rank_math_sitemap_cache()` aggiunta al mu-plugin rimuove file fisici, option, transient, esegue `wp_cache_flush()` e LiteSpeed purge. | E-012 | active | 2026-06-26 |
| O-004 | Il 21-06-2026 è stata eseguita una richiesta di indicizzazione manuale via GSC URL Inspection per la landing della sessione Bocca Serriola 21-06-2026. | E-007 | active | 2026-06-26 |
| O-005 | A circa 7 giorni dalla pubblicazione, GSC riportava la landing della sessione 21-06-2026 come non presente nell'indice di Google. È stata eseguita una seconda richiesta di indicizzazione. | E-007 | active | 2026-06-26 |

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
