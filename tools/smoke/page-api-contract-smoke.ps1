param(
    [Parameter(Mandatory=$true)]
    [string]$ProjectRoot
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $ProjectRoot)) {
    throw "ProjectRoot does not exist: $ProjectRoot"
}

Push-Location -LiteralPath $ProjectRoot
try {
    php bin/console page:api:contract
    php bin/console page:rc:readiness
    php bin/console lint:container
}
finally {
    Pop-Location
}
