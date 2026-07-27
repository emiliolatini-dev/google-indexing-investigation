# Test alias sitemap — URL vergine per forzare una lettura da parte di Google

> Creato il 27-07-2026. Esperimento previsto da [H-007/H-009](../registry/hypotheses.md),
> a valle di [E-015](../evidence/gsc/E-015_2026-07-27_gsc-ga4-crossread.md).
> **Reversibile:** si rimuove cancellando le righe aggiunte.

## Perché

I log del server dicono che in 9 giorni **Googlebot non ha mai richiesto una sitemap**
(0 richieste, contro 70-260/giorno di Bingbot). Le uniche 4 richieste di Google sono
`Google-InspectionTool` del 26/07, tutte con risposta **200**. Il file è sano e Google non è
bloccato: semplicemente non lo chiede. Lo stato "Impossibile recuperare/leggere" in GSC è
quindi uno **stato persistito**, non l'esito di una lettura recente.

Serve un URL che Google non ha mai visto, **senza redirect e senza storia in GSC**, per
verificare se una sitemap nuova viene letta.

> Il test del 27/07 con `https://fotomoto.click/sitemap.xml` **non è valido**: quell'URL
> risponde `301` verso `sitemap_index.xml`. Voce rimossa da GSC lo stesso giorno.

## Requisito

L'URL nuovo deve rispondere **200 diretto** con `Content-Type: text/xml`. Niente 301.

---

## La strada giusta: mu-plugin, non `.htaccess`

> **Scartata l'ipotesi iniziale del rewrite in `.htaccess`.** Un rewrite interno
> (`RewriteRule ^sitemap-fmc\.xml$ /sitemap_index.xml [L]`) cambia il path sul filesystem ma
> lascia `REQUEST_URI` invariato, e **WordPress instrada su `REQUEST_URI`**: il risultato
> sarebbe un 404, non la sitemap. L'`.htaccess` non va toccato — la regola catch-all di
> WordPress (`RewriteRule . /index.php [L]`) già manda `/sitemap-fmc.xml` a `index.php`,
> perché non è un file reale.

**Query var di Rank Math, verificata il 27-07-2026:**

```
https://fotomoto.click/?sitemap=1              -> 200, text/xml, 244 voci <sitemap>
https://fotomoto.click/?sitemap=page&sitemap_n=1 -> 200, text/xml, urlset
```

Serve quindi solo insegnare a WordPress a mappare il path nuovo su quella query var.

File da installare: [`fmc-sitemap-alias.php`](fmc-sitemap-alias.php) →
`/home/fotomoto.click/public_html/wp-content/mu-plugins/fmc-sitemap-alias.php`

```php
add_action( 'init', function () {
	add_rewrite_rule( '^sitemap-fmc\.xml$', 'index.php?sitemap=1', 'top' );
	if ( '1' !== get_option( 'fmc_sitemap_alias_flushed' ) ) {
		flush_rewrite_rules( false );
		update_option( 'fmc_sitemap_alias_flushed', '1', false );
	}
}, 20 );
```

Il flush è protetto da un'option perché rigenerare le rewrite rules a ogni caricamento è
costoso.

### Verifica obbligatoria dopo l'applicazione

```bash
curl -sSI -A "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" https://fotomoto.click/sitemap-fmc.xml | head -8
```

Deve mostrare:
- `HTTP/2 200` — **non** 301, **non** 404
- `content-type: text/xml; charset=UTF-8`

E il contenuto deve essere l'index con 244 voci:

```bash
curl -sS --compressed https://fotomoto.click/sitemap-fmc.xml | grep -c "<sitemap>"
```

Atteso: `244`.

**Se esce 301, 404 o `0` voci, non inviare nulla a GSC** — l'esperimento sarebbe di nuovo
sporco. In quel caso il flush delle rewrite rules non è andato a buon fine: rigenerarle da
WP admin → Impostazioni → Permalink → Salva.

### Rimozione a test concluso

1. Cancellare `wp-content/mu-plugins/fmc-sitemap-alias.php`.
2. `wp option delete fmc_sitemap_alias_flushed && wp rewrite flush`
   (in alternativa: Impostazioni → Permalink → Salva).

---

## Watch sul log — da lanciare prima dell'invio in GSC

Registra ogni richiesta di Google alle sitemap, in background, senza occupare il terminale:

```bash
nohup bash -c 'tail -F /home/fotomoto.click/logs/fotomoto.click.access_log | grep --line-buffered -i "sitemap" | grep --line-buffered "^\"66\.249\." >> /home/fotomoto.click/logs/GOOGLE-SITEMAP-WATCH.log' >/dev/null 2>&1 &
```

Lettura dei risultati:

```bash
cat /home/fotomoto.click/logs/GOOGLE-SITEMAP-WATCH.log
```

Spegnere il watch a test concluso:

```bash
pkill -f "GOOGLE-SITEMAP-WATCH" ; pkill -f "tail -F /home/fotomoto.click/logs/fotomoto.click.access_log"
```

---

## Sequenza

1. Backup `.htaccess`.
2. Applicare l'opzione A.
3. **Verificare con i due comandi curl.** Se non è 200 con 244 voci, fermarsi.
4. Avviare il watch sul log.
5. Inviare `https://fotomoto.click/sitemap-fmc.xml` in GSC.
6. Attendere 24-48 h e leggere il watch.

## Come si legge l'esito

| Nel watch | In GSC | Lettura |
|---|---|---|
| Compare una richiesta da `66.249.*` | "Riuscito", N pagine | **Lo stato sull'URL storico era bloccato.** Si migra la sitemap sul nuovo URL, si aggiorna `robots.txt` e si rimuove la vecchia voce. |
| Compare una richiesta da `66.249.*` | ancora "Impossibile leggere" | Google riceve qualcosa di diverso da quello che serviamo. Si cattura la risposta esatta con quel Ray ID / timestamp. |
| **Nessuna richiesta** | qualsiasi stato | Google non interroga le sitemap di questa proprietà. Problema a livello di proprietà GSC: si passa alla segnalazione al supporto Google, e le sitemap smettono di essere una leva su cui investire. |

## Nota di priorità

Qualunque sia l'esito, va tenuto presente il contesto misurato: senza alcuna sitemap letta,
Google indicizza comunque 4.822 pagine e porta 9.960 clic in 3 mesi. La sitemap non è la leva
più grande disponibile — restano tali le azioni della Fase 3 del
[piano operativo](../seo-audit/2026-07-27-piano-operativo.md).
