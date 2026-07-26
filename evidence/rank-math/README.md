# Evidence — Rank Math

Evidence related to Rank Math SEO plugin behaviour: sitemap cache files, option
values, flush operations, and post-patch validation.

Naming: `E-<id>_<YYYY-MM-DD>_rank-math_<short-description>.<ext>`
Entry format: [`templates/evidence-entry.md`](../../templates/evidence-entry.md).
Rules: [CONTRIBUTING.md §4](../../CONTRIBUTING.md#4-evidence-rules).

## Index

| ID | Date | Description | Demonstrates | Status |
|----|------|-------------|--------------|--------|
| E-008 | 2026-06-26 | DB `post_modified` vs sitemap `<lastmod>` comparison | `lastmod` coerente con DB per sessione 21-06-2026 | active |
| E-012 | 2026-06-26 | `sitemap-check.sh` output post-patch | 8 PASS / 0 FAIL — pipeline lato server validata | active |

## Entries

### E-008

- **Date collected:** 2026-06-26
- **Category:** rank-math
- **Provenance:** derived (primary DB query + primary sitemap fetch)
- **Derived from:** n/a (primary sources combined)
- **File(s):** `E-008_2026-06-26_rank-math_lastmod-vs-post-modified.md` *(raw output da allegare)*
- **What it demonstrates:** Per i prodotti della sessione Bocca Serriola 21-06-2026, il valore `<lastmod>` emesso da Rank Math in `product-sitemap1.xml` è coerente con `wp_posts.post_modified`. Nessuna anomalia rilevata.
- **Collection method / tool:** `SELECT post_date, post_modified FROM wp_posts WHERE post_title LIKE 'fotomotoclick-bocca-serriola-21-06-2026%'` + `curl https://fotomoto.click/product-sitemap1.xml`
- **Reproducible command:** vedere sopra
- **Collected by:** Project owner
- **Status:** active
- **Sensitive data removed:** n/a
- **Referenced by:** F-008

---

### E-012

- **Date collected:** 2026-06-26
- **Category:** rank-math
- **Provenance:** primary
- **Derived from:** n/a (primary)
- **File(s):** `E-012_2026-06-26_rank-math_sitemap-check-output.txt` *(raw output da allegare)*
- **What it demonstrates:** Esecuzione di `sitemap-check.sh` post-patch restituisce 8 PASS / 0 FAIL, confermando che la pipeline lato server (flush cache, rigenerazione sitemap, HTTP 200, canonical corretto, sessione presente nei product-sitemap) funziona correttamente.
- **Collection method / tool:** `./tools/sitemap-check.sh "bocca-serriola/21-06-2026"`
- **Reproducible command:** `./tools/sitemap-check.sh "<session-path>"`
- **Collected by:** Project owner
- **Status:** active
- **Sensitive data removed:** n/a
- **Referenced by:** F-011
