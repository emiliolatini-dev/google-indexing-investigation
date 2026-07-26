. "$PSScriptRoot\shared.ps1"

$config = Get-FotomotoConfig
Assert-PathInside -CandidatePath $config.ProjectRoot -ExpectedRoot 'D:\Web-Lab\projects\fotomoto-click'

Write-Info 'Ensuring local staging directory structure exists.'
$paths = @(
    $config.ProjectRoot,
    $config.WordPressRoot,
    $config.DatabaseRoot,
    $config.ScriptsRoot,
    $config.LogsRoot,
    $config.DocsRoot,
    $config.BackupsRoot
)

foreach ($path in $paths) {
    if (-not (Test-Path -LiteralPath $path)) {
        New-Item -ItemType Directory -Path $path | Out-Null
        Write-Ok "Created $path"
    }
}

if (-not (Test-Path -LiteralPath $config.SqlWorkingCopy)) {
    Copy-Item -LiteralPath (Join-Path $config.GoldenBackupRoot 'fotomoto.sql') -Destination $config.SqlWorkingCopy -Force
    Write-Ok "Copied working SQL dump to $($config.SqlWorkingCopy)"
}

Write-Warn 'This script does not re-extract the tar if WordPress files already exist. Re-run manual extraction only when needed.'
