# FotoMoto.click Sanitization Report

## Snapshot

- Date: `2026-07-17`
- Environment: local XAMPP staging
- Site URL forced to: `http://fotomoto.local`

## Integrations found

- WooCommerce
- Elementor / Elementor Pro
- FluentSMTP
- WooCommerce Stripe
- WooCommerce Payments
- WooCommerce PayPal Payments
- Google Site Kit
- IndexNow
- LiteSpeed Cache
- Rank Math
- Complianz
- custom FotoMoto MU-plugins

## Plugin bootstrap policy

- no plugin is filtered from the `active_plugins` or network-active plugin options
- production-facing effects are neutralized by the network, email, cron and webhook controls below

## Safety controls applied

- `wp_mail()` intercepted and prevented
- outbound HTTP blocked except local hosts
- `DISABLE_WP_CRON=true`
- `WP_HTTP_BLOCK_EXTERNAL=true`
- `blog_public=0`
- WooCommerce webhooks forced to `disabled`
- pending/in-progress Action Scheduler jobs forced to `canceled`

## HTTP blocker correction

- removed the duplicate MU-plugin `pre_http_request` filter
- retained `WP_HTTP_BLOCK_EXTERNAL=true` and the local-only `WP_ACCESSIBLE_HOSTS` allowlist
- the duplicate filter returned a custom `fotomoto_local_http_blocked` error before WordPress core could run its standard `block_request()` path, emit `http_api_debug`, and return `http_request_not_executed`
- the custom error itself was immediate and did not wait on DNS, TCP or TLS
- Complianz contains an explicit maximum-three-attempt retry on any `WP_Error`; both the custom and native blockers can enter that bounded branch, so it was not the source of a 120-second wait
- WordPress.org translation/update warnings are symptoms of a deliberately blocked request, not socket timeouts
- all plugin-list filters and the local dashboard redirect were removed so the database-active plugin set performs its normal bootstrap

## Measured cause

- clean Apache request: `test-static.txt` returned `200` in `0.160 s`
- clean Apache request: first `wp-login.php` returned `200` in `110.249 s`
- warm Apache request: second `wp-login.php` returned `200` in `92.306 s`
- instrumented full CLI bootstrap: `wp-load.php` completed in `90.816 s` with all `19` database-active plugins loaded
- the external HTTP decision after bootstrap returned `http_request_not_executed` in `0.04 ms`
- the Apache process had no outbound TCP connection during verification
- fatal locations on class declarations and WordPress include lines show that the 120-second budget was being exhausted before the reported line, while loading the complete plugin stack; the HTTP blocker was not consuming that budget
- Zend OPcache is not loaded in this XAMPP PHP installation, which leaves the large plugin stack without a compiled-code cache; this is a separate local-runtime performance risk, not a safety-layer retry

## Sensitive data handling

- production `wp-config.php` backed up locally
- golden SQL kept immutable
- local admin credentials stored only in local docs
- no secrets written into repo-managed workspace files intended for reuse outside staging

## Residual production references

From `logs\search-replace-report.json`:

- `wp_options.option_value`: residual, still needs deeper targeted cleanup
- `wp_postmeta.meta_value`: residual, still needs deeper targeted cleanup
- `wp_posts.post_content`: residual, still needs deeper targeted cleanup
- `wp_fsmpt_email_logs.subject`: historical and non-operational
- `wp_actionscheduler_actions.extended_args`: historical and non-operational

## Open risks

- `hosts` file could not be edited automatically because Windows elevation was denied
- authenticated `wp-admin` browser verification remains incomplete until `fotomoto.local` resolves through `hosts`
- heavy plugin bootstrap paths may still need independent performance analysis if they exceed the PHP limit after the HTTP blocker correction
- one custom MU-plugin path (`fotomoto-dynamic-og-image.php`) still appears in debug traces during some requests

## Checks completed

- immutable backup preserved
- hash verification completed
- WordPress files extracted locally
- MariaDB import completed
- Apache syntax validated
- local safety checks confirmed email and outbound HTTP blocking
- local REST API reachable
- local login page reachable
- one FotoMoto dated gallery route reachable
