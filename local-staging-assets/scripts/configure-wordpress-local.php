<?php
declare(strict_types=1);

set_time_limit(0);

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

function deep_replace(mixed $value, array $replacements, int &$changes): mixed {
    if (is_string($value)) {
        $updated = strtr($value, $replacements);
        if ($updated !== $value) {
            $changes++;
        }
        return $updated;
    }

    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = deep_replace($item, $replacements, $changes);
        }
        return $value;
    }

    if (is_object($value)) {
        if ($value instanceof __PHP_Incomplete_Class) {
            return $value;
        }
        foreach (get_object_vars($value) as $key => $item) {
            $value->{$key} = deep_replace($item, $replacements, $changes);
        }
        return $value;
    }

    return $value;
}

function replace_preserving_serialization(?string $value, array $replacements, int &$changes): ?string {
    if ($value === null || $value === '') {
        return $value;
    }

    $unserialized = @unserialize($value);
    if ($unserialized !== false || $value === 'b:0;') {
        $updated = deep_replace($unserialized, $replacements, $changes);
        return serialize($updated);
    }

    $updated = strtr($value, $replacements);
    if ($updated !== $value) {
        $changes++;
    }
    return $updated;
}

function write_credentials(string $path, string $username, string $password, string $siteUrl): void {
    $lines = [
        'This file is local-only and must not be copied to production.',
        'URL: ' . $siteUrl . '/wp-login.php',
        'Username: ' . $username,
        'Password: ' . $password,
        'Generated: ' . date('c'),
        '',
        'Delete or rotate this password after testing if desired.',
    ];
    file_put_contents($path, implode(PHP_EOL, $lines) . PHP_EOL);
}

function run_serialized_updates(wpdb $wpdb, array $jobs, array $replacements): array {
    $stats = [];

    foreach ($jobs as $job) {
        $table = $job['table'];
        $pk = $job['pk'];
        $column = $job['column'];
        $rows = $wpdb->get_results(
            "SELECT `{$pk}` AS row_id, `{$column}` AS target_value FROM `{$table}` WHERE `{$column}` LIKE '%fotomoto.click%'",
            ARRAY_A
        );

        if (!$rows) {
            continue;
        }

        $rowCount = 0;
        $replaceCount = 0;
        foreach ($rows as $row) {
            $changes = 0;
            $updated = replace_preserving_serialization((string) $row['target_value'], $replacements, $changes);
            if ($changes > 0 && $updated !== $row['target_value']) {
                $wpdb->update($table, [$column => $updated], [$pk => $row['row_id']]);
                $rowCount++;
                $replaceCount += $changes;
            }
        }

        $stats[] = [
            'table' => $table,
            'column' => $column,
            'rows_updated' => $rowCount,
            'replacement_count' => $replaceCount,
            'mode' => 'serialized-safe',
        ];
    }

    return $stats;
}

function run_plain_updates(wpdb $wpdb, array $jobs, array $replacements): array {
    $stats = [];

    foreach ($jobs as $job) {
        $table = $job['table'];
        $column = $job['column'];
        $set = "`{$column}`";
        foreach ($replacements as $old => $new) {
            $set = sprintf("REPLACE(%s, '%s', '%s')", $set, esc_sql($old), esc_sql($new));
        }

        $sql = "UPDATE `{$table}` SET `{$column}` = {$set} WHERE `{$column}` LIKE '%fotomoto.click%'";
        $wpdb->query($sql);
        $stats[] = [
            'table' => $table,
            'column' => $column,
            'rows_updated' => $wpdb->rows_affected,
            'mode' => 'plain-sql',
        ];
    }

    return $stats;
}

