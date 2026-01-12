<?php
// edit_user.php - editar usuário simples
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
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $tipo = $_POST['tipo'] ?? 'user';
            if ($nome === '') $errors[] = 'Nome é obrigatório.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
            if (!in_array($tipo, ['user','admin'])) $tipo = 'user';

            if (empty($errors)) {
                $stmt = $pdo->prepare('UPDATE Users SET nome = ?, email = ?, tipo = ? WHERE id = ?');
                $stmt->execute([$nome, $email, $tipo, $id]);
                $success = 'Usuário atualizado.';
            }
        }

        $stmt = $pdo->prepare('SELECT id,nome,email,tipo FROM Users WHERE id = ?');
        $stmt->execute([$id]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$userRow) $errors[] = 'Usuário não encontrado.';
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
  <title>Editar Usuário</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;max-width:720px;margin:20px auto;padding:0 12px} label{display:block;margin-top:8px} input,select{width:100%;padding:8px;margin-top:4px} .errors{background:#ffe6e6;border:1px solid #ffb3b3;padding:8px} .success{background:#e6ffe6;border:1px solid #b3ffb3;padding:8px}</style>
</head>
<body>
  <h1>Editar Usuário</h1>

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
  <?php endif; ?>

  <?php if (!empty($userRow)): ?>
    <form method="post" action="">
      <label>Nome
        <input type="text" name="nome" value="<?php echo htmlspecialchars($userRow['nome']); ?>" required>
      </label>

      <label>Email
        <input type="email" name="email" value="<?php echo htmlspecialchars($userRow['email']); ?>" required>
      </label>

      <label>Tipo
        <select name="tipo">
          <option value="user" <?php echo ($userRow['tipo'] === 'user') ? 'selected' : ''; ?>>Usuário</option>
          <option value="admin" <?php echo ($userRow['tipo'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
        </select>
      </label>

      <p><button type="submit">Salvar</button></p>
    </form>
  <?php endif; ?>

  <p><a href="users.php">Voltar</a></p>
</body>
</html>
