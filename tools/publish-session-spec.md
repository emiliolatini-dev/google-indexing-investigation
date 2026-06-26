# Spec — `wp83 fotomoto publish-session`

> **Status:** specifica di riferimento. L'implementazione appartiene al repo del
> mu-plugin FotoMoto importer, non a questo repository. Questo documento descrive
> input, sequenza, output e formato del report.

L'ultimo anello della pipeline: un singolo comando che pubblica una sessione,
invalida la cache sitemap, esegue il sanity check e produce un artefatto
verificabile per ogni pubblicazione.

---

## Input

```
wp83 fotomoto publish-session --manifest=<path>
```

| Parametro | Descrizione |
|-----------|-------------|
| `--manifest` | Path al manifest della sessione da pubblicare. |

---

## Sequenza

| # | Step | Stato componente |
|---|------|------------------|
| 1 | `import-session` | esistente |
| 2 | `repair-session` (solo se necessario) | esistente |
| 3 | `generate-json` | esistente |
| 4 | `flush_rank_math_sitemap_cache()` | **già implementato** (patch) |
| 5 | `sitemap-check.sh` | **già implementato** ([`tools/sitemap-check.sh`](sitemap-check.sh)) |
| 6 | Scrittura `publish-report-<session>.json` | da implementare |

Lo step 4 è già richiamato automaticamente da `flush_cache()`. Lo step 5 è
read-only e restituisce exit code 0 (PASS) o 1 (FAIL).

---

## Output — `publish-report-<session>.json`

Un artefatto per pubblicazione. Esempio:

```json
{
  "session": "bocca-serriola-28-06-2026",
  "published_at": "2026-06-28T18:42:10Z",
  "products": 2513,
  "rank_math_flush": true,
  "rank_math_files_removed": 179,
  "sitemap_check": "PASS",
  "sitemap_check_pass": 8,
  "sitemap_check_fail": 0,
  "landing_url": "https://fotomoto.click/foto/bocca-serriola/28-06-2026/",
  "patch_active": true
}
```

| Campo | Origine |
|-------|---------|
| `session` | slug derivato dal manifest |
| `published_at` | timestamp UTC fine pubblicazione |
| `products` | conteggio prodotti importati |
| `rank_math_flush` | esito step 4 |
| `rank_math_files_removed` | NNN dal flush (metrica diagnostica, 0 ammesso) |
| `sitemap_check` | `PASS` / `FAIL` dallo step 5 |
| `sitemap_check_pass` / `sitemap_check_fail` | conteggi dallo script |
| `landing_url` | URL landing della sessione |
| `patch_active` | sempre `true` post-patch |

---

## Relazione con l'Observatory

Il report alimenta i campi **T0–T1** di
[`observatory/session-observatory.json`](../observatory/session-observatory.json):

| Campo report | Campo observatory |
|--------------|-------------------|
| `published_at` | `t0_published_at` |
| `sitemap_check` | `t1_sitemap_check_status` |
| `products` | `t1_products_count` |
| `rank_math_flush` | `t1_rank_math_flush` |
| `patch_active` | `patch_active` |

I campi **T2–T5** (crawl Googlebot, indicizzazione GSC) si aggiungono
successivamente dai log LiteSpeed e da GSC URL Inspection — non sono noti al
momento della pubblicazione.

---

## Note di design

- Il comando **non deve fallire silenziosamente**: se `sitemap_check` è `FAIL`,
  il report lo registra comunque (`"sitemap_check": "FAIL"`) per non perdere
  l'evento. La decisione se bloccare la pubblicazione è una scelta operativa
  separata.
- Il report è un **artefatto immutabile per esecuzione**: nominato con lo slug e
  conservabile come evidenza (categoria `rank-math` o `crawl`) se necessario.
- `patch_active` nel report è ridondante (è sempre `true` post-patch) ma esplicito:
  rende l'artefatto autosufficiente senza contesto esterno.
