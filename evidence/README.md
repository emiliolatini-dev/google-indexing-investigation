# Evidence

Raw, **immutable, dated** evidence supporting the investigation. Evidence is
referenced from the report by ID (`E-NNN`) and is **never** pasted into the
report or interpreted here.

See [CONTRIBUTING.md §4](../CONTRIBUTING.md#4-evidence-rules) for the full rules.

---

## Naming convention

```
E-<id>_<YYYY-MM-DD>_<category>_<short-description>.<ext>
```

Example: `E-021_2026-06-25_http_product-url-headers.txt`

## Categories

| Folder | Scope |
|--------|-------|
| [`http/`](http/) | HTTP headers, response captures, status codes, `curl -I` output. |
| [`robots-sitemap/`](robots-sitemap/) | `robots.txt` and XML sitemap snapshots. |
| [`gsc/`](gsc/) | Google Search Console exports (CSV) and screenshots. |
| [`rendering/`](rendering/) | Rendered HTML, DOM vs. source comparisons. |
| [`crawl/`](crawl/) | Googlebot logs, server access logs, crawl-tool output. |
| [`network/`](network/) | CDN/Cloudflare, DNS, TLS, redirect-chain captures. |
| [`screenshots/`](screenshots/) | Dated screen captures. |

## Rules recap

- Immutable: never edit a committed evidence file. Supersede with a new item.
- Self-describing: record how, with what tool, and when it was collected.
- Reproducible: include the exact command where possible.
- No sensitive data (tokens, internal IPs, credentials). Anonymize procedures.

## Master index

Each category folder maintains its own index. Global evidence count is the sum of
the per-folder indices.

| Category | Index | Items |
|----------|-------|-------|
| http | [http/README.md](http/README.md) | 0 |
| robots-sitemap | [robots-sitemap/README.md](robots-sitemap/README.md) | 0 |
| gsc | [gsc/README.md](gsc/README.md) | 0 |
| rendering | [rendering/README.md](rendering/README.md) | 0 |
| crawl | [crawl/README.md](crawl/README.md) | 0 |
| network | [network/README.md](network/README.md) | 0 |
| screenshots | [screenshots/README.md](screenshots/README.md) | 0 |
