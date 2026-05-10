param(
    [Parameter(Mandatory = $true)]
    [string]$ProjectRoot
)

$ErrorActionPreference = 'Stop'

function Assert-FileExists {
    param([string]$Path)
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
        throw "Required file is missing: $Path"
    }
}

$root = [System.IO.Path]::GetFullPath($ProjectRoot)
if (-not (Test-Path -LiteralPath $root -PathType Container)) {
    throw "ProjectRoot does not exist: $root"
}

Push-Location $root
try {
    Assert-FileExists (Join-Path $root 'bin\console')
    Assert-FileExists (Join-Path $root 'composer.json')
    Assert-FileExists (Join-Path $root 'config\services.yaml')
    Assert-FileExists (Join-Path $root 'tools\smoke\page-operational-smoke.ps1')

    & php bin/console page:operations:check
    if ($LASTEXITCODE -ne 0) { throw 'page:operations:check failed' }

    & php bin/console page:rc:readiness
    if ($LASTEXITCODE -ne 0) { throw 'page:rc:readiness failed' }

    & php bin/console page:api:contract
    if ($LASTEXITCODE -ne 0) { throw 'page:api:contract failed' }

    Write-Host 'Paging operational smoke passed.'
} finally {
    Pop-Location
}
