# E-014 — Fix header sitemap applicato; "Impossibile leggere" persiste

- **Date:** 2026-07-26/27
- **Method:** modifica del mu-plugin `fotomotorankmathsitemapheaders.php` (filtro `nocache_headers`
  condizionato alle richieste sitemap) + verifica header via `curl` dalla rete utente + re-invio
  in GSC.
- **Demonstrates:** rigetta H-006; restringe la causa di "Impossibile leggere".

## Cosa è stato fatto
1. Individuata la fonte del `Cache-Control`: **`nocache_headers()` di WordPress** (in WP 6.x il
   default è `no-cache, must-revalidate, max-age=0, no-store, private`), NON l'array
   `rank_math/sitemap/http_headers` (che invece controlla Pragma/Expires/X-Robots-Tag).
2. Aggiunto al mu-plugin un filtro `nocache_headers` che, **solo per le richieste sitemap**,
   imposta `Cache-Control: public, max-age=300, must-revalidate` e rimuove Pragma/Expires.

## Verifica header (curl, UA Googlebot) — DOPO il fix
```
sitemap_index.xml   → Cache-Control: public, max-age=300, must-revalidate   (no no-store, no pragma/expires)
product-sitemap1.xml → idem
page-sitemap1.xml    → idem
```
✅ Header corretto e verificato.

## Esito in GSC
Re-inviata `sitemap_index.xml`. Pagina di dettaglio: **Ultima lettura 27/07/26** (lettura fresca,
DOPO il fix), **0 pagine, "Impossibile leggere la Sitemap"**.

→ Google ha ri-letto con l'header corretto e **ancora non interpreta la sitemap**.

## Conclusioni diagnostiche
- **H-006 (header no-store/private come causa) → RIGETTATA:** l'header è stato corretto e
  verificato, ma "Impossibile leggere" persiste su lettura fresca.
- **Cloudflare → ESCLUSO come causa:** il proprietario del sito conferma che il problema di
  indicizzazione delle sitemap è **antecedente all'introduzione di Cloudflare**.
- **Contenuto XML → già escluso (F-013):** Bing legge la stessa sitemap con successo (58.400 URL).
- **Miglioramento comunque acquisito:** header sitemap ora corretto (cacheabile, senza no-store),
  utile a prescindere.

## Causa ancora aperta — direzioni non ancora esplorate
- Numero/struttura delle sotto-sitemap (244 file) e generazione dinamica non-cacheata.
- Eventuale differenza tra ciò che Rank Math serve e ciò che Google si aspetta (formato index,
  `lastmod`, namespace, encoding a livello di byte per il fetcher Google).
- Verifica lato log server di cosa riceve esattamente il fetcher Google sulle richieste sitemap.
