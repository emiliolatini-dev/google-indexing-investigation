# Evidence — Crawl

Googlebot logs, server access logs, and crawl-tool output.

> **Status:** 3 items.

Naming: `E-<id>_<YYYY-MM-DD>_crawl_<short-description>.<ext>`
Entry format: [`templates/evidence-entry.md`](../../templates/evidence-entry.md).
Rules: [CONTRIBUTING.md §4](../../CONTRIBUTING.md#4-evidence-rules).

> **Privacy note:** anonymize visitor IP addresses and any personal data in
> server logs before committing.

## Index

| ID | Date | Description | Demonstrates | Status |
|----|------|-------------|--------------|--------|
| E-009 | 2026-06-24 | Log LiteSpeed: Googlebot GET sitemap durante periodo stale | Googlebot ha letto sitemap stale dimostrate dall'indagine | active |
| E-010 | 2026-06-21 | Log LiteSpeed: crawl pipeline sessione 20-06-2026 | Pipeline landing → prodotto → immagini completa e coerente | active |
| E-011 | 2026-06-21/26 | Log LiteSpeed: sessione 21-06-2026, finestra pre-GSC | Nessun crawl spontaneo osservato nei log disponibili prima del 26/06 | active |

## Entries

### E-009

- **Date collected:** 2026-06-24 (data log) / raccolto 2026-06-26
- **Category:** crawl
- **Provenance:** primary
- **Derived from:** n/a (primary)
- **File(s):** `E-009_2026-06-24_crawl_googlebot-get-sitemap-stale.txt` *(raw log da allegare, con IP anonimizzati)*
- **What it demonstrates:** Il 24/06/2026 Googlebot ha eseguito GET completi di `sitemap_index.xml` e `product-sitemap1.xml`. L'indagine ha dimostrato che in quella data le sitemap Rank Math servite erano stale e non includevano sessioni presenti nel database (vedi E-003). Questo esclude l'ipotesi che Google non avesse mai visto le sitemap; non dimostra causalità sulla mancata indicizzazione.
- **Collection method / tool:** LiteSpeed access log
- **Reproducible command:** n/a — log server
- **Collected by:** Project owner
- **Status:** active
- **Sensitive data removed:** IP da anonimizzare prima del commit
- **Referenced by:** F-009, H-001

---

### E-010

- **Date collected:** 2026-06-21 (data log) / raccolto 2026-06-26
- **Category:** crawl
- **Provenance:** primary
- **Derived from:** n/a (primary)
- **File(s):** `E-010_2026-06-21_crawl_session-20-06-2026-crawl-timeline.txt` *(raw log da allegare)*
- **What it demonstrates:** Per la sessione 20-06-2026: Googlebot ha eseguito GET della landing il 21/06 alle 15:50 UTC, GET del primo prodotto alle 18:49 UTC, seguiti da crawl Googlebot-Image su numerose immagini nei giorni successivi. La pipeline landing → prodotto → immagini è risultata completa e coerente.
- **Collection method / tool:** LiteSpeed access log
- **Reproducible command:** n/a — log server
- **Collected by:** Project owner
- **Status:** active
- **Sensitive data removed:** IP da anonimizzare prima del commit
- **Referenced by:** F-010

---

### E-011

- **Date collected:** 2026-06-21 → 2026-06-26 (finestra log) / raccolto 2026-06-26
- **Category:** crawl
- **Provenance:** primary
- **Derived from:** n/a (primary)
- **File(s):** `E-011_2026-06-21_crawl_session-21-06-2026-no-spontaneous-crawl.txt` *(raw log da allegare)*
- **What it demonstrates:** Nei log LiteSpeed disponibili relativi alla sessione 21-06-2026, non è stato osservato alcun crawl spontaneo di Googlebot sulla landing o sui prodotti prima della richiesta manuale GSC del 26/06. Il primo GET della landing è avvenuto subito dopo tale richiesta. Limitazione: i log disponibili potrebbero non coprire l'intera finestra temporale per rotazione o lacune di conservazione; non è possibile escludere eventi non intercettati.
- **Collection method / tool:** LiteSpeed access log
- **Reproducible command:** n/a — log server
- **Collected by:** Project owner
- **Status:** active
- **Sensitive data removed:** IP da anonimizzare prima del commit
- **Referenced by:** F-010
