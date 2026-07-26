<?php
/**
 * Plugin Name: FotoMoto Location Landing
 * Description: Landing SEO dinamica per località FotoMoto.Click
 * Version: 2.1.1
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Media Summary della landing località — stesso design contract di
 * fm_format_gallery_stats_line() (62358-galleria-fast-custom.php), finder e
 * homepage: icone SVG inline con stroke=currentColor, numero foto sempre,
 * segmento video solo quando presente. Duplicazione controllata del contract:
 * questa pagina non può accodare gli helper del WPCode della galleria.
 *
 * I conteggi vengono dagli slot di time-ranges-index.json: `count` è il
 * totale storico (foto+video), `video_count` è additivo — slot scritti prima
 * dell'introduzione del campo equivalgono a 0 video, quindi una sessione
 * vecchia mostra esattamente ciò che mostrava prima.
 */
function fml_slots_media_counts($slots) {
    $total = 0;
    $videos = 0;

    foreach ((array) $slots as $slot) {
        if (!is_array($slot)) {
            continue;
        }

        $total += max(0, intval($slot['count'] ?? 0));
        $videos += max(0, intval($slot['video_count'] ?? 0));
    }

    $videos = min($videos, $total);

    return [
        'total' => $total,
        'videos' => $videos,
        'photos' => $total - $videos,
    ];
}

function fml_media_summary_html($photos, $videos) {
    $camera = '<svg class="fml-mi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>';
    $video = '<svg class="fml-mi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>';

    $html = $camera . ' ' . esc_html(number_format_i18n(max(0, intval($photos))));

    if (intval($videos) > 0) {
        $html .= '<span class="fml-mi-sep">•</span>' . $video . ' ' . esc_html(number_format_i18n(intval($videos)));
    }

    return $html;
}

add_action('init', function () {
    add_rewrite_rule('^foto/([^/]+)/?$', 'index.php?fm_location=$matches[1]', 'top');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'fm_location';
    return $vars;
});

add_action('template_redirect', function () {
    $location = get_query_var('fm_location');

    if (!$location) {
        return;
    }

    $location = sanitize_title($location);
    $uploads = wp_upload_dir();

    $dates_path = trailingslashit($uploads['basedir']) . 'dates-index.json';
    $ranges_path = trailingslashit($uploads['basedir']) . 'time-ranges-index.json';

    if (!file_exists($dates_path) || !file_exists($ranges_path)) {
        status_header(404);
        nocache_headers();
        echo 'Galleria non disponibile.';
        exit;
    }

    $dates = json_decode(file_get_contents($dates_path), true);
    $ranges = json_decode(file_get_contents($ranges_path), true);

    if (!is_array($dates) || !is_array($ranges) || empty($dates[$location])) {
        status_header(404);
        nocache_headers();
        echo 'Località non trovata.';
        exit;
    }

    $location_label = ucwords(str_replace('-', ' ', $location));

    // === "Scopri il passo" — data layer nativo (fatti verificati 26-07-2026; fonti nel repo passi-data.json).
    //     Per aggiungere una localita': duplica un blocco e usa lo slug esatto di /foto/{slug}/ come chiave.
    $passi_info = [
        'bocca-serriola' => [
            'title' => 'Bocca Serriola, in sella',
            'lead'  => 'Un valico dell’Appennino umbro-marchigiano diventato un punto d’incontro per chi gira in moto nel weekend: la strada, i due versanti e cosa aspettarti.',
            'facts' => [['730 m','quota'],['SS257','Apecchiese'],['Umbria ↔ Marche',''],['2 versanti','15,9 / 7,6 km']],
            'versanti' => [
                ['Da Città di Castello','Umbria · più lungo','15,9 km · +423 m','Media 2,7%, max ~6,4%, 3 tornanti. Dolce e scorrevole.'],
                ['Da Apecchio','Marche · più ripido','7,6 km · +247 m','Media 3,3%, max 7,9%, 1 tornante. Corto e diretto tra i boschi.'],
            ],
            'body'  => 'Più che per la quota, Bocca Serriola conta per dove sta: la soglia tra la Val Tiberina e la valle del Biscubio. I tifernati lo chiamano «La Cima»; il nome deriverebbe dal latino <em>serrula</em>, “piccola sega”, per il legname che di qui scendeva a valle.',
            'table' => ['Quota'=>'730 m s.l.m.','Strada'=>'SS 257 Apecchiese','Regioni / Province'=>'Umbria (Perugia) ↔ Marche (Pesaro e Urbino)','Comuni'=>'Città di Castello (PG) · Apecchio (PU)'],
            'schema'=> ['types'=>['Mountain','TouristAttraction','Place'],'name'=>'Valico di Bocca Serriola','alt'=>'La Cima','desc'=>'Valico appenninico a 730 m tra Umbria e Marche, sulla SS257 Apecchiese.','elev'=>'730','lat'=>43.5333,'lon'=>12.4200,'sameAs'=>'https://it.wikipedia.org/wiki/Bocca_Serriola'],
        ],
        'terminillo' => [
            'title' => 'Terminillo, la montagna di Roma',
            'lead'  => 'Non un valico ma un massiccio dei Monti Reatini e comprensorio sciistico, tra le mete più cercate dell’Appennino centrale. La salita da Rieti è un classico: curve ampie e asfalto eccellente.',
            'facts' => [['2.217 m','vetta'],['~1.620 m','Pian de’ Valli'],['SS4bis','Via del Terminillo'],['Lazio','Rieti']],
            'versanti' => [],
            'body'  => 'Da Rieti si sale con la SS4bis “Via del Terminillo”: curve ampie e ben collegate fino a Pian de’ Valli, poi verso la Sella di Leonessa. I tratti alti sono più stretti e ombreggiati. Il tratto Terminillo–Leonessa può chiudere per neve nei mesi freddi.',
            'table' => ['Vetta'=>'2.217 m s.l.m.','Base sciistica'=>'Pian de’ Valli ~1.620 m','Strada'=>'SS 4bis (da Rieti)','Regione / Provincia'=>'Lazio (Rieti)','Catena'=>'Monti Reatini'],
            'schema'=> ['types'=>['Mountain','TouristAttraction'],'name'=>'Monte Terminillo','alt'=>'','desc'=>'Massiccio dei Monti Reatini e comprensorio sciistico nel Lazio, vetta 2.217 m.','elev'=>'2217','lat'=>42.4667,'lon'=>12.9833,'sameAs'=>'https://it.wikipedia.org/wiki/Monte_Terminillo'],
        ],
        'viamaggio' => [
            'title' => 'Passo di Viamaggio, tra due valli',
            'lead'  => 'Il valico sulla Marecchiese che collega Sansepolcro a Rimini, al confine tra Valtiberina e Valmarecchia. Strada storica e una delle più amate — e frequentate — dai motociclisti nei weekend.',
            'facts' => [['983 m','quota'],['SS258','Marecchiese'],['Toscana','Arezzo'],['Valtiberina ↔ Valmarecchia','']],
            'versanti' => [],
            'body'  => 'La SS258 Marecchiese valica al confine tra Pieve Santo Stefano e Badia Tedalda, collegando la Valtiberina toscana alla Valmarecchia verso Rimini. Direttrice storica (l’antica <em>Iter Tiberinum</em>) e scorrevole: nei weekend è trafficatissima di moto.',
            'table' => ['Quota'=>'983 m s.l.m.','Strada'=>'SS 258 Marecchiese','Regione / Provincia'=>'Toscana (Arezzo)','Comuni'=>'Pieve Santo Stefano · Badia Tedalda'],
            'schema'=> ['types'=>['Place','TouristAttraction'],'name'=>'Passo di Viamaggio','alt'=>'Valico di Viamaggio','desc'=>'Valico a 983 m sulla SS258 Marecchiese, tra Valtiberina e Valmarecchia, in Toscana.','elev'=>'983','lat'=>43.7100,'lon'=>12.1600,'sameAs'=>'https://it.wikipedia.org/wiki/Passo_di_Viamaggio'],
        ],
        'spino' => [
            'title' => 'Passo dello Spino, la provinciale che si crede una pista',
            'lead'  => 'Tracciato tecnico e panoramico, sede storica di cronoscalata, tra il Casentino e la Valtiberina, a due passi dal santuario della Verna.',
            'facts' => [['1.054 m','quota'],['SP208',''],['Toscana','Arezzo'],['Casentino ↔ Valtiberina','']],
            'versanti' => [],
            'body'  => 'La SP208 collega Chiusi della Verna a Pieve Santo Stefano, valicando lo Spino tra Casentino e Valtiberina. Sede storica di cronoscalata, tecnica e divertente, ma resta una provinciale aperta. Il cartello indica 1.005 m, la quota reale è 1.054 m.',
            'table' => ['Quota'=>'1.054 m s.l.m. (cartello 1.005 m)','Strada'=>'SP 208','Regione / Provincia'=>'Toscana (Arezzo)','Comuni'=>'Chiusi della Verna · Pieve Santo Stefano'],
            'schema'=> ['types'=>['Place','TouristAttraction'],'name'=>'Passo dello Spino','alt'=>'Valico dello Spino','desc'=>'Valico a 1.054 m sulla SP208, tra Casentino e Valtiberina, in Toscana.','elev'=>'1054','lat'=>43.6800,'lon'=>11.9400,'sameAs'=>''],
        ],
        'capannelle' => [
            'title' => 'Passo delle Capannelle, nel cuore del Gran Sasso',
            'lead'  => 'Definito da più testate “la strada più bella d’Italia”: un valico a 1.300 m sulla SS80 del Gran Sasso, dentro il Parco Nazionale, tra la conca aquilana e il Teramano.',
            'facts' => [['1.300 m','quota'],['SS80','del Gran Sasso'],['Abruzzo','L’Aquila ↔ Teramo'],['Parco Gran Sasso-Laga','']],
            'versanti' => [],
            'body'  => 'La SS80 del Gran Sasso valica le Capannelle collegando L’Aquila a Teramo, attraverso il Parco Nazionale. Pendenze morbide — circa 5% sul versante aquilano e 4% su quello teramano — e panorami sui Monti della Laga. Antica via della transumanza.',
            'table' => ['Quota'=>'1.300 m s.l.m.','Strada'=>'SS 80 del Gran Sasso d’Italia','Regione / Province'=>'Abruzzo (L’Aquila ↔ Teramo)','Area protetta'=>'Parco Nazionale del Gran Sasso e Monti della Laga'],
            'schema'=> ['types'=>['Place','TouristAttraction'],'name'=>'Passo delle Capannelle','alt'=>'Valico delle Capannelle','desc'=>'Valico a 1.300 m sulla SS80 del Gran Sasso, in Abruzzo, dentro il Parco Nazionale.','elev'=>'1300','lat'=>42.5000,'lon'=>13.3500,'sameAs'=>''],
        ],
    ];
    $passo = $passi_info[$location] ?? null;

    $allowed_ext = ['webp', 'avif', 'jpg', 'jpeg', 'png'];

    $get_images = function ($dir, $url) use ($allowed_ext) {
        if (!is_dir($dir)) {
            return [];
        }

        $images = [];

        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed_ext, true)) {
                $images[] = esc_url($url . $file);
            }
        }

        sort($images);

        return $images;
    };

    $hero_desktop = $get_images(
        trailingslashit($uploads['basedir']) . 'fm-hero/' . $location . '/desktop/',
        trailingslashit($uploads['baseurl']) . 'fm-hero/' . $location . '/desktop/'
    );

    $hero_mobile = $get_images(
        trailingslashit($uploads['basedir']) . 'fm-hero/' . $location . '/mobile/',
        trailingslashit($uploads['baseurl']) . 'fm-hero/' . $location . '/mobile/'
    );

    if (empty($hero_desktop)) {
        $hero_desktop = $get_images(
            trailingslashit($uploads['basedir']) . 'fm-hero/desktop/',
            trailingslashit($uploads['baseurl']) . 'fm-hero/desktop/'
        );
    }

    if (empty($hero_mobile)) {
        $hero_mobile = $get_images(
            trailingslashit($uploads['basedir']) . 'fm-hero/mobile/',
            trailingslashit($uploads['baseurl']) . 'fm-hero/mobile/'
        );
    }

    if (empty($hero_mobile)) {
        $hero_mobile = $hero_desktop;
    }

    shuffle($hero_desktop);
    shuffle($hero_mobile);

    $hero_desktop = array_slice(array_values($hero_desktop), 0, 5);
    $hero_mobile = array_slice(array_values($hero_mobile), 0, 5);

    $sessions = [];

