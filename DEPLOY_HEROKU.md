# 🚀 Guia de Deploy no Heroku

## Pré-requisitos

1. ✅ Conta no GitHub (Student Pack ativado)
2. ✅ Conta no Heroku (pode usar créditos do GitHub Student Pack)
3. ✅ Git instalado no seu computador
4. ✅ Heroku CLI instalado ([Download aqui](https://devcenter.heroku.com/articles/heroku-cli))

---

## 📋 Passo a Passo

### 1️⃣ Preparar o Repositório Git

Se ainda não inicializou o Git no projeto:

```powershell
cd c:\laragon\www\ControleExterno
git init
git add .
git commit -m "Preparando projeto para deploy no Heroku"
```

### 2️⃣ Fazer Login no Heroku CLI

```powershell
heroku login
```

Isso abrirá seu navegador para fazer login.

### 3️⃣ Criar a Aplicação no Heroku

```powershell
heroku create seu-app-controle-externo
```

> **Nota:** Substitua `seu-app-controle-externo` por um nome único. Ele será usado na URL: `https://seu-app-controle-externo.herokuapp.com`

### 4️⃣ Adicionar Banco de Dados MySQL

O Heroku usa addons para MySQL. Você tem duas opções:

#### Opção A: ClearDB (Recomendado - tem plano gratuito limitado)
```powershell
heroku addons:create cleardb:ignite
```

#### Opção B: JawsDB
```powershell
heroku addons:create jawsdb:kitefin
```

### 5️⃣ Configurar Variáveis de Ambiente

```powershell
# Obter URL do banco de dados
heroku config:get CLEARDB_DATABASE_URL

# Configurar variáveis de email (opcional, mas recomendado)
heroku config:set SMTP_HOST=smtp.zoho.com
heroku config:set SMTP_PORT=587
heroku config:set SMTP_USERNAME=seu-email@dominio.com
heroku config:set SMTP_PASSWORD=sua-senha
heroku config:set FROM_EMAIL=seu-email@dominio.com
heroku config:set FROM_NAME="Controle Externo"
heroku config:set HEROKU_APP_NAME=seu-app-controle-externo
```

### 6️⃣ Criar as Tabelas no Banco de Dados

Existem duas formas:

#### Opção A: Via Heroku CLI (Recomendado)
```powershell
# Conectar ao banco de dados
heroku config:get CLEARDB_DATABASE_URL
# Copie a URL exibida

# No MySQL Workbench ou outro cliente, conecte usando a URL
# Formato: mysql://usuario:senha@host/database
```

Então execute o SQL de criação das tabelas:

```sql
CREATE TABLE Users (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('user','admin') NOT NULL DEFAULT 'user',
    verification_token VARCHAR(64) NULL,
    verified TINYINT(1) NOT NULL DEFAULT 0,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    telefone VARCHAR(20),
    cpf_cnpj VARCHAR(20) UNIQUE,
    cep VARCHAR(10),
    endereco VARCHAR(255),
    numero VARCHAR(10),
    complemento VARCHAR(255),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT UNSIGNED,
    INDEX idx_nome (nome),
    INDEX idx_email (email),
    INDEX idx_cpf_cnpj (cpf_cnpj),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Fornecedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    telefone VARCHAR(20),
    cnpj VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT UNSIGNED,
    INDEX idx_nome (nome),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estoque INT NOT NULL DEFAULT 0,
    fornecedor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT UNSIGNED,
    INDEX idx_nome (nome),
    INDEX idx_fornecedor (fornecedor_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Opção B: Criar script de setup
Você pode criar um arquivo `setup_heroku.php` e acessá-lo pela URL após o deploy.

### 7️⃣ Fazer Deploy

```powershell
git push heroku main
```

Se sua branch principal for `master`:
```powershell
git push heroku master
```

### 8️⃣ Abrir a Aplicação

```powershell
heroku open
```

---

## 🔧 Comandos Úteis

### Ver logs em tempo real
```powershell
heroku logs --tail
```

### Reiniciar a aplicação
```powershell
heroku restart
```

### Ver configurações
```powershell
heroku config
```

### Conectar ao banco de dados
```powershell
heroku config:get CLEARDB_DATABASE_URL
```

### Executar comando no Heroku
```powershell
heroku run bash
```

---

## ⚠️ Importante

### Atualizar URLs no Código

Depois do deploy, você precisará atualizar algumas URLs hardcoded:

1. Em [signup.php](signup.php), linha ~68, a URL de confirmação usa `localhost:8080`. Agora ela usará automaticamente a função `getBaseUrl()` de [config.php](config.php).

### Limitações do Plano Gratuito

- ClearDB Ignite: 5MB de dados
- A aplicação "dorme" após 30 minutos de inatividade
- Máximo de 10.000 linhas no banco

### GitHub Student Pack Benefits

Com o GitHub Student Pack, você tem:
- **$13/mês de créditos no Heroku** por 24 meses
- Permite usar dynos pagos sem custo
- Mais recursos de banco de dados

Para ativar: [education.github.com/pack](https://education.github.com/pack)

---

## 🐛 Solução de Problemas

### Erro ao fazer push
```powershell
git remote -v  # Verificar se o remote heroku foi adicionado
heroku git:remote -a seu-app-controle-externo
```

### Aplicação não inicia
```powershell
heroku logs --tail  # Ver logs em tempo real
```

### Erro de conexão com banco
```powershell
heroku config  # Verificar se DATABASE_URL existe
```

---

## 📚 Recursos Adicionais

- [Documentação Heroku PHP](https://devcenter.heroku.com/articles/getting-started-with-php)
- [ClearDB Addon](https://devcenter.heroku.com/articles/cleardb)
- [Heroku CLI Commands](https://devcenter.heroku.com/articles/heroku-cli-commands)
- [GitHub Student Pack](https://education.github.com/pack)

---

## ✅ Checklist Final

- [ ] Git inicializado e código commitado
- [ ] Heroku CLI instalado e login feito
- [ ] App criado no Heroku
- [ ] Addon de MySQL adicionado
- [ ] Variáveis de ambiente configuradas
- [ ] Tabelas criadas no banco de dados
- [ ] Deploy realizado com sucesso
- [ ] Aplicação testada e funcionando

---

**Boa sorte com seu deploy! 🚀**
