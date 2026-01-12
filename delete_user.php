<?php
// delete_user.php - exclui usuário por id (GET confirma via JS, POST executa)
$id = intval($_GET['id'] ?? 0);
$errors = [];
$success = '';
if ($id <= 0) {
    $errors[] = 'ID inválido.';
} else {
    try {
        $host = '127.0.0.1'; $port = 3306; $db = 'ControleExterno'; $user = 'root'; $pass = '';
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $pdo->prepare('DELETE FROM Users WHERE id = ?');
            $stmt->execute([$id]);
            $success = 'Usuário excluído.';
        } else {
            $stmt = $pdo->prepare('SELECT id,nome,email FROM Users WHERE id = ?');
            $stmt->execute([$id]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$userRow) $errors[] = 'Usuário não encontrado.';
        }
    } catch (PDOException $e) {
        $errors[] = 'Erro no banco: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Excluir Usuário</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;max-width:720px;margin:20px auto;padding:0 12px} .errors{background:#ffe6e6;border:1px solid #ffb3b3;padding:8px} .success{background:#e6ffe6;border:1px solid #b3ffb3;padding:8px}</style>
</head>
<body>
  <h1>Excluir Usuário</h1>

  <?php if (!empty($errors)): ?>
    <div class="errors">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?php echo htmlspecialchars($err); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <p><a href="users.php">Voltar para lista</a></p>
  <?php else: ?>
    <?php if (!empty($userRow)): ?>
      <p>Confirma exclusão do usuário <strong><?php echo htmlspecialchars($userRow['nome']); ?></strong> (<?php echo htmlspecialchars($userRow['email']); ?>)?</p>
      <form method="post" action="">
        <button type="submit">Confirmar exclusão</button>
        <a href="users.php" style="margin-left:8px">Cancelar</a>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</body>
</html>
