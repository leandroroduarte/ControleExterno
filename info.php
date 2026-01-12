<?php
// info.php - Página informativa sobre o sistema de login
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Informações - ControleExterno</title>
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
      line-height: 1.6;
    }
    
    .navbar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .navbar h1 {
      font-size: 28px;
      font-weight: 600;
    }
    
    .container {
      max-width: 1200px;
      margin: 30px auto;
      padding: 0 20px;
    }
    
    .card {
      background: white;
      border-radius: 10px;
      padding: 30px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .card h2 {
      color: #667eea;
      margin-bottom: 20px;
      border-bottom: 2px solid #667eea;
      padding-bottom: 10px;
    }
    
    .card h3 {
      color: #764ba2;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    
    .feature-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin: 20px 0;
    }
    
    .feature-item {
      background: #f9f9f9;
      padding: 15px;
      border-left: 4px solid #667eea;
      border-radius: 5px;
    }
    
    .feature-item h4 {
      color: #667eea;
      margin-bottom: 8px;
    }
    
    .feature-item p {
      font-size: 14px;
      color: #666;
    }
    
    .flow-diagram {
      background: #f0f4ff;
      padding: 20px;
      border-radius: 10px;
      margin: 20px 0;
      overflow-x: auto;
    }
    
    .flow {
      display: flex;
      align-items: center;
      justify-content: space-around;
      gap: 15px;
      flex-wrap: wrap;
      min-width: max-content;
    }
    
    .flow-step {
      background: white;
      border: 2px solid #667eea;
      padding: 15px 20px;
      border-radius: 5px;
      font-weight: 600;
      color: #667eea;
      min-width: 150px;
      text-align: center;
    }
    
    .flow-arrow {
      color: #667eea;
      font-size: 24px;
      font-weight: bold;
    }
    
    .code-block {
      background: #2d2d2d;
      color: #f8f8f2;
      padding: 15px;
      border-radius: 5px;
      overflow-x: auto;
      margin: 10px 0;
      font-family: 'Courier New', monospace;
      font-size: 13px;
    }
    
    .btn-group {
      display: flex;
      gap: 10px;
      margin: 20px 0;
      flex-wrap: wrap;
    }
    
    .btn {
      display: inline-block;
      padding: 12px 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 600;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
    }
    
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .security-list {
      list-style: none;
      padding: 0;
    }
    
    .security-list li {
      padding: 10px 0;
      border-bottom: 1px solid #e0e0e0;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .security-list li:last-child {
      border-bottom: none;
    }
    
    .security-list li:before {
      content: "✅";
      font-size: 18px;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    
    th {
      background: #667eea;
      color: white;
      font-weight: 600;
    }
    
    tr:hover {
      background: #f9f9f9;
    }
    
    .highlight {
      background: #fff3cd;
      padding: 2px 6px;
      border-radius: 3px;
    }
    
    .success-msg {
      background: #d4edda;
      border: 1px solid #c3e6cb;
      color: #155724;
      padding: 15px;
      border-radius: 5px;
      margin: 20px 0;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>ℹ️ Informações do Sistema</h1>
  </div>
  
  <div class="container">
    <div class="success-msg">
      ✅ Sistema de Login e Autenticação foi implementado com sucesso!
    </div>
    
    <!-- O que foi feito -->
    <div class="card">
      <h2>🎯 O que foi Implementado</h2>
      <p>Um sistema completo de login, cadastro e gerenciamento de usuários com autenticação segura e interface moderna.</p>
      
      <div class="feature-list">
        <div class="feature-item">
          <h4>🔐 Login Seguro</h4>
          <p>Autenticação com email e senha criptografados. Suporte para Usuário e Administrador.</p>
        </div>
        <div class="feature-item">
          <h4>📝 Cadastro de Usuário</h4>
          <p>Formulário completo com validação de dados e verificação de email duplicado.</p>
        </div>
        <div class="feature-item">
          <h4>👤 Painel do Usuário</h4>
          <p>Dashboard intuitivo com informações da conta e menu de navegação.</p>
        </div>
        <div class="feature-item">
          <h4>👥 Gerenciamento de Usuários</h4>
          <p>Visualizar, editar e deletar usuários com filtros por tipo.</p>
        </div>
        <div class="feature-item">
          <h4>🔓 Logout Seguro</h4>
          <p>Destruição completa da sessão com redirecionamento para login.</p>
        </div>
        <div class="feature-item">
          <h4>🎨 Interface Moderna</h4>
          <p>Design responsivo e elegante com gradientes e transições suaves.</p>
        </div>
      </div>
    </div>
    
    <!-- Fluxo do Sistema -->
    <div class="card">
      <h2>📊 Fluxo da Aplicação</h2>
      <div class="flow-diagram">
        <div class="flow">
          <div class="flow-step">Login</div>
          <div class="flow-arrow">→</div>
          <div class="flow-step">Dashboard</div>
          <div class="flow-arrow">→</div>
          <div class="flow-step">Usuários</div>
          <div class="flow-arrow">→</div>
          <div class="flow-step">Logout</div>
        </div>
      </div>
      <p><strong>Ou na primeira vez:</strong> Cadastro → Login → Dashboard</p>
    </div>
    
    <!-- Páginas Criadas -->
    <div class="card">
      <h2>📄 Arquivos Criados/Modificados</h2>
      <table>
        <thead>
          <tr>
            <th>Arquivo</th>
            <th>Função</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>index.php</strong></td>
            <td>Página de Login</td>
            <td>✅ Criado</td>
          </tr>
          <tr>
            <td><strong>signup.php</strong></td>
            <td>Formulário de Cadastro</td>
            <td>✅ Atualizado</td>
          </tr>
          <tr>
            <td><strong>dashboard.php</strong></td>
            <td>Painel do Usuário</td>
            <td>✅ Criado</td>
          </tr>
          <tr>
            <td><strong>logout.php</strong></td>
            <td>Função de Logout</td>
            <td>✅ Criado</td>
          </tr>
          <tr>
            <td><strong>users.php</strong></td>
            <td>Gerenciamento de Usuários</td>
            <td>✅ Atualizado</td>
          </tr>
          <tr>
            <td><strong>edit_user.php</strong></td>
            <td>Edição de Usuário</td>
            <td>➖ Pré-existente</td>
          </tr>
          <tr>
            <td><strong>delete_user.php</strong></td>
            <td>Deleção de Usuário</td>
            <td>➖ Pré-existente</td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Segurança -->
    <div class="card">
      <h2>🔒 Medidas de Segurança</h2>
      <ul class="security-list">
        <li>Senhas criptografadas com bcrypt (PASSWORD_HASH)</li>
        <li>Prepared Statements para evitar SQL Injection</li>
        <li>Validação de email com FILTER_VALIDATE_EMAIL</li>
        <li>Proteção contra CSRF com sessions</li>
        <li>Escape de dados com htmlspecialchars()</li>
        <li>Logout seguro com session_destroy()</li>
        <li>Verificação de duplicação de email</li>
        <li>Senha mínima de 6 caracteres</li>
      </ul>
    </div>
    
    <!-- Como Começar -->
    <div class="card">
      <h2>🚀 Como Começar</h2>
      
      <h3>Passo 1: Acessar a página de login</h3>
      <p>Acesse: <span class="highlight">http://localhost:8080/ControleExterno/</span></p>
      
      <h3>Passo 2: Criar uma conta (primeira vez)</h3>
      <p>Clique em "Cadastre-se aqui" no formulário de login</p>
      <ul style="margin-left: 20px; margin: 10px 0;">
        <li>Preencha Nome Completo</li>
        <li>Digite um Email válido</li>
        <li>Escolha o tipo (Usuário ou Administrador)</li>
        <li>Crie uma senha com mínimo 6 caracteres</li>
        <li>Confirme a senha</li>
        <li>Clique em "Criar Conta"</li>
      </ul>
      
      <h3>Passo 3: Fazer login</h3>
      <ul style="margin-left: 20px; margin: 10px 0;">
        <li>Escolha o tipo de login (Usuário ou Administrador)</li>
        <li>Digite seu email</li>
        <li>Digite sua senha</li>
        <li>Clique em "Entrar"</li>
      </ul>
      
      <h3>Passo 4: Explorar o dashboard</h3>
      <p>Após login, você será redirecionado para o painel onde pode:</p>
      <ul style="margin-left: 20px; margin: 10px 0;">
        <li>Ver informações da sua conta</li>
        <li>Acessar a lista de usuários</li>
        <li>Cadastrar novos usuários</li>
        <li>Fazer logout</li>
      </ul>
    </div>
    
    <!-- Credenciais de Teste -->
    <div class="card" style="background: #e8f5e9; border-left: 4px solid #4caf50;">
      <h2>🧪 Testando o Sistema</h2>
      <p><strong>Você pode:</strong></p>
      <ul style="margin-left: 20px; margin: 20px 0;">
        <li>Criar uma nova conta em <span class="highlight">signup.php</span></li>
        <li>Fazer login com essa conta em <span class="highlight">index.php</span></li>
        <li>Ver o dashboard em <span class="highlight">dashboard.php</span></li>
        <li>Gerenciar usuários em <span class="highlight">users.php</span></li>
      </ul>
    </div>
    
    <!-- Funcionalidades do Administrador -->
    <div class="card">
      <h2>👨‍💼 Funcionalidades do Administrador</h2>
      <p>Se você criar uma conta como <strong>Administrador</strong>, terá acesso a:</p>
      <div class="feature-list">
        <div class="feature-item">
          <h4>Gerenciar Todos os Usuários</h4>
          <p>Visualizar, editar e deletar qualquer usuário do sistema</p>
        </div>
        <div class="feature-item">
          <h4>Filtrar por Tipo</h4>
          <p>Ver apenas usuários comuns ou apenas administradores</p>
        </div>
        <div class="feature-item">
          <h4>Painel Admin</h4>
          <p>Acesso a ferramentas administrativas avançadas no dashboard</p>
        </div>
        <div class="feature-item">
          <h4>Cadastro de Usuários</h4>
          <p>Criar novos usuários com qualquer tipo de conta</p>
        </div>
      </div>
    </div>
    
    <!-- Tabela de Rotas -->
    <div class="card">
      <h2>🗺️ Mapa de Rotas</h2>
      <table>
        <thead>
          <tr>
            <th>URL</th>
            <th>Descrição</th>
            <th>Requer Login</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>/ControleExterno/</td>
            <td>Página de Login</td>
            <td>❌ Não</td>
          </tr>
          <tr>
            <td>/ControleExterno/index.php</td>
            <td>Login (mesmo que acima)</td>
            <td>❌ Não</td>
          </tr>
          <tr>
            <td>/ControleExterno/signup.php</td>
            <td>Cadastro de Usuário</td>
            <td>❌ Não</td>
          </tr>
          <tr>
            <td>/ControleExterno/dashboard.php</td>
            <td>Painel do Usuário</td>
            <td>✅ Sim</td>
          </tr>
          <tr>
            <td>/ControleExterno/users.php</td>
            <td>Lista de Usuários</td>
            <td>❌ Não</td>
          </tr>
          <tr>
            <td>/ControleExterno/logout.php</td>
            <td>Fazer Logout</td>
            <td>✅ Sim</td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Dicas Úteis -->
    <div class="card">
      <h2>💡 Dicas Úteis</h2>
      <ul style="margin-left: 20px; margin: 20px 0;">
        <li><strong>Esqueceu a senha?</strong> No banco de dados, você pode atualizar a senha com: <code style="background: #f0f0f0; padding: 2px 4px;">UPDATE Users SET senha = PASSWORD_HASH('nova_senha', PASSWORD_DEFAULT) WHERE email = 'seu@email.com';</code></li>
        <li><strong>Criar admin via SQL:</strong> Use o SQL para inserir um admin direto no banco</li>
        <li><strong>Limpar banco:</strong> DELETE FROM Users para limpar todos os usuários (use com cuidado!)</li>
        <li><strong>Sessions expiram:</strong> Ao fechar o navegador, você será deslogado</li>
        <li><strong>Email único:</strong> Cada email só pode ser cadastrado uma vez no sistema</li>
      </ul>
    </div>
    
    <!-- Call to Action -->
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
      <h2>✨ Tudo Pronto!</h2>
      <p style="font-size: 18px; margin: 20px 0;">Seu sistema de login está funcionando e pronto para usar!</p>
      <div class="btn-group" style="justify-content: center;">
        <a href="index.php" class="btn" style="background: white; color: #667eea;">🔐 Ir para Login</a>
        <a href="signup.php" class="btn" style="background: white; color: #667eea;">📝 Criar Conta</a>
        <a href="users.php" class="btn" style="background: white; color: #667eea;">👥 Ver Usuários</a>
      </div>
    </div>
  </div>
</body>
</html>