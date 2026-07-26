<?php
declare(strict_types=1);

function parse_args(array $argv): array {
    $args = [];
    foreach ($argv as $arg) {
        if (strpos($arg, '--') !== 0) {
            continue;
        }
        $parts = explode('=', substr($arg, 2), 2);
        $args[$parts[0]] = $parts[1] ?? '1';
    }
    return $args;
}

$args = parse_args($argv);
$wpPath = $args['path'] ?? null;
$siteUrl = $args['site-url'] ?? null;
$reportFile = $args['report'] ?? null;

if (!$wpPath || !$siteUrl || !$reportFile) {
    fwrite(STDERR, "Missing required arguments.\n");
    exit(1);
}

chdir($wpPath);
require $wpPath . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

global $wpdb;

$activePlugins = (array) get_option('active_plugins', []);
$muPlugins = get_mu_plugins();

$report = [
    'timestamp' => gmdate('c'),
    'siteurl' => get_option('siteurl'),
    'home' => get_option('home'),
    'blog_public' => get_option('blog_public'),
    'active_plugins' => $activePlugins,
    'mu_plugins' => array_keys((array) $muPlugins),
    'can_send_mail' => has_filter('pre_wp_mail'),
    'http_block_external' => defined('WP_HTTP_BLOCK_EXTERNAL') ? WP_HTTP_BLOCK_EXTERNAL : null,
    'environment_type' => function_exists('wp_get_environment_type') ? wp_get_environment_type() : null,
    'table_count' => (int) $wpdb->get_var('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()'),
];

file_put_contents($reportFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Verification report completed.\n";
