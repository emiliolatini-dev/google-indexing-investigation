<?php
/**
 * Plugin Name: FMC — Conversions API: evento Purchase server-side
 * Description: Invia l'evento Purchase a Meta dal server, deduplicato con il pixel browser e condizionato al consenso marketing.
 * Version: 1.0
 *
 * Destinazione: wp-content/mu-plugins/fmc-capi-purchase.php
 * Dataset: 1910886889606030 (FotoMoto.Click Web)
 *
 * ─── COSA FA E COSA NON FA ──────────────────────────────────────────────────
 * FA: manda Purchase dal server, dove il dato dell'ordine è autoritativo. Recupera
 *     gli acquisti persi per adblocker, ITP di Safari, connessioni interrotte o
 *     pagina di conferma mai caricata. Alza la qualità della corrispondenza,
 *     perché usa email e telefono reali invece dei soli cookie.
 *
 * NON FA: aggirare il consenso. Se l'utente ha rifiutato il marketing, l'evento
 *     NON parte. Inviare dati personali a Meta dal server richiede la stessa base
 *     giuridica del browser.
 *
 * ─── DEDUPLICA ──────────────────────────────────────────────────────────────
 * Browser e server mandano lo STESSO event_id: 'order_' . $order_id.
 * Meta scarta il duplicato e tiene il primo arrivato. Perché funzioni, lo snippet
 * del pixel deve passare eventID nella chiamata Purchase — vedi fondo file.
 *
 * ─── TOKEN ──────────────────────────────────────────────────────────────────
 * Il token NON sta qui. Va in wp-config.php, sopra la riga "That's all":
 *
 *     define( 'FMC_CAPI_TOKEN', 'il-token-generato-in-gestione-eventi' );
 *
 * Così non finisce nel database, non compare negli export e non passa da WPCode.
 * Si genera in: Gestione eventi → dataset FotoMoto.Click Web → Impostazioni →
 * Conversions API → "Genera token di accesso".
 */

defined( 'ABSPATH' ) || exit;

const FMC_CAPI_DATASET_ID = '1910886889606030';
const FMC_CAPI_API_VERSION = 'v21.0';

/**
 * 1) Alla creazione dell'ordine, congela sul record tutto ciò che dopo non sarà
 *    più disponibile: consenso, cookie di Meta, IP e user agent.
 *
 *    Serve perché `woocommerce_payment_complete` può scattare in un contesto
 *    senza i cookie del cliente — per esempio la notifica asincrona di PayPal.
 */
