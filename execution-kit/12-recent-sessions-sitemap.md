# Sitemap "ultime sessioni" — canale di scoperta rapida  (roadmap azione 12, P0)

## Perche'
L'index attuale ha 244 file e ~55.360 URL. Le sessioni NUOVE — quelle che devono indicizzarsi
in fretta — sono annegate in mezzo. Una sitemap piccola e dedicata alle ultime 20-30 sessioni,
con `lastmod` accurato, e' un segnale di scoperta molto piu' diretto (master audit §4.2). E'
probabilmente la singola mossa piu' efficace contro il problema di indicizzazione, insieme al
fix del fetch (P0).

## Cosa deve contenere
Solo le **landing sessione** (`/foto/{loc}/{data}/`) delle ultime ~30 sessioni, ordinate per
data decrescente. NON i 53k prodotti. Piccola (< 50 URL), rigenerata a ogni `generate-json`.

## Implementazione (mu-plugin, endpoint dedicato)
```php
// Espone https://fotomoto.click/news-sitemap.xml con le ultime 30 landing sessione.
add_action('init', function () {
    add_rewrite_rule('^news-sitemap\.xml$', 'index.php?fmc_news_sitemap=1', 'top');
    add_rewrite_tag('%fmc_news_sitemap%', '1');
});
add_action('template_redirect', function () {
    if (!get_query_var('fmc_news_sitemap')) return;

    // Adatta la query alla tua struttura: qui assumo le sessioni come termini di una tassonomia
    // 'sessione' o come pagine con data. Sostituisci get_terms/WP_Query con la tua sorgente reale.
    $sessions = get_terms([
        'taxonomy'   => 'product_cat',      // <-- adatta
        'meta_key'   => 'session_date',     // <-- adatta
        'orderby'    => 'meta_value',
        'order'      => 'DESC',
        'number'     => 30,
        'hide_empty' => true,
    ]);

    header('Content-Type: application/xml; charset=UTF-8');
    header('Cache-Control: public, max-age=300, must-revalidate');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($sessions as $s) {
        $loc = get_term_meta($s->term_id, 'session_loc', true);   // <-- adatta
        $date = get_term_meta($s->term_id, 'session_date', true); // formato slug data
        $url  = home_url("/foto/{$loc}/{$date}/");
        $lastmod = gmdate('c', strtotime($date));
        echo "  <url><loc>" . esc_url($url) . "</loc>";
        echo "<lastmod>{$lastmod}</lastmod>";
        echo "<changefreq>daily</changefreq><priority>0.9</priority></url>\n";
    }
    echo "</urlset>\n";
    exit;
});
```
Dopo il deploy: `flush_rewrite_rules()` una volta (Impostazioni → Permalink → Salva).

## Collegamento
- Gia' aggiunta come 2ª riga `Sitemap:` nel `robots.txt` del kit.
- Inviarla in GSC come sitemap separata (§ fix P0, punto C).

## Verifica
```bash
curl -s -A Googlebot https://fotomoto.click/news-sitemap.xml | head -20
curl -sI -A Googlebot https://fotomoto.click/news-sitemap.xml | grep -i cache-control
```
