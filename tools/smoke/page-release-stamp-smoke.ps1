param(
    [Parameter(Mandatory = $true)]
    [string]$ProjectRoot
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $ProjectRoot)) {
    throw "Project root does not exist: $ProjectRoot"
}

Push-Location $ProjectRoot
try {
    php bin/console page:release:stamp
    php bin/console page:release:stamp --json
    php bin/console page:completion:status
    php bin/console page:canon:guard
} finally {
    Pop-Location
}
