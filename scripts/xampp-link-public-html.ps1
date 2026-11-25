# Run in PowerShell AS ADMINISTRATOR.
# Replaces C:\xampp\htdocs\explore-jawai with a junction to this repo's public_html
# so theme edits in the project folder appear immediately in the browser.

$ErrorActionPreference = 'Stop'
$target = 'D:\Office\Project\Projects\Explore Jawai\public_html'
$link   = 'C:\xampp\htdocs\explore-jawai'

if (-not (Test-Path $target)) {
	Write-Error "Missing target: $target"
}

if (Test-Path $link) {
	$backup = "${link}.bak-" + (Get-Date -Format 'yyyyMMdd-HHmm')
	Write-Host "Moving existing folder to $backup"
	Move-Item -LiteralPath $link -Destination $backup
}

cmd /c mklink /J "$link" "$target"
Write-Host ""
Write-Host "Done. Open: http://localhost/explore-jawai/"
Write-Host "Update wp-config WP_HOME and WP_SITEURL to http://localhost/explore-jawai if you use the junction."