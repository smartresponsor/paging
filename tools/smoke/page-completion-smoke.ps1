param(
    [Parameter(Mandatory = $true)]
    [string]$ProjectRoot
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $ProjectRoot -PathType Container)) {
    throw "ProjectRoot does not exist: $ProjectRoot"
}

Push-Location $ProjectRoot
try {
    php bin/console page:completion:status
    php bin/console page:completion:status --json
    php bin/console page:canon:guard
    php bin/console page:rc:final-status
}
finally {
    Pop-Location
}
