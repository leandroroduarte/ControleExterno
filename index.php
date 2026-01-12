<?php
// Página inicial do projeto ControleExterno
session_start();
$logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_nome'] ?? '';
$user_type = $_SESSION['user_tipo'] ?? '';

// Se clicou em logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Se já está logado, redireciona para painel
if ($logged_in) {
    header('Location: dashboard.php');
    exit;
}

// Processar login
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    if ($email === '') $errors[] = 'Email é obrigatório.';
    if ($senha === '') $errors[] = 'Senha é obrigatória.';
    
    if (empty($errors)) {
        try {
            $host = '127.0.0.1';
            $port = 3306;
            $db = 'ControleExterno';
            $user = 'root';
            $pass = '';
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
            
            $stmt = $pdo->prepare('SELECT id, nome, email, senha, tipo, verified FROM Users WHERE email = ?');
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
              if ((int)($usuario['verified'] ?? 0) !== 1) {
                $errors[] = 'Conta não confirmada. Verifique seu email para confirmar.';
              } else {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nome'] = $usuario['nome'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_tipo'] = $usuario['tipo'];
                header('Location: dashboard.php');
                exit;
              }
            } else {
                $errors[] = 'Email ou senha inválidos.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erro no banco: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - ControleExterno</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .container {
      width: 100%;
      max-width: 400px;
      padding: 20px;
    }
    
    .login-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      padding: 40px;
    }
    
    .login-card h1 {
      text-align: center;
      color: #333;
      margin-bottom: 30px;
      font-size: 28px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-weight: 600;
      font-size: 14px;
    }
    
    input[type="email"],
    input[type="password"],
    select {
      width: 100%;
      padding: 12px;
      border: 2px solid #e0e0e0;
      border-radius: 5px;
      font-size: 14px;
      transition: border-color 0.3s;
    }
    
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
      outline: none;
      border-color: #667eea;
    }
    
    .login-options {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
    }
    
    .login-option {
      flex: 1;
      padding: 15px;
      border: 2px solid #e0e0e0;
      border-radius: 5px;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s;
    }
    
    .login-option input[type="radio"] {
      display: none;
    }
    
    .login-option input[type="radio"]:checked + label {
      color: #667eea;
    }
    
    .login-option input[type="radio"]:checked {
      border-color: #667eea;
      background: #f0f4ff;
    }
    
    .login-option label {
      margin: 0;
      cursor: pointer;
      font-weight: 600;
    }
    
    .errors {
      background: #ffe6e6;
      border: 1px solid #ffb3b3;
      border-radius: 5px;
      padding: 12px;
      margin-bottom: 20px;
      color: #d32f2f;
      font-size: 14px;
    }
    
    .errors ul {
      margin: 0;
      padding-left: 20px;
    }
    
    .errors li {
      margin: 5px 0;
    }
    
    button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s;
    }
    
    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    button:active {
      transform: translateY(0);
    }
    
    .signup-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #666;
    }
    
    .signup-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
    }
    
    .signup-link a:hover {
      text-decoration: underline;
    }
    
    .nav-links {
      text-align: center;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #e0e0e0;
    }
    
    .nav-links a {
      display: inline-block;
      margin: 0 10px;
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
    }
    
    .nav-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="login-card">
      <h1>🔐 Controle Externo</h1>
      
      <?php if (!empty($errors)): ?>
        <div class="errors">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <form method="POST">
        <!-- Bloco de tipo de login removido conforme solicitação -->

        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="seu@email.com" 
            required
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
          >
        </div>
        
        <div class="form-group">
          <label for="senha">Senha</label>
          <input 
            type="password" 
            id="senha" 
            name="senha" 
            placeholder="Digite sua senha" 
            required
          >
        </div>
        
        <button type="submit">Entrar</button>
      </form>
      
      <div class="signup-link">
        Não tem conta? <a href="signup.php">Cadastre-se aqui</a>
      </div>
    </div>
  </div>
</body>
</html>
