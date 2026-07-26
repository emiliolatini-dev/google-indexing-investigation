<?php
/**
 * FotoMoto.Click - Rank Math Sitemap Headers Fix
 *
 * Controlla gli header HTTP delle sitemap XML/XSL generate da Rank Math
 * tramite il filtro `rank_math/sitemap/http_headers` (array autorevole:
 * cio' che sta qui determina gli header finali inviati da Rank Math).
 *
 * FIX P0 (26-07-2026) - indicizzazione sitemap in GSC ("Impossibile recuperare"):
 *   - Cache-Control era `no-cache, must-revalidate, max-age=0, no-store, private`.
 *     `no-store` + `private` sono l'estremo sbagliato per un file che Google deve
 *     recuperare periodicamente. Sostituito con `public, max-age=300, must-revalidate`:
 *     cacheabile per 5 minuti (la pipeline di publish fa comunque il purge), leggibile
 *     dai fetcher e dalle cache condivise.
 *   - Rimossi Pragma/Expires che forzavano il no-cache.
 *   - Mantenuta la rimozione di X-Robots-Tag: noindex (comportamento preesistente).
 *
 * Rif. repo google-indexing-investigation: seo-audit 4.2, evidence E-013,
 * execution-kit/00-P0-sitemap-fetch-fix.md.
 */

defined('ABSPATH') || exit;

add_filter('rank_math/sitemap/http_headers', function ($headers, $is_xsl) {

    // Rimuovi X-Robots-Tag: noindex (preesistente).
    if (isset($headers['X-Robots-Tag'])) {
        unset($headers['X-Robots-Tag']);
    }

    // Cache-Control corretto: cacheabile, breve, rivalidabile. Niente no-store/private.
    $headers['Cache-Control'] = 'public, max-age=300, must-revalidate';

    // Rimuovi header che forzano il no-cache (se presenti nell'array Rank Math).
    unset($headers['Pragma'], $headers['Expires']);

    return $headers;

}, 20, 2);
