<?php
// clientes.php - Lista de clientes
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_nome'];
$clientes = [];
$errors = [];

try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Buscar clientes do usuário logado
    $stmt = $pdo->prepare('SELECT id, nome, email, telefone, cpf_cnpj, cidade FROM Clientes WHERE user_id = ? ORDER BY nome ASC');
    $stmt->execute([$user_id]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Erro ao buscar clientes: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clientes - ControleExterno</title>
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
      align-items: center;
    }
    
    .navbar a, .navbar button {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: 1px solid white;
      padding: 8px 16px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .navbar a:hover, .navbar button:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    
    .container {
      max-width: 1200px;
      margin: 30px auto;
      padding: 0 20px;
    }
    
    .header-section {
      background: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .header-section h2 {
      color: #667eea;
      font-size: 24px;
    }
    
    .add-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .add-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    
    .errors {
      background: #ffe6e6;
      border: 1px solid #ffb3b3;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      color: #c33;
    }
    
    .errors ul {
      margin-left: 20px;
    }
    
    .empty-state {
      background: white;
      border-radius: 10px;
      padding: 40px;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .empty-state p {
      color: #999;
      margin-bottom: 20px;
      font-size: 16px;
    }
    
    .table-container {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed; /* evita que células muito longas empurrem ações */
    }
    
    thead {
      background: #f5f7fa;
      border-bottom: 2px solid #e0e0e0;
    }
    
    th {
      padding: 15px;
      text-align: left;
      font-weight: 600;
      color: #667eea;
    }
    
    td {
      padding: 15px;
      border-bottom: 1px solid #f0f0f0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap; /* usar ellipsis para textos longos */
    }
    
    tbody tr:hover {
      background: #fafbfc;
    }
    
    .actions {
      display: flex;
      gap: 10px;
      min-width: 140px; /* garante espaço mínimo para botões */
      justify-content: flex-end;
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
    }
    
    .btn-editar {
      background: #667eea;
      color: white;
    }
    
    .btn-editar:hover {
      background: #5568d3;
    }
    
    .btn-deletar {
      background: #ff6b6b;
      color: white;
      margin-left: 5px;
    }
    
    .btn-deletar:hover {
      background: #ee5a52;
    }
    
    .actions {
      display: flex;
      gap: 5px;
    }

    /* Garantir que a coluna de ações nunca seja truncada */
    td .actions, td:last-child { white-space: nowrap; }
    
    @media (max-width: 768px) {
      .container { margin: 20px auto; padding: 0 15px; }
      .header-section { flex-direction: column; align-items: flex-start; gap: 15px; }
      .header-section h2 { font-size: 20px; }
      .add-btn { width: 100%; text-align: center; }
      table { font-size: 13px; }
      th, td { padding: 10px; }
      .actions { min-width: 100px; }
      .btn-editar, .btn-deletar { padding: 5px 10px; font-size: 12px; }
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
      <h2>📋 Clientes</h2>
      <a href="cadastro_cliente.php" class="add-btn">➕ Novo Cliente</a>
    </div>
    
    <?php if (!empty($errors)): ?>
      <div class="errors">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <?php if (empty($clientes)): ?>
      <div class="empty-state">
        <p>Nenhum cliente cadastrado ainda.</p>
        <a href="cadastro_cliente.php" class="add-btn">Cadastrar Primeiro Cliente</a>
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
            <?php foreach ($clientes as $cliente): ?>
              <tr>
                <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                <td><?php echo htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($cliente['telefone'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($cliente['cpf_cnpj'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($cliente['cidade'] ?? '-'); ?></td>
                <td>
                  <div class="actions">
                    <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn-editar">✏️ Editar</a>
                    <a href="javascript:void(0)" onclick="confirmarDelecao(<?php echo $cliente['id']; ?>, '<?php echo htmlspecialchars($cliente['nome']); ?>')" class="btn-deletar">🗑️ Deletar</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
  
  <script>
    function confirmarDelecao(id, nome) {
      if (confirm(`Deseja realmente deletar o cliente "${nome}"? Esta ação não poderá ser desfeita.`)) {
        window.location.href = 'deletar_cliente.php?id=' + id;
      }
    }
  </script>
</body>
</html>
