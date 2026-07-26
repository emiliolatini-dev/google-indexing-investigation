<?php
/**
 * FotoMoto.Click — Modulo "Scopri il passo" per le landing località /foto/{loc}/
 * ---------------------------------------------------------------------------
 * COME USARLO (sicuro):
 *  1. WordPress → WPCode → Add Snippet → "Add Your Custom Code (New Snippet)"
 *     → tipo: PHP Snippet. Incolla TUTTO questo file (senza il tag <?php se WPCode
 *     lo aggiunge da sé — di norma va incollato il contenuto dopo <?php).
 *  2. Insertion: "Auto Insert" NON necessario — usa uno SHORTCODE. Location:
 *     lascia "Run Everywhere" (registra solo lo shortcode, non stampa nulla da solo).
 *  3. Salva e ATTIVA. Finché non inserisci lo shortcode non cambia niente sul sito.
 *  4. In Elementor, nel template della landing località, aggiungi un widget
 *     "Shortcode" dove vuoi il blocco (consigliato: sotto ricerca/sessioni) con:
 *        [scopri_il_passo]
 *     Su una pagina di prova puoi forzare la località:  [scopri_il_passo loc="bocca-serriola"]
 *  5. Testa PRIMA su staging locale (local-staging-assets) o su una sola località.
 *
 * Rileva la località da get_queried_object() (termine product_cat) o dall'URL /foto/{slug}/.
 * Se non trova dati per quella località, non stampa nulla (nessun errore).
 * Dati: portati da content/passi-data.json. Coordinate/profili [DA VERIFICARE] dove indicato.
 */

