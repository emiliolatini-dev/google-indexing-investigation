. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig

Write-Info 'Running serialized-safe search-replace and local sanitization.'
& $config.PhpExe (Join-Path $config.ScriptsRoot 'configure-wordpress-local.php') `
    "--path=$($config.WordPressRoot)" `
    "--site-url=$($config.SiteUrl)" `
    "--db-name=$($config.DbName)" `
    "--db-user=$($config.DbUser)" `
    "--db-password=$($config.DbPassword)" `
    "--db-host=$($config.DbHost)" `
    "--report=$($config.ConfigureReport)" `
    "--admin-file=$($config.LocalAdminFile)" `
    "--search-report=$($config.SearchReplaceReport)"
Write-Ok 'Local WordPress configuration completed.'
