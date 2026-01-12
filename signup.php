<?php
// signup.php - formulário de cadastro e processamento
// Carregar autoloader do Composer se disponível (para PHPMailer)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
  require __DIR__ . '/vendor/autoload.php';
}

$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tipo = $_POST['tipo'] ?? 'user';
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if ($nome === '') $errors[] = 'Nome é obrigatório.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if (strlen($senha) < 6) $errors[] = 'Senha deve ter ao menos 6 caracteres.';
    if ($senha !== $senha2) $errors[] = 'Senhas não conferem.';
    if (!in_array($tipo, ['user','admin'])) $tipo = 'user';

    if (empty($errors)) {
        try {
            $host = '127.0.0.1';
            $port = 3306; // ajuste se seu MySQL usa outra porta
            $db = 'ControleExterno';
            $user = 'root';
            $pass = '';
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

            // Garantir colunas de verificação existem
            $colsStmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'Users'");
            $colsStmt->execute([$db]);
            $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
            $needToken = !in_array('verification_token', $cols);
            $needVerified = !in_array('verified', $cols);
            $needVerifiedAt = !in_array('verified_at', $cols);
            if ($needToken) {
              $pdo->exec("ALTER TABLE Users ADD COLUMN verification_token VARCHAR(64) NULL");
            }
            if ($needVerified) {
              $pdo->exec("ALTER TABLE Users ADD COLUMN verified TINYINT(1) NOT NULL DEFAULT 0");
            }
            if ($needVerifiedAt) {
              $pdo->exec("ALTER TABLE Users ADD COLUMN verified_at TIMESTAMP NULL");
            }

            $stmt = $pdo->prepare('SELECT id FROM Users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email já cadastrado.';
            } else {
              $hash = password_hash($senha, PASSWORD_DEFAULT);
              $token = bin2hex(random_bytes(32));
              $ins = $pdo->prepare('INSERT INTO Users (nome, email, senha, tipo, verification_token, verified) VALUES (?,?,?,?,?,0)');
              $ins->execute([$nome, $email, $hash, $tipo, $token]);

              // Enviar e-mail de confirmação, se PHPMailer estiver disponível
              $mailOk = false;
              $mailMsg = '';
              if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                try {
                  $confirmUrl = 'http://localhost:8080/ControleExterno/confirm.php?token=' . urlencode($token) . '&email=' . urlencode($email);
                  $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                  $mail->isSMTP();
                  $mail->Host = 'smtp.zoho.com';
                  $mail->SMTPAuth = true;
                  $mail->Username = 'leandroroduarte@guilhermemduarte.systems';
                  $mail->Password = '@Gmd320808';
                  $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                  $mail->Port = 587;
                  $mail->CharSet = 'UTF-8';
                  $mail->setFrom('leandroroduarte@guilhermemduarte.systems', 'Controle Externo');
                  $mail->addAddress($email, $nome);
                  $mail->Subject = 'Confirme sua conta no Controle Externo';
                  $mail->isHTML(true);
                  $mail->Body = '<p>Olá ' . htmlspecialchars($nome) . ',</p>'
                    . '<p>Obrigado por se cadastrar. Para confirmar sua conta, clique no link abaixo:</p>'
                    . '<p><a href="' . htmlspecialchars($confirmUrl) . '">Confirmar minha conta</a></p>'
                    . '<p>Se não conseguiu clicar, copie e cole este link no navegador:<br>' . htmlspecialchars($confirmUrl) . '</p>'
                    . '<p>Atenciosamente,<br>Equipe Controle Externo</p>';
                  $mail->AltBody = 'Olá ' . $nome . "\n\n" . 'Confirme sua conta acessando: ' . $confirmUrl;
                  $mail->send();
                  // Atualiza timestamp de envio
                  $pdo->prepare('UPDATE Users SET verified_at = NULL WHERE email = ?')->execute([$email]);
                  $mailOk = true;
                  $mailMsg = 'Email de confirmação enviado.';
                } catch (Throwable $me) {
                  $mailMsg = 'Conta criada, mas falhou envio de email: ' . $me->getMessage();
                }
              } else {
                $mailMsg = 'Conta criada. Configure PHPMailer para enviar o e-mail de confirmação.';
              }

              $success = $mailMsg;
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
  <title>Cadastro - ControleExterno</title>
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
      padding: 20px;
    }
    
    .container {
      width: 100%;
      max-width: 500px;
    }
    
    .signup-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      padding: 40px;
    }
    
    .signup-card h1 {
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
    
    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
      width: 100%;
      padding: 12px;
      border: 2px solid #e0e0e0;
      border-radius: 5px;
      font-size: 14px;
      transition: border-color 0.3s;
      font-family: inherit;
    }
    
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
      outline: none;
      border-color: #667eea;
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
    
    .success {
      background: #e6ffe6;
      border: 1px solid #b3ffb3;
      border-radius: 5px;
      padding: 12px;
      margin-bottom: 20px;
      color: #2e7d32;
      font-size: 14px;
      text-align: center;
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
    
    .login-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #666;
    }
    
    .login-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
    }
    
    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="signup-card">
      <h1>📝 Criar Conta</h1>

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
        <div class="success">
          ✓ <?php echo htmlspecialchars($success); ?>
          <p style="margin-top: 10px; font-size: 13px;">
            <a href="index.php" style="color: #2e7d32; text-decoration: none;">Clique aqui para fazer login</a>
          </p>
        </div>
      <?php endif; ?>

      <?php if (!$success): ?>
        <form method="post" action="">
          <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input 
              type="text" 
              id="nome"
              name="nome" 
              placeholder="Seu nome completo" 
              value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" 
              required
            >
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input 
              type="email" 
              id="email"
              name="email" 
              placeholder="seu@email.com" 
              value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
              required
            >
          </div>

          <div class="form-group">
            <label for="tipo">Tipo de Conta</label>
            <select id="tipo" name="tipo">
              <option value="user" <?php echo (($_POST['tipo'] ?? '') === 'user') ? 'selected' : ''; ?>>👤 Usuário</option>
              <option value="admin" <?php echo (($_POST['tipo'] ?? '') === 'admin') ? 'selected' : ''; ?>>👨‍💼 Administrador</option>
            </select>
          </div>

          <div class="form-group">
            <label for="senha">Senha</label>
            <input 
              type="password" 
              id="senha"
              name="senha" 
              placeholder="Mínimo 6 caracteres" 
              required
            >
          </div>

          <div class="form-group">
            <label for="senha2">Confirme a Senha</label>
            <input 
              type="password" 
              id="senha2"
              name="senha2" 
              placeholder="Confirme sua senha" 
              required
            >
          </div>

          <button type="submit">Criar Conta</button>
        </form>

        <div class="login-link">
          Já tem uma conta? <a href="index.php">Faça login aqui</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