if ( ! function_exists( 'fmc_passi_data' ) ) {
	function fmc_passi_data() {
		return array(

			'bocca-serriola' => array(
				'title' => 'Bocca Serriola, in sella',
				'lead'  => 'Un valico dell’Appennino umbro-marchigiano diventato un punto d’incontro per chi gira in moto nel weekend. Ecco la strada, i due versanti e cosa aspettarti.',
				'facts' => array( '<b>730 m</b> <span>quota</span>', '<b>SS257</b> <span>Apecchiese</span>', '<b>Umbria ↔ Marche</b>', '<b>2 versanti</b> <span>15,9 / 7,6 km</span>' ),
				'blocks' => array(
					array( 'La strada, versante per versante', '<p>Il fascino di Bocca Serriola non sta nell’altitudine — è un passo “basso” — ma nei due avvicinamenti. Il tratto che sale da Città di Castello è considerato un vero paradiso per i motociclisti.</p><div class="fmc-cols"><div class="fmc-vc"><span class="fmc-tag">Umbria</span> <span class="fmc-tag">più lungo</span><h4>Da Città di Castello</h4><p class="fmc-big">15,9 km · +423 m</p><p class="fmc-sm">Media 2,7%, max ~6,4%, 3 tornanti. Dolce e scorrevole.</p></div><div class="fmc-vc"><span class="fmc-tag">Marche</span> <span class="fmc-tag">più ripido</span><h4>Da Apecchio</h4><p class="fmc-big">7,6 km · +247 m</p><p class="fmc-sm">Media 3,3%, max 7,9%, 1 tornante. Corto e diretto tra i boschi.</p></div></div><p><b>Fondo e sicurezza.</b> Statale ben tenuta; nei weekend il traffico moto è intenso. Strada aperta: guida con margine.</p>' ),
					array( 'Il valico in breve', '<p>Più che per la quota, Bocca Serriola conta per dove sta: la soglia tra la Val Tiberina e la valle del Biscubio. I tifernati lo chiamano «La Cima»; il nome deriverebbe dal latino <em>serrula</em>, “piccola sega”, per il legname che di qui scendeva a valle.</p><table class="fmc-tab"><tr><th>Quota</th><td>730 m s.l.m.</td></tr><tr><th>Strada</th><td>SS 257 Apecchiese</td></tr><tr><th>Regioni / Province</th><td>Umbria (Perugia) ↔ Marche (Pesaro e Urbino)</td></tr><tr><th>Comuni</th><td>Città di Castello (PG) · Apecchio (PU)</td></tr></table>' ),
				),
				'faq' => array(
					array( 'C’è un fotografo che fotografa le moto a Bocca Serriola?', 'Sì. FotoMoto.Click è sul valico nei weekend di stagione; le foto sono online entro 24 ore, con luogo, data e ora, in alta risoluzione senza watermark.' ),
					array( 'Quanto è alto il valico di Bocca Serriola?', '730 metri, sulla SS257 Apecchiese, tra Città di Castello (Umbria) e Apecchio (Marche).' ),
					array( 'Qual è il versante più bello per la moto?', 'Da Città di Castello è più lungo e scorrevole (15,9 km, 3 tornanti); da Apecchio più corto e ripido (7,6 km, max 7,9%).' ),
				),
				'schema' => array( 'types' => array( 'Mountain', 'TouristAttraction', 'Place' ), 'name' => 'Valico di Bocca Serriola', 'alt' => 'La Cima', 'desc' => 'Valico appenninico a 730 m tra Umbria e Marche, sulla SS257 Apecchiese.', 'elev' => '730', 'lat' => 43.5333, 'lon' => 12.4200, 'sameAs' => 'https://it.wikipedia.org/wiki/Bocca_Serriola' ),
			),

			'terminillo' => array(
				'title' => 'Terminillo, la montagna di Roma',
				'lead'  => 'Non un valico ma un massiccio dei Monti Reatini e comprensorio sciistico, tra le mete più cercate dell’Appennino centrale. La salita da Rieti è un classico: curve ampie, asfalto eccellente.',
				'facts' => array( '<b>2.217 m</b> <span>vetta</span>', '<b>~1.620 m</b> <span>Pian de’ Valli</span>', '<b>SS4bis</b> <span>Via del Terminillo</span>', '<b>Lazio</b> <span>Rieti</span>' ),
				'blocks' => array(
					array( 'La strada', '<p>Da <b>Rieti</b> si sale con la SS4bis “Via del Terminillo”: curve ampie e ben collegate fino a Pian de’ Valli, asfalto ottimo. Da lì verso la <b>Sella di Leonessa</b> e giù su Leonessa. I tratti alti sono più stretti e ombreggiati: prudenza.</p><p><b>Attenzione stagionale:</b> il tratto Terminillo–Leonessa può chiudere per neve — verifica prima di salire.</p>' ),
					array( 'Il monte in breve', '<table class="fmc-tab"><tr><th>Vetta</th><td>2.217 m s.l.m.</td></tr><tr><th>Base sciistica</th><td>Pian de’ Valli ~1.620 m</td></tr><tr><th>Strada</th><td>SS 4bis (da Rieti)</td></tr><tr><th>Regione / Provincia</th><td>Lazio (Rieti)</td></tr><tr><th>Catena</th><td>Monti Reatini</td></tr></table>' ),
				),
				'faq' => array(
					array( 'C’è un fotografo che fotografa le moto al Terminillo?', 'Sì. FotoMoto.Click è sulla montagna nei weekend di stagione; le foto sono online entro 24 ore, con luogo, data e ora.' ),
					array( 'Quanto è alto il Terminillo?', 'La vetta è a 2.217 m; la base sciistica di Pian de’ Valli è intorno ai 1.620 m.' ),
				),
				'schema' => array( 'types' => array( 'Mountain', 'TouristAttraction' ), 'name' => 'Monte Terminillo', 'alt' => '', 'desc' => 'Massiccio dei Monti Reatini e comprensorio sciistico nel Lazio, vetta 2.217 m.', 'elev' => '2217', 'lat' => 42.4667, 'lon' => 12.9833, 'sameAs' => 'https://it.wikipedia.org/wiki/Monte_Terminillo' ),
			),

			'viamaggio' => array(
				'title' => 'Passo di Viamaggio, tra due valli',
				'lead'  => 'Il valico sulla Marecchiese che collega Sansepolcro a Rimini, al confine tra Valtiberina e Valmarecchia. Strada storica e una delle più amate — e frequentate — dai motociclisti nei weekend.',
				'facts' => array( '<b>983 m</b> <span>quota</span>', '<b>SS258</b> <span>Marecchiese</span>', '<b>Toscana</b> <span>Arezzo</span>', '<b>Valtiberina ↔ Valmarecchia</b>' ),
				'blocks' => array(
					array( 'La strada', '<p>La <b>SS258 Marecchiese</b> sale al passo al confine tra <b>Pieve Santo Stefano</b> e <b>Badia Tedalda</b>, collegando la Valtiberina toscana alla Valmarecchia verso Rimini. Direttrice storica e scorrevole: nei weekend è trafficatissima di moto — attenzione ai gruppi.</p>' ),
					array( 'Il valico in breve', '<p>Fa da spartiacque tra Valtiberina e Valmarecchia (torrente Sinigiola). In epoca romana di qui passava l’<em>Iter Tiberinum</em>.</p><table class="fmc-tab"><tr><th>Quota</th><td>983 m s.l.m.</td></tr><tr><th>Strada</th><td>SS 258 Marecchiese</td></tr><tr><th>Regione / Provincia</th><td>Toscana (Arezzo)</td></tr><tr><th>Comuni</th><td>Pieve Santo Stefano · Badia Tedalda</td></tr></table>' ),
				),
				'faq' => array(
					array( 'C’è un fotografo a Viamaggio?', 'Sì. FotoMoto.Click fotografa i motociclisti sul passo nei weekend di stagione; foto online entro 24 ore.' ),
					array( 'Quanto è alto il Passo di Viamaggio?', '983 metri, sulla SS258 Marecchiese, tra Valtiberina e Valmarecchia.' ),
				),
				'schema' => array( 'types' => array( 'Place', 'TouristAttraction' ), 'name' => 'Passo di Viamaggio', 'alt' => 'Valico di Viamaggio', 'desc' => 'Valico a 983 m sulla SS258 Marecchiese, tra Valtiberina e Valmarecchia, in Toscana.', 'elev' => '983', 'lat' => 43.7100, 'lon' => 12.1600, 'sameAs' => 'https://it.wikipedia.org/wiki/Passo_di_Viamaggio' ),
			),

			'spino' => array(
				'title' => 'Passo dello Spino, la provinciale che si crede una pista',
				'lead'  => 'Tracciato tecnico e panoramico, sede storica di cronoscalata, tra il Casentino e la Valtiberina, a due passi dal santuario della Verna.',
				'facts' => array( '<b>1.054 m</b> <span>quota</span>', '<b>SP208</b>', '<b>Toscana</b> <span>Arezzo</span>', '<b>Casentino ↔ Valtiberina</b>' ),
				'blocks' => array(
					array( 'La strada', '<p>La <b>SP208</b> collega <b>Chiusi della Verna</b> a <b>Pieve Santo Stefano</b>, valicando lo Spino tra Casentino e Valtiberina. Sede storica di cronoscalata, è tecnica e divertente — ma resta una provinciale aperta. Dal passo si vedono l’Alpe della Luna, l’Alpe di Catenaia e il Monte Penna.</p><p class="fmc-note">Il cartello indica 1.005 m; la quota reale è 1.054 m.</p>' ),
					array( 'Il valico in breve', '<table class="fmc-tab"><tr><th>Quota</th><td>1.054 m s.l.m. (cartello 1.005 m)</td></tr><tr><th>Strada</th><td>SP 208</td></tr><tr><th>Regione / Provincia</th><td>Toscana (Arezzo)</td></tr><tr><th>Comuni</th><td>Chiusi della Verna · Pieve Santo Stefano</td></tr></table>' ),
				),
				'faq' => array(
					array( 'C’è un fotografo al Passo dello Spino?', 'Sì. FotoMoto.Click è sul passo nei weekend di stagione; foto online entro 24 ore con luogo, data e ora.' ),
					array( 'Quanto è alto il Passo dello Spino?', '1.054 metri (il cartello riporta 1.005 m), sulla SP208 tra Casentino e Valtiberina.' ),
				),
				'schema' => array( 'types' => array( 'Place', 'TouristAttraction' ), 'name' => 'Passo dello Spino', 'alt' => 'Valico dello Spino', 'desc' => 'Valico a 1.054 m sulla SP208, tra Casentino e Valtiberina, in Toscana.', 'elev' => '1054', 'lat' => 43.6800, 'lon' => 11.9400, 'sameAs' => '' ),
			),

			'capannelle' => array(
				'title' => 'Passo delle Capannelle, nel cuore del Gran Sasso',
				'lead'  => 'Definito da più testate “la strada più bella d’Italia”: un valico a 1.300 m sulla SS80 del Gran Sasso, dentro il Parco Nazionale, tra la conca aquilana e il Teramano.',
				'facts' => array( '<b>1.300 m</b> <span>quota</span>', '<b>SS80</b> <span>del Gran Sasso</span>', '<b>Abruzzo</b> <span>L’Aquila ↔ Teramo</span>', '<b>Parco Gran Sasso-Laga</b>' ),
				'blocks' => array(
					array( 'La strada', '<p>La <b>SS80 del Gran Sasso d’Italia</b> valica le Capannelle collegando <b>L’Aquila</b> a <b>Teramo</b>, attraverso il Parco Nazionale. Pendenze morbide — circa 5% sul versante aquilano e 4% su quello teramano — e panorami sul massiccio e sui Monti della Laga. Quota 1.300 m: possibili neve e chiusure in inverno.</p>' ),
					array( 'Il valico in breve', '<p>Il valico era percorso dalle greggi durante la transumanza, lungo l’antica via Cecilia.</p><table class="fmc-tab"><tr><th>Quota</th><td>1.300 m s.l.m.</td></tr><tr><th>Strada</th><td>SS 80 del Gran Sasso d’Italia</td></tr><tr><th>Regione / Province</th><td>Abruzzo (L’Aquila ↔ Teramo)</td></tr><tr><th>Area protetta</th><td>Parco Nazionale del Gran Sasso e Monti della Laga</td></tr></table>' ),
				),
				'faq' => array(
					array( 'C’è un fotografo al Passo delle Capannelle?', 'Sì. FotoMoto.Click fotografa i motociclisti sul valico nei weekend di stagione; foto online entro 24 ore.' ),
					array( 'Quanto è alto il Passo delle Capannelle?', '1.300 metri, sulla SS80 del Gran Sasso, tra L’Aquila e Teramo.' ),
				),
				'schema' => array( 'types' => array( 'Place', 'TouristAttraction' ), 'name' => 'Passo delle Capannelle', 'alt' => 'Valico delle Capannelle', 'desc' => 'Valico a 1.300 m sulla SS80 del Gran Sasso, in Abruzzo, dentro il Parco Nazionale.', 'elev' => '1300', 'lat' => 42.5000, 'lon' => 13.3500, 'sameAs' => '' ),
			),

		);
	}
}

