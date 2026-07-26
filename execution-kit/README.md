# Execution Kit — FotoMoto.Click SEO

> Creato il 26-07-2026. Traduce il [master audit](../seo-audit/2026-07-26-master-audit.md) e le
> [evidenze GSC/GA](../seo-audit/2026-07-26-gsc-ga-evidence.md) in **artefatti pronti al deploy**.
> Ogni file qui e' applicabile; le prove HTTP che li giustificano sono state ri-verificate dal
> server di produzione lo stesso giorno.

## Verifiche fresche del 26-07-2026 (dalla rete di Emilio)
- Il server risponde **200 a Googlebot** (desktop+mobile) su home, `sitemap_index.xml` e
  sotto-sitemap, in 0,3-0,4 s. → **Nessun blocco di Googlebot per user-agent.** Il 403 visto in
  precedenza era per IP datacenter (Cloudflare), NON applicabile al vero Googlebot verificato.
  Ipotesi H1 "il WAF blocca Googlebot" → **declassata**. Dettagli in `00-P0-sitemap-fetch-fix.md`.
- Confermati ancora attivi: `/page/N/`→200 (trappola infinita), pagine vuote `fotomotoclick-*`→200,
  `/foto/{loc}/feed/`→200, `product-tag/spino`→200, `/gallerie-foto/`→301.
- N2 chiarito: `/gallerie/fotomotoclick-*` e' un **301** verso l'URL prodotto canonico (non un
  duplicato live). Alimenta il conteggio "Pagina con reindirizzamento" (1.176), non i duplicati.

## Ordine di esecuzione (allineato ai "primi 10 giorni" del master audit §15)

### 🔴 Blocco 0 — sblocca tutto il resto
| # | File | Chi | Accesso serve |
|---|---|---|---|
| 0 | `00-P0-sitemap-fetch-fix.md` | Emilio + dev | mu-plugin, Cloudflare, GSC |

### 🟢 Quick wins deploy-ready (0-2 settimane)
| # | File / artefatto | Chi | Accesso serve |
|---|---|---|---|
| 1 | `robots.txt` (sostituisce quello attuale) | dev | FTP/hosting |
| 2 | `11-warmup-cache.sh` (in coda a `generate-json`) | dev | pipeline Processor |
| 3 | `12-recent-sessions-sitemap.md` (news-sitemap) | dev | mu-plugin |
| 4 | `09-pagination-fix.md` (canonical/410 su page/N) | dev | .htaccess o template |
| 5 | `schema/person-organization.jsonld.html` | dev | <head> globale |
| 6 | `llms.txt` (in root del sito) | dev | FTP/hosting |
| 7 | `schema/video-object.jsonld.html` (12 video) | dev | template prodotto video |

### 🟡 Alto impatto (2-8 settimane) — richiedono lavoro sul template/pipeline
| # | File / artefatto | Nota |
|---|---|---|
| 8 | `schema/session-landing.jsonld.html` + riscrittura HTML landing sessione | azione 20/21, pagina piu' importante |
| 9 | `schema/image-licensable.jsonld.html` + `13-iptc-pipeline.md` | azione 22, canale Google Images (oggi 29 clic/3 mesi) |

## Interventi che NON sono file qui, ma vanno fatti a mano (breve, alto ROI)
Rif. roadmap master audit. Questi richiedono l'editor WordPress/Elementor, non un artefatto:
- **H1** su home, prodotto, sessione, contatti, link, collabora, scopri-qualita (azione 3).
- **Sostituire gli 8 link home → `/gallerie-foto/` (301)** con il target reale. NB dai dati:
  `/gallerie-foto/` e' la **4ª pagina per traffico** (426 clic) pur essendo un 301 → valutare di
  **ripristinarla come pagina reale** invece del semplice fix del link (evidenze §3, N4).
- **Fix `<meta description>` sessioni** (oggi "Foto del {data} |", pipe orfana).
- **`alt` immagini gallerie**: template passo+data+ora+numero (oggi 1 valore duplicato per 18 img).
- **`max-image-preview:large`** anche sulla landing localita'.
- **Rimuovere/noindex i 4 `product_tag`** e rimuovere `/gallerie-foto/` da `product-sitemap1`.
- **Fix `SearchAction`** → puntare a `/cerca-foto-archivio/`, non a `/?s=` (che e' in Disallow).

## Decisioni consapevoli prese in questo kit (rivedibili)
1. **AI crawler**: `robots.txt` apre il TESTO a GPTBot/Google-Extended/CCBot e protegge i FILE
   IMMAGINE (`Disallow: /wp-content/uploads/`). Rif. §11.1. Per ri-bloccare del tutto: rimettere
   `Disallow: /` sotto ciascuno.
2. **Curated indexing** (§3.1 opzione B): lo schema sessione e la pipeline assumono che si
   indicizzino **20-40 foto/sessione** con caption unica e il resto vada `noindex`. Costo
   misurato della deindicizzazione: **0,55% dei clic** (evidenze §3). Da eseguire dopo il fix P0.

## Verifica globale post-deploy
1. `sitemap_index.xml` in GSC: da "Impossibile recuperare" a "Riuscito", "Ultima lettura" valorizzata.
2. Rich Results Test verde su: prodotto (ImageObject license), sessione (Event+ItemList), video.
3. `curl -sI -A Googlebot .../sitemap_index.xml` → `Cache-Control: public, max-age=300`.
4. GSC → Indicizzazione: calo di "scansionata ma non indicizzata" (oggi 5.640) nelle settimane
   successive; le nuove sessioni indicizzate in ore, non mai.
5. Registrare ogni esito come `F-`/`O-` in `registry/claims.md` (metodo del repo).
