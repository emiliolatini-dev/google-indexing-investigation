Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Write-Info {
    param([string]$Message)
    Write-Host "[INFO] $Message"
}

function Write-Ok {
    param([string]$Message)
    Write-Host "[OK] $Message"
}

function Write-Warn {
    param([string]$Message)
    Write-Host "[WARN] $Message"
}

function Write-ErrorLog {
    param([string]$Message)
    Write-Host "[ERROR] $Message"
}

function Assert-PathInside {
    param(
        [Parameter(Mandatory = $true)][string]$CandidatePath,
        [Parameter(Mandatory = $true)][string]$ExpectedRoot
    )

    $resolvedCandidate = [System.IO.Path]::GetFullPath($CandidatePath)
    $resolvedRoot = [System.IO.Path]::GetFullPath($ExpectedRoot)
    if (-not $resolvedCandidate.StartsWith($resolvedRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Path '$resolvedCandidate' is outside allowed root '$resolvedRoot'."
    }
}

function Get-FotomotoConfig {
    param(
        [string]$ProjectRoot = 'D:\Web-Lab\projects\fotomoto-click',
        [string]$XamppRoot = 'D:\XAMPP',
        [string]$GoldenBackupRoot = 'D:\Web-Lab\backups\fotomoto\2026-07-17',
        [string]$DbName = 'fotomoto_local',
        [string]$DbUser = 'root',
        [string]$DbPassword = '',
        [string]$DbHost = '127.0.0.1',
        [string]$SiteUrl = 'http://fotomoto.local'
    )

    $wordpressRoot = Join-Path $ProjectRoot 'wordpress'
    $databaseRoot = Join-Path $ProjectRoot 'database'
    $scriptsRoot = Join-Path $ProjectRoot 'scripts'
    $logsRoot = Join-Path $ProjectRoot 'logs'
    $docsRoot = Join-Path $ProjectRoot 'docs'
    $backupsRoot = Join-Path $ProjectRoot 'backups'

    [pscustomobject]@{
        ProjectRoot = $ProjectRoot
        WordPressRoot = $wordpressRoot
        DatabaseRoot = $databaseRoot
        ScriptsRoot = $scriptsRoot
        LogsRoot = $logsRoot
        DocsRoot = $docsRoot
        BackupsRoot = $backupsRoot
        GoldenBackupRoot = $GoldenBackupRoot
        SqlWorkingCopy = Join-Path $databaseRoot 'fotomoto.sql'
        SqlTransformLog = Join-Path $logsRoot 'sql-transform.log'
        ImportStdoutLog = Join-Path $logsRoot 'mysql-import.stdout.log'
        ImportStderrLog = Join-Path $logsRoot 'mysql-import.stderr.log'
        SearchReplaceReport = Join-Path $logsRoot 'search-replace-report.json'
        ConfigureReport = Join-Path $logsRoot 'configure-local-report.json'
        VerifyReport = Join-Path $logsRoot 'verify-local-report.json'
        LocalAdminFile = Join-Path $docsRoot 'LOCAL_ADMIN_CREDENTIALS.txt'
        XamppRoot = $XamppRoot
        ApacheBin = Join-Path $XamppRoot 'apache\bin\httpd.exe'
        ApacheConf = Join-Path $XamppRoot 'apache\conf\extra\httpd-vhosts.conf'
        ApacheMainConf = Join-Path $XamppRoot 'apache\conf\httpd.conf'
        HostsFile = 'C:\Windows\System32\drivers\etc\hosts'
        PhpExe = Join-Path $XamppRoot 'php\php.exe'
        MysqlExe = Join-Path $XamppRoot 'mysql\bin\mysql.exe'
        MysqlAdminExe = Join-Path $XamppRoot 'mysql\bin\mysqladmin.exe'
        DbName = $DbName
        DbUser = $DbUser
        DbPassword = $DbPassword
        DbHost = $DbHost
        SiteUrl = $SiteUrl
    }
}

function Get-MySqlArgs {
    param([Parameter(Mandatory = $true)]$Config)

    $args = @(
        '--protocol=TCP',
        "--host=$($Config.DbHost)",
        "--user=$($Config.DbUser)",
        '--default-character-set=utf8mb4',
        '--binary-mode=1'
    )

    if ($Config.DbPassword -ne '') {
        $args += "--password=$($Config.DbPassword)"
    }

    return $args
}
