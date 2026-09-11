param(
    [string]$AppRoot = (Join-Path $PSScriptRoot '..\..\App')
)

$ErrorActionPreference = 'Stop'
$resolvedAppRoot = (Resolve-Path $AppRoot).Path
$console = Join-Path $resolvedAppRoot 'bin\console'

if (-not (Test-Path $console)) {
    throw "Symfony console was not found: $console"
}

Push-Location $resolvedAppRoot
try {
    & php $console doctrine:migrations:migrate 'App\Paging\Migrations\Version20260730053500' --em=postgres --no-interaction
    if ($LASTEXITCODE -ne 0) {
        throw "Paging migration failed with exit code $LASTEXITCODE."
    }

    & php $console doctrine:schema:validate --em=postgres --skip-sync
    if ($LASTEXITCODE -ne 0) {
        throw "Doctrine mapping validation failed with exit code $LASTEXITCODE."
    }

    & php $console page:seed:demo
    if ($LASTEXITCODE -ne 0) {
        throw "Paging demo seed failed with exit code $LASTEXITCODE."
    }

    Write-Host 'Paging migration and demo seed completed successfully.'
}
finally {
    Pop-Location
}