$parent_term = get_term_by('slug', $location, 'product_cat');

foreach ($dates[$location] as $item) {
        $iso = $item['iso_date'] ?? '';
        $date = $item['date'] ?? '';
        $slots = $ranges[$location][$iso] ?? [];
        $counts = fml_slots_media_counts($slots);
        $count = $counts['total'];

        $session_url = home_url('/foto/' . $location . '/' . $date . '/');

if ($parent_term && !is_wp_error($parent_term)) {
    $child_terms = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'parent' => (int) $parent_term->term_id,
        'name' => $date,
        'number' => 1,
    ]);

    if (!empty($child_terms) && !is_wp_error($child_terms)) {
        $term_link = get_term_link($child_terms[0], 'product_cat');

        if (!is_wp_error($term_link)) {
            $session_url = $term_link;
        }
    }
}

$sessions[] = [
    'date' => $date,
    'iso' => $iso,
    'count' => $count,
    'photos' => $counts['photos'],
    'videos' => $counts['videos'],
    'slots' => $slots,
    'url' => $session_url,
];
    }

    usort($sessions, function ($a, $b) {
        $ta = !empty($a['iso']) ? strtotime($a['iso']) : 0;
        $tb = !empty($b['iso']) ? strtotime($b['iso']) : 0;

        return $tb <=> $ta;
    });

    $latest = array_slice($sessions, 0, 4);
    $total_sessions = count($sessions);
    $total_photos = array_sum(array_column($sessions, 'photos'));
    $total_videos = array_sum(array_column($sessions, 'videos'));

    $other_locations = [];

    foreach ($dates as $other_slug => $other_items) {
        $other_slug = sanitize_title($other_slug);

        if ($other_slug === $location || empty($other_items) || !is_array($other_items)) {
            continue;
        }

        $other_label = ucwords(str_replace('-', ' ', $other_slug));
        $other_count = 0;
        $other_photos = 0;
        $other_videos = 0;
        $other_sessions = 0;

        foreach ($other_items as $other_item) {
            $other_iso = $other_item['iso_date'] ?? '';
            $other_slots = $ranges[$other_slug][$other_iso] ?? [];

            if (!empty($other_slots) && is_array($other_slots)) {
                $other_sessions++;

                $other_counts = fml_slots_media_counts($other_slots);
                $other_count += $other_counts['total'];
                $other_photos += $other_counts['photos'];
                $other_videos += $other_counts['videos'];
            }
        }

        $other_locations[] = [
            'slug' => $other_slug,
            'label' => $other_label,
            'sessions' => $other_sessions,
            'count' => $other_count,
            'photos' => $other_photos,
            'videos' => $other_videos,
            'url' => home_url('/foto/' . $other_slug . '/'),
        ];
    }

    usort($other_locations, function ($a, $b) {
        return intval($b['count'] ?? 0) <=> intval($a['count'] ?? 0);
    });

    $other_locations = array_slice($other_locations, 0, 8);

    $title = 'Foto Moto ' . $location_label . ' | Trova la tua foto | FotoMoto.Click';
    $description = 'Hai percorso ' . $location_label . '? Trova le tue foto moto scegliendo data e fascia oraria. Download digitale in alta risoluzione, senza watermark.';

    status_header(200);
    nocache_headers();

    ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html($title); ?></title>
<meta name="description" content="<?php echo esc_attr($description); ?>">
<link rel="canonical" href="<?php echo esc_url(home_url('/foto/' . $location . '/')); ?>">

<?php if (!empty($hero_desktop[0])): ?>
<link rel="preload" as="image" href="<?php echo esc_url($hero_desktop[0]); ?>" media="(min-width:1025px)" fetchpriority="high">
<?php endif; ?>

