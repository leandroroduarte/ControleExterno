<?php
// users.php - lista usuários e filtra por tipo
session_start();

$tipo = $_GET['tipo'] ?? 'all';
$allowed = ['all','user','admin'];
if (!in_array($tipo, $allowed, true)) $tipo = 'all';
$errors = [];
$users = [];
$logged_in = isset($_SESSION['user_id']);

try {
    $host = '127.0.0.1';
    $port = 3306; // ajuste se necessário
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    if ($tipo === 'all') {
        $stmt = $pdo->query('SELECT id,nome,email,tipo,created_at FROM Users ORDER BY id DESC');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare('SELECT id,nome,email,tipo,created_at FROM Users WHERE tipo = ? ORDER BY id DESC');
        $stmt->execute([$tipo]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $errors[] = 'Erro no banco: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Usuários - ControleExterno</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f7fa;
      color: #333;
    }
    
    .navbar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .navbar h1 {
      font-size: 24px;
      font-weight: 600;
    }
    
    .navbar-right {
      display: flex;
      gap: 15px;
    }
    
    .navbar a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 8px 16px;
      border-radius: 5px;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.2);
    }
    
    .navbar a:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    
    .container {
      max-width: 1200px;
      margin: 30px auto;
      padding: 0 20px;
    }
    
    .nav-buttons {
      display: flex;
      gap: 10px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }
    
    .nav-buttons a {
      display: inline-block;
      padding: 10px 16px;
      background: white;
      color: #667eea;
      text-decoration: none;
      border-radius: 5px;
      border: 2px solid #667eea;
      font-weight: 600;
      transition: all 0.3s;
      cursor: pointer;
    }
    
    .nav-buttons a:hover {
      background: #667eea;
      color: white;
    }
    
    .nav-buttons a.active {
      background: #667eea;
      color: white;
    }
    
    .errors {
      background: #ffe6e6;
      border: 1px solid #ffb3b3;
      border-radius: 5px;
      padding: 12px;
      margin-bottom: 20px;
      color: #d32f2f;
    }
    
    .errors ul {
      margin: 0;
      padding-left: 20px;
    }
    
    .errors li {
      margin: 5px 0;
    }
    
    .table-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      overflow-x: auto;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    
    th {
      background: #f4f4f4;
      font-weight: 600;
      color: #333;
    }
    
    tr:hover {
      background: #f9f9f9;
    }
    
    a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
    }
    
    a:hover {
      text-decoration: underline;
    }
    
    .btn-editar, .btn-deletar {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
      transition: all 0.3s;
      display: inline-block;
    }
    
    .btn-editar {
      background: #667eea;
      color: white;
    }
    
    .btn-editar:hover {
      background: #5568d3;
      text-decoration: none;
    }
    
    .btn-deletar {
      background: #ff6b6b;
      color: white;
      margin-left: 5px;
    }
    
    .btn-deletar:hover {
      background: #ee5a52;
      text-decoration: none;
    }
    
    .actions {
      display: flex;
      gap: 5px;
    }
    
    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #666;
    }
    
    .empty-state p {
      font-size: 18px;
      margin: 10px 0;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>👥 Usuários</h1>
    <div class="navbar-right">
      <?php if ($logged_in): ?>
        <span style="display: flex; align-items: center; margin-right: 20px;">
          Olá, <strong><?php echo htmlspecialchars($_SESSION['user_nome']); ?></strong>
        </span>
        <a href="logout.php">Sair</a>
      <?php else: ?>
        <a href="index.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="container">
    <div class="nav-buttons">
      <a href="users.php?tipo=all" <?php echo ($tipo === 'all') ? 'class="active"' : ''; ?>>📋 Todos</a>
      <a href="users.php?tipo=user" <?php echo ($tipo === 'user') ? 'class="active"' : ''; ?>>👤 Usuários</a>
      <a href="users.php?tipo=admin" <?php echo ($tipo === 'admin') ? 'class="active"' : ''; ?>>👨‍💼 Administradores</a>
      <a href="signup.php">➕ Cadastrar Novo</a>
      <a href="dashboard.php">🏠 Dashboard</a>
      <a href="index.php">🔙 Voltar</a>
    </div>

    <?php if ($errors): ?>
      <div class="errors">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?php echo htmlspecialchars($err); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="table-card">
      <?php if (empty($users)): ?>
        <div class="empty-state">
          <p>📭 Nenhum usuário encontrado.</p>
          <p style="font-size: 14px; color: #999;">
            <a href="signup.php">Cadastre um novo usuário aqui</a>
          </p>
        </div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nome</th>
              <th>Email</th>
              <th>Tipo</th>
              <th>Criado em</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?php echo htmlspecialchars($u['id']); ?></td>
                <td><?php echo htmlspecialchars($u['nome']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                  <?php 
                    if ($u['tipo'] === 'admin') {
                      echo '👨‍💼 Administrador';
                    } else {
                      echo '👤 Usuário';
                    }
                  ?>
                </td>
                <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                <td>
                  <div class="actions">
                    <a href="edit_user.php?id=<?php echo urlencode($u['id']); ?>" class="btn-editar">✏️ Editar</a>
                    <a href="javascript:void(0)" class="btn-deletar" onclick="if(confirm('Confirma exclusão do usuário: <?php echo addslashes(htmlspecialchars($u['nome'])); ?>?')) location.href='delete_user.php?id=<?php echo urlencode($u['id']); ?>'" >🗑️ Deletar</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
