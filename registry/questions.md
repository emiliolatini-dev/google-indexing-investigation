# Open Questions Registry

> **Status:** empty — no questions recorded yet.

This registry holds every **QUESTION** (`Q-`): an open question raised by the
investigation. A question implies no claim. When a question is answered, link the
resolving evidence or fact and update its status.

See [CONTRIBUTING.md §1](../CONTRIBUTING.md#1-epistemic-rules-non-negotiable).

---

## Index

| ID | Question | Status | Resolved by | Added |
|----|----------|--------|-------------|-------|
| Q-001 | Il drift della cache sitemap di Rank Math ha contribuito ai problemi GSC e alla scarsa indicizzazione? | open | — | 2026-06-26 |
| Q-001a | Google ha aggiornato la propria crawl queue usando uno snapshot sitemap incompleto durante il periodo del bug? | open | — | 2026-06-26 |
| Q-002 | Con pipeline post-patch, la prossima sessione verrà crawlata spontaneamente da Googlebot entro le prime 24h dalla pubblicazione? | open | — | 2026-06-26 |

Status values: `open` · `answered` · `superseded`.

---

## Entries

### Q-001

- **Question:** Il drift della cache fisica delle sitemap di Rank Math ha contribuito (o è stata la causa principale) dei problemi Google Search Console ("Impossibile recuperare") e della scarsa indicizzazione di fotomoto.click?
- **Status:** open
- **Raised by:** H-001, O-005, F-009
- **Resolved by:** —
- **Added:** 2026-06-26
- **Notes:** O-004 e O-005 sono coerenti con H-001, ma la coerenza temporale non costituisce prova causale. L'indagine non esclude che altri fattori del processo di indicizzazione Google abbiano contribuito. Vedi Q-001a per la sotto-domanda operativa. Q-002 è l'esperimento controllato che potrà fornire evidenza indiretta.

---

### Q-001a

- **Question:** Google ha aggiornato la propria crawl queue utilizzando uno snapshot sitemap incompleto durante il periodo in cui Rank Math serviva sitemap stale (dimostrato dall'indagine)? In altri termini: la fotografia incompleta del sito fornita dalle sitemap stale si è tradotta in una crawl queue incompleta?
- **Status:** open
- **Raised by:** F-009, F-010, H-001
- **Resolved by:** —
- **Added:** 2026-06-26
- **Notes:** Sotto-domanda operativa di Q-001. F-009 dimostra che Googlebot ha letto le sitemap stale; F-010 mostra l'assenza di crawl spontaneo per la sessione 21-06-2026 nei log disponibili. La correlazione è forte ma non sufficiente per rispondere affermativamente. Non abbiamo accesso alla crawl queue interna di Google. T-002 (prossima sessione post-patch) fornirà un confronto diretto PRE vs POST PATCH.

---

### Q-002

- **Question:** Con la pipeline post-patch operativa (invalidazione automatica cache Rank Math, sitemap sempre coerenti), la prossima sessione FotoMoto.Click verrà crawlata spontaneamente da Googlebot entro le prime 24h dalla pubblicazione, senza necessità di richiesta manuale GSC?
- **Status:** open
- **Raised by:** F-010, F-011, T-002
- **Resolved by:** —
- **Added:** 2026-06-26
- **Notes:** Questa è la domanda che l'esperimento controllato T-002 è progettato per rispondere. La timeline T0→T7 (pubblicazione → flush → sitemap check PASS → primo GET Googlebot sitemap → primo GET landing → primo GET prodotto → indicizzazione GSC) permetterà di confrontare direttamente il comportamento PRE PATCH (sessione 21-06-2026) vs POST PATCH (prossima sessione).
