$ErrorActionPreference = "Stop"
$src = Split-Path -Parent $MyInvocation.MyCommand.Path
$parent = Split-Path -Parent $src
$versionLine = Select-String -Path (Join-Path $src "functions.php") -Pattern "ELITE_SHIPPING_VERSION',\s*'([^']+)'" | Select-Object -First 1
$version = $versionLine.Matches.Groups[1].Value
$zipName = "elite-shipping-theme-v$version.zip"
$zipPath = Join-Path $parent $zipName

Add-Type -AssemblyName System.IO.Compression.FileSystem
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, 'Create')
Get-ChildItem $src -Recurse -File | Where-Object { $_.Name -ne 'build.ps1' } | ForEach-Object {
	$rel = $_.FullName.Substring($parent.Length + 1).Replace('\', '/')
	[System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, $rel) | Out-Null
}
$zip.Dispose()

Copy-Item -Path $zipPath -Destination (Join-Path $parent 'elite-shipping-theme.zip') -Force

Write-Host "Built $zipName"
Write-Host "Copied to elite-shipping-theme.zip"
