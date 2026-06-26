# 05 — Tests Performed

> **Status:** draft
> **Version:** 0.2
> **Last updated:** 2026-06-26
> **Depends on:** 02-methodology, 04-observations

---

## Purpose of this chapter

Document each verification carried out: what was tested, the procedure (with
reproducible commands where possible), the result, and the evidence it produced.
Each test links to its evidence item(s). This chapter reports test outcomes
factually; it does not interpret them into conclusions.

---

## T-001 — Eliminazione cache fisica Rank Math e rigenerazione sitemap

**Date:** 2026-06-26
**Addresses:** H-001 (Rank Math sitemap cache drift)
**Related evidence:** E-004, E-005, E-006

### Setup

- Sito: https://fotomoto.click
- Stack: WordPress + WooCommerce + Rank Math + WP-CLI (mu-plugin FotoMoto importer)
- File fisici presenti prima del test: `wp-content/uploads/rank-math/rank_math_*.xml`
- Option presente prima del test: `rank_math_sitemap_cache_files`

### Variabili controllate

- Nessuna modifica ai contenuti del database durante il test
- Nessuna modifica alla configurazione di Rank Math
- Il test è stato eseguito su sessione già pubblicata (Bocca Serriola 21-06-2026)

### Comando eseguito

```
wp83 fotomoto generate-json --session=bocca-serriola-21-06-2026
```

### Risultato atteso

Le sitemap vengono rigenerate automaticamente dopo l'eliminazione della cache
fisica; la sessione compare immediatamente in `product-sitemap1.xml`.

### Risultato effettivo

```
Success:
Rank Math sitemap cache invalidata: 179 file XML rimossi
```

- `product-sitemap1.xml`: contiene immediatamente la sessione Bocca Serriola 21-06-2026 ✓
- Sitemap index: corretto ✓
- 177 product sitemap presenti ✓
- HTTP 200 ✓
- Header `Cache-Control: no-cache` ✓
- `CF-Cache-Status: DYNAMIC` ✓
- Nessun riferimento a sitemap obsolete 178/179 ✓

### Interpretazione

[OBSERVATION · O-003] La funzione `flush_rank_math_sitemap_cache()` ha rimosso
179 file XML fisici e rigenerato correttamente le sitemap. Questo è coerente con
H-001 (la cache fisica era la causa della sitemap stale). Non si tratta di una
dimostrazione di causalità esclusiva rispetto ai problemi di indicizzazione
Google; vedi [QUESTION · Q-001].

### Limitazioni

- Il test è stato eseguito una sola volta su una singola sessione.
- Non è stato misurato il tempo esatto di rigenerazione delle sitemap da parte
  di Rank Math dopo l'eliminazione della cache.
- L'impatto sull'indicizzazione Google non è verificabile in tempo reale.

---

## Procedura operativa post-import (checklist)

Eseguire dopo ogni `import-session`, `repair-session`, o `generate-json`.
Lo script di automazione si trova in [`tools/sitemap-check.sh`](../tools/sitemap-check.sh).

### FASE 1 — Verifica flush cache (immediata, lato server)

```
[ ] Output WP-CLI contiene: "Rank Math sitemap cache invalidata: NNN file XML rimossi"
[ ] NNN annotato come metrica diagnostica (0 è accettabile se la cache era già
    vuota; va segnalato se l'output non menziona il flush)
[ ] Nessun errore PHP/WP-CLI nell'output
```

### FASE 2 — Verifica sitemap_index.xml (entro 2 minuti)

```
[ ] HTTP 200
[ ] Cache-Control: no-cache (o equivalente)
[ ] CF-Cache-Status: DYNAMIC o MISS (HIT = warning diagnostico)
[ ] Numero di <loc> product sitemap coerente con il numero atteso
[ ] Nessun riferimento a sitemap con indice fuori range
```

### FASE 3 — Verifica presenza URL prodotto in product-sitemap*.xml (entro 5 minuti)

```
[ ] URL prodotto WooCommerce della sessione compaiono in almeno un
    product-sitemap*.xml
[ ] Gli URL trovati corrispondono ai permalink canonici attesi
```

### FASE 4 — Verifica URL landing sessione via HTTP (separata)

```
[ ] HTTP 200 sull'URL landing (es. /foto/bocca-serriola/21-06-2026/)
[ ] Canonical tag corrisponde esattamente all'URL richiesta
[ ] Nessun noindex nel <head>
[ ] Nessun X-Robots-Tag: noindex negli header HTTP
```

### FASE 5 — GSC (asincrona, non blocca le fasi precedenti)

```
[ ] URL Inspection in GSC: stato annotato con data
[ ] Se "not on Google": richiedere indicizzazione e annotare data
[ ] NON concludere che il problema sia risolto prima della conferma GSC
```

### Esecuzione automatica

```bash
chmod +x tools/sitemap-check.sh
./tools/sitemap-check.sh "bocca-serriola/21-06-2026"
```

Exit code 0 = tutti i check PASS. Exit code 1 = almeno un FAIL.

---

## Referenced evidence

- E-004: file fisici `rank_math_*.xml` e option `rank_math_sitemap_cache_files`
- E-005: output reale del comando `generate-json` con flush cache
- E-006: `product-sitemap1.xml` post-fix con sessione 21-06-2026

## Related open questions

- [Q-001](../registry/questions.md#q-001): il drift della cache ha contribuito
  ai problemi GSC e alla scarsa indicizzazione?

## Confidence Assessment

**Level: Medium.** T-001 è stato eseguito e produce risultati riproducibili
lato sitemap. L'impatto sull'indicizzazione Google non è dimostrabile con i
dati attualmente disponibili.

## Chapter changelog

| Date | Version | Change | Author |
|------|---------|--------|--------|
| 2026-06-25 | 0.1 | Skeleton created | |
| 2026-06-26 | 0.2 | T-001 (cache flush + rigenerazione sitemap); checklist operativa post-import; collegamento a tools/sitemap-check.sh | |
