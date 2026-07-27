param(
    [int]$Port = 8000,
    [switch]$NoBrowser,
    [switch]$CheckOnly
)

$ErrorActionPreference = 'Stop'
$siteRoot = Split-Path -Parent $PSScriptRoot
$runtimeRoot = Join-Path $env:LOCALAPPDATA 'NetWIS\php'

function Find-Php {
    $command = Get-Command php.exe -ErrorAction SilentlyContinue
    if ($command) {
        return $command.Source
    }

    $knownPaths = @(
        'C:\xampp\php\php.exe',
        'C:\php\php.exe',
        'C:\tools\php\php.exe',
        'C:\Program Files\PHP\php.exe',
        'F:\xampp\php\php.exe',
        'F:\php\php.exe'
    )

    foreach ($path in $knownPaths) {
        if (Test-Path -LiteralPath $path) {
            return $path
        }
    }

    if (Test-Path -LiteralPath $runtimeRoot) {
        $portablePhp = Get-ChildItem -LiteralPath $runtimeRoot -Filter php.exe -File -Recurse |
            Sort-Object LastWriteTime -Descending |
            Select-Object -First 1
        if ($portablePhp) {
            return $portablePhp.FullName
        }
    }

    return $null
}

function Install-PortablePhp {
    $downloadUrl = 'https://windows.php.net/downloads/releases/latest/php-8.5-nts-Win32-vs17-x64-latest.zip'
    $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    $installDir = Join-Path $runtimeRoot $stamp
    $archivePath = Join-Path $runtimeRoot "php-$stamp.zip"

    New-Item -ItemType Directory -Path $runtimeRoot -Force | Out-Null

    Write-Host 'PHP was not found. Downloading the official portable PHP runtime...'
    Invoke-WebRequest -Uri $downloadUrl -OutFile $archivePath -UseBasicParsing

    Write-Host 'Installing the portable runtime for this Windows user...'
    New-Item -ItemType Directory -Path $installDir | Out-Null
    Expand-Archive -LiteralPath $archivePath -DestinationPath $installDir

    $installedPhp = Join-Path $installDir 'php.exe'
    if (-not (Test-Path -LiteralPath $installedPhp)) {
        throw "PHP installation did not produce php.exe in $installDir"
    }

    return $installedPhp
}

function Test-LocalPort {
    param([int]$Candidate)

    $listener = [System.Net.Sockets.TcpListener]::new(
        [System.Net.IPAddress]::Loopback,
        $Candidate
    )

    try {
        $listener.Start()
        return $true
    }
    catch {
        return $false
    }
    finally {
        $listener.Stop()
    }
}

$phpExe = Find-Php
if (-not $phpExe) {
    $phpExe = Install-PortablePhp
}

Write-Host "Using PHP: $phpExe"
& $phpExe --version
if ($LASTEXITCODE -ne 0) {
    throw 'PHP could not start. The Microsoft Visual C++ Redistributable may be missing.'
}

if ($CheckOnly) {
    Write-Host 'PHP runtime check passed.'
    exit 0
}

$selectedPort = $null
foreach ($candidate in $Port..($Port + 20)) {
    if (Test-LocalPort -Candidate $candidate) {
        $selectedPort = $candidate
        break
    }
}

if (-not $selectedPort) {
    throw "No free local port was found between $Port and $($Port + 20)."
}

$url = "http://127.0.0.1:$selectedPort/"
Write-Host ''
Write-Host "NetWIS is available at $url" -ForegroundColor Green
Write-Host 'Press Ctrl+C or close this window to stop the server.'
Write-Host ''

if (-not $NoBrowser) {
    Start-Process $url
}

& $phpExe -S "127.0.0.1:$selectedPort" -t $siteRoot
exit $LASTEXITCODE
