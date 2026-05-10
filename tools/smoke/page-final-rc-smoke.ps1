param(
    [Parameter(Mandatory = $true)]
    [string] $ProjectRoot
)

$ErrorActionPreference = 'Stop'

function Invoke-PageCommand {
    param([string] $CommandLine)

    Write-Host "==> $CommandLine"
    Push-Location $ProjectRoot
    try {
        Invoke-Expression $CommandLine
        if ($LASTEXITCODE -ne 0) {
            throw "Command failed with exit code $LASTEXITCODE: $CommandLine"
        }
    } finally {
        Pop-Location
    }
}

if (-not (Test-Path -LiteralPath $ProjectRoot -PathType Container)) {
    throw "ProjectRoot does not exist: $ProjectRoot"
}

Invoke-PageCommand 'php bin/console lint:container'
Invoke-PageCommand 'php bin/console page:rc:readiness'
Invoke-PageCommand 'php bin/console page:api:contract'
Invoke-PageCommand 'php bin/console page:host:integration-check'
Invoke-PageCommand 'php bin/console page:operations:check'
Invoke-PageCommand 'php bin/console page:rc:final-status'
Invoke-PageCommand 'php bin/console page:rc:final-status --json'

Write-Host 'Paging final RC smoke passed.'
