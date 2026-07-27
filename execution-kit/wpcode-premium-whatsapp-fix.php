<?php
/**
 * FotoMoto.click — Pulsante "Richiedi la Versione Premium": link WhatsApp
 *
 * Da usare in WPCode come PHP Snippet -> Auto Insert -> Run Everywhere.
 * Sostituisce lo script inline attualmente presente nel template prodotto,
 * che va RIMOSSO contestualmente (vedi nota in fondo).
 *
 * ─── IL PROBLEMA ────────────────────────────────────────────────────────────
 * Lo script inline originale muore con
 *     Uncaught SyntaxError: Invalid or unexpected token
 * perché le parentesi quadre sono state codificate come entità HTML:
 *
 *     const match = candidates&#091;0&#093;.match(/fotomotoclick-&#091;a-z0-9\-&#093;+/i);
 *                             ^^^^^^^ ^^^^^^^                ^^^^^^^      ^^^^^^^
 *
 * Dentro un <script> le entità non vengono decodificate: il parser si ferma lì
 * e tutto il codice successivo non viene eseguito — compreso l'assegnamento
 * dell'href. Il pulsante resta con `href="#"` e non porta da nessuna parte.
 *
 * La codifica avviene perché lo script sta in un campo di contenuto (widget HTML
 * Elementor o simile), dove un filtro di WordPress converte [ e ] per evitare
 * che vengano letti come shortcode. In uno snippet PHP il problema non esiste.
 *
 * ─── LA CORREZIONE ──────────────────────────────────────────────────────────
 * Il nome della foto lo conosce già PHP: non serve cercarlo nel DOM. Sparisce
 * l'uso di array e di classi di caratteri regex, quindi non c'è più nessuna
 * parentesi quadra da codificare. In più il riferimento è esatto invece che
 * dedotto dal testo della pagina.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'fm_premium_whatsapp_link', 60 );
function fm_premium_whatsapp_link() {

	if ( is_admin() || ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	// Lo slug è il riferimento univoco della foto: fotomotoclick-{localita}-{data}-{n}-{ora}
	$photo_name = $product->get_slug();

	if ( ! $photo_name ) {
		$photo_name = $product->get_name();
	}

	$message = 'Ciao! Vorrei richiedere la Versione Premium per questa foto: ' . $photo_name;
	$wa_url  = 'https://wa.me/393668070970?text=' . rawurlencode( $message );
	?>
	<!-- FotoMoto.click — link WhatsApp Versione Premium -->
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		var waButton = document.getElementById('fmPremiumWhatsappV2');
		if (!waButton) {
			return;
		}
		waButton.href = <?php echo wp_json_encode( $wa_url ); ?>;
	});
	</script>
	<?php
}

/**
 * ─── DA FARE INSIEME AL DEPLOY ──────────────────────────────────────────────
 * Rimuovere il vecchio script inline dal template prodotto (widget HTML di
 * Elementor, quello che inizia con:
 *     document.addEventListener('DOMContentLoaded', function(){
 *       const waButton = document.getElementById('fmPremiumWhatsappV2');
 * ).
 * Lasciarlo non rompe nulla — muore comunque all'errore di sintassi — ma
 * continua a sporcare la console e a confondere chi legge il codice.
 *
 * ─── VERIFICA ───────────────────────────────────────────────────────────────
 * 1. Aprire una pagina prodotto e controllare che la console non riporti più
 *    "Uncaught SyntaxError: Invalid or unexpected token".
 * 2. In console:  document.getElementById('fmPremiumWhatsappV2').href
 *    Deve restituire un URL wa.me con il nome della foto, non "#".
 * 3. Cliccare il pulsante: deve aprire WhatsApp con il messaggio precompilato.
 * 4. Ripetere su una seconda foto e verificare che il nome cambi.
 *
 * ─── NOTA ───────────────────────────────────────────────────────────────────
 * Il pulsante non emette alcun evento di tracciamento. Quando il pixel e GA4
 * saranno verificati, vale la pena aggiungere un evento sul click: è una
 * richiesta di upsell da 15 EUR e oggi non lascia traccia da nessuna parte.
 */
