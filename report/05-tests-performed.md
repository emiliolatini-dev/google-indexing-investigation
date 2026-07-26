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

---

## T-002 — Esperimento controllato: prima sessione post-patch (in attesa)

**Status:** in attesa — da eseguire alla prossima sessione fotografica
**Addresses:** Q-002, Q-001a
**Related evidence:** da raccogliere

### Obiettivo

Confrontare il comportamento di crawl di Googlebot PRE PATCH (sessione 21-06-2026,
baseline documentata in F-010) vs POST PATCH (prossima sessione), eliminando la
variabile "sitemap stale". Se la pipeline post-patch produce crawl spontaneo entro
24h, questo costituisce evidenza indirettamente a supporto di Q-001a.

### Variabile eliminata

Sitemap stale di Rank Math: la patch garantisce sitemap coerenti entro pochi minuti
dalla pubblicazione (dimostrato da F-011).

### Timeline da registrare

| Punto | Evento | Da registrare |
|-------|--------|--------------|
| T0 | Pubblicazione sessione | timestamp UTC |
| T1 | Esecuzione `generate-json` | timestamp UTC |
| T2 | Flush automatico cache Rank Math (output WP-CLI) | timestamp UTC + NNN file rimossi |
| T3 | `sitemap-check.sh` STATUS=PASS | timestamp UTC |
| T4 | Primo GET Googlebot `product-sitemap1.xml` (log LiteSpeed) | timestamp UTC |
| T5 | Primo GET Googlebot landing sessione | timestamp UTC |
| T6 | Primo GET Googlebot URL prodotto | timestamp UTC |
| T7 | URL landing presente nell'indice GSC (URL Inspection) | timestamp UTC |

### Criteri di interpretazione

- T4 − T3 < 24h: crawl spontaneo precoce → coerente con Q-002 = sì
- T4 assente dopo 48h: crawl non spontaneo → crawl budget o altri fattori limitanti
- Confronto T5(POST) vs T5(PRE=26/06 dopo GSC manuale): misura impatto patch

### Limitazioni

- Singolo datapoint: una sola sessione non è sufficiente per conclusioni generali.
- I log LiteSpeed potrebbero avere rotazione; verificare copertura prima dell'esperimento.
- Google può crawlare da IP non identificabili come Googlebot senza user-agent standard.

---

## Referenced evidence

- E-004: file fisici `rank_math_*.xml` e option `rank_math_sitemap_cache_files`
- E-005: output reale del comando `generate-json` con flush cache
- E-006: `product-sitemap1.xml` post-fix con sessione 21-06-2026
- E-008: confronto `post_modified` DB vs `<lastmod>` sitemap
- E-009: log LiteSpeed — Googlebot GET sitemap stale il 24/06/2026
- E-010: log LiteSpeed — pipeline crawl sessione 20-06-2026
- E-011: log LiteSpeed — sessione 21-06-2026, nessun crawl spontaneo osservato
- E-012: output `sitemap-check.sh` post-patch (8 PASS / 0 FAIL)

## Related open questions

- [Q-001](../registry/questions.md#q-001): il drift della cache ha contribuito ai problemi GSC e alla scarsa indicizzazione?
- [Q-001a](../registry/questions.md#q-001a): Google ha aggiornato la crawl queue su uno snapshot sitemap incompleto?
- [Q-002](../registry/questions.md#q-002): la prossima sessione post-patch verrà crawlata spontaneamente entro 24h?

## Confidence Assessment

**Level: Medium.** T-001 eseguito e riproducibile lato sitemap. F-008 chiude la
pista lastmod. F-009 dimostra che Googlebot ha letto sitemap stale. F-010 fornisce
un confronto comportamentale tra sessione 20 (crawl completo) e sessione 21 (nessun
crawl spontaneo osservato). L'impatto causale sull'indicizzazione Google non è
ancora dimostrabile; T-002 è progettato per raccogliere evidenza comparativa.

## Chapter changelog

| Date | Version | Change | Author |
|------|---------|--------|--------|
| 2026-06-25 | 0.1 | Skeleton created | |
| 2026-06-26 | 0.2 | T-001 (cache flush + rigenerazione sitemap); checklist operativa post-import; collegamento a tools/sitemap-check.sh | |
| 2026-06-26 | 0.3 | T-002 (esperimento controllato prossima sessione, T0→T7); aggiornamento referenced evidence con E-008–E-012; aggiornamento open questions con Q-001a e Q-002 | |