<?php if (!empty($hero_mobile[0])): ?>
<link rel="preload" as="image" href="<?php echo esc_url($hero_mobile[0]); ?>" media="(max-width:1024px)" fetchpriority="high">
<?php endif; ?>

<?php wp_head(); ?>

<style>
html,
body {
    max-width: 100%;
    overflow-x: clip;
}

@supports not (overflow: clip) {
    html,
    body {
        overflow-x: hidden;
    }
}

.fml-page {
    margin: 0;
    background: #070707;
    color: #fff;
    font-family: "Red Hat Display", Inter, system-ui, -apple-system, "Segoe UI", sans-serif;
}

.fml-page *,
.fml-page *::before,
.fml-page *::after {
    box-sizing: border-box;
}

.fml-wrap {
    width: min(1180px, calc(100% - 32px));
    margin: 0 auto;
}

.fml-hero {
    position: relative;
    min-height: clamp(720px, 92svh, 900px);
    overflow: hidden;
    isolation: isolate;
    background: #050505;
}

.fml-hero__media,
.fml-hero__slide,
.fml-hero__slide picture,
.fml-hero__slide img,
.fml-hero__overlay {
    position: absolute;
    inset: 0;
}

.fml-hero__media {
    z-index: 0;
}

.fml-hero__slide {
    opacity: 0;
    transform: scale(1.025);
    filter: blur(7px);
    transition: opacity 850ms ease, transform 1150ms ease, filter 850ms ease;
}

.fml-hero__slide.is-active {
    z-index: 2;
    opacity: 1;
    transform: scale(1);
    filter: blur(0);
}

.fml-hero__slide.is-leaving {
    z-index: 1;
    opacity: 0;
    transform: scale(1.03);
    filter: blur(9px);
}

.fml-hero__slide picture,
.fml-hero__slide img {
    width: 100%;
    height: 100%;
    display: block;
}

.fml-hero__slide img {
    object-fit: cover;
    object-position: center center;
    transform: scale(1.035);
    animation: fmlHeroKen 9000ms ease-in-out both;
    will-change: transform;
}

@keyframes fmlHeroKen {
    from {
        transform: scale(1.035) translate3d(0, 0, 0);
    }

    to {
        transform: scale(1.095) translate3d(-0.8%, 0, 0);
    }
}

.fml-hero__overlay {
    z-index: 3;
    pointer-events: none;
    background:
        linear-gradient(
            90deg,
            rgba(0, 0, 0, .76) 0%,
            rgba(0, 0, 0, .55) 36%,
            rgba(0, 0, 0, .18) 68%,
            rgba(0, 0, 0, .26) 100%
        ),
        linear-gradient(
            180deg,
            rgba(0, 0, 0, .10) 0%,
            rgba(0, 0, 0, .08) 46%,
            rgba(7, 7, 7, .92) 100%
        );
}

.fml-hero__inner {
    position: relative;
    z-index: 5;
    min-height: inherit;
    display: grid;
    align-content: center;
    padding: clamp(46px, 6vw, 82px) 0 44px;
}

.fml-hero__content {
    width: min(100%, 900px);
    padding-bottom: 28px;
}

.fml-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 0 0 16px;
    color: rgba(255, 255, 255, .76);
    font-size: 13px;
    font-weight: 800;
}

.fml-breadcrumb a {
    color: rgba(255, 255, 255, .88) !important;
    text-decoration: none !important;
    transition: color .18s ease;
}

.fml-breadcrumb a:hover {
    color: #fff !important;
}

.fml-breadcrumb span {
    color: rgba(255, 255, 255, .50);
}

.fml-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 18px;
    padding: 8px 12px;
    border: 1px solid rgba(255, 255, 255, .20);
    border-radius: 999px;
    background: rgba(255, 255, 255, .07);
    color: rgba(255, 255, 255, .88);
    font-size: 13px;
    font-weight: 850;
    letter-spacing: .04em;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
}

.fml-title {
    max-width: 980px;
    margin: 0;
    color: #fff !important;
    font-size: clamp(48px, 6.2vw, 92px);
    line-height: .92;
    font-weight: 950;
    letter-spacing: -.06em;
    text-shadow: 0 20px 54px rgba(0, 0, 0, .42);
}

.fml-lead {
    max-width: 760px;
    margin: 22px 0 0;
    color: rgba(255, 255, 255, .90);
    font-size: clamp(18px, 2vw, 24px);
    line-height: 1.46;
    font-weight: 560;
    text-shadow: 0 12px 34px rgba(0, 0, 0, .44);
}

.fml-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 26px;
}

.fml-stat {
    padding: 13px 16px;
    border: 1px solid rgba(255, 255, 255, .16);
    border-radius: 18px;
    background: rgba(255, 255, 255, .075);
    backdrop-filter: blur(10px);
    box-shadow: 0 14px 34px rgba(0, 0, 0, .18);
}

.fml-stat strong {
    display: block;
    color: #fff;
    font-size: 24px;
    line-height: 1;
}

.fml-stat span {
    display: block;
    margin-top: 4px;
    color: rgba(255, 255, 255, .78);
    font-size: 13px;
    font-weight: 750;
}

.fml-finder {
    width: 100%;
    margin-top: 28px;
}

.fml-finder__inner {
    width: min(1120px, 100%);
    background: rgba(255, 255, 255, .92);
    color: #101010;
    border: 1px solid rgba(255, 255, 255, .72);
    border-radius: 30px;
    padding: 28px 26px 22px;
    box-shadow: 0 28px 80px rgba(0, 0, 0, .36), 0 1px 0 rgba(255, 255, 255, .9) inset;
    backdrop-filter: blur(14px);
}

.fml-finder__top {
    margin-bottom: 22px;
    text-align: center;
}

.fml-eyebrow {
    margin: 0 0 8px;
    color: #666;
    font-size: .8rem;
    font-weight: 950;
    letter-spacing: .10em;
    text-transform: uppercase;
}

.fml-finder__title {
    margin: 0 0 10px;
    color: #101010 !important;
    font-size: clamp(2rem, 4.5vw, 3.25rem);
    line-height: 1.02;
    font-weight: 950;
    letter-spacing: -.04em;
}

.fml-finder__text {
    max-width: 760px;
    margin: 0 auto;
    color: #5c5c5c;
    font-size: 1rem;
    line-height: 1.6;
}

.fml-finder__form {
    display: grid;
    grid-template-columns: 1fr 1.05fr .85fr;
    gap: 16px;
    align-items: end;
}

.fml-field {
    min-width: 0;
}

.fml-field label {
    display: block;
    margin: 0 0 8px;
    color: #1c1c1c;
    font-size: .95rem;
    font-weight: 850;
}

.fml-field--wheel {
    position: relative;
}

.fml-field--wheel > select {
    position: absolute !important;
    opacity: 0 !important;
    pointer-events: none !important;
    width: 1px !important;
    height: 1px !important;
    min-height: 1px !important;
    max-height: 1px !important;
    padding: 0 !important;
    margin: 0 !important;
    border: 0 !important;
    outline: 0 !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
}

.fml-roll,
.fml-roll * {
    appearance: none !important;
    -webkit-appearance: none !important;
}

.fml-roll {
    width: 100%;
    min-height: 56px;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 999px;
    background:
        radial-gradient(130% 85% at 50% -32%, rgba(255,255,255,.30) 0%, rgba(255,255,255,.11) 24%, rgba(255,255,255,0) 54%),
        radial-gradient(120% 80% at 50% 130%, rgba(0,0,0,.98) 0%, rgba(0,0,0,.42) 40%, rgba(0,0,0,0) 72%),
        linear-gradient(90deg, rgba(0,0,0,.42) 0%, rgba(255,255,255,.04) 18%, rgba(255,255,255,.04) 82%, rgba(0,0,0,.42) 100%),
        linear-gradient(180deg, #34363d 0%, #121318 42%, #050506 100%);
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.20),
        inset 0 14px 20px -20px rgba(255,255,255,.28),
        inset 0 -14px 22px -20px rgba(0,0,0,.92),
        inset 12px 0 16px -14px rgba(0,0,0,.62),
        inset -12px 0 16px -14px rgba(0,0,0,.62),
        0 10px 20px rgba(0,0,0,.20),
        0 2px 6px rgba(0,0,0,.15);
    display: grid;
    grid-template-columns: 40px minmax(0, 1fr) 40px;
    align-items: center;
    overflow: hidden;
    transition: border-color .2s ease, box-shadow .2s ease, filter .2s ease;
}

