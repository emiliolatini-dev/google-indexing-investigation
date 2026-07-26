. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig
$wp = $config.WordPressRoot
$scripts = $config.ScriptsRoot
$docs = $config.DocsRoot
$mu = Join-Path $wp 'wp-content\mu-plugins'

if (-not (Test-Path -LiteralPath $mu)) {
    New-Item -ItemType Directory -Path $mu | Out-Null
}

Copy-Item -LiteralPath (Join-Path $PSScriptRoot '*') -Destination $scripts -Recurse -Force
Copy-Item -LiteralPath (Join-Path (Split-Path $PSScriptRoot -Parent) 'docs\*') -Destination $docs -Recurse -Force
Copy-Item -LiteralPath (Join-Path (Split-Path $PSScriptRoot -Parent) 'wordpress\fotomoto-local-safety.php') -Destination (Join-Path $mu 'fotomoto-local-safety.php') -Force
Copy-Item -LiteralPath (Join-Path (Split-Path $PSScriptRoot -Parent) 'wordpress\wp-config.local.php') -Destination (Join-Path $wp 'wp-config.local.php') -Force

Copy-Item -LiteralPath (Join-Path $wp 'wp-config.php') -Destination (Join-Path $wp 'wp-config.production-backup.php') -Force
Copy-Item -LiteralPath (Join-Path $wp '.htaccess') -Destination (Join-Path $wp '.htaccess.production-backup') -Force

$configPath = Join-Path $wp 'wp-config.php'
$configContent = Get-Content -LiteralPath $configPath -Raw
$configContent = [regex]::Replace($configContent, "define\(\s*'DB_NAME'\s*,\s*'[^']*'\s*\);", "define( 'DB_NAME', 'fotomoto_local' );")
$configContent = [regex]::Replace($configContent, "define\(\s*'DB_USER'\s*,\s*'[^']*'\s*\);", "define( 'DB_USER', 'root' );")
$configContent = [regex]::Replace($configContent, "define\(\s*'DB_PASSWORD'\s*,\s*'[^']*'\s*\);", "define( 'DB_PASSWORD', '' );")
$configContent = [regex]::Replace($configContent, "define\(\s*'DB_HOST'\s*,\s*'[^']*'\s*\);", "define( 'DB_HOST', '127.0.0.1' );")
$configContent = [regex]::Replace($configContent, "define\(\s*'DB_CHARSET'\s*,\s*'[^']*'\s*\);", "define( 'DB_CHARSET', 'utf8mb4' );")

$replacementBlock = @"
define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '814658751b6653894221a90245b047a2' );
define( 'WP_MEMORY_LIMIT', '1024M' );
define( 'WP_MAX_MEMORY_LIMIT', '1024M' );
if ( file_exists( __DIR__ . '/wp-config.local.php' ) ) {
	require __DIR__ . '/wp-config.local.php';
}
"@

$configContent = [regex]::Replace(
    $configContent,
    "if \( ! defined\( 'WP_DEBUG' \) \) \s*\{.*?define\('WP_MAX_MEMORY_LIMIT', '1024M'\);",
    $replacementBlock,
    [System.Text.RegularExpressions.RegexOptions]::Singleline
)
Set-Content -LiteralPath $configPath -Value $configContent -Encoding UTF8

$htaccessContent = @'
# Local staging .htaccess adapted from production backup.
<Files "xmlrpc.php">
    Require all denied
</Files>

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^gallerie-foto$ /passi-e-valichi/ [R=301,L]
RewriteRule ^gallerie-foto/$ /passi-e-valichi/ [R=301,L]
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
'@
Set-Content -LiteralPath (Join-Path $wp '.htaccess') -Value $htaccessContent -Encoding UTF8

[pscustomobject]@{
    ScriptsCopied = (Get-ChildItem -File $scripts | Measure-Object).Count
    DocsCopied = (Get-ChildItem -File $docs | Measure-Object).Count
    MuPluginExists = Test-Path -LiteralPath (Join-Path $mu 'fotomoto-local-safety.php')
    ConfigBackupExists = Test-Path -LiteralPath (Join-Path $wp 'wp-config.production-backup.php')
    HtaccessBackupExists = Test-Path -LiteralPath (Join-Path $wp '.htaccess.production-backup')
}
