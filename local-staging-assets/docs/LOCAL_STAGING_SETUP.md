# FotoMoto.click Local Staging Setup

## Overview

This local staging was prepared on Windows 11 with XAMPP from the immutable backup at `D:\Web-Lab\backups\fotomoto\2026-07-17`.

Local URL target:

- `http://fotomoto.local`

Actual validation date:

- `2026-07-17`

## Local architecture

- XAMPP root: `D:\XAMPP`
- Apache: `D:\XAMPP\apache\bin\httpd.exe`
- MariaDB: `D:\XAMPP\mysql\bin\mysql.exe`
- PHP: `D:\XAMPP\php\php.exe`
- Project root: `D:\Web-Lab\projects\fotomoto-click`
- WordPress root: `D:\Web-Lab\projects\fotomoto-click\wordpress`
- Database: `fotomoto_local`

## Directory layout

- `wordpress`
- `database`
- `scripts`
- `logs`
- `docs`
- `backups`

## Backup origin and hash verification

Verified against the immutable backup:

- `fotomoto-files.tar.gz`
- `fotomoto.sql`

SHA256 matched the expected values during preflight.

## Extraction

- The archive root `public_html/` was extracted into `wordpress`.
- The final structure includes `wp-admin`, `wp-content`, `wp-includes`, and `wp-config.php`.
- The golden backup was never modified.

## Database

- Working SQL copy: `database\fotomoto.sql`
- Import target: `fotomoto_local`
- Compatibility transform applied only to the working copy:
  - removed MariaDB sandbox header line
  - prepared the dump for local import
- Import method that succeeded: direct file redirection into `mysql.exe`

## WordPress local overrides

Applied local-only overrides:

- `wp-config.production-backup.php`
- `wp-config.local.php`
- `wp-content\mu-plugins\fotomoto-local-safety.php`

Key local behaviors:

- `WP_ENVIRONMENT_TYPE=local`
- `DISABLE_WP_CRON=true`
- `WP_HTTP_BLOCK_EXTERNAL=true`
- `WP_HOME` and `WP_SITEURL` forced to `http://fotomoto.local`
- real email blocked
- external HTTP blocked except `127.0.0.1`, `localhost`, `fotomoto.local`
- all database-active plugins remain active during local bootstrap

## Virtual host

Configured in:

- `D:\XAMPP\apache\conf\extra\httpd-vhosts.conf`

Backups created in:

- `backups\httpd-vhosts.YYYYMMDD-HHMMSS.conf.bak`
- `backups\hosts.YYYYMMDD-HHMMSS.bak`

Apache syntax check result:

- `Syntax OK`

## Hosts file

The automatic write to `C:\Windows\System32\drivers\etc\hosts` was blocked by Windows permissions.

Manual line still required:

- `127.0.0.1 fotomoto.local`

## Sanitization highlights

- emails blocked via MU-plugin `pre_wp_mail`
- outbound HTTP blocked by WordPress core via `WP_HTTP_BLOCK_EXTERNAL` and `WP_ACCESSIBLE_HOSTS`
- no plugin is filtered from `active_plugins`; payment, SMTP, indexing, cache and SEO code can bootstrap normally
- automatic cron disabled
- search engines discouraged via `blog_public=0`

## Search and replace

Search-replace was intentionally conservative.

Completed safely:

- core URL options updated to `http://fotomoto.local`
- selected serialized and plain-content columns updated

Residual references remain in:

- `wp_options`
- `wp_postmeta`
- `wp_posts`
- `wp_fsmpt_email_logs`
- `wp_actionscheduler_actions`

See `SANITIZATION_REPORT.md` and `logs\search-replace-report.json`.

## Validation summary

Confirmed directly:

- MariaDB import completed
- `wp-login.php` returns 200 locally
- `wp-json/` returns 200 locally
- homepage returned 200 after local runtime lightening
- gallery route `/foto/bocca-serriola/05-07-2026/` returned 200 locally
- safety helper confirmed:
  - `wp_mail()` intercepted
  - external HTTP blocked
  - cron disabled

Not fully completed automatically:

- authenticated browser-level `wp-admin` verification through `fotomoto.local`
- `hosts` write, due Windows elevation requirement

## Reset and rollback

Use:

- `scripts\reset-local.ps1`

This removes only:

- `D:\Web-Lab\projects\fotomoto-click`
- local database `fotomoto_local`

It must never touch the immutable backup.

## Known gaps versus production

- local `hosts` mapping still needs a manual admin edit
- some production URLs remain in non-critical historical data
- the duplicate `pre_http_request` blocker was removed because it bypassed the WordPress core HTTP-blocking path and could trigger plugin retry/fallback logic
- measured after the correction: full plugin bootstrap `90.816 s`, native external-block decision `0.04 ms`
- the remaining slow bootstrap is independent of the HTTP blocker; XAMPP PHP currently has no Zend OPcache loaded
- some frontend/backend paths still require manual browser verification after the `hosts` entry is added