.fml-roll:hover {
    border-color: rgba(255,255,255,.16);
    filter: brightness(1.035);
}

.fml-roll:focus-within {
    border-color: rgba(255,255,255,.22);
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.22),
        inset 0 14px 20px -20px rgba(255,255,255,.30),
        inset 0 -14px 22px -20px rgba(0,0,0,.92),
        inset 12px 0 16px -14px rgba(0,0,0,.62),
        inset -12px 0 16px -14px rgba(0,0,0,.62),
        0 0 0 3px rgba(0,0,0,.16),
        0 12px 24px rgba(0,0,0,.24);
}

.fml-roll.is-disabled {
    background: linear-gradient(180deg, #202126 0%, #111216 100%);
    border-color: rgba(255,255,255,.04);
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.06),
        inset 0 -1px 2px rgba(0,0,0,.55);
    filter: none;
}

.fml-roll__viewport {
    position: relative;
    height: 56px;
    overflow: hidden;
    outline: none;
    cursor: grab;
    touch-action: none;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: auto;
    background:
        radial-gradient(82% 68% at 50% 50%, rgba(255,255,255,.06) 0%, rgba(255,255,255,.018) 38%, rgba(0,0,0,.18) 76%),
        linear-gradient(180deg, rgba(255,255,255,.10) 0%, rgba(255,255,255,0) 30%, rgba(0,0,0,0) 62%, rgba(0,0,0,.38) 100%);
    box-shadow:
        inset 1px 0 0 rgba(255,255,255,.045),
        inset -1px 0 0 rgba(255,255,255,.035),
        inset 0 8px 14px -14px rgba(255,255,255,.22),
        inset 0 -10px 16px -14px rgba(0,0,0,.85);
}

.fml-roll__viewport:active {
    cursor: grabbing;
}

.fml-roll__track {
    will-change: transform;
    transition: transform .34s cubic-bezier(.2, .8, .2, 1);
}

.fml-roll__item {
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 8px;
    color: #F3F2EE;
    font-size: 1rem;
    font-weight: 850;
    text-align: center;
    text-shadow:
        0 1px 1px rgba(0,0,0,.78),
        0 0 10px rgba(255,255,255,.07);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.fml-roll.is-disabled .fml-roll__item,
.fml-roll__item.is-placeholder {
    color: rgba(243,242,238,.48);
    font-weight: 700;
    text-shadow: 0 1px 1px rgba(0,0,0,.65);
}

.fml-roll__nav {
    width: 40px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0 !important;
    outline: 0 !important;
    background:
        radial-gradient(circle at 50% 45%, rgba(255,255,255,.09) 0%, rgba(255,255,255,.025) 34%, rgba(0,0,0,.28) 72%, rgba(0,0,0,.45) 100%) !important;
    color: #ECEAE4 !important;
    -webkit-text-fill-color: #ECEAE4 !important;
    font-size: 1.45rem;
    line-height: 1;
    font-weight: 900;
    cursor: pointer;
    opacity: .82;
    text-shadow:
        0 1px 0 rgba(255,255,255,.10),
        0 -1px 1px rgba(0,0,0,.85);
    transform: rotate(90deg);
    transition: opacity .18s ease, background .18s ease, transform .18s ease, color .18s ease;
    box-shadow: none !important;
}

.fml-roll__nav:hover:not(:disabled) {
    opacity: 1;
    color: #fff !important;
    -webkit-text-fill-color: #fff !important;
    background:
        radial-gradient(circle at 50% 45%, rgba(255,255,255,.14) 0%, rgba(255,255,255,.045) 34%, rgba(0,0,0,.24) 72%, rgba(0,0,0,.42) 100%) !important;
}

.fml-roll__nav:active:not(:disabled) {
    transform: rotate(90deg) scale(.94);
}

.fml-roll__nav:disabled {
    opacity: .22;
    cursor: not-allowed;
}

.fml-submit {
    width: 100%;
    min-height: 56px;
    border: 0 !important;
    border-radius: 999px;
    background: linear-gradient(180deg, #171717 0%, #030303 100%) !important;
    color: #fff !important;
    font-size: 1rem;
    font-weight: 950;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 16px 34px rgba(0, 0, 0, .18), 0 1px 0 rgba(255, 255, 255, .1) inset;
    transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
}

.fml-submit:hover {
    transform: translateY(-1px);
    filter: brightness(1.02);
    box-shadow: 0 20px 38px rgba(0, 0, 0, .22), 0 1px 0 rgba(255, 255, 255, .1) inset;
}

.fml-submit span {
    white-space: nowrap;
}

.fml-help {
    display: block;
    min-height: 1.5em;
    margin-top: 10px;
    color: #6a6a6a;
    font-size: .84rem;
    line-height: 1.45;
}

.fml-hero__dots {
    position: absolute;
    z-index: 8;
    left: max(24px, calc((100vw - 1180px) / 2));
    bottom: 24px;
    display: flex;
    align-items: center;
    gap: 9px;
}

.fml-hero__dots button {
    appearance: none !important;
    width: 34px;
    height: 4px;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 999px;
    background: rgba(255, 255, 255, .34) !important;
    cursor: pointer;
    transition: width .22s ease, background .22s ease;
}

.fml-hero__dots button.is-active {
    width: 54px;
    background: #fff !important;
}

.fml-section {
    padding: 56px 0;
}

.fml-h2 {
    margin: 0 0 18px;
    color: #fff !important;
    font-size: clamp(28px, 4vw, 44px);
    line-height: 1.05;
    font-weight: 950;
    letter-spacing: -.04em;
}

.fml-muted {
    color: #aaa;
    line-height: 1.65;
}

.fml-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.fml-session {
    display: block;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, .10);
    border-radius: 22px;
    background: #111;
    color: #fff !important;
    text-decoration: none !important;
    transition: transform .2s ease, border-color .2s ease;
}

.fml-session:hover {
    transform: translateY(-3px);
    border-color: rgba(255, 255, 255, .28);
}

.fml-session strong {
    display: block;
    margin-bottom: 8px;
    font-size: 22px;
}

.fml-session span {
    display: block;
    color: #aaa;
    font-size: 14px;
}

.fml-session em {
    display: inline-flex;
    margin-top: 14px;
    color: #fff;
    font-style: normal;
    font-weight: 850;
}

.fml-mi {
    width: 1em;
    height: 1em;
    vertical-align: -0.15em;
    margin-right: 2px;
}

.fml-mi-sep {
    margin: 0 6px;
    color: rgba(255, 255, 255, .40);
}

/* Le card impongono display:block a ogni <span> interno (.fml-session span,
   .fml-date span): il separatore del Media Summary deve restare inline,
   altrimenti foto e video si impilano su righe diverse. */
.fml-session span.fml-mi-sep,
.fml-date span.fml-mi-sep {
    display: inline;
    margin-top: 0;
}

.fml-trust {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.fml-trust__item {
    padding: 18px;
    border: 1px solid rgba(255, 255, 255, .10);
    border-radius: 20px;
    background: #111;
}

.fml-trust__item strong {
    display: block;
    margin-bottom: 6px;
}

.fml-archive {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}

.fml-date {
    display: block;
    padding: 14px 12px;
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 16px;
    background: #111;
    color: #fff !important;
    text-decoration: none !important;
    text-align: center;
    transition: border-color .2s ease, transform .2s ease;
}

.fml-date:hover {
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, .25);
}

.fml-date strong {
    display: block;
    font-size: 15px;
    font-weight: 850;
}

.fml-date span {
    display: block;
    margin-top: 4px;
    color: #aaa;
    font-size: 12px;
    font-weight: 700;
}

.fml-inline-links {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
}

.fml-inline-links a {
    display: inline-flex;
    align-items: center;
    min-height: 42px;
    padding: 10px 14px;
    border: 1px solid rgba(255, 255, 255, .12);
    border-radius: 999px;
    background: #111;
    color: #fff !important;
    font-size: 14px;
    font-weight: 850;
    text-decoration: none !important;
    transition: transform .2s ease, border-color .2s ease;
}

.fml-inline-links a:hover {
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, .28);
}

