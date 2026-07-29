$csvPath = 'E:\4. Work\reply\database\wc-product-export-26-7-2026-1785094781676(eliteshippingcontainers.co.uk).csv'
$outPath = 'E:\4. Work\reply\database\category-cleanup-suggestions.csv'
$rows = Import-Csv $csvPath

function Get-SuggestedCategory {
    param(
        [string[]]$Categories,
        [string]$ProductName
    )

    $name = $ProductName.ToLower()
    $cats = @($Categories | ForEach-Object { $_.Trim() } | Where-Object { $_ })
    $catText = ($cats -join ' ').ToLower()

    # Bad merged import category
    if ($cats -contains 'Containers for Sale Flat Pack Cabins for Sale Flat Pack Containers Flat Pack Units') {
        return @{ Primary = 'Flat Pack Units'; Action = 'FIX: remove bad merged category'; Keep = @('Flat Pack Units') }
    }

    if ($cats -contains 'Uncategorized' -or $cats.Count -eq 0) {
        return @{ Primary = '(assign manually)'; Action = 'FIX: uncategorized'; Keep = @() }
    }

    if ($cats -contains 'Home' -and $cats.Count -eq 1) {
        return @{ Primary = '(assign manually)'; Action = 'FIX: invalid Home category'; Keep = @() }
    }

    # Flat pack
    if ($catText -match 'flat pack') {
        if ($name -match 'cabin|office') { return @{ Primary = 'Flat Pack Cabins for Sale'; Action = 'Merge flat pack group'; Keep = @('Flat Pack Units','Flat Pack Containers','Flat Pack Cabins for Sale Flat Pack Units') } }
        return @{ Primary = 'Flat Pack Units'; Action = 'Merge flat pack group'; Keep = @('Flat Pack Units','Flat Pack Containers') }
    }

    # Refrigerated
    if ($catText -match 'refrigerated') {
        return @{ Primary = 'Refrigerated Containers'; Action = 'Merge refrigerated duplicates'; Keep = @('Refrigerated Containers') }
    }

    # Accessories / parts
    if ($catText -match 'parts|accessories|cabin accessories') {
        if ($name -match 'cabin|furniture') { return @{ Primary = 'Cabin Accessories & Furniture'; Action = 'Keep accessory type'; Keep = @('Cabin Accessories & Furniture') } }
        return @{ Primary = 'Container Accessories'; Action = 'Merge Parts + Parts & Accessories'; Keep = @('Container Accessories','Parts & Accessories') }
    }

    # Cabins
    if ($catText -match 'cabins for sale|jackleg|steel cabins|homes & cabins') {
        if ($name -match 'jackleg') { return @{ Primary = 'Jackleg Cabins for Sale'; Action = 'OK'; Keep = @('Jackleg Cabins for Sale') } }
        if ($name -match 'steel cabin') { return @{ Primary = 'Steel Cabins for Sale'; Action = 'OK'; Keep = @('Steel Cabins for Sale') } }
        if ($name -match 'home|office') { return @{ Primary = 'Container Home Office'; Action = 'Review cabin vs office'; Keep = @('Container Home Office','Shipping Container Offices') } }
        return @{ Primary = 'Cabins for Sale'; Action = 'OK'; Keep = @('Cabins for Sale') }
    }

    # Office
    if ($catText -match 'office|container home office') {
        return @{ Primary = 'Shipping Container Offices'; Action = 'Merge office categories'; Keep = @('Shipping Container Offices','Container Home Office') }
    }

    # Modified
    if ($catText -match 'modified') {
        return @{ Primary = 'Modified Shipping Containers'; Action = 'Merge Modified Containers'; Keep = @('Modified Shipping Containers') }
    }

    # New / 1-trip
    if ($catText -match '1-trip|new shipping|new containers') {
        return @{ Primary = '1-Trip Shipping Containers'; Action = 'Merge new/1-trip'; Keep = @('1-Trip Shipping Containers','New Containers') }
    }

    # Used / refurbished / WWT
    if ($catText -match 'used shipping|refurbished|wind & watertight|wind and watertight') {
        if ($catText -match 'refurbished') { return @{ Primary = 'Refurbished Shipping Containers'; Action = 'Pick one used-type category'; Keep = @('Refurbished Shipping Containers') } }
        if ($catText -match 'wind') { return @{ Primary = 'Wind & Watertight-Cargo Worthy'; Action = 'Pick one used-type category'; Keep = @('Wind & Watertight-Cargo Worthy') } }
        return @{ Primary = 'Used Shipping Containers'; Action = 'Pick one used-type category'; Keep = @('Used Shipping Containers') }
    }

    # Size-based
    $sizeMap = @{
        '8ft x 10ft' = '8ft x 10ft Containers'
        '10ft'       = '10Ft Containers'
        '16ft'       = '16ft Storage Containers'
        '20ft'       = '20ft Containers'
        '30ft'       = '30ft Containers'
        '40ft'       = '40ft Containers'
        '45ft'       = '45Ft Containers'
        'high cube'  = 'High Cube Containers'
    }
    foreach ($key in $sizeMap.Keys) {
        if ($catText -match [regex]::Escape($key) -or $name -match [regex]::Escape($key)) {
            return @{ Primary = $sizeMap[$key]; Action = 'OK - size category'; Keep = @($sizeMap[$key]) }
        }
    }

    # Specialized
    if ($catText -match 'pool') { return @{ Primary = 'Shipping Container Pool'; Action = 'OK'; Keep = @('Shipping Container Pool') } }
    if ($catText -match 'toilet|shower') { return @{ Primary = 'Toilet & Shower Blocks for Sale'; Action = 'OK'; Keep = @('Toilet & Shower Blocks for Sale') } }
    if ($catText -match 'canopy') { return @{ Primary = 'Shipping Container Canopy Structures'; Action = 'OK'; Keep = @('Shipping Container Canopy Structures') } }
    if ($catText -match 'specialized|tunnel|carbon steel|modular|top brand|storage containers for sale|containers for sale') {
        $primary = ($cats | Where-Object { $_ -ne 'Home' } | Select-Object -First 1)
        if (-not $primary) { $primary = 'Containers for Sale' }
        $action = if ($cats -contains 'Home') { 'Remove Home tag' } else { 'Review' }
        return @{ Primary = $primary; Action = $action; Keep = @($primary) }
    }

    $primary = ($cats | Where-Object { $_ -ne 'Home' } | Select-Object -First 1)
    if (-not $primary) { $primary = ($cats | Select-Object -First 1) }
    $action = if ($cats -contains 'Home') { 'Remove Home tag' } else { 'Review manually' }
    return @{ Primary = $primary; Action = $action; Keep = @($primary) }
}

