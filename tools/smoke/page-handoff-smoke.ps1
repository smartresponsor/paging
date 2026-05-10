param(
    [Parameter(Mandatory = $true)]
    [string]$ProjectRoot
)

Set-StrictMode -Version 2.0
$ErrorActionPreference = 'Stop'

function Invoke-PageCommand {
    param([Parameter(Mandatory = $true)][string]$Command)

    Push-Location $ProjectRoot
    try {
        Write-Host "Running: $Command"
        & php bin/console $Command
        if ($LASTEXITCODE -ne 0) {
            throw "Command failed: $Command"
        }
    }
    finally {
        Pop-Location
    }
}

if (-not (Test-Path -LiteralPath $ProjectRoot -PathType Container)) {
    throw "Project root does not exist: $ProjectRoot"
}

Invoke-PageCommand 'page:rc:final-status'
Invoke-PageCommand 'page:handoff:summary'
Invoke-PageCommand 'page:handoff:summary --json'
Write-Host 'Paging handoff smoke passed.'
