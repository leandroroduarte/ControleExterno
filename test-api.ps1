param(
  [string]$Base = "https://localhost:7008"
)

Write-Host "== Teste API: $Base ==" -ForegroundColor Cyan

# Ignorar certificado local (apenas dev)
try {
  add-type @"
using System.Net;
using System.Security.Cryptography.X509Certificates;
public class TrustAllCertsPolicy : ICertificatePolicy {
    public bool CheckValidationResult(
        ServicePoint srvPoint, X509Certificate certificate,
        WebRequest request, int certificateProblem) {
        return true;
    }
}
"@
  [System.Net.ServicePointManager]::CertificatePolicy = New-Object TrustAllCertsPolicy
} catch {}

$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

function Assert($cond, $msg) {
  if (-not $cond) { throw "FALHA: $msg" } else { Write-Host "OK - $msg" -ForegroundColor Green }
}

# 1) Login
$loginBody = @{ email = 'demo@email.com'; senha = 'demo123456' } | ConvertTo-Json
$login = Invoke-RestMethod -Method POST -Uri "$Base/api/usuarios/login" -ContentType 'application/json' -Body $loginBody -WebSession $session
Assert ($login.mensagem -like '*sucesso*') "Login realizado"

# 2) Listar clientes (deve retornar 200)
$clientes = Invoke-RestMethod -Method GET -Uri "$Base/api/clientes" -WebSession $session
Assert ($clients -or $clientes -ne $null) "Listagem de clientes acessível"

# 3) Criar cliente
$novoCliente = @{
  Nome = "Cliente Test $(Get-Random)"
  CPF_CNPJ = "12345678901"
  Email = "cliente.$([guid]::NewGuid().ToString('N').Substring(0,8))@example.com"
  Telefone = "+55 11 90000-0000"
  Endereco = "Rua Teste, 123"
} | ConvertTo-Json
$criado = Invoke-RestMethod -Method POST -Uri "$Base/api/clientes" -ContentType 'application/json' -Body $novoCliente -WebSession $session
Assert ($criado.Id -gt 0) "Cliente criado ID=$($criado.Id)"

# 4) Buscar por ID
$buscado = Invoke-RestMethod -Method GET -Uri "$Base/api/clientes/$($criado.Id)" -WebSession $session
Assert ($buscado.Id -eq $criado.Id) "Cliente buscado por ID"

# 5) Atualizar
$update = @{
  Id = $criado.Id
  Nome = "$($buscado.Nome) Atualizado"
  CPF_CNPJ = $buscado.CPF_CNPJ
  Email = $buscado.Email
  Telefone = "(11) 98888-7777"
  Endereco = "Rua Teste, 456"
} | ConvertTo-Json
$respUpdate = Invoke-RestMethod -Method PUT -Uri "$Base/api/clientes/$($criado.Id)" -ContentType 'application/json' -Body $update -WebSession $session
Assert ($respUpdate.mensagem -like '*sucesso*') "Cliente atualizado"

# 6) Excluir
$respDelete = Invoke-RestMethod -Method DELETE -Uri "$Base/api/clientes/$($criado.Id)" -WebSession $session
Assert ($respDelete.mensagem -like '*sucesso*') "Cliente excluído"

Write-Host "== Todos os testes passaram! ==" -ForegroundColor Cyan
