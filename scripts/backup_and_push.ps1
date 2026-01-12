# backup_and_push.ps1 - Gera backup e envia para GitHub
# Pré-requisitos:
# - Git instalado e no PATH
# - PHP disponível (Laragon)
# - Repositório inicializado (git init) e remoto configurado (origin)
# - Opcional: definir $Branch = "main" ou "master"

param(
    [string]$Branch = "main"
)

$ErrorActionPreference = 'Stop'

Push-Location (Split-Path $MyInvocation.MyCommand.Path -Parent) | Out-Null
Push-Location (Resolve-Path "..") | Out-Null

# 1) Gerar backup
Write-Host "Gerando backup..." -ForegroundColor Cyan
php backup_run.php | Out-String | Write-Host

# 2) Selecionar último dump SQL
$backupDir = Join-Path $PWD "backups"
$lastSql = Get-ChildItem -Path $backupDir -Filter "db-*.sql" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
if (-not $lastSql) { throw "Nenhum dump SQL encontrado em $backupDir" }

# 3) Commit
Write-Host "Commit do dump: $($lastSql.Name)" -ForegroundColor Cyan
& git add $lastSql.FullName
$ts = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
& git commit -m "Backup: $ts - $($lastSql.Name)" | Out-String | Write-Host

# 4) Push
$remotes = git remote | Out-String
if ($remotes.Trim().Length -eq 0) {
    Write-Warning "Nenhum remoto configurado. Configure com: git remote add origin <URL>"
} else {
    Write-Host "Enviando para remoto 'origin'..." -ForegroundColor Cyan
    & git push origin $Branch | Out-String | Write-Host
}

Pop-Location | Out-Null
Pop-Location | Out-Null
Write-Host "Concluído." -ForegroundColor Green
