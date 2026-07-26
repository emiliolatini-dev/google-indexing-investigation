. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig
$mysqlArgs = Get-MySqlArgs -Config $config

Write-Info "Ensuring MariaDB is reachable at $($config.DbHost)."
& $config.MysqlAdminExe @mysqlArgs ping | Out-Null
Write-Ok 'MariaDB ping succeeded.'

$createSql = "CREATE DATABASE IF NOT EXISTS ``{0}`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;" -f $config.DbName

Write-Info "Creating database $($config.DbName) if missing."
& $config.MysqlExe @mysqlArgs -e $createSql
Write-Ok "Database $($config.DbName) is ready."
