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
| H-005 | Un filtro bot (WAF/Cloudflare) blocca il fetcher sitemap di Google | **rejected** | 2026-07-26 |
| H-006 | Header `no-store, private` + stato stale causano il fallimento lettura sitemap lato Google | **rejected** (header fixato, problema persiste) | 2026-07-27 |

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

- **Status:** rejected
- **Created:** 2026-07-26
- **Last updated:** 2026-07-26
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
