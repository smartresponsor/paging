param(
    [Parameter(Mandatory = $false)]
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
)

$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($ProjectRoot)) {
    throw 'ProjectRoot is required.'
}

$projectFull = [System.IO.Path]::GetFullPath($ProjectRoot)
if (-not (Test-Path -LiteralPath $projectFull -PathType Container)) {
    throw "ProjectRoot does not exist: $projectFull"
}

Push-Location $projectFull
try {
    php bin/console page:canon:guard
    php bin/console page:canon:guard --json
    php bin/console page:rc:final-status
    php bin/console page:handoff:summary
}
finally {
    Pop-Location
}
