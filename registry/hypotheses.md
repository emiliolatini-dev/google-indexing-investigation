# Hypotheses Registry

> **Status:** empty — no hypotheses recorded yet.

This registry holds every **HYPOTHESIS** (`H-`): a possible explanation **not yet
verified**. Each entry must list both supporting and falsifying evidence, and is
always treated as provisional until evidence resolves it.

See the entry format in [`templates/hypothesis.md`](../templates/hypothesis.md)
and the rules in [CONTRIBUTING.md §1](../CONTRIBUTING.md#1-epistemic-rules-non-negotiable).

---

## Index

| ID | Title | Status | Updated |
|----|-------|--------|---------|
| H-001 | Rank Math physical sitemap cache drift | **supported (rafforzata)** | 2026-06-26 |
| H-002 | Prodotti WooCommerce non pubblicati o non accessibili | **rejected** | 2026-06-26 |
| H-003 | Errore di canonical URL o Schema markup | **rejected** | 2026-06-26 |
| H-004 | `lastmod` Rank Math errato o incoerente con il DB | **rejected** | 2026-06-26 |
| H-005 | Un filtro bot (WAF/Cloudflare) blocca il fetcher sitemap di Google | **riaperta** → superata da H-007 | 2026-07-27 |
| H-006 | Header `no-store, private` + stato stale causano il fallimento lettura sitemap lato Google | **rejected** (header fixato, problema persiste) | 2026-07-27 |
| H-007 | Il fetcher sitemap di Search Console non è il Googlebot verificato e riceve il 403 della regola anti-datacenter | **rejected** (nessun fetch di Google raggiunge l'origine, né bloccato né riuscito) | 2026-07-27 |
| H-009 | Google non richiede più le sitemap di questa proprietà: lo stato GSC è persistito, non è l'esito di una lettura | **testing** | 2026-07-27 |
| H-008 | Il crawler di Meta è bloccato dalle Regole gestite Cloudflare, e questo spiega lo stato del dataset pixel | **open** | 2026-07-27 |

Status values: `open` · `testing` · `supported` · `rejected` · `superseded`.

---

## Entries

### H-001 — Rank Math physical sitemap cache drift

- **Status:** supported (rafforzata al 2026-06-26)
- **Created:** 2026-06-26
- **Last updated:** 2026-06-26
- **Statement:** La cache fisica delle sitemap di Rank Math non veniva invalidata correttamente dopo le operazioni della pipeline WP-CLI di FotoMoto.Click, causando la distribuzione di sitemap stale che non riflettevano lo stato aggiornato del database. Classificazione: "operational incompatibility between Rank Math physical sitemap cache and the FotoMoto.Click WP-CLI import/recovery workflow."
- **Supporting evidence:** E-003 (diff DB↔sitemap, sessioni mancanti); E-004 (file fisici stale presenti); E-005 (flush: 179 file XML rimossi); E-006 (product-sitemap1.xml corretto post-flush); E-009 (Googlebot ha letto sitemap stale il 24/06 durante il periodo del bug); E-012 (pipeline post-patch validata: 8 PASS / 0 FAIL)
- **Falsifying evidence:** Nessuna trovata. Tutte le ipotesi alternative verificate sono state rigettate.
- **Test plan:** Eseguito (T-001). Esperimento controllato in corso (T-002): prossima sessione post-patch monitorate con timeline T0→T7.
- **Related:** F-004, F-005, F-006, F-007, F-009, F-011, O-001, O-002, O-003, Q-001, Q-001a, Q-002
- **Notes:** H-001 è supportata dall'evidenza ma non dimostra causalità esclusiva sui problemi di indicizzazione Google. La correlazione temporale (Googlebot ha letto sitemap stale il 24/06, sessione 21/06 non crawlata spontaneamente) è forte ma non sufficiente per affermare che il drift della cache sia la sola causa della mancata indicizzazione. Vedi Q-001a.

---

### H-002 — Prodotti WooCommerce non pubblicati o non accessibili

- **Status:** rejected
- **Created:** 2026-06-26
- **Last updated:** 2026-06-26
- **Statement:** I prodotti WooCommerce non erano nello stato `published` o non restituivano HTTP 200, impedendo l'indicizzazione.
- **Supporting evidence:** nessuna
- **Falsifying evidence:** F-001 (prodotti published), F-002 (HTTP 200, permalink e canonical corretti), F-003 (schema e database corretti)
- **Test plan:** n/a — falsificata da evidenza diretta
- **Related:** F-001, F-002, F-003
- **Notes:** —

---

### H-003 — Errore di canonical URL o Schema markup

- **Status:** rejected
- **Created:** 2026-06-26
- **Last updated:** 2026-06-26
- **Statement:** Il tag canonical o il markup Schema.org dei prodotti contenevano errori che impedivano la corretta selezione del canonical da parte di Google.
- **Supporting evidence:** nessuna
- **Falsifying evidence:** F-002 (canonical corretti), F-003 (schema corretto)
- **Test plan:** n/a — falsificata da evidenza diretta
- **Related:** F-002, F-003
- **Notes:** —

---

### H-004 — `lastmod` Rank Math errato o incoerente con il DB

- **Status:** rejected
- **Created:** 2026-06-26
- **Last updated:** 2026-06-26
- **Statement:** Rank Math emetteva valori `<lastmod>` non coerenti con `wp_posts.post_modified`, fornendo a Google un segnale di recrawl errato.
- **Supporting evidence:** nessuna
- **Falsifying evidence:** F-008 (lastmod coerente con post_modified, nessuna anomalia rilevata)
- **Test plan:** n/a — falsificata da E-008
- **Related:** F-008, E-008
- **Notes:** —

---

### H-005 — Un filtro bot (WAF/Cloudflare) blocca il fetcher sitemap di Google

- **Status:** **riaperta il 27-07-2026, superata da [H-007](#h-007--il-fetcher-sitemap-di-search-console-non-è-il-googlebot-verificato-e-riceve-il-403-della-regola-anti-datacenter)**
- **Created:** 2026-07-26
- **Last updated:** 2026-07-27
- **Motivo della riapertura:** la rigetto poggiava sull'assunto che il 403 su IP datacenter non potesse riguardare alcun client di Google. F-016 dimostra che la regola 403 è attiva ancora oggi; F-017 esclude le spiegazioni alternative. L'assunto regge per il Googlebot verificato, non per il fetcher sitemap di GSC. L'ipotesi è quindi corretta nella sostanza e imprecisa nella formulazione: la riformulazione è H-007.
- **Statement:** Il "Impossibile recuperare" sulle sitemap era causato da una regola bot/WAF (Cloudflare) che restituiva 403 al fetcher di Google, impedendogli di leggere le sitemap.
- **Supporting evidence:** solo il 403 osservato dal fetcher in cloud in sessioni precedenti — spiegato da F-014 come challenge Cloudflare su IP datacenter non verificato.
- **Falsifying evidence:** F-012 (Googlebot recupera l'HTML in tempo reale via GSC live test; curl UA Googlebot → 200), F-013 (Bing legge la stessa sitemap con Success, 58.400 URL), F-014 (il 403 era IP-datacenter, non applicabile al Googlebot verificato).
- **Test plan:** Eseguito nella sessione live del 26/07 (E-013). Tre prove indipendenti falsificano l'ipotesi.
- **Related:** F-012, F-013, F-014, E-013, H-006
- **Notes:** Declassa l'ipotesi principale registrata nell'addendum GSC/GA del 26/07. Il server è sano per il Googlebot verificato.

---

### H-006 — Header `no-store, private` + stato stale causano il fallimento lettura sitemap lato Google

- **Status:** rejected (2026-07-27)
- **Created:** 2026-07-26
- **Last updated:** 2026-07-27
- **Statement:** Il fallimento di lettura delle sitemap è lato Google: combinazione dell'header `Cache-Control: no-cache, no-store, private, max-age=0` sulla sitemap e di uno stato GSC stale. Non è un problema di contenuto (F-013) né di raggiungibilità (F-012, F-014).
- **Supporting evidence:** (storica) F-014 header misurato; F-013 Bing legge il file.
- **Falsifying evidence:** **F-015** — l'header è stato corretto a `public, max-age=300` (verificato via curl) e la lettura fresca del 27/07 mostra ancora "Impossibile leggere / 0 pagine". Il fix dell'header NON ha risolto.
- **Test plan:** Eseguito (E-014). Header rigettato come causa.
- **Related:** F-012, F-013, F-014, F-015, E-013, E-014, H-007
- **Notes:** Fix header comunque acquisito e utile. Anche Cloudflare escluso (problema antecedente a CF). Causa "Impossibile leggere" ancora da individuare — vedi E-014 §direzioni non esplorate.

---

### H-007 — Il fetcher sitemap di Search Console non è il Googlebot verificato e riceve il 403 della regola anti-datacenter

- **Status:** **rejected (27-07-2026)** — falsificata da F-021: nessuna richiesta di Google alle sitemap raggiunge l'origine, né bloccata né riuscita. Non c'è nulla da bloccare. Sostituita da H-009.
- **Created:** 2026-07-27
- **Last updated:** 2026-07-27
- **Esito del test:** Cloudflare Security Events, ultime 24 ore, filtro `ASN di origine = AS15169`: **nessun evento**. Nella finestra dei due invii sitemap del 27/07 Cloudflare non ha mitigato alcuna richiesta di Google. Il vettore Cloudflare è escluso. Il 403 di F-016 non compare fra gli eventi di mitigazione, quindi è verosimilmente generato **dall'origine** (LiteSpeed / plugin di sicurezza / ModSecurity sul VPS). Prossimo punto di osservazione: access log del server di origine filtrato su `/sitemap*.xml`. Da verificare anche l'alternativa che "Ultima lettura 27/07" sia il timestamp dell'invio e non di un fetch eseguito.
- **Statement:** Sul sito è attiva una regola che restituisce **403 Forbidden** ai client su IP datacenter, con `robots.txt` in eccezione (F-016). Il crawl delle pagine HTML non ne risente perché avviene tramite il **Googlebot verificato**, riconosciuto per reverse DNS e lasciato passare (F-012, 37.003 richieste con 82% di 200). Il recupero delle sitemap avviato da Search Console viene invece eseguito da un client che **non supera la stessa verifica**, ricade nella regola e riceve 403 — che GSC riporta come "Impossibile recuperare / Impossibile leggere".
- **Supporting evidence:** F-016 (403 da datacenter su `/` e `sitemap.xml`, 200 su `robots.txt`, riprodotto il 27/07); F-017 (un URL sitemap vergine fallisce identicamente e istantaneamente → non è uno stato bloccato lato GSC); F-013 (Bing legge lo stesso file: il contenuto è sano); F-015 (header corretto: non è l'header); E-015 §1 (nessun difetto di trasporto, formato o encoding misurabile; 3 fallimenti di rete su 37.003 richieste in 90 giorni).
- **Falsifying evidence:** nessuna finora.
- **Test plan:** Cloudflare → Security Events (o log LiteSpeed) filtrati su richieste a `/sitemap*.xml` da IP Google (ASN 15169) nella finestra dei due invii GSC del 27-07-2026, che hanno timestamp noto. Se compare un blocco/challenge → confermata; si procede con una regola di bypass sui path sitemap e si rinvia. Se non compare nulla → falsificata, e il vettore successivo è cosa esattamente riceve il fetcher (log applicativo lato origine).
- **Related:** F-012, F-013, F-015, F-016, F-017, E-013, E-014, E-015, H-005
- **Notes:** Supera H-005, la cui rigetto poggiava sull'assunto che il 403 su IP datacenter non potesse riguardare **alcun** client di Google. L'assunto vale per Googlebot verificato, non necessariamente per il fetcher sitemap di GSC. Da qui la riapertura. **Aggiornamento 27/07 sera:** il vettore Cloudflare è escluso dai log; l'ipotesi va riformulata sull'origine prima di essere ritestata.

---

### H-009 — Google non richiede più le sitemap di questa proprietà: lo stato GSC è persistito, non è l'esito di una lettura

- **Status:** testing
- **Created:** 2026-07-27
- **Last updated:** 2026-07-27
- **Statement:** Googlebot crawla il sito normalmente e senza ostacoli, ma **non emette alcuna richiesta verso le sitemap** (F-021). Lo stato "Impossibile recuperare / Impossibile leggere" mostrato da GSC è quindi uno stato **persistito** da fallimenti passati — plausibilmente il periodo in cui il file era servito con `no-store, private` e cache Rank Math stale (F-014, F-011) — dopo il quale Google ha smesso di ritentare. Il campo "Ultima lettura" riflette la registrazione dell'invio, non un fetch. Ne consegue che il fix dell'header (F-015) era corretto ma inefficace: non c'era nessuna lettura da correggere.
- **Supporting evidence:** F-021 (0 richieste Google alle sitemap in 9 giorni; Bing 70-263/giorno; Googlebot attivo con 587 risposte 200 e nessun 403; Cloudflare non cacha le sitemap); F-022 (l'unico "controllo su URL vergine" tentato era un 301, quindi non concludente); F-013 (Bing legge lo stesso file); F-015 (header corretto senza effetto).
- **Falsifying evidence:** comparirebbe una richiesta da `66.249.*` verso una sitemap nel watch sul log, a fronte di un invio in GSC.
- **Test plan:** [execution-kit/01-sitemap-alias-test.md](../execution-kit/01-sitemap-alias-test.md). Esporre l'index su un URL mai visto da Google che risponda **200 diretto** (`/sitemap-fmc.xml`, rewrite interno), avviare il watch sull'access log, inviare l'URL in GSC, leggere l'esito a 24-48 h. Tre esiti possibili e loro interpretazione nella tabella in fondo a quel documento.
- **Related:** F-011, F-013, F-014, F-015, F-021, F-022, E-013, E-014, E-015, H-005, H-006, H-007
- **Notes:** Se confermata, chiude una catena di quattro ipotesi (H-005, H-006, H-007 e l'ipotesi WAF dell'audit del 26/07) che cercavano tutte un ostacolo dove non c'era traffico da ostacolare. Nota di priorità: senza alcuna sitemap letta Google indicizza comunque 4.822 pagine e porta 9.960 clic in 3 mesi — la sitemap non è la leva principale.

---

### H-008 — Il crawler di Meta è bloccato dalle Regole gestite Cloudflare, e questo spiega lo stato del dataset pixel

- **Status:** open
- **Created:** 2026-07-27
- **Last updated:** 2026-07-27
- **Statement:** Le Regole gestite Cloudflare bloccano richieste provenienti da `2a03:2880::/32`, che è lo spazio di indirizzi di Meta/Facebook: 3 blocchi osservati nelle ultime 24 ore (06:22:59, 07:34:34, 08:23:15 CEST). Se il crawler di Meta non raggiunge il sito, Meta non può verificare il dominio né associarlo al dataset — coerente con quanto mostra Gestione eventi per il pixel `636147212576293`: "Nessun sito web trovato", nessuna integrazione, configurazione al 33%.
- **Supporting evidence:** E-015 §1-ter (log dei blocchi), E-015 §6-bis (stato del dataset).
- **Falsifying evidence:** nessuna finora. Da notare che i blocchi osservati potrebbero riguardare lo scraper dei link condivisi e non il crawler di verifica del dominio: la correlazione non è ancora causalità.
- **Test plan:** (1) scheda "Testa gli eventi" del dataset + caricamento del sito, per stabilire se il pixel riceve eventi in tempo reale — separa "pixel morto" da "visibilità parziale del portfolio"; (2) Meta Business → Verifica del dominio, per vedere se la verifica fallisce; (3) se confermata, valutare una regola di skip Cloudflare per gli IP di Meta.
- **Related:** F-016, E-015, H-007
- **Notes:** Ha impatto diretto sulle campagne Paid Social, non sulla SEO. Va trattata nel workstream misurazione (Fase 0 del piano operativo), non nel P0 sitemap.
