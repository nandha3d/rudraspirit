<#
  Single-command deploy for RudraSpirit.

  Usage (from project root):
      .\deploy.ps1                "optional commit message"
      .\deploy.ps1 -Direct        # also SSH in and run deploy.sh immediately

  Default flow:
    1. Stages + commits local changes (if any).
    2. Pushes to origin/main.
    3. GitHub Actions -> POST https://rudraspirit.com/deploy-webhook.php
       -> server runs deploy.sh: git pull, composer install,
          AUTO DATABASE UPDATE (migrate), cache rebuild.

  -Direct also opens an SSH session to the server and runs deploy.sh right away
  (useful if GitHub Actions is disabled). Requires an SSH key or you type the
  password once. Set $ServerDir below to the project path on the server.
#>
param(
    [Parameter(ValueFromRemainingArguments = $true)][string[]]$Message,
    [switch]$Direct
)

$ErrorActionPreference = "Stop"

# --- Server (Hostinger) ---------------------------------------------------
$SshHost = "u362580417@217.21.74.44"
$SshPort = "65002"
# Absolute path of the project on the server. Find it once with:
#   ssh -p 65002 u362580417@217.21.74.44 'cd <wherever> && pwd'
$ServerDir = "~/domains/rudraspirit.com/public_html"
# --------------------------------------------------------------------------

$msg = if ($Message) { $Message -join " " } else { "deploy: " + (Get-Date -Format "yyyy-MM-dd HH:mm") }

Write-Host "==> Staging changes..." -ForegroundColor Cyan
git add -A

git diff --cached --quiet
if ($LASTEXITCODE -ne 0) {
    Write-Host "==> Committing: $msg" -ForegroundColor Cyan
    git commit -m $msg
} else {
    Write-Host "No staged changes; pushing current HEAD." -ForegroundColor Yellow
}

Write-Host "==> Pushing to origin/main..." -ForegroundColor Cyan
git push origin main

if ($Direct) {
    Write-Host "==> Running deploy.sh on server over SSH..." -ForegroundColor Cyan
    ssh -p $SshPort $SshHost "cd $ServerDir && bash deploy.sh"
    Write-Host "Done (direct)." -ForegroundColor Green
} else {
    Write-Host "Pushed. GitHub Actions will trigger the deploy webhook." -ForegroundColor Green
    Write-Host "   Auto: pull + composer + DB migrate + cache rebuild on the server." -ForegroundColor Green
    Write-Host "   Watch:   https://github.com/nandha3d/rudraspirit/actions" -ForegroundColor DarkGray
    Write-Host "   Log:     ssh -p $SshPort $SshHost 'tail -n 50 $ServerDir/storage/logs/deploy.log'" -ForegroundColor DarkGray
}
