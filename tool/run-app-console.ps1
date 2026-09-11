param(
    [Parameter(Mandatory = $true)]
    [string]$AppRoot,

    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$ConsoleArguments
)

$ErrorActionPreference = 'Stop'
$resolvedAppRoot = (Resolve-Path $AppRoot).Path
$console = Join-Path $resolvedAppRoot 'bin\console'

Push-Location $resolvedAppRoot
try {
    & php $console @ConsoleArguments
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}
