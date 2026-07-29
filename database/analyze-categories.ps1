$csvPath = 'E:\4. Work\reply\database\wc-product-export-26-7-2026-1785094781676(eliteshippingcontainers.co.uk).csv'
$csv = Import-Csv $csvPath
$allCats = @{}
$badCombos = @{}
$empty = 0

foreach ($row in $csv) {
    $catField = $row.Categories
    if ([string]::IsNullOrWhiteSpace($catField)) {
        $empty++
        continue
    }
    if (-not $badCombos.ContainsKey($catField)) { $badCombos[$catField] = 0 }
    $badCombos[$catField]++
    foreach ($p in ($catField -split ',\s*')) {
        $name = $p.Trim()
        if ($name) {
            if (-not $allCats.ContainsKey($name)) { $allCats[$name] = 0 }
            $allCats[$name]++
        }
    }
}

Write-Host '=== SUMMARY ==='
Write-Host "Total products: $($csv.Count)"
Write-Host "Products with no category: $empty"
Write-Host "Unique category names: $($allCats.Count)"
Write-Host ''

Write-Host '=== CASE-INSENSITIVE DUPLICATE NAMES ==='
$byLower = @{}
foreach ($k in $allCats.Keys) {
    $lk = $k.ToLower().Trim()
    if (-not $byLower.ContainsKey($lk)) { $byLower[$lk] = @() }
    $byLower[$lk] += $k
}
$found = $false
foreach ($lk in ($byLower.Keys | Sort-Object)) {
    if ($byLower[$lk].Count -gt 1) {
        $found = $true
        Write-Host ('  ' + ($byLower[$lk] -join ' | '))
    }
}
if (-not $found) { Write-Host '  None found' }
Write-Host ''

function Normalize-Name([string]$s) {
    $s = $s.ToLower()
    $s = $s -replace '[^a-z0-9]+', ' '
    $s = $s.Trim() -replace '\s+', ' '
    return $s
}

Write-Host '=== NORMALIZED DUPLICATE NAMES (same words, different spacing/punctuation) ==='
$byNorm = @{}
foreach ($k in $allCats.Keys) {
    $n = Normalize-Name $k
    if (-not $byNorm.ContainsKey($n)) { $byNorm[$n] = @() }
    $byNorm[$n] += $k
}
$found = $false
foreach ($n in ($byNorm.Keys | Sort-Object)) {
    if ($byNorm[$n].Count -gt 1) {
        $found = $true
        Write-Host ('  ' + ($byNorm[$n] -join ' | '))
    }
}
if (-not $found) { Write-Host '  None found' }
Write-Host ''

Write-Host '=== SUSPECTED OVERLAPPING GROUPS ==='
$groups = @(
    @('flat pack', 'flatpack', 'flat-pack'),
    @('container home office', 'office container', 'office containers'),
    @('modified container', 'modified containers', 'modified shipping'),
    @('used shipping', 'used container'),
    @('refrigerated container', 'refrigerated containers'),
    @('new container', 'new containers', 'new shipping', '1-trip'),
    @('cabins for sale', 'cabin', 'steel cabins', 'jackleg cabins', 'flat pack cabins'),
    @('flat pack units', 'flat pack containers', 'flat pack cabins'),
    @('container accessories', 'accessories', 'parts', 'cabin accessories'),
    @('storage containers', 'containers for sale'),
    @('refurbished', 'wind & watertight', 'wind and watertight'),
    @('high cube', '45ft', '40ft', '20ft', '10ft', '30ft', '8ft')
)
foreach ($keywords in $groups) {
    $matches = @()
    foreach ($c in $allCats.Keys) {
        $cl = $c.ToLower()
        foreach ($kw in $keywords) {
            if ($cl -like "*$kw*") { $matches += $c; break }
        }
    }
    $matches = $matches | Select-Object -Unique | Sort-Object
    if ($matches.Count -gt 1) {
        Write-Host ('  ' + ($matches -join ' | '))
    }
}
Write-Host ''

Write-Host '=== BAD MERGED CATEGORY STRINGS (import errors) ==='
foreach ($combo in ($badCombos.Keys | Sort-Object { $badCombos[$_] } -Descending)) {
    if ($combo.Length -gt 55 -and (($combo.ToCharArray() | Where-Object { $_ -eq ' ' }).Count -ge 4)) {
        Write-Host "  [$($badCombos[$combo]) products] $combo"
    }
}
Write-Host ''

Write-Host '=== ALL CATEGORIES (count -> name) ==='
$allCats.GetEnumerator() | Sort-Object Name | ForEach-Object {
    Write-Host ("  {0,3}  {1}" -f $_.Value, $_.Name)
}
