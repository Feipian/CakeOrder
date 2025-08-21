<#
Simple MariaDB backup (PowerShell)
Usage examples:
  powershell -ExecutionPolicy Bypass -File .\public\export-local-db.ps1 -DbName cake_shop
  powershell -ExecutionPolicy Bypass -File .\public\export-local-db.ps1 -DbName cake_shop -Timestamp
#>

[CmdletBinding()]
param(
  [string] $DbName       = 'cake_shop',
  [string] $OutDir       = "$PSScriptRoot",
  [string] $Mysqldump    = 'C:\xampp\mysql\bin\mysqldump.exe',
  [string] $User         = 'root',
  [switch] $Timestamp,
  [string] $FileName     = 'cakeshop.sql'
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path $Mysqldump)) {
  Write-Error "mysqldump not found at $Mysqldump"
  exit 1
}

# Ensure OutDir fallback if empty
if ([string]::IsNullOrWhiteSpace($OutDir)) {
  $OutDir = (Split-Path -Parent $PSCommandPath)
  if ([string]::IsNullOrWhiteSpace($OutDir)) { $OutDir = (Get-Location).Path }
}

New-Item -ItemType Directory -Force -Path $OutDir | Out-Null

$fileName = $FileName
$sqlPath  = Join-Path $OutDir $fileName
$errPath  = "$sqlPath.err"

# Rotate existing output before writing new file
if (Test-Path $sqlPath) {
  $nameNoExt = [System.IO.Path]::GetFileNameWithoutExtension($FileName)
  $bakPath = Join-Path $OutDir ("{0}-{1}.sql" -f $nameNoExt, (Get-Date -Format yyyyMMdd-HHmmss))
  Move-Item -Path $sqlPath -Destination $bakPath -Force
  Write-Host "Previous backup moved to: $bakPath"
}

Write-Host "Dumping database '$DbName' to $sqlPath ..."

& $Mysqldump -u $User $DbName 1> $sqlPath 2> $errPath

if ((Test-Path $errPath) -and (Get-Content $errPath -TotalCount 1)) {
  Write-Host "Completed with errors. See: $errPath"
  exit 2
}

if (Test-Path $errPath) { Remove-Item $errPath -Force }

$sizeMb = [math]::Round((Get-Item $sqlPath).Length / 1MB, 2)
Write-Host "Backup complete: $sqlPath ($sizeMb MB)"

exit 0