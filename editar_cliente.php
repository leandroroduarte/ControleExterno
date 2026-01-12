<?php
// editar_cliente.php - Editar dados do cliente
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_nome'];
$id = intval($_GET['id'] ?? 0);
$errors = [];
$success = '';
$cliente = null;

if ($id <= 0) {
    $errors[] = 'ID inválido.';
} else {
    try {
        $host = '127.0.0.1';
        $port = 3306;
        $db = 'ControleExterno';
        $user = 'root';
        $pass = '';
        
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Buscar cliente
        $stmt = $pdo->prepare('SELECT * FROM Clientes WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user_id]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cliente) {
            $errors[] = 'Cliente não encontrado.';
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cliente) {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $cpf_cnpj = trim($_POST['cpf_cnpj'] ?? '');
            $cep = trim($_POST['cep'] ?? '');
            $endereco = trim($_POST['endereco'] ?? '');
            $numero = trim($_POST['numero'] ?? '');
            $complemento = trim($_POST['complemento'] ?? '');
            $bairro = trim($_POST['bairro'] ?? '');
            $cidade = trim($_POST['cidade'] ?? '');
            $estado = trim($_POST['estado'] ?? '');
            
            // Validações
            if ($nome === '') $errors[] = 'Nome é obrigatório.';
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
            
            if (empty($errors)) {
                $stmt = $pdo->prepare('UPDATE Clientes SET nome = ?, email = ?, telefone = ?, cpf_cnpj = ?, cep = ?, endereco = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ? WHERE id = ? AND user_id = ?');
                
                $stmt->execute([
                    $nome,
                    $email ?: null,
                    $telefone ?: null,
                    $cpf_cnpj ?: null,
                    $cep ?: null,
                    $endereco ?: null,
                    $numero ?: null,
                    $complemento ?: null,
                    $bairro ?: null,
                    $cidade ?: null,
                    $estado ?: null,
                    $id,
                    $user_id
                ]);
                
                $success = 'Cliente atualizado com sucesso!';
                
                // Recarregar dados
                $stmt = $pdo->prepare('SELECT * FROM Clientes WHERE id = ? AND user_id = ?');
                $stmt->execute([$id, $user_id]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    } catch (PDOException $e) {
        $errors[] = 'Erro no banco: ' . $e->getMessage();
    }
}

// Se não temos cliente, usar valores padrão
if (!$cliente) {
    $cliente = [
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'cpf_cnpj' => $_POST['cpf_cnpj'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'endereco' => $_POST['endereco'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'complemento' => $_POST['complemento'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? ''
    ];
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Cliente - ControleExterno</title>
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
    
    .navbar a:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    
    .container {
      max-width: 800px;
      margin: 30px auto;
      padding: 0 20px;
    }
    
    .form-card {
      background: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .form-card h2 {
      color: #667eea;
      margin-bottom: 30px;
      font-size: 24px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      font-family: inherit;
      transition: border-color 0.3s;
    }
    
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    select:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .section-title {
      color: #764ba2;
      font-size: 16px;
      font-weight: 600;
      margin-top: 30px;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #667eea;
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
    
    .success {
      background: #e6ffe6;
      border: 1px solid #b3ffb3;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      color: #333;
    }
    
    .form-actions {
      display: flex;
      gap: 10px;
      margin-top: 30px;
    }
    
    button, .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
      font-size: 14px;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-submit {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-cancel {
      background: #f0f0f0;
      color: #333;
      border: 1px solid #ddd;
    }
    
    .btn-cancel:hover {
      background: #e0e0e0;
    }
    
    .loading {
      display: none;
      color: #667eea;
      font-size: 13px;
      margin-top: 8px;
    }
    
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
      }
      .navbar h1 {
        font-size: 18px;
      }
      .navbar-right {
        width: 100%;
        justify-content: space-between;
      }
      .container {
        padding: 0 10px;
      }
      .form-card {
        padding: 20px;
      }
      .form-card h2 {
        font-size: 20px;
      }
      .form-row {
        grid-template-columns: 1fr;
      }
      label {
        font-size: 14px;
      }
      input[type="text"], input[type="email"], input[type="tel"], select {
        font-size: 16px;
        padding: 10px;
      }
      button, .btn {
        padding: 10px 16px;
        font-size: 12px;
      }
      .form-actions {
        flex-direction: column;
      }
      .btn-submit, .btn-cancel {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>🔐 Controle Externo</h1>
    <div class="navbar-right">
      <span><?php echo htmlspecialchars($user_name); ?></span>
      <a href="clientes.php">Voltar</a>
      <a href="index.php?logout=1">Sair</a>
    </div>
  </div>
  
  <div class="container">
    <div class="form-card">
      <h2>✏️ Editar Cliente</h2>
      
      <?php if (!empty($errors)): ?>
        <div class="errors">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="success">
          <?php echo htmlspecialchars($success); ?>
          <br><br>
          <a href="clientes.php" style="color: #667eea; font-weight: 600;">Voltar para lista de clientes</a>
        </div>
      <?php endif; ?>
      
      <?php if ($cliente && empty($errors) && !$success): ?>
      <form method="POST">
        <div class="section-title">📝 Informações Básicas</div>
        
        <div class="form-group">
          <label for="nome">Nome *</label>
          <input type="text" id="nome" name="nome" maxlength="255" required value="<?php echo htmlspecialchars($cliente['nome']); ?>">
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="255" value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>" placeholder="seu@exemplo.com" autocomplete="email">
          </div>
          <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" maxlength="20" inputmode="tel" placeholder="(11) 99999-9999" value="<?php echo htmlspecialchars($cliente['telefone'] ?? ''); ?>">
          </div>
        </div>
        
        <div class="form-group">
          <label for="cpf_cnpj">CPF/CNPJ</label>
          <input type="text" id="cpf_cnpj" name="cpf_cnpj" maxlength="20" placeholder="000.000.000-00 ou 00.000.000/0000-00" value="<?php echo htmlspecialchars($cliente['cpf_cnpj'] ?? ''); ?>">
        </div>
        
        <div class="section-title">📍 Endereço</div>
        
        <div class="form-group">
          <label for="cep">CEP</label>
          <div style="display: flex; gap: 10px;">
            <input type="text" id="cep" name="cep" maxlength="10" placeholder="00000-000" value="<?php echo htmlspecialchars($cliente['cep'] ?? ''); ?>" style="flex: 1;" inputmode="numeric">
            <button type="button" id="btn-buscar-cep" style="padding: 12px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">Buscar</button>
          </div>
          <div class="loading" id="loading-cep">Buscando endereço...</div>
        </div>
        
        <div class="form-group">
          <label for="endereco">Endereço</label>
          <input type="text" id="endereco" name="endereco" maxlength="255" value="<?php echo htmlspecialchars($cliente['endereco'] ?? ''); ?>">
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" maxlength="10" value="<?php echo htmlspecialchars($cliente['numero'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="complemento">Complemento</label>
            <input type="text" id="complemento" name="complemento" maxlength="255" value="<?php echo htmlspecialchars($cliente['complemento'] ?? ''); ?>">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="bairro">Bairro</label>
            <input type="text" id="bairro" name="bairro" maxlength="100" value="<?php echo htmlspecialchars($cliente['bairro'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="cidade">Cidade</label>
            <input type="text" id="cidade" name="cidade" maxlength="100" value="<?php echo htmlspecialchars($cliente['cidade'] ?? ''); ?>">
          </div>
        </div>
        
        <div class="form-group">
          <label for="estado">Estado</label>
          <select id="estado" name="estado">
            <option value="">Selecione um estado</option>
            <option value="AC" <?php echo ($cliente['estado'] === 'AC' ? 'selected' : ''); ?>>Acre</option>
            <option value="AL" <?php echo ($cliente['estado'] === 'AL' ? 'selected' : ''); ?>>Alagoas</option>
            <option value="AP" <?php echo ($cliente['estado'] === 'AP' ? 'selected' : ''); ?>>Amapá</option>
            <option value="AM" <?php echo ($cliente['estado'] === 'AM' ? 'selected' : ''); ?>>Amazonas</option>
            <option value="BA" <?php echo ($cliente['estado'] === 'BA' ? 'selected' : ''); ?>>Bahia</option>
            <option value="CE" <?php echo ($cliente['estado'] === 'CE' ? 'selected' : ''); ?>>Ceará</option>
            <option value="DF" <?php echo ($cliente['estado'] === 'DF' ? 'selected' : ''); ?>>Distrito Federal</option>
            <option value="ES" <?php echo ($cliente['estado'] === 'ES' ? 'selected' : ''); ?>>Espírito Santo</option>
            <option value="GO" <?php echo ($cliente['estado'] === 'GO' ? 'selected' : ''); ?>>Goiás</option>
            <option value="MA" <?php echo ($cliente['estado'] === 'MA' ? 'selected' : ''); ?>>Maranhão</option>
            <option value="MT" <?php echo ($cliente['estado'] === 'MT' ? 'selected' : ''); ?>>Mato Grosso</option>
            <option value="MS" <?php echo ($cliente['estado'] === 'MS' ? 'selected' : ''); ?>>Mato Grosso do Sul</option>
            <option value="MG" <?php echo ($cliente['estado'] === 'MG' ? 'selected' : ''); ?>>Minas Gerais</option>
            <option value="PA" <?php echo ($cliente['estado'] === 'PA' ? 'selected' : ''); ?>>Pará</option>
            <option value="PB" <?php echo ($cliente['estado'] === 'PB' ? 'selected' : ''); ?>>Paraíba</option>
            <option value="PR" <?php echo ($cliente['estado'] === 'PR' ? 'selected' : ''); ?>>Paraná</option>
            <option value="PE" <?php echo ($cliente['estado'] === 'PE' ? 'selected' : ''); ?>>Pernambuco</option>
            <option value="PI" <?php echo ($cliente['estado'] === 'PI' ? 'selected' : ''); ?>>Piauí</option>
            <option value="RJ" <?php echo ($cliente['estado'] === 'RJ' ? 'selected' : ''); ?>>Rio de Janeiro</option>
            <option value="RN" <?php echo ($cliente['estado'] === 'RN' ? 'selected' : ''); ?>>Rio Grande do Norte</option>
            <option value="RS" <?php echo ($cliente['estado'] === 'RS' ? 'selected' : ''); ?>>Rio Grande do Sul</option>
            <option value="RO" <?php echo ($cliente['estado'] === 'RO' ? 'selected' : ''); ?>>Rondônia</option>
            <option value="RR" <?php echo ($cliente['estado'] === 'RR' ? 'selected' : ''); ?>>Roraima</option>
            <option value="SC" <?php echo ($cliente['estado'] === 'SC' ? 'selected' : ''); ?>>Santa Catarina</option>
            <option value="SP" <?php echo ($cliente['estado'] === 'SP' ? 'selected' : ''); ?>>São Paulo</option>
            <option value="SE" <?php echo ($cliente['estado'] === 'SE' ? 'selected' : ''); ?>>Sergipe</option>
            <option value="TO" <?php echo ($cliente['estado'] === 'TO' ? 'selected' : ''); ?>>Tocantins</option>
          </select>
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn-submit">💾 Atualizar Cliente</button>
          <a href="clientes.php" class="btn btn-cancel">Cancelar</a>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>
  
  <script>
    // Máscaras: Telefone, CEP, CPF/CNPJ
    function onlyDigits(str) { return (str || '').replace(/\D/g, ''); }

    // Telefone: (00) 0000-0000 ou (00) 00000-0000
    const telefoneEl = document.getElementById('telefone');
    telefoneEl && telefoneEl.addEventListener('input', function (e) {
      let v = onlyDigits(e.target.value).slice(0,11);
      let out = '';
      if (v.length <= 2) out = v;
      else if (v.length <= 6) out = '(' + v.slice(0,2) + ') ' + v.slice(2);
      else if (v.length <= 10) out = '(' + v.slice(0,2) + ') ' + v.slice(2,6) + '-' + v.slice(6);
      else out = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
      e.target.value = out;
    });

    // CEP: 00000-000
    const cepEl = document.getElementById('cep');
    cepEl && cepEl.addEventListener('input', function (e) {
      let v = onlyDigits(e.target.value).slice(0,8);
      if (v.length > 5) e.target.value = v.slice(0,5) + '-' + v.slice(5);
      else e.target.value = v;
    });

    // CPF/CNPJ (dinâmico)
    const cpfEl = document.getElementById('cpf_cnpj');
    cpfEl && cpfEl.addEventListener('input', function (e) {
      let v = onlyDigits(e.target.value).slice(0,14);
      if (v.length <= 11) {
        v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, function(_,a,b,c,d){
          let s = a || '';
          if (b) s += '.' + b;
          if (c) s += '.' + c;
          if (d) s += '-' + d;
          return s;
        });
      } else {
        v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*/, function(_,a,b,c,d,f){
          let s = a || '';
          if (b) s += '.' + b;
          if (c) s += '.' + c;
          if (d) s += '/' + d;
          if (f) s += '-' + f;
          return s;
        });
      }
      e.target.value = v;
    });

    // Buscar CEP (ViaCEP)
    document.getElementById('btn-buscar-cep').addEventListener('click', function() {
      const cep = onlyDigits(document.getElementById('cep').value);
      if (cep.length !== 8) {
        alert('CEP deve ter 8 dígitos');
        return;
      }

      const loading = document.getElementById('loading-cep');
      loading.style.display = 'block';

      fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
          loading.style.display = 'none';
          if (data.erro) { alert('CEP não encontrado'); return; }
          document.getElementById('endereco').value = data.logradouro || '';
          document.getElementById('bairro').value = data.bairro || '';
          document.getElementById('cidade').value = data.localidade || '';
          document.getElementById('estado').value = data.uf || '';
        })
        .catch(error => {
          loading.style.display = 'none';
          console.error('Erro ao buscar CEP:', error);
          alert('Erro ao buscar CEP. Verifique sua conexão de internet.');
        });
    });

    // Permitir buscar CEP ao pressionar Enter
    cepEl && cepEl.addEventListener('keypress', function(e) { if (e.key === 'Enter') { e.preventDefault(); document.getElementById('btn-buscar-cep').click(); } });

    // Validação simples de email antes do submit
    document.querySelector('form').addEventListener('submit', function (e) {
      const email = document.getElementById('email').value.trim();
      if (email && email.length > 255) { e.preventDefault(); alert('Email muito longo (máx 255 caracteres).'); return false; }
      if (email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) { e.preventDefault(); alert('Email inválido.'); return false; }
    });
  </script>
</body>
</html>
