param(
    [switch]$WithSeed = $false,
    [switch]$SkipBuild = $false
)

$ErrorActionPreference = 'Stop'

Write-Host '== ScanHadir Phase 10 Production Deploy ==' -ForegroundColor Cyan

Write-Host '1) Install/update PHP dependencies...'
composer install --no-dev --prefer-dist --optimize-autoloader

if (-not $SkipBuild) {
    Write-Host '2) Install/build frontend assets...'
    npm ci
    npm run build
}

Write-Host '3) Run production preparation command...'
$commandArgs = @('artisan', 'app:prepare-production', '--with-migrate', '--force')
if ($WithSeed) {
    $commandArgs += '--with-seed'
}

php @commandArgs

Write-Host '4) Verify health endpoint...'
try {
    $response = Invoke-WebRequest -Uri 'http://127.0.0.1/up' -UseBasicParsing -TimeoutSec 10
    Write-Host "Health check status: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Warning 'Health check could not be verified automatically. Validate /up on deployed host.'
}

Write-Host 'Deploy routine finished.' -ForegroundColor Green
