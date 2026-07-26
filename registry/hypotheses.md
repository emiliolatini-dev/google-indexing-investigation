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
