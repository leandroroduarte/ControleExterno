# 🔐 Sistema de Login - ControleExterno

## Descrição
Sistema completo de autenticação e gerenciamento de usuários com suporte para dois tipos de conta: Usuário comum e Administrador.

## ✨ Funcionalidades Implementadas

### 1. **Página de Login** (`index.php`)
- Campo de email
- Campo de senha
- Seleção entre Login de Usuário ou Administrador
- Design moderno e responsivo
- Validação de credenciais
- Mensagens de erro clara
- Link para cadastro de novo usuário

### 2. **Formulário de Cadastro** (`signup.php`)
- Nome completo
- Email
- Tipo de conta (Usuário ou Administrador)
- Senha com confirmação
- Validação de senha mínima (6 caracteres)
- Verificação de email duplicado
- Design elegante e responsivo

### 3. **Painel do Usuário** (`dashboard.php`)
- Exibe informações do usuário logado
- Menu de navegação rápida
- Diferentes opções para Administrador
- Botão de logout seguro
- Design intuitivo com ícones

### 4. **Funcionalidade de Logout** (`logout.php`)
- Destrui a sessão seguramente
- Redireciona para página de login
- Método seguro e limpo

### 5. **Gerenciamento de Usuários** (`users.php`)
- Filtro por tipo (Todos, Usuários, Administradores)
- Exibe lista de todos os usuários cadastrados
- Botões de edição e exclusão
- Acesso rápido ao dashboard
- Design melhorado com cards e tabelas modernas

## 🚀 Como Usar

### Passo 1: Preparar o Banco de Dados
Certifique-se de ter criado a tabela `Users` com a seguinte estrutura:

```sql
CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Passo 2: Criar Usuário Administrador Inicial (Opcional)
Execute este comando SQL para criar um admin de teste:

```sql
INSERT INTO Users (nome, email, senha, tipo) 
VALUES ('Administrador', 'admin@teste.com', '$2y$10$...sua_senha_hash_aqui...', 'admin');
```

Ou use o formulário de cadastro (`signup.php`) para criar um administrador.

### Passo 3: Acessar a Página de Login
Acesse: **http://localhost:8080/ControleExterno/**

### Passo 4: Escolher Tipo de Login
- **👤 Usuário**: Acesso padrão ao sistema
- **👨‍💼 Administrador**: Acesso com privilégios administrativos

### Passo 5: Inserir Credenciais
- Email registrado
- Senha cadastrada

## 📋 Fluxo da Aplicação

```
index.php (Login)
    ↓
    ├─→ Credenciais válidas → dashboard.php (Painel)
    │                              ↓
    │                         users.php (Gerenciar)
    │                              ↓
    │                         logout.php (Sair)
    │                              ↓
    │                         index.php (Volta ao Login)
    │
    └─→ Sem conta? → signup.php (Cadastro)
                         ↓
                    Volta ao index.php (Login)
```

## 🎯 Características de Segurança

✅ **Senhas Criptografadas**: Usa `password_hash()` com algoritmo bcrypt
✅ **Sessões Seguras**: Utiliza `$_SESSION` do PHP
✅ **Validação de Email**: Verifica formato e duplicação
✅ **Proteção contra SQL Injection**: Usa prepared statements
✅ **Logout Seguro**: Destroi completamente a sessão

## 🎨 Design

- **Gradiente Moderno**: Cores roxa e azul (#667eea e #764ba2)
- **Responsivo**: Funciona em desktop, tablet e mobile
- **Interface Intuitiva**: Ícones e labels claros
- **Feedback Visual**: Transições suaves e hover effects

## 📄 Arquivos

| Arquivo | Função |
|---------|--------|
| `index.php` | Página de login |
| `signup.php` | Formulário de cadastro |
| `dashboard.php` | Painel do usuário |
| `logout.php` | Realiza logout |
| `users.php` | Gerencia usuários |
| `edit_user.php` | Edita usuário (pré-existente) |
| `delete_user.php` | Deleta usuário (pré-existente) |

## 🔧 Configuração

### Conexão com Banco de Dados
Os seguintes arquivos possuem configurações de banco de dados:
- `index.php` (linha 35-40)
- `signup.php` (linha 23-28)
- `users.php` (linha 16-21)
- `dashboard.php` (não conecta, usa apenas sessão)

**Dados padrão**:
- Host: `127.0.0.1`
- Porta: `3306`
- Database: `ControleExterno`
- User: `root`
- Password: (vazio)

Para mudar, edite as variáveis de conexão em cada arquivo.

## 💡 Dicas de Uso

1. **Primeira vez**: Cadastre-se em `signup.php`
2. **Teste Admin**: Crie uma conta com tipo "Administrador"
3. **Logout**: Clique em "Sair" no painel ou no botão de logout
4. **Gerenciar**: Use a página de usuários para editar ou deletar contas

## ⚠️ Notas Importantes

- A sessão expira quando o navegador é fechado (por padrão)
- Ao fazer logout, a sessão é completamente destruída
- O tipo de login (Usuário/Admin) é determinado no banco de dados
- Email e senha são obrigatórios para login

## 📞 Suporte

Para problemas:
1. Verifique se a tabela `Users` existe no banco
2. Confirme as credenciais de conexão MySQL
3. Limpe o cache do navegador
4. Verifique os logs de erro do servidor PHP

---

**Desenvolvido com ❤️ para ControleExterno**
