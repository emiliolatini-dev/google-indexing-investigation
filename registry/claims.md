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
| F-021 | Nei 9 giorni di access log dell'origine (18-27/07/2026) **Googlebot non ha mai richiesto una sitemap**: 0 richieste da `66.249.*`, contro 70-260/giorno di Bingbot. Le uniche 4 richieste di Google sono `Google-InspectionTool` del 26/07 19:27 UTC, tutte con **200**. Cloudflare non cacha le sitemap (`cf-cache-status: DYNAMIC`), quindi ogni richiesta raggiunge l'origine e sarebbe visibile. | E-015 | active | 2026-07-27 |
| F-022 | `https://fotomoto.click/sitemap.xml` risponde **301** verso `sitemap_index.xml`, non 200. L'esperimento del 27/07 che lo usava come "URL equivalente mai inviato" non è quindi un controllo valido. Voce rimossa da GSC in pari data. | E-015 | active | 2026-07-27 |
| F-016 | Da IP datacenter il sito restituisce **403** su `/` e su `sitemap.xml`, mentre `robots.txt` risponde 200; dalla rete residenziale dell'utente tutti e tre rispondono 200. Esiste una regola attiva che discrimina per rete/client. | E-015 | active | 2026-07-27 |
| F-017 | `https://fotomoto.click/sitemap.xml`, URL mai inviato prima e servente lo stesso index, inviato in GSC il 27/07 fallisce entro pochi secondi con "Impossibile leggere", 0 pagine. Uno stato bloccato per singola voce sitemap è escluso. | E-015 | active | 2026-07-27 |
| F-018 | Le 1.129 pagine "Non trovata (404)" con convalida fallita sono tutte del namespace legacy `/gallerie/fotomotoclick-*`; le 2 richieste "Impossibile raggiungere la pagina" delle statistiche di scansione sono dello stesso namespace, con 34.100 ms di risposta media; `/gallerie/` → `/gallerie-foto/` → `/passi-e-valichi/` è una catena di due redirect. | E-015 | active | 2026-07-27 |
| F-019 | In GA4, 28 giorni: `purchase` 87 eventi / 84 utenti / 1.238,00 € contro `begin_checkout` ~78 eventi / 53 utenti. Il funnel non è monotòno decrescente: `begin_checkout` è sotto-rilevato. Assenti `view_cart`, `add_shipping_info`, `add_payment_info`. | E-015 | active | 2026-07-27 |
| F-020 | Su 16 mesi `/gallerie-foto/` (un 301 a due hop) raccoglie 27.098 impressioni e `/contatti/` 20.023, con CTR 3,4% e 0,8% contro il 16,1% del sito. Su `foto moto` (14.818 impr., pos. 3,2) cinque URL del sito compaiono in SERP e solo la home converte. | E-015 | active | 2026-07-27 |
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

### F-021

