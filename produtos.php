<?php
// produtos.php - Lista de produtos
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_nome'];
$produtos = [];
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
    
    // Buscar produtos do usuário logado
    $stmt = $pdo->prepare('SELECT id, nome, descricao, preco, quantidade, imagem FROM Produtos WHERE user_id = ? ORDER BY nome ASC');
    $stmt->execute([$user_id]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Erro ao buscar produtos: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produtos - ControleExterno</title>
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
    
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 15px;
      margin-bottom: 30px;
    }
    
    .product-card {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: all 0.3s;
      display: flex;
      flex-direction: column;
    }
    
    .product-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
    }
    
    .product-image {
      width: 100%;
      height: 150px;
      object-fit: cover;
      background: #f5f7fa;
      display: block;
    }
    
    .product-info {
      padding: 12px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .product-name {
      font-size: 14px;
      font-weight: 600;
      color: #333;
      margin-bottom: 6px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      line-height: 1.2;
    }
    
    .product-desc {
      font-size: 12px;
      color: #666;
      margin-bottom: 8px;
      display: -webkit-box;
      -webkit-line-clamp: 1;
      -webkit-box-orient: vertical;
      overflow: hidden;
      height: 16px;
    }
    
    .product-details {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      padding-bottom: 10px;
      border-bottom: 1px solid #f0f0f0;
    }
    
    .product-price {
      font-size: 15px;
      font-weight: 600;
      color: #667eea;
    }
    
    .product-qty {
      font-size: 12px;
      color: #999;
    }
    
    .actions {
      display: flex;
      gap: 5px;
      flex: 1;
      align-items: flex-end;
    }
    
    .btn-editar {
      background: #667eea;
      color: white;
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      text-decoration: none;
      font-size: 11px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      flex: 1;
      text-align: center;
    }
    
    .btn-editar:hover {
      background: #5568d3;
    }
    
    .btn-deletar {
      background: #ff6b6b;
      color: white;
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      text-decoration: none;
      font-size: 11px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      flex: 1;
      text-align: center;
    }
    
    .btn-deletar:hover {
      background: #ee5a52;
    }
    
    @media (max-width: 768px) {
      .container { margin: 20px auto; padding: 0 15px; }
      .header-section { flex-direction: column; align-items: flex-start; gap: 15px; }
      .header-section h2 { font-size: 20px; }
      .add-btn { width: 100%; text-align: center; }
      .products-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; }
      .product-image { height: 120px; }
      .product-info { padding: 10px; }
      .product-name { font-size: 12px; }
      .product-price { font-size: 13px; }
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
      <h2>📦 Produtos</h2>
      <a href="cadastro_produto.php" class="add-btn">➕ Novo Produto</a>
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
    
    <?php if (empty($produtos)): ?>
      <div class="empty-state">
        <p>Nenhum produto cadastrado ainda.</p>
        <a href="cadastro_produto.php" class="add-btn">Cadastrar Primeiro Produto</a>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php foreach ($produtos as $produto): ?>
          <div class="product-card">
            <?php if ($produto['imagem'] && file_exists('uploads/' . $produto['imagem'])): ?>
              <img src="uploads/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image">
            <?php else: ?>
              <div class="product-image" style="display: flex; align-items: center; justify-content: center; font-size: 50px; color: #ddd;">📸</div>
            <?php endif; ?>
            <div class="product-info">
              <div class="product-name"><?php echo htmlspecialchars($produto['nome']); ?></div>
              <div class="product-desc"><?php echo htmlspecialchars($produto['descricao'] ?? 'Sem descrição'); ?></div>
              <div class="product-details">
                <div class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                <div class="product-qty">Qtd: <?php echo intval($produto['quantidade']); ?></div>
              </div>
              <div class="actions">
                <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn-editar">✏️ Editar</a>
                <a href="javascript:void(0)" class="btn-deletar" onclick="if(confirm('Deletar produto: <?php echo addslashes(htmlspecialchars($produto['nome'])); ?>?')) location.href='deletar_produto.php?id=<?php echo $produto['id']; ?>'">🗑️ Deletar</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
