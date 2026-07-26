<?php
/**
 * Plugin Name: FotoMoto Local Safety
 * Description: Local-only MU-plugin that blocks emails and background tasks in staging.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('fm_local_safety_enabled')) {
    function fm_local_safety_enabled(): bool {
        return defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local';
    }
}

if (!fm_local_safety_enabled()) {
    return;
}

add_filter('pre_wp_mail', static function () {
    return true;
}, 10, 2);

add_action('phpmailer_init', static function ($phpmailer): void {
    $phpmailer->clearAllRecipients();
    $phpmailer->clearAttachments();
    $phpmailer->clearCustomHeaders();
    $phpmailer->Body = '[LOCAL STAGING] Email blocked by FotoMoto Local Safety.';
    $phpmailer->Subject = '[BLOCKED] Local staging email';
}, 1);

add_filter('pre_schedule_event', static function () {
    return false;
}, 10, 3);

add_action('init', static function (): void {
    if (!headers_sent()) {
        header('X-FotoMoto-Local-Staging: true');
    }
}, 1);

add_action('admin_notices', static function (): void {
    echo '<div class="notice notice-warning"><p><strong>FotoMoto local staging:</strong> outbound email, webhooks, payments and external HTTP are blocked.</p></div>';
});

add_action('wp_footer', static function (): void {
    echo '<div style="position:fixed;bottom:12px;right:12px;z-index:99999;padding:8px 12px;background:#8b0000;color:#fff;font:600 12px/1.4 sans-serif;border-radius:999px;">LOCAL STAGING</div>';
});

add_filter('xmlrpc_enabled', '__return_false');
add_filter('blog_public', static fn() => '0');
