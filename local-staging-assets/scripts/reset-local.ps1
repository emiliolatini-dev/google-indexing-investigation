. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig
Assert-PathInside -CandidatePath $config.ProjectRoot -ExpectedRoot 'D:\Web-Lab\projects\fotomoto-click'

Write-Warn "This script removes the local project root and drops database $($config.DbName)."
Write-Warn 'The golden backup is never touched by this script.'

$mysqlArgs = Get-MySqlArgs -Config $config
$dropSql = "DROP DATABASE IF EXISTS ``{0}``;" -f $config.DbName
& $config.MysqlExe @mysqlArgs -e $dropSql
Write-Ok "Dropped database $($config.DbName) if it existed."

if (Test-Path -LiteralPath $config.ProjectRoot) {
    Remove-Item -LiteralPath $config.ProjectRoot -Recurse -Force
    Write-Ok "Removed $($config.ProjectRoot)"
}
