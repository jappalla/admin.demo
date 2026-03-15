param(
    [string]$ServerHost = "127.0.0.1",
    [int]$Port = 8112
)

$ErrorActionPreference = "Stop"
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
Set-Location $projectRoot

$checks = New-Object System.Collections.Generic.List[object]
$failures = New-Object System.Collections.Generic.List[string]

function Add-Check {
    param(
        [string]$Name,
        [bool]$Passed,
        [string]$Details = ""
    )

    $checks.Add([pscustomobject]@{
        Name = $Name
        Passed = $Passed
        Details = $Details
    }) | Out-Null

    if (-not $Passed) {
        $failures.Add($Name) | Out-Null
    }
}

function Get-CsrfToken {
    param([string]$Html)
    $match = [regex]::Match($Html, 'name="csrf_token" value="([a-f0-9]{64})"')
    if ($match.Success) {
        return $match.Groups[1].Value
    }
    return $null
}

function Read-DotEnv {
    param([string]$Path)

    $values = @{}
    if (-not (Test-Path $Path)) {
        return $values
    }

    Get-Content $Path | ForEach-Object {
        $line = $_.Trim()
        if ($line -eq "" -or $line.StartsWith("#")) {
            return
        }
        $parts = $line -split "=", 2
        if ($parts.Count -ne 2) {
            return
        }

        $key = $parts[0].Trim()
        $value = $parts[1].Trim()
        if ($value.StartsWith('"') -and $value.EndsWith('"') -and $value.Length -ge 2) {
            $value = $value.Substring(1, $value.Length - 2)
        }
        $values[$key] = $value
    }

    return $values
}

