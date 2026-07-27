<?php
/**
 * Plugin Name: FMC — 410 Gone sul namespace archiviato /gallerie/
 * Description: Restituisce 410 leggero al posto del 404 WordPress da 140 KB per gli URL /gallerie/ il cui prodotto non esiste più. Non tocca i redirect esistenti.
 * Version: 1.0
 *
 * Destinazione: wp-content/mu-plugins/fmc-gallerie-410.php
 *
 * CONTESTO (vedi registry/claims.md O-007, F-018)
 * Fino al 2025 le sessioni più vecchie venivano archiviate in gallerie di sola
 * lettura sotto /gallerie/, fuori da WooCommerce. Tornando a tenere tutto come
 * prodotti, una parte delle foto non è stata reimportata. Risultato misurato il
 * 27-07-2026:
 *
 *   /gallerie/{slug}/  con prodotto esistente  -> 301 verso il prodotto  (funziona già)
 *   /gallerie/{slug}/  senza prodotto           -> 404 da 139.937 byte   (1.129 URL in GSC)
 *
 * PERCHÉ NON UNA REGOLA IN .htaccess
 * `RewriteRule ^gallerie/.+ - [G,L]` restituirebbe 410 a TUTTO il namespace,
 * distruggendo i 301 che oggi funzionano. Il 410 va dato solo sul ramo in cui la
 * risoluzione è già fallita.
 *
 * PERCHÉ È SICURO
 * L'hook agisce a valle: se il prodotto esiste, il redirect scatta prima e
 * `is_404()` è falso, quindi questo codice non viene mai raggiunto.
 *
 * PERCHÉ 410 E NON 404
 * Il contenuto non esiste più e non tornerà. Google ritira un 410 dall'indice più
 * in fretta di un 404, e qui la risposta costa zero PHP e zero template invece di
 * 140 KB renderizzati — rilevante perché il 5% delle richieste di Googlebot al
 * sito finisce in 404, e due URL di questo namespace sono andati in timeout a 34 s.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}

		$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
		if ( 0 !== strpos( (string) $path, '/gallerie/' ) ) {
			return;
		}

		status_header( 410 );
		nocache_headers();
		header( 'Content-Type: text/plain; charset=UTF-8' );
		header( 'X-Robots-Tag: noindex' );
		echo "410 Gone\n";
		exit;
	},
	0
);

/**
 * RIMOZIONE
 * Cancellare questo file. Nessuna option, nessuna rewrite rule, nessun residuo.
 */
