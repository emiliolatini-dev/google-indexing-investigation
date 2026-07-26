. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig
$mysqlArgs = Get-MySqlArgs -Config $config

Write-Info 'Running WordPress verification helper.'
& $config.PhpExe (Join-Path $config.ScriptsRoot 'verify-wordpress-local.php') `
    "--path=$($config.WordPressRoot)" `
    "--site-url=$($config.SiteUrl)" `
    "--report=$($config.VerifyReport)"
Write-Ok 'PHP-side verification completed.'

Write-Info 'Checking required database tables.'
$tableSql = "SHOW TABLES FROM ``{0}`` LIKE 'wp_%';" -f $config.DbName
$tables = & $config.MysqlExe @mysqlArgs -N -e $tableSql
$required = @(
    'wp_options',
    'wp_posts',
    'wp_postmeta',
    'wp_users',
    'wp_usermeta'
)

foreach ($table in $required) {
    if ($tables -contains $table) {
        Write-Ok "Found $table"
    }
    else {
        Write-Warn "Missing $table"
    }
}
