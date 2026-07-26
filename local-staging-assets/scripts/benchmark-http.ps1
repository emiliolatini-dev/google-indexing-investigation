param(
    [Parameter(Mandatory = $true)]
    [string]$Phase,

    [Parameter(Mandatory = $true)]
    [string]$OutputFile
)

$ErrorActionPreference = 'Stop'

$apacheExe = 'D:\XAMPP\apache\bin\httpd.exe'
$apacheConfig = 'D:/XAMPP/apache/conf/httpd.conf'
$curlExe = 'C:\Windows\System32\curl.exe'
$debugLog = 'D:\Web-Lab\projects\fotomoto-click\wordpress\wp-content\debug.log'
$urls = @(
    '/test-static.txt',
    '/wp-login.php',
    '/',
    '/wp-json/'
)

function Restart-LocalApache {
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

    $deadline = (Get-Date).AddSeconds(20)
    do {
        Start-Sleep -Milliseconds 500
        $processes = @(Get-Process httpd -ErrorAction SilentlyContinue)
    } while ($processes.Count -lt 2 -and (Get-Date) -lt $deadline)

    if ($processes.Count -lt 2) {
        throw 'Apache did not start both parent and worker processes.'
    }

    Start-Sleep -Seconds 1
}

function Get-DebugOffset {
    if (Test-Path -LiteralPath $debugLog) {
        return (Get-Item -LiteralPath $debugLog).Length
    }

    return 0L
}

function Read-DebugDelta([long]$Offset) {
    if (-not (Test-Path -LiteralPath $debugLog)) {
        return ''
    }

    $stream = [System.IO.File]::Open($debugLog, 'Open', 'Read', 'ReadWrite')
    try {
        [void]$stream.Seek($Offset, [System.IO.SeekOrigin]::Begin)
        $reader = [System.IO.StreamReader]::new($stream)
        try {
            return $reader.ReadToEnd()
        }
        finally {
            $reader.Dispose()
        }
    }
    finally {
        $stream.Dispose()
    }
}

$results = [System.Collections.Generic.List[object]]::new()

foreach ($path in $urls) {
    Restart-LocalApache

    for ($attempt = 1; $attempt -le 3; $attempt++) {
        $debugOffset = Get-DebugOffset
        $started = Get-Date
        $previousErrorActionPreference = $ErrorActionPreference
        $ErrorActionPreference = 'Continue'
        try {
            $raw = & $curlExe --noproxy '*' --resolve 'fotomoto.local:80:127.0.0.1' `
                --max-time 135 --silent --show-error --output NUL `
                --write-out '%{http_code}|%{time_total}|%{size_download}' `
                "http://fotomoto.local$path" 2>&1
            $curlExitCode = $LASTEXITCODE
        }
        finally {
            $ErrorActionPreference = $previousErrorActionPreference
        }
        $finished = Get-Date
        $rawText = (($raw | ForEach-Object { [string]$_ }) -join "`n").Trim()
        $measurement = [regex]::Match(
            $rawText,
            '(?<status>\d{3})\|(?<seconds>\d+(?:\.\d+)?)\|(?<size>\d+)\s*$'
        )

        $results.Add([pscustomobject]@{
            path = $path
            attempt = $attempt
            cold_after_restart = ($attempt -eq 1)
            started_at = $started.ToString('o')
            finished_at = $finished.ToString('o')
            status = if ($measurement.Success) { $measurement.Groups['status'].Value } else { $null }
            total_seconds = if ($measurement.Success) { [double]::Parse($measurement.Groups['seconds'].Value, [Globalization.CultureInfo]::InvariantCulture) } else { $null }
            size_download = if ($measurement.Success) { [long]$measurement.Groups['size'].Value } else { $null }
            curl_exit_code = $curlExitCode
            curl_message = if ($measurement.Success) { $rawText.Substring(0, $measurement.Index).Trim() } else { $rawText }
            debug_delta = (Read-DebugDelta -Offset $debugOffset).Trim()
        })
    }
}

$report = [pscustomobject]@{
    phase = $Phase
    generated_at = (Get-Date).ToString('o')
    apache_executable = $apacheExe
    php_executable = 'D:\XAMPP\php\php.exe'
    results = $results
}

$report | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath $OutputFile -Encoding UTF8
$results | Format-Table path, attempt, cold_after_restart, status, total_seconds, curl_exit_code -AutoSize