if ( ! function_exists( 'fmc_scopri_il_passo_shortcode' ) ) {
	function fmc_scopri_il_passo_shortcode( $atts ) {
		$atts = shortcode_atts( array( 'loc' => '' ), $atts, 'scopri_il_passo' );
		$slug = sanitize_title( $atts['loc'] );

		if ( ! $slug ) {
			$obj = get_queried_object();
			if ( $obj instanceof WP_Term ) {
				$slug = $obj->slug;
			}
		}
		if ( ! $slug ) { // fallback: dall'URL /foto/{slug}/
			$path  = trim( wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?? '', '/' );
			$parts = explode( '/', $path );
			if ( isset( $parts[0] ) && 'foto' === $parts[0] && ! empty( $parts[1] ) ) {
				$slug = sanitize_title( $parts[1] );
			}
		}

		$data = fmc_passi_data();
		if ( ! $slug || ! isset( $data[ $slug ] ) ) {
			return ''; // nessun dato per questa località → non stampa nulla
		}
		$p = $data[ $slug ];

		// ---- CSS (una volta per pagina) ----
		static $css_done = false;
		$out = '';
		if ( ! $css_done ) {
			$css_done = true;
			$out .= '<style>
.fmc-scopri{--l:rgba(0,0,0,.08);--a:#ff5a1f;max-width:1180px;margin:40px auto;padding:0 4px;font-family:inherit;color:inherit}
.fmc-scopri .fmc-eye{display:inline-block;text-transform:uppercase;letter-spacing:.16em;font-size:.72rem;font-weight:700;color:var(--a);margin-bottom:6px}
.fmc-scopri h3{font-size:1.9rem;font-weight:800;letter-spacing:-.02em;margin:.1em 0 .2em}
.fmc-scopri .fmc-lead{font-size:1.08rem;opacity:.9;max-width:70ch;margin:0 0 16px}
.fmc-scopri .fmc-facts{display:flex;flex-wrap:wrap;gap:10px;margin:0 0 8px}
.fmc-scopri .fmc-fact{border:1px solid var(--l);border-radius:999px;padding:8px 16px;font-size:.9rem;opacity:.95}
.fmc-scopri details{border:1px solid var(--l);border-radius:14px;margin:12px 0;overflow:hidden}
.fmc-scopri summary{cursor:pointer;list-style:none;padding:16px 20px;font-weight:700;font-size:1.06rem;display:flex;justify-content:space-between;align-items:center;gap:12px}
.fmc-scopri summary::-webkit-details-marker{display:none}
.fmc-scopri summary .fmc-chev{color:var(--a);font-size:1.4rem;line-height:1;transition:transform .2s}
.fmc-scopri details[open] summary .fmc-chev{transform:rotate(45deg)}
.fmc-scopri .fmc-body{padding:0 20px 18px}
.fmc-scopri .fmc-cols{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin:8px 0}
.fmc-scopri .fmc-vc{border:1px solid var(--l);border-radius:12px;padding:14px}
.fmc-scopri .fmc-vc h4{margin:.2em 0}
.fmc-scopri .fmc-big{font-size:1.25rem;font-weight:800;margin:.1em 0}
.fmc-scopri .fmc-sm{font-size:.9rem;opacity:.8;margin:0}
.fmc-scopri .fmc-tag{display:inline-block;background:rgba(255,90,31,.14);color:#c8460f;font-size:.66rem;font-weight:700;padding:3px 9px;border-radius:999px;margin-right:4px;text-transform:uppercase}
.fmc-scopri .fmc-tab{width:100%;border-collapse:collapse}
.fmc-scopri .fmc-tab th,.fmc-scopri .fmc-tab td{text-align:left;padding:9px 4px;border-bottom:1px solid var(--l);vertical-align:top;font-size:.95rem}
.fmc-scopri .fmc-tab th{opacity:.65;font-weight:600;width:42%}
.fmc-scopri .fmc-qa{border-top:1px solid var(--l);padding:14px 0}.fmc-scopri .fmc-qa:first-child{border-top:0}
.fmc-scopri .fmc-qa b{display:block;margin-bottom:4px}
.fmc-scopri .fmc-note{font-size:.85rem;opacity:.7}
@media(max-width:760px){.fmc-scopri .fmc-cols{grid-template-columns:1fr}.fmc-scopri h3{font-size:1.5rem}}
</style>';
		}

		// ---- HTML ----
		$out .= '<section class="fmc-scopri" id="scopri-il-passo">';
		$out .= '<span class="fmc-eye">Scopri il passo</span>';
		$out .= '<h3>' . esc_html( $p['title'] ) . '</h3>';
		$out .= '<p class="fmc-lead">' . esc_html( $p['lead'] ) . '</p>';

		$out .= '<div class="fmc-facts">';
		foreach ( $p['facts'] as $f ) {
			$out .= '<div class="fmc-fact">' . wp_kses_post( $f ) . '</div>';
		}
		$out .= '</div>';

		$first = true;
		foreach ( $p['blocks'] as $b ) {
			$open  = $first ? ' open' : '';
			$first = false;
			$out  .= '<details' . $open . '><summary>' . esc_html( $b[0] ) . ' <span class="fmc-chev">＋</span></summary><div class="fmc-body">' . wp_kses_post( $b[1] ) . '</div></details>';
		}

		// blocco FAQ
		if ( ! empty( $p['faq'] ) ) {
			$out .= '<details><summary>Domande frequenti <span class="fmc-chev">＋</span></summary><div class="fmc-body">';
			foreach ( $p['faq'] as $qa ) {
				$out .= '<div class="fmc-qa"><b>' . esc_html( $qa[0] ) . '</b>' . esc_html( $qa[1] ) . '</div>';
			}
			$out .= '</div></details>';
		}

		$out .= '</section>';

		// ---- Schema JSON-LD ----
		$s     = $p['schema'];
		$graph = array();
		$place = array(
			'@type'       => count( $s['types'] ) === 1 ? $s['types'][0] : $s['types'],
			'name'        => $s['name'],
			'description' => $s['desc'],
			'elevation'   => $s['elev'],
			'geo'         => array( '@type' => 'GeoCoordinates', 'latitude' => $s['lat'], 'longitude' => $s['lon'] ),
		);
		if ( ! empty( $s['alt'] ) )    { $place['alternateName'] = $s['alt']; }
		if ( ! empty( $s['sameAs'] ) ) { $place['sameAs'] = $s['sameAs']; }
		$graph[] = $place;

		if ( ! empty( $p['faq'] ) ) {
			$main = array();
			foreach ( $p['faq'] as $qa ) {
				$main[] = array( '@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $qa[1] ) );
			}
			$graph[] = array( '@type' => 'FAQPage', 'mainEntity' => $main );
		}

		$json = wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		$out .= '<script type="application/ld+json">' . $json . '</script>';

		return $out;
	}
	add_shortcode( 'scopri_il_passo', 'fmc_scopri_il_passo_shortcode' );
}
