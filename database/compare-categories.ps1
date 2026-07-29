$newPath = 'E:\4. Work\reply\database\wc-product-export-26-7-2026-1785094781676(eliteshippingcontainers.co.uk).csv'
$oldPath = 'E:\4. Work\reply\database\wc-product-export-26-7-2026-1785094597524(firstchoiceshippingcontainers.com).csv'

function Get-CategoriesFromCsv([string]$path) {
    $cats = @{}
    Import-Csv $path | ForEach-Object {
        $catField = $_.Categories
        if ([string]::IsNullOrWhiteSpace($catField)) { return }
        foreach ($p in ($catField -split ',\s*')) {
            $name = $p.Trim()
            if ($name) {
                if (-not $cats.ContainsKey($name)) { $cats[$name] = 0 }
                $cats[$name]++
            }
        }
    }
    return $cats
}

$new = Get-CategoriesFromCsv $newPath
$old = Get-CategoriesFromCsv $oldPath

Write-Host '=== ONLY ON NEW SITE ==='
foreach ($k in ($new.Keys | Sort-Object)) {
    if (-not $old.ContainsKey($k)) { Write-Host "  $k ($($new[$k]) products)" }
}
Write-Host ''
Write-Host '=== ONLY ON OLD SITE ==='
foreach ($k in ($old.Keys | Sort-Object)) {
    if (-not $new.ContainsKey($k)) { Write-Host "  $k ($($old[$k]) products)" }
}
Write-Host ''
Write-Host '=== ON BOTH SITES (same name) ==='
foreach ($k in ($new.Keys | Sort-Object)) {
    if ($old.ContainsKey($k)) { Write-Host "  $k (new: $($new[$k]), old: $($old[$k]))" }
}
