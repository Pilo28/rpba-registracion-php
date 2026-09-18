<#
  Levanta todo lo necesario para correr el sitio en local:
  1. El servidor Postgres (si no está corriendo).
  2. El servidor de desarrollo de PHP en http://localhost:8000.

  Uso: click derecho -> "Ejecutar con PowerShell", o desde una terminal:
    powershell -ExecutionPolicy Bypass -File php\start-local.ps1
#>

$ErrorActionPreference = 'Stop'

$pgCtl   = "$env:USERPROFILE\scoop\apps\postgresql\current\bin\pg_ctl.exe"
$pgData  = "$env:USERPROFILE\scoop\persist\postgresql\data"
$pgLog   = "$env:USERPROFILE\scoop\persist\postgresql\startup.log"
$repoRoot = Split-Path -Parent $PSScriptRoot
$phpPublic = Join-Path $PSScriptRoot 'public'
$phpIndex  = Join-Path $phpPublic 'index.php'

Write-Host "Verificando Postgres..."
& $pgCtl -D $pgData status *> $null
if ($LASTEXITCODE -eq 0) {
    Write-Host "  Postgres ya está corriendo."
} else {
    Write-Host "  Iniciando Postgres..."
    & $pgCtl -D $pgData -l $pgLog start
    Start-Sleep -Seconds 2
}

Write-Host "Iniciando servidor PHP en http://localhost:8000 ..."
Write-Host "  (dejá esta ventana abierta; cerrala para apagar el servidor)"
Set-Location $repoRoot
& php -S localhost:8000 -t $phpPublic $phpIndex
