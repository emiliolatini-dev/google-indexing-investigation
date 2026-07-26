. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig

Write-Info 'Running read-only preflight summary.'

$summary = [ordered]@{
    ApacheRunning = [bool](Get-Process -Name httpd -ErrorAction SilentlyContinue)
    MariaDbRunning = [bool](Get-Process -Name mysqld -ErrorAction SilentlyContinue)
    PhpExe = $config.PhpExe
    MysqlExe = $config.MysqlExe
    WordPressRootExists = Test-Path -LiteralPath $config.WordPressRoot
    WorkingSqlExists = Test-Path -LiteralPath $config.SqlWorkingCopy
    GoldenBackupRoot = $config.GoldenBackupRoot
    ApacheVhosts = $config.ApacheConf
    HostsFile = $config.HostsFile
}

$summary.GetEnumerator() | ForEach-Object {
    Write-Host ("{0}={1}" -f $_.Key, $_.Value)
}

Write-Ok 'Preflight summary completed.'
