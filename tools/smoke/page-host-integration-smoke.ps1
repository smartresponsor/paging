param(
    [Parameter(Mandatory = $true)]
    [string]$ProjectRoot
)

Set-StrictMode -Version 2.0
$ErrorActionPreference = 'Stop'

function Resolve-FullPathSafe {
    param([Parameter(Mandatory = $true)][string]$PathValue)
    $resolved = Resolve-Path -LiteralPath $PathValue -ErrorAction Stop
    return $resolved.ProviderPath
}

$root = Resolve-FullPathSafe -PathValue $ProjectRoot
Push-Location $root
try {
    Write-Host '== Paging host integration smoke =='
    php bin/console page:host:integration-check
    php bin/console page:rc:readiness
    php bin/console page:audit:readiness
    php bin/console debug:router | Select-String page
    php bin/console lint:container
    Write-Host 'Paging host integration smoke passed.'
}
finally {
    Pop-Location
}