$out = foreach ($row in $rows) {
    $cats = @()
    if ($row.Categories) {
        $cats = @($row.Categories -split ',\s*' | ForEach-Object { $_.Trim() } | Where-Object { $_ })
    }
    $s = Get-SuggestedCategory -Categories $cats -ProductName $row.Name
    $remove = @($cats | Where-Object { $s.Keep -notcontains $_ -and $_ -ne 'Home' })
    if ($cats -contains 'Home') { $remove += 'Home' }

    [PSCustomObject]@{
        ID                   = $row.ID
        ProductName          = $row.Name
        CurrentCategories    = ($cats -join ' | ')
        CategoryCount        = $cats.Count
        SuggestedPrimary     = $s.Primary
        SuggestedKeep        = ($s.Keep -join ' | ')
        RemoveThese          = ($remove | Select-Object -Unique | Sort-Object) -join ' | '
        Action               = $s.Action
        NeedsFix             = if ($s.Action -like 'FIX:*' -or $remove.Count -gt 0 -or $cats.Count -gt 2) { 'YES' } else { 'NO' }
    }
}

$out | Export-Csv -Path $outPath -NoTypeInformation -Encoding UTF8

$fixCount = ($out | Where-Object { $_.NeedsFix -eq 'YES' }).Count
Write-Host "Wrote $outPath"
Write-Host "Products needing cleanup: $fixCount / $($out.Count)"
