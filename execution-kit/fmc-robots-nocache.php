<?php
/**
 * Plugin Name: FMC — robots.txt fuori dalla cache LiteSpeed
 * Description: Esclude robots.txt dalla cache LiteSpeed, così le modifiche sono immediate senza purga manuale.
 * Version: 1.0
 *
 * Destinazione: wp-content/mu-plugins/fmc-robots-nocache.php
 *
 * CONTESTO (E-015, piano operativo §2.4-bis)
 * Il 27-07-2026 una modifica al robots.txt non è arrivata a Googlebot per due
 * strati di cache sovrapposti:
 *   - Cloudflare, con TTL cache browser a 1 mese  -> risolto con la Cache Rule
 *     "Controllo SEO - robots.txt e sitemap sempre fresche" (bypass)
 *   - LiteSpeed, con x-litespeed-cache: hit       -> risolto da questo file
 *
 * Le sitemap erano già coperte: il mu-plugin fotomotorankmathsitemapheaders.php
 * imposta x-litespeed-cache-control: no-cache su tutte. Restava solo robots.txt.
 *
 * PERCHÉ ESCLUDERLO INVECE DI PURGARE
 * robots.txt viene richiesto poche volte al giorno: il costo di non cacharlo è
 * nullo. Il costo di cacharlo per 7 giorni è che ogni modifica futura richiede
 * di ricordarsi una purga manuale — ed è il passo che si dimentica.
 *
 * NOTA: questo file impedisce nuove memorizzazioni, non svuota quelle esistenti.
 * Alla prima installazione serve una purga per URL di https://fotomoto.click/robots.txt
 * (LiteSpeed -> Cassetta degli attrezzi -> Cache svuotata e pulita da... -> Indirizzo URL).
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'do_robotstxt',
	function () {
		// API di controllo di LiteSpeed Cache: no-op se il plugin non è attivo.
		do_action( 'litespeed_control_set_nocache', 'FMC: robots.txt deve essere sempre fresco' );
	}
);

/**
 * RIMOZIONE
 * Cancellare questo file. Nessuna option, nessun residuo.
 */
