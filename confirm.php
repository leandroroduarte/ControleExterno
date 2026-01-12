<?php
// confirm.php - Confirmação de conta por token
$ok = false;
$msg = '';
try {
    $token = $_GET['token'] ?? '';
    $email = $_GET['email'] ?? '';
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        throw new Exception('Token inválido.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido.');
    }
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    // Verificar usuário e token
    $stmt = $pdo->prepare('SELECT id, verified FROM Users WHERE email = ? AND verification_token = ?');
    $stmt->execute([$email, $token]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$u) {
        throw new Exception('Link de confirmação inválido ou expirado.');
    }
    if ((int)$u['verified'] === 1) {
        $ok = true;
        $msg = 'Conta já foi confirmada anteriormente.';
    } else {
        $upd = $pdo->prepare('UPDATE Users SET verified = 1, verification_token = NULL, verified_at = CURRENT_TIMESTAMP WHERE id = ?');
        $upd->execute([$u['id']]);
        $ok = true;
        $msg = 'Conta confirmada com sucesso!';
    }
} catch (Throwable $e) {
    $msg = $e->getMessage();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmação de Conta</title>
  <style>
    body { font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; background:#f5f7fa; display:flex; align-items:center; justify-content:center; min-height:100vh; }
    .card { background:#fff; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,0.1); padding:30px; max-width:500px; }
    h1 { margin:0 0 15px; color:#333; }
    p { color:#555; }
    .ok { color:#2e7d32; }
    .err { color:#d32f2f; }
    a.btn { display:inline-block; margin-top:15px; padding:10px 16px; background:linear-gradient(135deg,#667eea 0%, #764ba2 100%); color:#fff; text-decoration:none; border-radius:6px; }
  </style>
</head>
<body>
  <div class="card">
    <h1><?php echo $ok ? '✅ Sucesso' : '❌ Atenção'; ?></h1>
    <p class="<?php echo $ok ? 'ok' : 'err'; ?>"><?php echo htmlspecialchars($msg); ?></p>
    <a class="btn" href="index.php">Ir para Login</a>
  </div>
</body>
</html>
