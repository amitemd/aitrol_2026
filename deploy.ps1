[CmdletBinding()]
param(
    [string]$Php = 'php',
    [string]$Composer = 'composer',
    [switch]$SkipPull,
    [switch]$SkipComposer,
    [switch]$SkipAssets,
    [switch]$SkipMigrations,
    [switch]$RunTests
)

$ErrorActionPreference = 'Stop'
$projectPath = [System.IO.Path]::GetFullPath($PSScriptRoot)
$previousPath = (Get-Location).Path
$maintenanceActivated = $false
$exitCode = 0

function Invoke-Checked {
    param(
        [Parameter(Mandatory)] [string]$Command,
        [Parameter(Mandatory)] [string[]]$Arguments
    )

    Write-Host "`n> $Command $($Arguments -join ' ')" -ForegroundColor Cyan
    & $Command @Arguments

    if ($LASTEXITCODE -ne 0) {
        throw "Falló: $Command $($Arguments -join ' ')"
    }
}

function Invoke-Composer {
    param([Parameter(Mandatory)] [string[]]$Arguments)

    if ([System.IO.Path]::GetExtension($Composer) -eq '.phar') {
        Invoke-Checked -Command $Php -Arguments (@($Composer) + $Arguments)
    } else {
        Invoke-Checked -Command $Composer -Arguments $Arguments
    }
}

try {
    Set-Location -LiteralPath $projectPath

    if (-not (Test-Path -LiteralPath (Join-Path $projectPath 'artisan'))) {
        throw "No se encontró artisan en $projectPath"
    }

    $downFile = Join-Path $projectPath 'storage\framework\down'
    $wasAlreadyDown = Test-Path -LiteralPath $downFile

    if (-not $wasAlreadyDown) {
        Invoke-Checked -Command $Php -Arguments @('artisan', 'down', '--retry=60', '--refresh=15')
        $maintenanceActivated = $true
    }

    if (-not $SkipPull) {
        Invoke-Checked -Command 'git' -Arguments @('pull', '--ff-only')
    }

    if (-not $SkipComposer) {
        $composerArguments = @('install', '--no-interaction', '--prefer-dist', '--optimize-autoloader')

        if (-not $RunTests) {
            $composerArguments += '--no-dev'
        }

        Invoke-Composer -Arguments $composerArguments
    }

    if (-not $SkipAssets) {
        Invoke-Checked -Command 'npm' -Arguments @('ci')
        Invoke-Checked -Command 'npm' -Arguments @('run', 'build')
    }

    $manifestPath = Join-Path $projectPath 'public\build\manifest.json'

    if (-not (Test-Path -LiteralPath $manifestPath)) {
        throw 'No existe public\build\manifest.json. No se puede publicar una interfaz sin compilar.'
    }

    if ($RunTests) {
        Invoke-Checked -Command $Php -Arguments @('artisan', 'test', '--compact')

        if (-not $SkipComposer) {
            Invoke-Composer -Arguments @('install', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader')
        }
    }

    $deployArguments = @('artisan', 'app:deploy', '--force', '--no-maintenance')

    if ($SkipMigrations) {
        $deployArguments += '--no-migrate'
    }

    Invoke-Checked -Command $Php -Arguments $deployArguments
    Write-Host "`nDespliegue completado correctamente." -ForegroundColor Green
} catch {
    $exitCode = 1
    Write-Error $_
} finally {
    if ($maintenanceActivated) {
        try {
            Invoke-Checked -Command $Php -Arguments @('artisan', 'up')
        } catch {
            $downFile = Join-Path $projectPath 'storage\framework\down'

            if (Test-Path -LiteralPath $downFile) {
                Remove-Item -LiteralPath $downFile -Force
            }
        }
    }

    Set-Location -LiteralPath $previousPath
}

exit $exitCode
