param(
    [Parameter(Mandatory = $false)]
    [string] $ProjectRoot = (Get-Location).Path
)

$ErrorActionPreference = 'Stop'

function Invoke-PageCommand {
    param([string[]] $Arguments)

    Push-Location $ProjectRoot
    try {
        & php @Arguments
        if ($LASTEXITCODE -ne 0) {
            throw "Command failed: php $($Arguments -join ' ')"
        }
    }
    finally {
        Pop-Location
    }
}

Invoke-PageCommand @('bin/console', 'lint:container')
Invoke-PageCommand @('bin/console', 'page:bridge:contract')
Invoke-PageCommand @('bin/console', 'page:bridge:contract', '--json')

Write-Host 'Page bridge contract smoke passed.'
