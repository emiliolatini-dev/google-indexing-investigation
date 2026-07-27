<?php
/**
 * Plugin Name: FMC — alias sitemap per test GSC
 * Description: Espone l'index sitemap di Rank Math su /sitemap-fmc.xml con risposta 200 diretta, per l'esperimento H-009. Temporaneo: rimuovere a test concluso.
 * Version: 1.0
 *
 * Destinazione: wp-content/mu-plugins/fmc-sitemap-alias.php
 *
 * Perché una regola di WordPress e non un rewrite in .htaccess:
 * un rewrite interno in .htaccess cambia il path sul filesystem ma lascia
 * REQUEST_URI invariato, e WordPress instrada su REQUEST_URI — il risultato
 * sarebbe un 404, non la sitemap.
 *
 * La query var `sitemap=1` è quella di Rank Math, verificata il 27-07-2026:
 * https://fotomoto.click/?sitemap=1 -> 200, text/xml, 244 voci <sitemap>.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		add_rewrite_rule( '^sitemap-fmc\.xml$', 'index.php?sitemap=1', 'top' );

		// Flush una sola volta: le rewrite rules sono costose da rigenerare a ogni load.
		if ( '1' !== get_option( 'fmc_sitemap_alias_flushed' ) ) {
			flush_rewrite_rules( false );
			update_option( 'fmc_sitemap_alias_flushed', '1', false );
		}
	},
	20
);

/**
 * RIMOZIONE
 * 1. Cancellare questo file.
 * 2. Ripulire l'option e rigenerare le rewrite rules:
 *    wp option delete fmc_sitemap_alias_flushed && wp rewrite flush
 *    (oppure: WP admin -> Impostazioni -> Permalink -> Salva, senza modificare nulla)
 */