add_action(
	'woocommerce_checkout_create_order',
	function ( $order ) {
		// Complianz salva la scelta in cookie: 'allow' | 'deny'
		$consenso = isset( $_COOKIE['cmplz_marketing'] )
			? sanitize_text_field( wp_unslash( $_COOKIE['cmplz_marketing'] ) )
			: '';

		$order->update_meta_data( '_fmc_consenso_marketing', $consenso );
		$order->update_meta_data( '_fmc_fbp', isset( $_COOKIE['_fbp'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['_fbp'] ) ) : '' );
		$order->update_meta_data( '_fmc_fbc', isset( $_COOKIE['_fbc'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['_fbc'] ) ) : '' );
		$order->update_meta_data( '_fmc_ip', isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
		$order->update_meta_data( '_fmc_ua', isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '' );
	},
	10,
	1
);

/**
 * 2) A pagamento completato, invia Purchase.
 *
 *    Si aggancia sia a payment_complete sia ai passaggi di stato, perché non tutti
 *    i gateway percorrono la stessa strada. Il flag sull'ordine garantisce un solo
 *    invio.
 */
add_action( 'woocommerce_payment_complete', 'fmc_capi_invia_purchase', 20, 1 );
add_action( 'woocommerce_order_status_processing', 'fmc_capi_invia_purchase', 20, 1 );
add_action( 'woocommerce_order_status_completed', 'fmc_capi_invia_purchase', 20, 1 );

function fmc_capi_invia_purchase( $order_id ) {

	if ( ! defined( 'FMC_CAPI_TOKEN' ) || ! FMC_CAPI_TOKEN ) {
		return; // token non configurato: non fare nulla, silenziosamente
	}

	$order = wc_get_order( $order_id );
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	// Un solo invio per ordine.
	if ( $order->get_meta( '_fmc_capi_inviato' ) ) {
		return;
	}

	// Consenso: se non è 'allow', non si invia nulla. Nessuna eccezione.
	if ( 'allow' !== $order->get_meta( '_fmc_consenso_marketing' ) ) {
		$order->update_meta_data( '_fmc_capi_inviato', 'saltato: nessun consenso marketing' );
		$order->save();
		return;
	}

	$hash = static function ( $valore ) {
		$valore = trim( strtolower( (string) $valore ) );
		return '' === $valore ? null : hash( 'sha256', $valore );
	};

	// Telefono: solo cifre, con prefisso internazionale se assente.
	$telefono = preg_replace( '/\D+/', '', (string) $order->get_billing_phone() );
	if ( $telefono && 2 === strlen( (string) $order->get_billing_country() ) && '39' !== substr( $telefono, 0, 2 ) && 'IT' === $order->get_billing_country() ) {
		$telefono = '39' . ltrim( $telefono, '0' );
	}

	$user_data = array_filter(
		array(
			'em'          => array_filter( array( $hash( $order->get_billing_email() ) ) ),
			'ph'          => array_filter( array( $hash( $telefono ) ) ),
			'fn'          => array_filter( array( $hash( $order->get_billing_first_name() ) ) ),
			'ln'          => array_filter( array( $hash( $order->get_billing_last_name() ) ) ),
			'ct'          => array_filter( array( $hash( preg_replace( '/\s+/', '', (string) $order->get_billing_city() ) ) ) ),
			'zp'          => array_filter( array( $hash( $order->get_billing_postcode() ) ) ),
			'country'     => array_filter( array( $hash( $order->get_billing_country() ) ) ),
			'fbp'         => $order->get_meta( '_fmc_fbp' ) ?: null,
			'fbc'         => $order->get_meta( '_fmc_fbc' ) ?: null,
			'client_ip_address' => $order->get_meta( '_fmc_ip' ) ?: null,
			'client_user_agent' => $order->get_meta( '_fmc_ua' ) ?: null,
		),
		static function ( $v ) {
			return ! empty( $v );
		}
	);

	$content_ids = array();
	$contents    = array();

	foreach ( $order->get_items() as $item ) {
		$product_id = $item->get_product_id();
		if ( ! $product_id ) {
			continue;
		}
		$content_ids[] = (string) $product_id;
		$contents[]    = array(
			'id'         => (string) $product_id,
			'quantity'   => (int) $item->get_quantity(),
			'item_price' => (float) $order->get_item_subtotal( $item, false, false ),
		);
	}

	$payload = array(
		'data' => array(
			array(
				'event_name'       => 'Purchase',
				'event_time'       => $order->get_date_created() ? $order->get_date_created()->getTimestamp() : time(),
				'event_id'         => 'order_' . $order->get_id(),   // ← stesso id del browser
				'event_source_url' => $order->get_checkout_order_received_url(),
				'action_source'    => 'website',
				'user_data'        => $user_data,
				'custom_data'      => array(
					'currency'     => $order->get_currency(),
					'value'        => (float) $order->get_total(),
					'content_type' => 'product',
					'content_ids'  => array_values( array_unique( $content_ids ) ),
					'contents'     => $contents,
					'order_id'     => (string) $order->get_id(),
				),
			),
		),
	);

	$risposta = wp_remote_post(
		'https://graph.facebook.com/' . FMC_CAPI_API_VERSION . '/' . FMC_CAPI_DATASET_ID . '/events',
		array(
			'timeout' => 15,
			'body'    => array(
				'data'         => wp_json_encode( $payload['data'] ),
				'access_token' => FMC_CAPI_TOKEN,
			),
		)
	);

	if ( is_wp_error( $risposta ) ) {
		$order->add_order_note( 'CAPI Purchase — errore di rete: ' . $risposta->get_error_message() );
		return; // niente flag: al prossimo cambio di stato riprova
	}

	$codice = wp_remote_retrieve_response_code( $risposta );
	$corpo  = wp_remote_retrieve_body( $risposta );

	if ( 200 === $codice ) {
		$order->update_meta_data( '_fmc_capi_inviato', gmdate( 'c' ) );
		$order->add_order_note( 'CAPI Purchase inviato a Meta (event_id: order_' . $order->get_id() . ')' );
	} else {
		$order->add_order_note( 'CAPI Purchase — risposta ' . $codice . ': ' . substr( $corpo, 0, 300 ) );
	}

	$order->save();
}

/**
 * ─── DA FARE NELLO SNIPPET DEL PIXEL (obbligatorio per la deduplica) ────────
 *
 * In fm_meta_pixel_purchase(), la chiamata attuale:
 *
 *     fbq('track', 'Purchase', payload);
 *
 * diventa:
 *
 *     fbq('track', 'Purchase', payload, {eventID: 'order_<?php echo (int) $order->get_id(); ?>'});
 *
 * Senza questo, Meta conta ogni acquisto DUE volte: una dal browser e una dal
 * server. Con questo, riconosce che sono lo stesso evento e ne tiene uno solo.
 *
 * ─── VERIFICA ───────────────────────────────────────────────────────────────
 * 1. Fai un ordine di prova.
 * 2. Nelle note dell'ordine deve comparire "CAPI Purchase inviato a Meta".
 * 3. In Gestione eventi → dataset → Panoramica, l'evento Purchase deve mostrare
 *    origine "Sito web e server" e una percentuale di deduplica > 0.
 * 4. Se l'ordine arriva da un utente che ha RIFIUTATO i cookie, la nota deve dire
 *    "saltato: nessun consenso marketing" e a Meta non deve arrivare nulla.
 *
 * Il punto 4 è quello che non va saltato: è la differenza fra misurare meglio e
 * violare il GDPR.
 *
 * ─── RIMOZIONE ──────────────────────────────────────────────────────────────
 * Cancellare questo file e la costante FMC_CAPI_TOKEN da wp-config.php.
 */
