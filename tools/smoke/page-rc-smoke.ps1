param(
    [Parameter(Mandatory = $false)]
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Invoke-PageStep {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Label,
        [Parameter(Mandatory = $true)]
        [scriptblock]$Script
    )

    Write-Host "==> $Label"
    & $Script
}

$resolvedRoot = (Resolve-Path -LiteralPath $ProjectRoot).Path
Push-Location $resolvedRoot
try {
    Invoke-PageStep 'Composer autoload' { composer dump-autoload }
    Invoke-PageStep 'PHP syntax lint: src' {
        Get-ChildItem -LiteralPath (Join-Path $resolvedRoot 'src') -Recurse -Filter '*.php' -File | ForEach-Object {
            php -l $_.FullName | Out-Host
        }
    }
    Invoke-PageStep 'Container lint' { php bin/console lint:container }
    Invoke-PageStep 'Paging debug container' { php bin/console page:debug:container }
    Invoke-PageStep 'Paging runtime audit' { php bin/console page:audit:readiness }
    Invoke-PageStep 'Paging RC readiness' { php bin/console page:rc:readiness }
    Invoke-PageStep 'Router page surface' { php bin/console debug:router | Select-String page }

    Write-Host 'Paging RC smoke completed.'
} finally {
    Pop-Location
}