.fml-faq {
    display: grid;
    gap: 12px;
}

.fml-faq details {
    padding: 18px;
    border: 1px solid rgba(255, 255, 255, .10);
    border-radius: 18px;
    background: #111;
}

.fml-faq summary {
    cursor: pointer;
    font-weight: 850;
}

.fml-faq p {
    margin-bottom: 0;
    color: #aaa;
    line-height: 1.6;
}

@media (max-width: 1080px) {
    .fml-finder__form {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .fml-actions {
        grid-column: 1 / -1;
    }

    .fml-trust {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 900px) {
    .fml-hero {
        min-height: 100svh;
    }

    .fml-hero__overlay {
        background:
            linear-gradient(
                180deg,
                rgba(0, 0, 0, .58) 0%,
                rgba(0, 0, 0, .28) 30%,
                rgba(0, 0, 0, .52) 62%,
                rgba(7, 7, 7, .96) 100%
            );
    }

    .fml-hero__inner {
        align-content: start;
        padding: 28px 0 74px;
    }

    .fml-hero__content {
        text-align: center;
        padding-bottom: 8px;
    }

    .fml-breadcrumb {
        justify-content: center;
        margin-bottom: 12px;
        font-size: 12px;
    }

    .fml-kicker {
        margin-bottom: 10px;
        font-size: 11px;
        letter-spacing: .10em;
    }

    .fml-title {
        margin-inline: auto;
        font-size: clamp(34px, 9vw, 48px);
        line-height: .98;
        letter-spacing: -.04em;
    }

    .fml-lead {
        max-width: 34ch;
        margin: 14px auto 0;
        font-size: 15px;
        line-height: 1.42;
    }

    .fml-stats {
        justify-content: center;
        gap: 8px;
        margin-top: 16px;
    }

    .fml-stat {
        padding: 10px 12px;
        border-radius: 15px;
    }

    .fml-stat strong {
        font-size: 19px;
    }

    .fml-stat span {
        font-size: 11px;
    }

    .fml-finder {
        margin-top: 18px;
    }

    .fml-finder__inner {
        border-radius: 24px;
        padding: 20px 16px 18px;
    }

    .fml-finder__title {
        font-size: clamp(1.65rem, 7vw, 2.25rem);
    }

    .fml-finder__text {
        font-size: .94rem;
    }

    .fml-grid {
        grid-template-columns: 1fr 1fr;
    }

    .fml-archive {
        grid-template-columns: 1fr 1fr;
    }

    .fml-hero__dots {
        left: 50%;
        bottom: 18px;
        transform: translateX(-50%);
    }
}

@media (max-width: 640px) {
    .fml-wrap {
        width: min(100% - 28px, 1180px);
    }

    .fml-finder__form {
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .fml-actions {
        grid-column: auto;
    }

    .fml-roll {
        min-height: 66px;
        grid-template-columns: 46px minmax(0, 1fr) 46px;
        border-radius: 20px;
    }

    .fml-roll__viewport,
    .fml-roll__item,
    .fml-roll__nav {
        height: 66px;
    }

    .fml-roll__nav {
        width: 46px;
        font-size: 1.65rem;
    }

    .fml-roll__item {
        font-size: 1.03rem;
        padding: 0 6px;
    }

    .fml-grid,
    .fml-trust {
        grid-template-columns: 1fr;
    }

    .fml-section {
        padding: 46px 0;
    }
}

@media (max-width: 374px) {
    .fml-title {
        font-size: clamp(30px, 8.5vw, 38px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .fml-hero__slide,
    .fml-hero__slide img,
    .fml-submit,
    .fml-session,
    .fml-date,
    .fml-hero__dots button {
        animation: none !important;
        transition: none !important;
        transform: none !important;
        filter: none !important;
    }
}
</style>

<script type="application/ld+json">
<?php
echo wp_json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'Come trovo la mia foto a ' . $location_label . '?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Seleziona la data e la fascia oraria in cui sei passato. Il sistema mostra le foto più vicine a quell’orario.',
            ],
        ],
        [
            '@type' => 'Question',
            'name' => 'Quanto costano foto e video?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Ogni foto digitale costa 10 euro, i video 15 euro. Entrambi in alta risoluzione e senza watermark.',
            ],
        ],
        [
            '@type' => 'Question',
            'name' => 'Le foto hanno watermark?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Le anteprime online possono avere watermark, ma il file acquistato viene fornito senza watermark.',
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
</script>

<?php if ($passo && !empty($passo['schema'])): $sc = $passo['schema']; ?>
<script type="application/ld+json">
<?php
$place = [
    '@context'   => 'https://schema.org',
    '@type'      => (count($sc['types']) === 1 ? $sc['types'][0] : $sc['types']),
    'name'       => $sc['name'],
    'description' => $sc['desc'],
    'elevation'  => $sc['elev'],
    'geo'        => ['@type' => 'GeoCoordinates', 'latitude' => $sc['lat'], 'longitude' => $sc['lon']],
];
if (!empty($sc['alt']))    { $place['alternateName'] = $sc['alt']; }
if (!empty($sc['sameAs'])) { $place['sameAs'] = $sc['sameAs']; }
echo wp_json_encode($place, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
</script>
<?php endif; ?>
</head>

<body <?php body_class('fml-page'); ?>>
<?php wp_body_open(); ?>

<main>
    <section class="fml-hero" data-fml-hero>
        <div class="fml-hero__media" aria-hidden="true">
            <?php for ($i = 0; $i < 3; $i++): ?>
                <?php
                $desktop_src = $hero_desktop[$i % max(1, count($hero_desktop))] ?? '';
                $mobile_src = $hero_mobile[$i % max(1, count($hero_mobile))] ?? $desktop_src;
                $active_class = $i === 0 ? ' is-active' : '';
                $loading = $i === 0 ? 'eager' : 'lazy';
                $fetchpriority = $i === 0 ? 'fetchpriority="high"' : '';
                ?>

                <?php if ($desktop_src): ?>
                    <div class="fml-hero__slide<?php echo esc_attr($active_class); ?>" data-fml-hero-slide>
                        <picture>
                            <source media="(max-width:1024px)" srcset="<?php echo esc_url($mobile_src); ?>" data-fml-mobile-source>
                            <source media="(min-width:1025px)" srcset="<?php echo esc_url($desktop_src); ?>" data-fml-desktop-source>
                            <img
                                src="<?php echo esc_url($desktop_src); ?>"
                                alt="Motociclista fotografato a <?php echo esc_attr($location_label); ?> - FotoMoto.Click"
                                title="Foto moto <?php echo esc_attr($location_label); ?> | FotoMoto.Click"
                                width="1920"
                                height="1080"
                                <?php echo $fetchpriority; ?>
                                loading="<?php echo esc_attr($loading); ?>"
                                decoding="async"
                                class="skip-lazy"
                                data-no-lazy="1"
                                data-skip-lazy="1">
                        </picture>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>

        <div class="fml-hero__overlay" aria-hidden="true"></div>

        <div class="fml-wrap fml-hero__inner">
            <div class="fml-hero__content">
                <nav class="fml-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                    <span aria-hidden="true">/</span>
                    <a href="<?php echo esc_url(home_url('/passi-e-valichi/')); ?>">Passi e valichi</a>
                    <span aria-hidden="true">/</span>
                    <strong><?php echo esc_html($location_label); ?></strong>
                </nav>

                <div class="fml-kicker">FotoMoto.Click · Galleria moto</div>
                <h1 class="fml-title">Trova la tua foto<br>a <?php echo esc_html($location_label); ?></h1>
                <p class="fml-lead">Hai percorso <?php echo esc_html($location_label); ?>? Seleziona data e fascia oraria: in pochi secondi trovi le foto della tua moto.</p>

                <div class="fml-stats">
                    <div class="fml-stat">
                        <strong><?php echo esc_html(number_format_i18n($total_sessions)); ?></strong>
                        <span>gallerie</span>
                    </div>
                    <div class="fml-stat">
                        <strong><?php echo esc_html(number_format_i18n($total_photos)); ?></strong>
                        <span>foto disponibili</span>
                    </div>
                    <?php if ($total_videos > 0): ?>
                    <div class="fml-stat">
                        <strong><?php echo esc_html(number_format_i18n($total_videos)); ?></strong>
                        <span>video disponibili</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <section class="fml-finder" id="trova-foto" data-fml-finder>
                <div class="fml-finder__inner">
                    <div class="fml-finder__top">
                        <p class="fml-eyebrow">RICERCA RAPIDA</p>
                        <h2 class="fml-finder__title">Cerca tra le foto di <?php echo esc_html($location_label); ?></h2>
                        <p class="fml-finder__text">Il passo è già impostato. Scegli solo giorno e fascia oraria realmente disponibile.</p>
                    </div>

                    <form class="fml-finder__form" action="<?php echo esc_url(home_url('/cerca-foto-moto/')); ?>" method="get" novalidate>
                        <input type="hidden" name="passo" value="<?php echo esc_attr($location); ?>">

                        <div class="fml-field fml-field--wheel">
                            <label for="fml-data">Data</label>
                            <select id="fml-data" name="data" data-fml-data required>
                                <option value="">Seleziona una data</option>
                            </select>

                            <div class="fml-roll" data-fml-roll="data">
                                <button type="button" class="fml-roll__nav fml-roll__nav--prev" data-fml-roll-prev disabled aria-label="Data precedente">‹</button>
                                <div class="fml-roll__viewport" data-fml-roll-viewport tabindex="0" role="button" aria-label="Seleziona data">
                                    <div class="fml-roll__track" data-fml-roll-track>
                                        <div class="fml-roll__item is-active">Caricamento date...</div>
                                    </div>
                                </div>
                                <button type="button" class="fml-roll__nav fml-roll__nav--next" data-fml-roll-next disabled aria-label="Data successiva">›</button>
                            </div>
                        </div>

                        <div class="fml-field fml-field--wheel">
                            <label for="fml-slot">Fascia oraria</label>
                            <select id="fml-slot" data-fml-slot required disabled>
                                <option value="">Prima scegli la data</option>
                            </select>

                            <div class="fml-roll is-disabled" data-fml-roll="slot">
                                <button type="button" class="fml-roll__nav fml-roll__nav--prev" data-fml-roll-prev disabled aria-label="Fascia precedente">‹</button>
                                <div class="fml-roll__viewport" data-fml-roll-viewport tabindex="0" role="button" aria-label="Seleziona fascia oraria">
                                    <div class="fml-roll__track" data-fml-roll-track>
                                        <div class="fml-roll__item is-active is-placeholder">Prima scegli la data</div>
                                    </div>
                                </div>
                                <button type="button" class="fml-roll__nav fml-roll__nav--next" data-fml-roll-next disabled aria-label="Fascia successiva">›</button>
                            </div>
                        </div>

                        <input type="hidden" name="ora" data-fml-ora value="">
                        <input type="hidden" name="slot_label" data-fml-slot-label value="">

                        <div class="fml-actions">
                            <button type="submit" class="fml-submit"><span>Cerca le tue foto</span></button>
                        </div>
                    </form>

                    <small class="fml-help" data-fml-help></small>
                </div>
            </section>
        </div>

        <div class="fml-hero__dots" aria-label="Slider hero">
            <button class="is-active" type="button" aria-label="Slide 1" data-fml-hero-dot="0"></button>
            <button type="button" aria-label="Slide 2" data-fml-hero-dot="1"></button>
            <button type="button" aria-label="Slide 3" data-fml-hero-dot="2"></button>
        </div>
    </section>

    <section class="fml-section">
        <div class="fml-wrap">
            <h2 class="fml-h2">Ultime sessioni a <?php echo esc_html($location_label); ?></h2>
            <div class="fml-grid">
                <?php foreach ($latest as $session): ?>
                    <a class="fml-session" href="<?php echo esc_url($session['url']); ?>">
                        <strong><?php echo esc_html($session['date']); ?></strong>
                        <span><?php echo fml_media_summary_html($session['photos'], $session['videos']); ?></span>
                        <em>Guarda la galleria →</em>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="fml-section">
        <div class="fml-wrap">
            <h2 class="fml-h2">Perché usare FotoMoto.Click</h2>
            <div class="fml-trust">
                <div class="fml-trust__item">
                    <strong>Download immediato</strong>
                    <span class="fml-muted">Acquisti e scarichi il file digitale.</span>
                </div>
                <div class="fml-trust__item">
                    <strong>Alta risoluzione</strong>
                    <span class="fml-muted">File fotografici pronti da conservare.</span>
                </div>
                <div class="fml-trust__item">
                    <strong>Senza watermark</strong>
                    <span class="fml-muted">Il file acquistato è pulito.</span>
                </div>
                <div class="fml-trust__item">
                    <strong>Ricerca veloce</strong>
                    <span class="fml-muted">Data e fascia oraria, senza app.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="fml-section">
        <div class="fml-wrap">
            <h2 class="fml-h2">Gallerie fotografiche <?php echo esc_html($location_label); ?></h2>
            <div class="fml-archive">
                <?php foreach ($sessions as $session): ?>
                    <a class="fml-date" href="<?php echo esc_url($session['url']); ?>">
                        <strong><?php echo esc_html($session['date']); ?></strong>
                        <span><?php echo fml_media_summary_html($session['photos'], $session['videos']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($passo): ?>
    <section class="fml-section" id="scopri-il-passo">
        <div class="fml-wrap">
            <p class="fml-eyebrow" style="color:#8a8a8a;text-align:left;margin:0 0 6px">Scopri il passo</p>
            <h2 class="fml-h2"><?php echo esc_html($passo['title']); ?></h2>
            <p class="fml-muted" style="max-width:72ch"><?php echo esc_html($passo['lead']); ?></p>

            <?php if (!empty($passo['facts'])): ?>
            <div class="fml-stats" style="margin-top:20px">
                <?php foreach ($passo['facts'] as $f): ?>
                    <div class="fml-stat">
                        <strong><?php echo esc_html($f[0]); ?></strong>
                        <?php if (!empty($f[1])): ?><span><?php echo esc_html($f[1]); ?></span><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($passo['versanti'])): ?>
            <div class="fml-trust" style="margin-top:22px;grid-template-columns:1fr 1fr;max-width:820px">
                <?php foreach ($passo['versanti'] as $v): ?>
                    <div class="fml-trust__item">
                        <span class="fml-muted" style="font-size:12px;font-weight:850;letter-spacing:.04em;text-transform:uppercase"><?php echo esc_html($v[1]); ?></span>
                        <strong style="display:block;margin-top:8px"><?php echo esc_html($v[0]); ?></strong>
                        <strong style="display:block;font-size:20px;margin:4px 0"><?php echo esc_html($v[2]); ?></strong>
                        <span class="fml-muted"><?php echo esc_html($v[3]); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($passo['body'])): ?>
            <p class="fml-muted" style="margin-top:22px;max-width:76ch"><?php echo wp_kses_post($passo['body']); ?></p>
            <?php endif; ?>

            <?php if (!empty($passo['table'])): ?>
            <div class="fml-trust__item" style="margin-top:18px;max-width:640px">
                <?php $rows = $passo['table']; $i = 0; $n = count($rows); foreach ($rows as $k => $val): $i++; ?>
                    <div style="display:flex;justify-content:space-between;gap:16px;padding:9px 0;<?php echo $i < $n ? 'border-bottom:1px solid rgba(255,255,255,.09)' : ''; ?>">
                        <span class="fml-muted" style="font-weight:750"><?php echo esc_html($k); ?></span>
                        <span style="text-align:right;color:#fff"><?php echo esc_html($val); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="fml-section">
        <div class="fml-wrap">
            <h2 class="fml-h2">Foto moto a <?php echo esc_html($location_label); ?></h2>
            <p class="fml-muted">
                In questa pagina trovi tutte le gallerie fotografiche realizzate da FotoMoto.Click a <?php echo esc_html($location_label); ?>.
                Puoi trovare rapidamente la tua moto selezionando la data e la fascia oraria di passaggio oppure sfogliando le singole gallerie disponibili.
                Le foto acquistate vengono fornite in alta risoluzione e senza watermark.
            </p>

            <div class="fml-inline-links" aria-label="Link utili <?php echo esc_attr($location_label); ?>">
                <a href="<?php echo esc_url(home_url('/#trova-la-tua-foto')); ?>">Cerca in tutte le località →</a>
                <a href="<?php echo esc_url(home_url('/passi-e-valichi/')); ?>">Tutti i passi e valichi →</a>
                <a href="<?php echo esc_url(home_url('/')); ?>">Vai alla home →</a>
            </div>
        </div>
    </section>

    <?php if (!empty($other_locations)): ?>
        <section class="fml-section">
            <div class="fml-wrap">
                <h2 class="fml-h2">Cerchi foto in altri passi?</h2>
                <p class="fml-muted">
                    Se nella stessa giornata hai percorso anche altre strade, puoi consultare le gallerie fotografiche disponibili nelle altre località coperte da FotoMoto.Click.
                </p>

                <div class="fml-grid">
                    <?php foreach ($other_locations as $other_location): ?>
                        <a class="fml-session" href="<?php echo esc_url($other_location['url']); ?>">
                            <strong><?php echo esc_html($other_location['label']); ?></strong>
                            <span><?php echo fml_media_summary_html($other_location['photos'], $other_location['videos']); ?></span>
                            <em>Apri la galleria →</em>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="fml-section">
        <div class="fml-wrap">
            <h2 class="fml-h2">Domande frequenti</h2>
            <div class="fml-faq">
                <details>
                    <summary>Come trovo la mia foto?</summary>
                    <p>Seleziona la data e l’orario approssimativo in cui sei passato. Il finder mostrerà le foto più vicine a quella fascia.</p>
                </details>
                <details>
                    <summary>Quanto costano foto e video?</summary>
                    <p>Ogni foto digitale costa 10&euro;, i video 15&euro;. Entrambi in alta risoluzione e senza watermark.</p>
                </details>
                <details>
                    <summary>Il file acquistato ha il watermark?</summary>
                    <p>No. Il file scaricato dopo l’acquisto è senza watermark.</p>
                </details>
                <details>
                    <summary>Posso richiedere una versione Premium?</summary>
                    <p>Sì, dalla pagina della singola foto puoi richiedere anche una lavorazione Premium.</p>
                </details>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const hero = document.querySelector("[data-fml-hero]");
    const heroSlides = hero ? Array.from(hero.querySelectorAll("[data-fml-hero-slide]")) : [];
    const heroDots = hero ? Array.from(hero.querySelectorAll("[data-fml-hero-dot]")) : [];

    if (heroSlides.length > 1) {
        const interval = 6200;
        const leaveTime = 950;
        let current = 0;
        let timer = null;

        function restartAnimation(slide) {
            const img = slide.querySelector("img");

            if (!img) {
                return;
            }

            img.style.animation = "none";
            void img.offsetWidth;
            img.style.animation = "";
        }

        function goTo(index) {
            if (index === current || !heroSlides[index]) {
                return;
            }

            const previous = heroSlides[current];
            const next = heroSlides[index];

            previous.classList.remove("is-active");
            previous.classList.add("is-leaving");
            next.classList.add("is-active");

            restartAnimation(next);

            heroDots.forEach(function (dot, dotIndex) {
                dot.classList.toggle("is-active", dotIndex === index);
            });

            window.setTimeout(function () {
                previous.classList.remove("is-leaving");
            }, leaveTime);

            current = index;
        }

        function next() {
            goTo((current + 1) % heroSlides.length);
        }

        function start() {
            stop();
            timer = window.setInterval(next, interval);
        }

        function stop() {
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
        }

        heroDots.forEach(function (dot) {
            dot.addEventListener("click", function () {
                const index = Number(dot.getAttribute("data-fml-hero-dot"));
                goTo(index);
                start();
            });
        });

        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                stop();
            } else {
                start();
            }
        });

        start();
    }

    const root = document.querySelector("[data-fml-finder]");

    if (!root) {
        return;
    }

    const sessions = <?php echo wp_json_encode($sessions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const locationSlug = <?php echo wp_json_encode($location); ?>;
    const locationLabel = <?php echo wp_json_encode($location_label); ?>;

    const form = root.querySelector("form");
    const dataSelect = root.querySelector("[data-fml-data]");
    const slotSelect = root.querySelector("[data-fml-slot]");
    const oraHidden = root.querySelector("[data-fml-ora]");
    const slotLabelHidden = root.querySelector("[data-fml-slot-label]");
    const help = root.querySelector("[data-fml-help]");
    const rollStates = {};

    function fmTrack(eventName, extraData) {
        if (typeof gtag !== "function") {
            return;
        }

        gtag("event", eventName, Object.assign({
            event_category: "finder",
            page_type: "location_landing",
            finder_name: "location_landing_lux",
            passo: locationSlug,
            location_name: locationLabel
        }, extraData || {}));
    }

    function formatDateLabel(iso) {
        const parts = String(iso || "").split("-");

        if (parts.length !== 3) {
            return iso;
        }

        const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));

        return date.toLocaleDateString("it-IT", {
            weekday: "short",
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        });
    }

    function getRoll(name) {
        if (rollStates[name]) {
            return rollStates[name];
        }

        const roll = root.querySelector('[data-fml-roll="' + name + '"]');

        rollStates[name] = {
            name: name,
            root: roll,
            viewport: roll ? roll.querySelector("[data-fml-roll-viewport]") : null,
            track: roll ? roll.querySelector("[data-fml-roll-track]") : null,
            prev: roll ? roll.querySelector("[data-fml-roll-prev]") : null,
            next: roll ? roll.querySelector("[data-fml-roll-next]") : null,
            options: [],
            index: 0,
            itemHeight: 56,
            touchStartY: null,
            select: null,
            onChange: null
        };

        return rollStates[name];
    }

    function updateRollHeight(state) {
        if (!state || !state.viewport) {
            return;
        }

        const firstItem = state.track ? state.track.querySelector(".fml-roll__item") : null;
        state.itemHeight = firstItem ? firstItem.offsetHeight || 56 : 56;
    }

    function setRollDisabled(name, disabled) {
        const state = getRoll(name);

        if (!state.root) {
            return;
        }

        state.root.classList.toggle("is-disabled", !!disabled);

        if (state.prev) {
            state.prev.disabled = !!disabled;
        }

        if (state.next) {
            state.next.disabled = !!disabled;
        }

        if (state.viewport) {
            state.viewport.setAttribute("aria-disabled", disabled ? "true" : "false");
        }
    }

    function renderRoll(name, options, config) {
        const state = getRoll(name);

        if (!state.track) {
            return;
        }

        config = config || {};
        state.options = Array.isArray(options) ? options : [];
        state.index = Number.isFinite(config.startIndex) ? config.startIndex : 0;
        state.select = config.select || null;
        state.onChange = typeof config.onChange === "function" ? config.onChange : null;
        state.track.innerHTML = "";

        if (!state.options.length) {
            const item = document.createElement("div");
            item.className = "fml-roll__item is-active is-placeholder";
            item.textContent = config.emptyLabel || "Non disponibile";
            state.track.appendChild(item);
            state.track.style.transform = "translateY(0px)";
            setRollDisabled(name, true);
            return;
        }

        state.options.forEach(function (option, index) {
            const item = document.createElement("div");
            item.className = "fml-roll__item" + (index === state.index ? " is-active" : "") + (!option.value ? " is-placeholder" : "");
            item.textContent = option.label || "";
            state.track.appendChild(item);
        });

        setRollDisabled(name, state.options.length <= 1);

        requestAnimationFrame(function () {
            applyRollIndex(name, state.index, true);
        });
    }

    function applyRollIndex(name, index, silent) {
        const state = getRoll(name);

        if (!state.options.length || !state.track) {
            return;
        }

        updateRollHeight(state);

        const maxIndex = state.options.length - 1;
        state.index = Math.max(0, Math.min(index, maxIndex));
        state.track.style.transform = "translateY(-" + (state.index * state.itemHeight) + "px)";

        state.track.querySelectorAll(".fml-roll__item").forEach(function (item, itemIndex) {
            item.classList.toggle("is-active", itemIndex === state.index);
        });

        const selected = state.options[state.index];

        if (state.select) {
            state.select.value = selected.value || "";
        }

        if (state.prev) {
            state.prev.disabled = state.index <= 0;
        }

        if (state.next) {
            state.next.disabled = state.index >= maxIndex;
        }

        if (!silent && state.onChange) {
            state.onChange(selected, state.index);
        }
    }

    function moveRoll(name, direction) {
        const state = getRoll(name);

        if (!state.options.length) {
            return;
        }

        applyRollIndex(name, state.index + direction, false);
    }

    function bindRoll(name) {
        const state = getRoll(name);

        if (!state.root || state.root.dataset.bound === "1") {
            return;
        }

        state.root.dataset.bound = "1";

        if (state.prev) {
            state.prev.addEventListener("click", function () {
                moveRoll(name, -1);
            });
        }

        if (state.next) {
            state.next.addEventListener("click", function () {
                moveRoll(name, 1);
            });
        }

        if (state.viewport) {
            state.viewport.addEventListener("wheel", function (event) {
                if (!state.options.length) {
                    return;
                }

                event.preventDefault();

                if (event.deltaY > 0) {
                    moveRoll(name, 1);
                }

                if (event.deltaY < 0) {
                    moveRoll(name, -1);
                }
            }, { passive: false });

            state.viewport.addEventListener("keydown", function (event) {
                if (event.key === "ArrowDown" || event.key === "ArrowRight") {
                    event.preventDefault();
                    moveRoll(name, 1);
                }

                if (event.key === "ArrowUp" || event.key === "ArrowLeft") {
                    event.preventDefault();
                    moveRoll(name, -1);
                }
            });

            state.viewport.addEventListener("touchstart", function (event) {
                if (!event.touches || !event.touches.length) {
                    return;
                }

                state.touchStartY = event.touches[0].clientY;
            }, { passive: true });

            state.viewport.addEventListener("touchmove", function (event) {
                if (!state.options.length) {
                    return;
                }

                event.preventDefault();
            }, { passive: false });

            state.viewport.addEventListener("touchend", function (event) {
                if (state.touchStartY === null) {
                    return;
                }

                if (!event.changedTouches || !event.changedTouches.length) {
                    return;
                }

                const touchEndY = event.changedTouches[0].clientY;
                const diff = state.touchStartY - touchEndY;

                state.touchStartY = null;

                if (Math.abs(diff) < 22) {
                    return;
                }

                if (diff > 0) {
                    moveRoll(name, 1);
                } else {
                    moveRoll(name, -1);
                }
            }, { passive: true });
        }
    }

    function populateDates() {
        dataSelect.innerHTML = "";

        const placeholder = document.createElement("option");
        placeholder.value = "";
        placeholder.textContent = "Seleziona una data";
        dataSelect.appendChild(placeholder);

        const dateOptions = [{ value: "", label: "Seleziona una data" }];

        sessions.forEach(function (session) {
            const option = document.createElement("option");
            option.value = session.iso;
            option.textContent = formatDateLabel(session.iso);
            dataSelect.appendChild(option);

            dateOptions.push({
                value: session.iso,
                label: option.textContent
            });
        });

        renderRoll("data", dateOptions, {
            select: dataSelect,
            emptyLabel: "Nessuna data disponibile",
            onChange: function () {
                dataSelect.dispatchEvent(new Event("change", { bubbles: true }));

                fmTrack("finder_date_wheel_roll", {
                    selected_date: String(dataSelect.value || "").trim()
                });
            }
        });

        help.textContent = sessions.length + " date disponibili per " + locationLabel + ".";
    }

    function populateSlots(slots) {
        slotSelect.innerHTML = "";

        const placeholder = document.createElement("option");
        placeholder.value = "";
        placeholder.textContent = "Seleziona una fascia";
        slotSelect.appendChild(placeholder);

        const slotOptions = [{ value: "", label: "Seleziona una fascia" }];

        slots.forEach(function (slot) {
            const count = Number(slot.count || 0);
            const option = document.createElement("option");

            option.value = slot.center || slot.start || "";
            option.textContent = slot.label || option.value;
            option.dataset.label = slot.label || "";
            slotSelect.appendChild(option);

            slotOptions.push({
                value: option.value,
                label: option.textContent
            });
        });

        slotSelect.disabled = false;

        renderRoll("slot", slotOptions, {
            select: slotSelect,
            emptyLabel: "Nessuna fascia disponibile",
            onChange: function () {
                slotSelect.dispatchEvent(new Event("change", { bubbles: true }));
            }
        });
    }

    ["data", "slot"].forEach(bindRoll);

    dataSelect.addEventListener("change", function () {
        const selectedIso = String(dataSelect.value || "").trim();

        oraHidden.value = "";
        slotLabelHidden.value = "";

        if (!selectedIso) {
            slotSelect.disabled = true;

            renderRoll("slot", [{ value: "", label: "Prima scegli la data" }], {
                select: slotSelect
            });

            setRollDisabled("slot", true);

            help.textContent = "Scegli una data, poi una fascia oraria realmente disponibile.";
            return;
        }

        const session = sessions.find(function (item) {
            return String(item.iso || "") === selectedIso;
        });

        const slots = session && Array.isArray(session.slots) ? session.slots : [];

        fmTrack("finder_data_select", {
            selected_date: selectedIso
        });

        if (!slots.length) {
            slotSelect.disabled = true;

            renderRoll("slot", [{ value: "", label: "Nessuna fascia disponibile" }], {
                select: slotSelect
            });

            setRollDisabled("slot", true);

            help.textContent = "Nessuna fascia disponibile per questa data.";
            return;
        }

        populateSlots(slots);
        help.textContent = slots.length + " fasce disponibili per questa data.";
    });

    slotSelect.addEventListener("change", function () {
        const value = String(slotSelect.value || "").trim();
        const selectedOption = slotSelect.options[slotSelect.selectedIndex];
        const label = selectedOption && selectedOption.value ? (selectedOption.dataset.label || selectedOption.textContent) : "";

        oraHidden.value = value;
        slotLabelHidden.value = label;

        fmTrack("finder_slot_select", {
            selected_date: String(dataSelect.value || "").trim(),
            selected_time: value,
            selected_slot_label: label
        });
    });

    form.addEventListener("submit", function (event) {
        if (!dataSelect.value) {
            event.preventDefault();
            alert("Seleziona una data disponibile.");
            return;
        }

        if (!slotSelect.value) {
            event.preventDefault();
            alert("Seleziona una fascia oraria.");
            return;
        }

        const selectedOption = slotSelect.options[slotSelect.selectedIndex];

        oraHidden.value = slotSelect.value;
        slotLabelHidden.value = selectedOption && selectedOption.value
            ? (selectedOption.dataset.label || selectedOption.textContent)
            : "";

        fmTrack("finder_submit", {
            selected_date: String(dataSelect.value || "").trim(),
            selected_time: String(slotSelect.value || "").trim(),
            selected_slot_label: slotLabelHidden.value || ""
        });
    });

    renderRoll("data", [{ value: "", label: "Caricamento date..." }], {
        select: dataSelect
    });

    renderRoll("slot", [{ value: "", label: "Prima scegli la data" }], {
        select: slotSelect
    });

    setRollDisabled("slot", true);

    populateDates();

    fmTrack("finder_view", {
        available_dates: sessions.length
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
<?php
    exit;
});
/**
 * Redirect storico Via Maggio -> Viamaggio
 */
add_action('template_redirect', function () {

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($path === '/foto/via-maggio/' || $path === '/foto/via-maggio') {
        wp_redirect(home_url('/foto/viamaggio/'), 301);
        exit;
    }

}, 1);
