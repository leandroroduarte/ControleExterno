<?php
// fornecedores.php - Lista de fornecedores
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_nome'];
$fornecedores = [];
$errors = [];
try {
    $host = '127.0.0.1'; $port = 3306; $db = 'ControleExterno'; $user = 'root'; $pass = '';
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare('SELECT id, nome, email, telefone, cpf_cnpj, cidade FROM Fornecedores WHERE user_id = ? ORDER BY nome ASC');
    $stmt->execute([$user_id]);
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $errors[] = 'Erro ao buscar fornecedores: ' . $e->getMessage(); }
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fornecedores - ControleExterno</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;background:#f5f7fa;color:#333}
    .navbar{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center}
    .navbar a{background:rgba(255,255,255,0.2);color:white;padding:8px 16px;border-radius:5px;text-decoration:none}
    .container{max-width:1200px;margin:30px auto;padding:0 20px}
    .header-section{background:white;border-radius:10px;padding:20px;margin-bottom:30px;display:flex;justify-content:space-between;align-items:center}
    .add-btn{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:12px 24px;border-radius:5px;text-decoration:none}
    .table-container{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.05)}
    table{width:100%;border-collapse:collapse;table-layout:fixed}
    thead{background:#f5f7fa;border-bottom:2px solid #e0e0e0}
    th{padding:15px;text-align:left;font-weight:600;color:#667eea}
    td{padding:15px;border-bottom:1px solid #f0f0f0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .actions{display:flex;gap:10px;min-width:140px;justify-content:flex-end}
    .btn-editar{background:#667eea;color:white;padding:6px 12px;border-radius:4px;text-decoration:none;font-size:13px;font-weight:600;transition:all 0.3s;display:inline-block}
    .btn-editar:hover{background:#5568d3}
    .btn-deletar{background:#ff6b6b;color:white;padding:6px 12px;border-radius:4px;text-decoration:none;font-size:13px;font-weight:600;transition:all 0.3s;display:inline-block;margin-left:5px}
    .btn-deletar:hover{background:#ee5a52}
    
    @media (max-width: 768px) {
      .navbar { flex-direction: column; gap: 10px; padding: 10px; }
      .navbar h1 { font-size: 16px; }
      .navbar-right { width: 100%; justify-content: space-between; }
      .container { padding: 0 10px; margin: 15px auto; }
      .header-section { flex-direction: column; gap: 15px; }
      .header-section h2 { font-size: 18px; }
      .add-btn { width: 100%; text-align: center; }
      table { font-size: 12px; }
      th, td { padding: 8px; }
      .actions { flex-direction: column; min-width: auto; }
      .btn-editar, .btn-deletar { padding: 4px 8px; font-size: 11px; width: 100%; }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>🔐 Controle Externo</h1>
    <div class="navbar-right">
      <span><?php echo htmlspecialchars($user_name); ?></span>
      <a href="dashboard.php">Dashboard</a>
      <a href="index.php?logout=1">Sair</a>
    </div>
  </div>
  <div class="container">
    <div class="header-section">
      <h2>📋 Fornecedores</h2>
      <a href="cadastro_fornecedor.php" class="add-btn">➕ Novo Fornecedor</a>
    </div>
    <?php if (!empty($errors)): ?>
      <div style="background:#ffe6e6;padding:12px;border-radius:6px;margin-bottom:20px;color:#c33">
        <ul><?php foreach($errors as $err) echo '<li>'.htmlspecialchars($err).'</li>';?></ul>
      </div>
    <?php endif; ?>

    <?php if (empty($fornecedores)): ?>
      <div style="background:white;border-radius:10px;padding:40px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,0.05)">
        <p>Nenhum fornecedor cadastrado ainda.</p>
        <a href="cadastro_fornecedor.php" class="add-btn">Cadastrar Primeiro Fornecedor</a>
      </div>
    <?php else: ?>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Nome</th>
              <th>Email</th>
              <th>Telefone</th>
              <th>CPF/CNPJ</th>
              <th>Cidade</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($fornecedores as $f): ?>
            <tr>
              <td><?php echo htmlspecialchars($f['nome']);?></td>
              <td><?php echo htmlspecialchars($f['email'] ?? '-');?></td>
              <td><?php echo htmlspecialchars($f['telefone'] ?? '-');?></td>
              <td><?php echo htmlspecialchars($f['cpf_cnpj'] ?? '-');?></td>
              <td><?php echo htmlspecialchars($f['cidade'] ?? '-');?></td>
              <td><div class="actions"><a class="btn-editar" href="editar_fornecedor.php?id=<?php echo $f['id']; ?>">✏️ Editar</a><a class="btn-deletar" href="javascript:void(0)" onclick="if(confirm('Deletar <?php echo addslashes(htmlspecialchars($f['nome'])); ?>?')) location.href='deletar_fornecedor.php?id=<?php echo $f['id']; ?>'">🗑️ Deletar</a></div></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