- **Statement:** Access log dell'origine (`/home/fotomoto.click/logs/`), 9 file giornalieri dal 18 al 27 luglio 2026. Richieste a URL contenenti "sitemap" provenienti da IP Google (`66.249.*`): **0** il 18, 19, 20, 21, 23, 24, 25 e 26 luglio; **4** il 27 luglio, tutte con user-agent `Google-InspectionTool/1.0`, timestamp 26/07 19:27 UTC, tutte con risposta **200 / 2.077 byte**. Nello stesso periodo Bingbot ha effettuato da 70 a 263 richieste di sitemap al giorno. Googlebot è attivo e non ostacolato: nel solo file precedente al 27/07 sono registrate 656 richieste da `66.249.*` con 587 risposte 200, 64 301, 4 404, 1 302, e **nessun 403**. Poiché `cf-cache-status` è `DYNAMIC` su `sitemap_index.xml`, `sitemap.xml` e `page-sitemap1.xml`, Cloudflare non serve queste risorse dalla cache di bordo: ogni richiesta raggiunge l'origine e comparirebbe nel log. Ne segue che **Google non sta richiedendo le sitemap**, e che lo stato "Impossibile recuperare/leggere" mostrato da GSC non è l'esito di una lettura recente.
- **Evidence:** [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §1-quater
- **Status:** active
- **Added:** 2026-07-27

---

### F-022

- **Statement:** `https://fotomoto.click/sitemap.xml` risponde `HTTP 301` con `location: https://fotomoto.click/sitemap_index.xml`, confermato sia da `curl -I` sia dall'access log dell'origine. La misurazione del 27/07 che lo dava per equivalente all'index era stata eseguita con `curl -L`, che segue i redirect e restituisce quindi il contenuto finale mascherando il 301. L'invio di quell'URL in GSC come "controllo su URL vergine" non è un esperimento valido; la voce è stata rimossa da GSC il 27/07.
- **Evidence:** [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §1-quater
- **Status:** active
- **Added:** 2026-07-27

---

### F-016

- **Statement:** Il 27-07-2026, richiedendo le stesse URL dalla rete residenziale dell'utente e da un fetcher su IP datacenter: `robots.txt` → 200 da entrambi; `https://fotomoto.click/` → 200 da rete utente, **403 Forbidden** da datacenter; `https://fotomoto.click/sitemap.xml` → 200 con XML valido da rete utente, **403 Forbidden** da datacenter. Esiste quindi una regola attiva che restituisce 403 ai client su IP datacenter, con `robots.txt` in eccezione esplicita. È la stessa firma osservata nel crawl del 26-07 e mai risolta.
- **Evidence:** [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §1-bis
- **Status:** active
- **Added:** 2026-07-27

---

### F-017

- **Statement:** Con autorizzazione dell'utente è stata inviata in GSC `https://fotomoto.click/sitemap.xml`, URL mai inviato prima, che serve byte per byte lo stesso index di `sitemap_index.xml` (31.615 byte, verificato con curl). Entro pochi secondi la voce riporta "Ultima lettura 27/07/26", 0 pagine, "Impossibile leggere la Sitemap" — identico all'URL storico, lasciato in elenco come controllo. L'ipotesi di uno stato bloccato lato GSC sulla singola voce sitemap è quindi esclusa.
- **Evidence:** [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §1-bis
- **Status:** active
- **Added:** 2026-07-27

---

### F-018

- **Statement:** Il drill-down GSC "Non trovata (404)" (1.129 pagine, convalida avviata 18/06 e fallita 01/07) restituisce esclusivamente URL del pattern `https://fotomoto.click/gallerie/fotomotoclick-*`. Le 2 richieste classificate "Impossibile raggiungere la pagina" nelle statistiche di scansione appartengono allo stesso namespace e hanno tempo medio di risposta 34.100 ms. Verifica HTTP diretta: i prodotti sotto `/gallerie/` rispondono 404; `/gallerie/` risponde 301 verso `/gallerie-foto/`, che a sua volta risponde 301 verso `/passi-e-valichi/`. Le voci N1, N2 e N4 delle evidenze del 26-07 sono quindi lo stesso problema.
- **Evidence:** [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §2
- **Status:** active
- **Added:** 2026-07-27

---

### F-019

- **Statement:** GA4, proprietà `fotomoto.click`, 29/06–26/07/2026: `view_item` 3.122 (485 utenti) → `add_to_cart` 429 (~99 utenti) → `begin_checkout` ~78 (53 utenti) → `purchase` 87 eventi / 84 utenti / 1.238,00 €. `purchase` supera `begin_checkout` sia in eventi sia in utenti, il che è strutturalmente impossibile in un funnel strumentato correttamente: `begin_checkout` è sotto-rilevato. Ordinando gli eventi alfabeticamente la prima riga è `add_to_cart`, quindi `add_payment_info` e `add_shipping_info` non esistono; assenti anche `view_cart` e `remove_from_cart`.
- **Evidence:** [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §5
- **Status:** active
- **Added:** 2026-07-27

---

### F-020

- **Statement:** GSC Rendimento, 16 mesi (08/05/2025–24/07/2026), totali 19.000 clic / 118.000 impressioni / CTR 16,1% / posizione 5,8 / 871 query. `/gallerie-foto/` — che risponde 301 verso `/passi-e-valichi/` — è la 2ª pagina del sito per impressioni con 27.098 e CTR 3,4%; `/contatti/` è la 3ª con 20.023 impressioni e CTR 0,8%. Insieme: 47.121 impressioni a CTR combinato 1,7%. Con filtro query esatta `foto moto` (176 clic, 14.818 impressioni, pos. 3,2) compaiono in SERP `/` (173 clic), `/gallerie-foto/` (7), `/passi-e-valichi/` (1), `/foto/bocca-serriola/` (0 clic, 3.224 impr.), `/contatti/` (0 clic, 3.073 impr.), `/link/` (0).
- **Evidence:** [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §3
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
| O-007 | Fino al 2025 le sessioni più vecchie venivano archiviate in gallerie di sola lettura sotto `/gallerie/`, fuori da WooCommerce. Tornando a tenere tutto come prodotti, parte delle foto non è stata reimportata: da qui i 1.129 404. Il redirect `/gallerie/{slug}/` → prodotto esiste e funziona quando il prodotto c'è. | proprietario del sito + E-015 | active | 2026-07-27 |
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

### O-007

- **Statement:** Contesto storico riferito dal proprietario del sito il 27-07-2026 e verificato per misura lo stesso giorno. Fino al 2025 il sito teneva online come prodotti WooCommerce solo le sessioni più recenti, e archiviava le più vecchie in **gallerie di sola lettura sotto `/gallerie/`**, fuori da WooCommerce. Con la decisione successiva di mantenere tutto come prodotti, una parte delle foto archiviate **non è stata reimportata**. Verifica HTTP: `/gallerie/{slug}/` risponde **301** verso il prodotto quando il prodotto esiste (`…07-09-2025-1-10-13`, `…07-09-2025-18-10-14` → 301, e i rispettivi `/foto/bocca-serriola/{slug}/` → 200) e **404** quando non esiste (`…07-09-2025-292-10-47`, `…09-08-2025-567-11-35` → 404 su entrambi i percorsi). Le landing di sessione 2025 restano invece online (`/foto/bocca-serriola/07-09-2025/` → 200, con 96 link prodotto). I 1.129 404 di F-018 sono quindi **esclusivamente le foto senza successore**, non un difetto di routing. Il 404 servito pesa 139.937 byte.
- **Evidence:** proprietario del sito; misure in [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md) §2
- **Status:** active
- **Added:** 2026-07-27
- **Conseguenza operativa:** il segnale corretto è `410 Gone`, non un redirect. Una regola secca in `.htaccess` sull'intero namespace distruggerebbe i 301 funzionanti — vedi [execution-kit/02-gallerie-410.md](../execution-kit/02-gallerie-410.md).

---

### O-006

- **Statement:** IndexNow risulta già attivo su fotomoto.click (Bing Webmaster Tools → IndexNow): 156.200 URL inviati in totale, 60 nelle ultime 19 ore, con sorgenti "Rankmath" e "Wordpress". Il canale di notifica istantanea degli URL a Bing/Yandex è quindi già operativo; non richiede setup. Rilevante come contesto: la scoperta/segnalazione degli URL verso Bing funziona, isolando ulteriormente il problema di indicizzazione al solo Google.
- **Evidence:** [E-013](../evidence/gsc/E-013_2026-07-26_gsc_live-session-p0-diagnosis.md)
- **Status:** active
- **Added:** 2026-07-26
