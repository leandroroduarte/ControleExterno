<?php
// dashboard.php - Painel do usuário logado
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_name = $_SESSION['user_nome'];
$user_email = $_SESSION['user_email'];
$user_type = $_SESSION['user_tipo'];
$is_admin = ($user_type === 'admin');

// Se clicou em logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - ControleExterno</title>
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
      gap: 20px;
      align-items: center;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .user-badge {
      background: rgba(255, 255, 255, 0.2);
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .logout-btn {
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
    
    .logout-btn:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    
    .container {
      max-width: 1200px;
      margin: 30px auto;
      padding: 0 20px;
    }
    
    .welcome-card {
      background: white;
      border-radius: 10px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .welcome-card h2 {
      color: #667eea;
      margin-bottom: 15px;
      font-size: 28px;
    }
    
    .welcome-card p {
      color: #666;
      margin: 8px 0;
      font-size: 16px;
    }
    
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .menu-card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
      text-decoration: none;
      color: inherit;
    }
    
    .menu-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
    }
    
    .menu-card .icon {
      font-size: 40px;
      margin-bottom: 15px;
    }
    
    .menu-card h3 {
      color: #333;
      margin-bottom: 8px;
      font-size: 18px;
    }
    
    .menu-card p {
      color: #666;
      font-size: 14px;
    }
    
    .admin-section {
      background: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }
    
    .admin-section h3 {
      color: #764ba2;
      margin-bottom: 20px;
      border-bottom: 2px solid #667eea;
      padding-bottom: 10px;
    }
    
    .admin-menu {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }
    
    .admin-link {
      background: #f5f7fa;
      padding: 15px;
      border-radius: 5px;
      text-decoration: none;
      color: #667eea;
      border: 1px solid #e0e0e0;
      transition: all 0.3s;
      text-align: center;
      font-weight: 600;
    }
    
    .admin-link:hover {
      background: #667eea;
      color: white;
      border-color: #667eea;
    }
    
    .info-box {
      background: #e8eaf6;
      border-left: 4px solid #667eea;
      padding: 15px;
      border-radius: 5px;
      margin: 20px 0;
      color: #333;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>🔐 Controle Externo</h1>
    <div class="navbar-right">
      <div class="user-info">
        <span><?php echo htmlspecialchars($user_name); ?></span>
        <span class="user-badge"><?php echo ($is_admin ? '👨‍💼 Administrador' : '👤 Usuário'); ?></span>
      </div>
      <a href="?logout=1" class="logout-btn">Sair</a>
    </div>
  </div>
  
  <div class="container">
    <div class="welcome-card">
      <h2>Bem-vindo, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
      <p><strong>Tipo de Conta:</strong> <?php echo ($is_admin ? 'Administrador' : 'Usuário comum'); ?></p>
      <div class="info-box">
        <?php if ($is_admin): ?>
          Você tem acesso a todas as funcionalidades administrativas do sistema.
        <?php else: ?>
          Você tem acesso às funcionalidades de usuário comum do sistema.
        <?php endif; ?>
      </div>
    </div>
    
    <h2 style="margin-bottom: 20px;">📋 Menu Principal</h2>
    <div class="menu-grid">
      <a href="clientes.php" class="menu-card">
        <div class="icon">🤝</div>
        <h3>Clientes</h3>
        <p>Gerenciar clientes cadastrados</p>
      </a>
      <a href="fornecedores.php" class="menu-card">
        <div class="icon">🏷️</div>
        <h3>Fornecedores</h3>
        <p>Gerenciar fornecedores cadastrados</p>
      </a>
      
      <a href="produtos.php" class="menu-card">
        <div class="icon">📦</div>
        <h3>Produtos</h3>
        <p>Gerenciar produtos com imagens</p>
      </a>
      
      <a href="users.php" class="menu-card">
        <div class="icon">👥</div>
        <h3>Usuários</h3>
        <p>Visualizar lista de usuários</p>
      </a>
      
      <a href="signup.php" class="menu-card">
        <div class="icon">➕</div>
          <h3>Signup</h3>
          <p>Criar novo usuário no sistema</p>
      </a>
      
      <a href="index.php" class="menu-card">
        <div class="icon">🏠</div>
        <h3>Página Inicial</h3>
        <p>Voltar para a página de login</p>
      </a>
    </div>
    
    <?php if ($is_admin): ?>
      <div class="admin-section">
        <h3>🔧 Painel Administrativo</h3>
        <p style="color: #666; margin-bottom: 20px;">Você tem acesso a ferramentas administrativas avançadas:</p>
        <div class="admin-menu">
          <a href="users.php?tipo=all" class="admin-link">Gerenciar Usuários</a>
          <a href="users.php?tipo=user" class="admin-link">Ver Usuários Comuns</a>
          <a href="users.php?tipo=admin" class="admin-link">Ver Administradores</a>
          <a href="signup.php" class="admin-link">Criar Novo Usuário</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>