# Code checks: lint + migration/verify
$phpFiles = Get-ChildItem -Path $projectRoot -Recurse -Filter *.php | Where-Object { $_.FullName -notmatch "\\vendor\\" }
$lintPassed = $true
foreach ($file in $phpFiles) {
    cmd /c "php -l `"$($file.FullName)`" >nul 2>nul"
    if ($LASTEXITCODE -ne 0) {
        $lintPassed = $false
        break
    }
}
Add-Check -Name "Code/Lint PHP" -Passed $lintPassed

cmd /c "php database\migrate.php >nul 2>nul"
Add-Check -Name "Code/Migrations" -Passed ($LASTEXITCODE -eq 0)

cmd /c "php database\verify_phase5.php >nul 2>nul"
Add-Check -Name "Code/Verify Phase5 Schema" -Passed ($LASTEXITCODE -eq 0)

# Runtime checks
$server = Start-Process -FilePath php -ArgumentList "-S", "$ServerHost`:$Port", "router.php" -WorkingDirectory $projectRoot -PassThru
Start-Sleep -Seconds 2

try {
    $baseUrl = "http://$ServerHost`:$Port"
    $respHome = Invoke-WebRequest -Uri "$baseUrl/" -UseBasicParsing -SessionVariable ws
    $respCss = Invoke-WebRequest -Uri "$baseUrl/assets/css/style.css" -UseBasicParsing
    $respExport = Invoke-WebRequest -Uri "$baseUrl/cv/export" -UseBasicParsing
    $respPdf = Invoke-WebRequest -Uri "$baseUrl/cv/pdf" -UseBasicParsing

    $navigation = ($respHome.Content -match 'href="#home"') -and
                  ($respHome.Content -match 'href="#profilo"') -and
                  ($respHome.Content -match 'href="#esperienza"') -and
                  ($respHome.Content -match 'href="#competenze"') -and
                  ($respHome.Content -match 'href="#contatti"')
    Add-Check -Name "Navigazione/Menu Anchors" -Passed $navigation

    $wiring = ($respHome.Content -match 'assets/css/style.css') -and
              ($respHome.Content -match 'assets/js/app.js') -and
              ($respCss.StatusCode -eq 200)
    Add-Check -Name "Wiring/Asset Links" -Passed $wiring

    $exportHub = ($respExport.StatusCode -eq 200) -and
                 ($respExport.Content -match 'Export Curriculum in PDF') -and
                 ($respExport.Content -match 'export-preview-frame')
    Add-Check -Name "Funzionalita/Export Hub UX" -Passed $exportHub

    $footerShare = ($respHome.Content -match 'facebook.com/sharer/sharer.php') -and
                   ($respHome.Content -match 'twitter.com/intent/tweet') -and
                   ($respHome.Content -match 'linkedin.com/sharing/share-offsite') -and
                   ($respHome.Content -match 'api.whatsapp.com/send')
    Add-Check -Name "Funzionalita/Footer Social Share" -Passed $footerShare

    $pdfHeader = New-Object byte[] 4
    [void]$respPdf.RawContentStream.Read($pdfHeader, 0, 4)
    $pdfSignature = [System.Text.Encoding]::ASCII.GetString($pdfHeader)
    $pdfExport = ($respPdf.Headers['Content-Type'] -match 'application/pdf') -and ($pdfSignature -eq '%PDF')
    Add-Check -Name "Funzionalita/Export CV PDF" -Passed $pdfExport

    $anchorsOffset = ($respCss.Content -match 'scroll-padding-top') -and ($respCss.Content -match 'scroll-margin-top')
    Add-Check -Name "Navigazione/Anchor Offset" -Passed $anchorsOffset

    $linkStyleNoUnderline = $respCss.Content -match 'a\s*\{[\s\S]*text-decoration:\s*none'
    Add-Check -Name "UI/Link senza sottolineatura" -Passed $linkStyleNoUnderline

    $envValues = Read-DotEnv -Path ".env"
    $adminEmail = if ($envValues.ContainsKey("ADMIN_EMAIL")) { $envValues["ADMIN_EMAIL"] } else { "admin@local.test" }
    $adminPassword = if ($envValues.ContainsKey("ADMIN_PASSWORD")) { $envValues["ADMIN_PASSWORD"] } else { "ChangeMe123!" }

    $loginPage = Invoke-WebRequest -Uri "$baseUrl/admin" -UseBasicParsing -WebSession $ws
    $loginCsrf = Get-CsrfToken -Html $loginPage.Content
    $loginOk = ($loginCsrf -ne $null)
    Add-Check -Name "Funzionalita/Admin Login Page" -Passed $loginOk

    if ($loginOk) {
        $dashboard = Invoke-WebRequest -Uri "$baseUrl/admin/login" -Method Post -Body @{
            csrf_token = $loginCsrf
            email = $adminEmail
            password = $adminPassword
        } -UseBasicParsing -WebSession $ws

        $dashboardFeatures = ($dashboard.Content -match 'Profilo e Contatti') -and
                             ($dashboard.Content -match 'Aggiungi Esperienza') -and
                             ($dashboard.Content -match 'Aggiungi Competenza') -and
                             ($dashboard.Content -match 'Messaggi istantanei ricevuti')
        Add-Check -Name "Funzionalita/Dashboard Admin" -Passed $dashboardFeatures

        $profileHtmlSupport = ($dashboard.Content -match 'supporta HTML') -and
                              ($dashboard.Content -match 'Tag consentiti') -and
                              ($dashboard.Content -match 'Descrizione \(supporta HTML\)') -and
                              ($dashboard.Content -match 'Testo Intro Form Contatti \(supporta HTML\)')
        Add-Check -Name "Funzionalita/Admin Profilo HTML" -Passed $profileHtmlSupport
    } else {
        Add-Check -Name "Funzionalita/Dashboard Admin" -Passed $false -Details "Login page without csrf token"
        Add-Check -Name "Funzionalita/Admin Profilo HTML" -Passed $false -Details "Dashboard non raggiungibile"
    }

    $contactForm = ($respHome.Content -match 'action="[^"]*contact/send"') -and
                   ($respHome.Content -match 'name="full_name"') -and
                   ($respHome.Content -match 'name="message"')
    Add-Check -Name "Funzionalita/Form Messaggio Istantaneo" -Passed $contactForm

    $seo = ($respHome.Content -match '<title>') -and
           ($respHome.Content -match 'meta\s+name="description"') -and
           ($respHome.Content -match 'meta\s+property="og:title"') -and
           ($respHome.Content -match 'meta\s+property="og:description"') -and
           ($respHome.Content -match 'link\s+rel="canonical"') -and
           ($respHome.Content -match 'application/ld\+json')
    Add-Check -Name "SEO/Meta + Structured Data" -Passed $seo
}
finally {
    if ($server -and -not $server.HasExited) {
        Stop-Process -Id $server.Id -Force
    }
}

Write-Host ""
Write-Host "=== Phase 6 Quality Gate ==="
foreach ($check in $checks) {
    $status = if ($check.Passed) { "[OK]" } else { "[FAIL]" }
    if ($check.Details -ne "") {
        Write-Host "$status $($check.Name) - $($check.Details)"
    } else {
        Write-Host "$status $($check.Name)"
    }
}

if ($failures.Count -gt 0) {
    Write-Error ("Quality gate failed: " + ($failures -join ", "))
    exit 1
}

Write-Host "Quality gate passed."
exit 0