function residual_count(wpdb $wpdb, string $table, string $column): int {
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` LIKE '%fotomoto.click%'");
}

$args = parse_args($argv);
$wpPath = $args['path'] ?? null;
$siteUrl = $args['site-url'] ?? null;
$reportFile = $args['report'] ?? null;
$adminFile = $args['admin-file'] ?? null;
$searchReportFile = $args['search-report'] ?? null;

if (!$wpPath || !$siteUrl || !$reportFile || !$adminFile || !$searchReportFile) {
    fwrite(STDERR, "Missing required arguments.\n");
    exit(1);
}

chdir($wpPath);
require $wpPath . '/wp-load.php';

global $wpdb;

$replacements = [
    'https://fotomoto.click' => $siteUrl,
    'http://fotomoto.click' => $siteUrl,
    'https://www.fotomoto.click' => $siteUrl,
    'http://www.fotomoto.click' => $siteUrl,
];

$serializedJobs = [
    ['table' => $wpdb->options, 'pk' => 'option_id', 'column' => 'option_value'],
    ['table' => $wpdb->postmeta, 'pk' => 'meta_id', 'column' => 'meta_value'],
    ['table' => $wpdb->usermeta, 'pk' => 'umeta_id', 'column' => 'meta_value'],
    ['table' => $wpdb->commentmeta, 'pk' => 'meta_id', 'column' => 'meta_value'],
    ['table' => $wpdb->prefix . 'woocommerce_order_itemmeta', 'pk' => 'meta_id', 'column' => 'meta_value'],
];

$plainJobs = [
    ['table' => $wpdb->posts, 'column' => 'post_content'],
    ['table' => $wpdb->posts, 'column' => 'post_excerpt'],
    ['table' => $wpdb->posts, 'column' => 'pinged'],
    ['table' => $wpdb->posts, 'column' => 'to_ping'],
    ['table' => $wpdb->comments, 'column' => 'comment_content'],
];

$searchStats = array_merge(
    run_serialized_updates($wpdb, $serializedJobs, $replacements),
    run_plain_updates($wpdb, $plainJobs, $replacements)
);

update_option('siteurl', $siteUrl);
update_option('home', $siteUrl);
update_option('blog_public', '0');
update_option('admin_email', 'local-admin@fotomoto.local');

$webhookTable = $wpdb->prefix . 'wc_webhooks';
if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $webhookTable)) === $webhookTable) {
    $wpdb->query("UPDATE `{$webhookTable}` SET status = 'disabled'");
}

$actionsTable = $wpdb->prefix . 'actionscheduler_actions';
if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $actionsTable)) === $actionsTable) {
    $wpdb->query("UPDATE `{$actionsTable}` SET status = 'canceled' WHERE status IN ('pending','in-progress')");
}

$username = 'fotomoto-local-admin';
$password = wp_generate_password(24, true, true);
$existing = get_user_by('login', $username);
if (!$existing) {
    $userId = wp_create_user($username, $password, 'local-admin@fotomoto.local');
    if (is_wp_error($userId)) {
        throw new RuntimeException($userId->get_error_message());
    }
    $user = new WP_User($userId);
    $user->set_role('administrator');
} else {
    wp_set_password($password, $existing->ID);
    $user = $existing;
    $user->set_role('administrator');
}

write_credentials($adminFile, $username, $password, $siteUrl);

$residualChecks = [];
foreach ([
    [$wpdb->options, 'option_value', 'needed'],
    [$wpdb->postmeta, 'meta_value', 'needed'],
    [$wpdb->posts, 'post_content', 'needed'],
    [$wpdb->prefix . 'fsmpt_email_logs', 'subject', 'innocuous'],
    [$wpdb->prefix . 'fsmpt_email_logs', 'content', 'risky'],
    [$wpdb->prefix . 'actionscheduler_actions', 'extended_args', 'innocuous'],
] as $check) {
    [$table, $column, $classification] = $check;
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        continue;
    }
    $count = residual_count($wpdb, $table, $column);
    if ($count > 0) {
        $residualChecks[] = [
            'table' => $table,
            'column' => $column,
            'count' => $count,
            'classification' => $classification,
        ];
    }
}

file_put_contents($searchReportFile, json_encode([
    'timestamp' => gmdate('c'),
    'site_url' => $siteUrl,
    'operations' => $searchStats,
    'residual_hits' => $residualChecks,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

file_put_contents($reportFile, json_encode([
    'timestamp' => gmdate('c'),
    'site_url' => $siteUrl,
    'admin_user' => $username,
    'webhooks_disabled' => $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $webhookTable)) === $webhookTable,
    'actionscheduler_sanitized' => $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $actionsTable)) === $actionsTable,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Local configuration completed.\n";
