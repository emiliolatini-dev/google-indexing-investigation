param(
    [Parameter(Mandatory = $true)]
    [string]$OutputFile,

    [switch]$SkipApacheRestart
)

$ErrorActionPreference = 'Stop'

$wordpressRoot = 'D:\Web-Lab\projects\fotomoto-click\wordpress'
$apacheExe = 'D:\XAMPP\apache\bin\httpd.exe'
$apacheConfig = 'D:/XAMPP/apache/conf/httpd.conf'
$curlExe = 'C:\Windows\System32\curl.exe'
$mysqlExe = 'D:\XAMPP\mysql\bin\mysql.exe'
$logsRoot = 'D:\Web-Lab\projects\fotomoto-click\logs'
$curlOutput = Join-Path $logsRoot 'filesystem-probe-curl.out'
$curlError = Join-Path $logsRoot 'filesystem-probe-curl.err'

function Measure-PhpEnumeration {
    $stopwatch = [Diagnostics.Stopwatch]::StartNew()
    $count = 0L
    $bytes = 0L

    foreach ($path in [IO.Directory]::EnumerateFiles($wordpressRoot, '*.php', [IO.SearchOption]::AllDirectories)) {
        $count++
        $bytes += ([IO.FileInfo]$path).Length
    }

    $stopwatch.Stop()
    return [pscustomobject]@{
        count = $count
        bytes = $bytes
        seconds = [Math]::Round($stopwatch.Elapsed.TotalSeconds, 3)
    }
}

function Get-MySqlStatus {
    $rows = & $mysqlExe --host=127.0.0.1 --user=root --default-character-set=utf8mb4 `
        --batch --skip-column-names --database=fotomoto_local `
        --execute="SHOW GLOBAL STATUS WHERE Variable_name IN ('Questions','Slow_queries','Threads_connected','Bytes_received','Bytes_sent');"

    $status = @{}
    foreach ($row in $rows) {
        $parts = $row -split "`t", 2
        if ($parts.Count -eq 2) {
            $status[$parts[0]] = [long]$parts[1]
        }
    }

    return $status
}

$enumerationCold = Measure-PhpEnumeration
$enumerationWarm = Measure-PhpEnumeration

if (-not $SkipApacheRestart) {
    Get-Process httpd -ErrorAction SilentlyContinue | Stop-Process -Force
    $stopDeadline = (Get-Date).AddSeconds(15)
    while (@(Get-Process httpd -ErrorAction SilentlyContinue).Count -gt 0 -and (Get-Date) -lt $stopDeadline) {
        Start-Sleep -Milliseconds 250
    }
    if (@(Get-Process httpd -ErrorAction SilentlyContinue).Count -gt 0) {
        throw 'Apache processes did not stop within 15 seconds.'
    }
    Start-Sleep -Seconds 2
    Start-Process -FilePath $apacheExe -ArgumentList '-f', $apacheConfig -WindowStyle Hidden
    Start-Sleep -Seconds 5
}

$mysqlBefore = Get-MySqlStatus
$arguments = @(
    '--noproxy', '*',
    '--resolve', 'fotomoto.local:80:127.0.0.1',
    '--max-time', '135',
    '--silent', '--show-error',
    '--output', 'NUL',
    '--write-out', '%{http_code}|%{time_total}|%{size_download}',
    'http://fotomoto.local/wp-login.php'
)

$curlProcess = Start-Process -FilePath $curlExe -ArgumentList $arguments -NoNewWindow -PassThru `
    -RedirectStandardOutput $curlOutput -RedirectStandardError $curlError
$samples = [Collections.Generic.List[object]]::new()
$sampleNumber = 0

while (-not $curlProcess.HasExited) {
    $disk = Get-CimInstance Win32_PerfFormattedData_PerfDisk_PhysicalDisk |
        Where-Object { $_.Name -eq '1 D:' } |
        Select-Object -First 1
    $processes = Get-CimInstance Win32_PerfFormattedData_PerfProc_Process |
        Where-Object { $_.Name -match '^(httpd|mysqld|MsMpEng|SearchIndexer)' } |
        Select-Object Name, IDProcess, PercentProcessorTime, IOReadBytesPerSec, IODataBytesPerSec, WorkingSetPrivate

    $externalConnections = @()
    if (($sampleNumber % 5) -eq 0) {
        $httpdIds = @(Get-Process httpd -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Id)
        $externalConnections = @(Get-NetTCPConnection -ErrorAction SilentlyContinue |
            Where-Object {
                $_.OwningProcess -in $httpdIds -and
                $_.State -ne 'Listen' -and
                $_.RemoteAddress -notin @('127.0.0.1', '::1', '0.0.0.0', '::')
            } |
            Select-Object State, LocalAddress, LocalPort, RemoteAddress, RemotePort, OwningProcess)
    }

    $samples.Add([pscustomobject]@{
        timestamp = (Get-Date).ToString('o')
        disk = if ($disk) {
            [pscustomobject]@{
                percent_disk_time = [long]$disk.PercentDiskTime
                reads_per_second = [long]$disk.DiskReadsPerSec
                read_bytes_per_second = [long]$disk.DiskReadBytesPerSec
                avg_seconds_per_read = [double]$disk.AvgDiskSecPerRead
                queue_length = [long]$disk.CurrentDiskQueueLength
            }
        } else { $null }
        processes = @($processes)
        external_connections = $externalConnections
    })

    $sampleNumber++
    Start-Sleep -Seconds 1
    $curlProcess.Refresh()
}

$mysqlAfter = Get-MySqlStatus
$curlStdout = ''
$curlStderr = ''
if (Test-Path -LiteralPath $curlOutput) {
    $content = Get-Content -LiteralPath $curlOutput -Raw
    if ($null -ne $content) {
        $curlStdout = ([string]$content).Trim()
    }
}
if (Test-Path -LiteralPath $curlError) {
    $content = Get-Content -LiteralPath $curlError -Raw
    if ($null -ne $content) {
        $curlStderr = ([string]$content).Trim()
    }
}
$measurement = [regex]::Match($curlStdout, '^(?<status>\d{3})\|(?<seconds>\d+(?:\.\d+)?)\|(?<size>\d+)$')

$report = [pscustomobject]@{
    generated_at = (Get-Date).ToString('o')
    wordpress_root = $wordpressRoot
    php_enumeration_first = $enumerationCold
    php_enumeration_second = $enumerationWarm
    request = [pscustomobject]@{
        path = '/wp-login.php'
        status = if ($measurement.Success) { $measurement.Groups['status'].Value } else { $null }
        total_seconds = if ($measurement.Success) { [double]::Parse($measurement.Groups['seconds'].Value, [Globalization.CultureInfo]::InvariantCulture) } else { $null }
        size_download = if ($measurement.Success) { [long]$measurement.Groups['size'].Value } else { $null }
        curl_exit_code = $curlProcess.ExitCode
        curl_error = $curlStderr
    }
    mysql_before = $mysqlBefore
    mysql_after = $mysqlAfter
    samples = $samples
}

$report | ConvertTo-Json -Depth 8 | Set-Content -LiteralPath $OutputFile -Encoding UTF8
$report | Select-Object generated_at, php_enumeration_first, php_enumeration_second, request, mysql_before, mysql_after | Format-List
