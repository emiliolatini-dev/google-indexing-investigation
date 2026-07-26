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
$source = $args['source'] ?? null;
$logFile = $args['log'] ?? null;

if (!$source || !$logFile) {
    fwrite(STDERR, "Missing required arguments.\n");
    exit(1);
}

$log = [];
$patterns = [
    '/^\/\*M!999999\\\\- enable the sandbox mode \*\/\s*/m' => '',
    '/\bDEFINER=`[^`]+`@`[^`]+`/i' => '',
    '/ROW_FORMAT=FIXED/i' => '',
];

$input = fopen($source, 'rb');
if ($input === false) {
    fwrite(STDERR, "Unable to open SQL dump for reading.\n");
    exit(1);
}

$tmp = $source . '.tmp';
$output = fopen($tmp, 'wb');
if ($output === false) {
    fclose($input);
    fwrite(STDERR, "Unable to open temp SQL dump for writing.\n");
    exit(1);
}

$counts = array_fill_keys(array_keys($patterns), 0);

while (($line = fgets($input)) !== false) {
    foreach ($patterns as $pattern => $replacement) {
        $count = 0;
        $line = preg_replace($pattern, $replacement, $line, -1, $count);
        $counts[$pattern] += $count;
    }
    fwrite($output, $line);
}

fclose($input);
fclose($output);

if (!rename($tmp, $source)) {
    @unlink($tmp);
    fwrite(STDERR, "Unable to replace transformed SQL dump.\n");
    exit(1);
}

foreach ($counts as $pattern => $count) {
    $log[] = ['pattern' => $pattern, 'replacements' => $count];
}

file_put_contents($logFile, json_encode([
    'timestamp' => gmdate('c'),
    'transforms' => $log,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "SQL transform completed.\n";
