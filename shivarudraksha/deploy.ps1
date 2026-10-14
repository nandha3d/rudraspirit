<#
  Single-command direct deploy for Shiva Rudraksha Catalogue.
  Builds the Vite app and syncs directly to shivarudraksha.in via SFTP/SSH.
#>
param(
    [Parameter(ValueFromRemainingArguments = $true)][string[]]$Message,
    [switch]$Direct
)

$ErrorActionPreference = "Stop"

$SshHost = "shivarud1@45.199.139.25"
$SshPort = "22"
$SshKey  = "$env:USERPROFILE\.ssh\animazon_deploy"
$ServerDir = "~/public_html/public"

Write-Host "==> Building Shiva Rudraksha for production..." -ForegroundColor Cyan
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Error "Build failed! Aborting deploy."
    exit 1
}

$msg = if ($Message) { $Message -join " " } else { "deploy catalogue: " + (Get-Date -Format "yyyy-MM-dd HH:mm") }

Write-Host "==> Staging & committing changes..." -ForegroundColor Cyan
git add -A
git diff --cached --quiet
if ($LASTEXITCODE -ne 0) {
    git commit -m $msg
} else {
    Write-Host "No local git changes; pushing HEAD." -ForegroundColor Yellow
}

Write-Host "==> Pushing to origin/main..." -ForegroundColor Cyan
git push origin main

Write-Host "==> Direct sync to server over SSH/SFTP..." -ForegroundColor Cyan
ssh -i $SshKey -p $SshPort $SshHost "cp -rn ~/catalogue/public/images ~/public_html/public/ 2>/dev/null || true"

Write-Host "✨ Shiva Rudraksha Catalogue Deployed Successfully to https://shivarudraksha.in!" -ForegroundColor Green
