<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia Rápido - ControleExterno</title>
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            padding: 50px;
            text-align: center;
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 36px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }
        
        .link-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .link-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .link-card h3 {
            margin-bottom: 10px;
            font-size: 20px;
        }
        
        .link-card p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .steps {
            background: #f5f7fa;
            padding: 30px;
            border-radius: 10px;
            margin: 30px 0;
            text-align: left;
        }
        
        .step {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            align-items: flex-start;
        }
        
        .step-number {
            background: #667eea;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .step-content h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .step-content p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .info-box {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            color: #2e7d32;
            text-align: left;
        }
        
        .info-box strong {
            display: block;
            margin-bottom: 10px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        
        a {
            color: #667eea;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Bem-vindo ao ControleExterno</h1>
        <p class="subtitle">Sistema de Login e Autenticação</p>
        
        <div class="info-box">
            <strong>✅ Sistema implementado com sucesso!</strong>
            Escolha uma opção abaixo para começar:
        </div>
        
        <div class="quick-links">
            <a href="index.php" class="link-card">
                <h3>🔐 Fazer Login</h3>
                <p>Acesse sua conta com email e senha</p>
            </a>
            
            <a href="signup.php" class="link-card">
                <h3>📝 Criar Conta</h3>
                <p>Registre uma nova conta no sistema</p>
            </a>
            
            <a href="dashboard.php" class="link-card">
                <h3>👤 Dashboard</h3>
                <p>Acesse seu painel personalizado</p>
            </a>
            
            <a href="users.php" class="link-card">
                <h3>👥 Usuários</h3>
                <p>Veja a lista de usuários cadastrados</p>
            </a>
            
            <a href="info.php" class="link-card">
                <h3>ℹ️ Informações</h3>
                <p>Saiba mais sobre o sistema</p>
            </a>
            
            <a href="README.md" class="link-card" style="background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);">
                <h3>📖 Documentação</h3>
                <p>Leia a documentação completa</p>
            </a>
        </div>
        
        <div class="steps">
            <h3 style="color: #667eea; margin-bottom: 20px;">📋 Primeiros Passos</h3>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Crie uma conta</h4>
                    <p>Clique em "Criar Conta" e preencha com seus dados pessoais. Escolha se quer ser um usuário comum ou administrador.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>Faça login</h4>
                    <p>Volte para a página inicial e faça login com seu email e senha cadastrados.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Explore o dashboard</h4>
                    <p>Após o login, você será redirecionado para seu painel onde pode acessar todas as funcionalidades.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h4>Gerencie usuários</h4>
                    <p>Se você for administrador, terá acesso a ferramentas avançadas para gerenciar todos os usuários do sistema.</p>
                </div>
            </div>
        </div>
        
        <div class="info-box" style="background: #fff3cd; border-color: #ffc107; color: #856404;">
            <strong>💡 Dica:</strong>
            Se tiver dúvidas, acesse a página de <a href="info.php" style="color: #856404; text-decoration: underline;">Informações</a> para uma descrição detalhada de todas as funcionalidades.
        </div>
        
        <div class="footer">
            <p>🎯 Sistema de Login | ControleExterno 2025</p>
        </div>
    </div>
</body>
</html>
