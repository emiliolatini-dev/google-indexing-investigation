. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig
$mysqlArgs = Get-MySqlArgs -Config $config

Assert-PathInside -CandidatePath $config.SqlWorkingCopy -ExpectedRoot $config.ProjectRoot

if (-not (Test-Path -LiteralPath $config.SqlWorkingCopy)) {
    throw "Working SQL dump not found: $($config.SqlWorkingCopy)"
}

Write-Info 'Applying compatibility transforms to the working SQL dump.'
& $config.PhpExe (Join-Path $config.ScriptsRoot 'transform-sql-dump.php') `
    "--source=$($config.SqlWorkingCopy)" `
    "--log=$($config.SqlTransformLog)"
if ($LASTEXITCODE -ne 0) {
    throw "SQL compatibility transform failed with exit code $LASTEXITCODE."
}
Write-Ok 'SQL compatibility transform completed.'

Write-Info "Importing SQL dump into $($config.DbName). This may take a while."
$mysqlForCmd = ('"{0}"' -f $config.MysqlExe)
$sqlForCmd = ('"{0}"' -f $config.SqlWorkingCopy)
$stdoutForCmd = ('"{0}"' -f $config.ImportStdoutLog)
$stderrForCmd = ('"{0}"' -f $config.ImportStderrLog)
$argForCmd = (($mysqlArgs + @($config.DbName)) -join ' ')
$cmdLine = "$mysqlForCmd $argForCmd < $sqlForCmd 1> $stdoutForCmd 2> $stderrForCmd"
cmd /c $cmdLine | Out-Null

if ($LASTEXITCODE -ne 0) {
    throw "MySQL import failed with exit code $LASTEXITCODE."
}

Write-Ok 'Database import completed successfully.'
