# Sistema de Cadastro de Usuários - C#

Sistema de cadastro de usuários desenvolvido em ASP.NET Core 8.0 com suporte a SQLite (desenvolvimento) e PostgreSQL (produção no Heroku).

## 🚀 Funcionalidades

- ✅ Cadastro de usuários
- ✅ Listagem de usuários
- ✅ Busca por ID
- ✅ Atualização de dados
- ✅ Exclusão de usuários
- ✅ Validação de email único
- ✅ API REST documentada com Swagger

## 🛠️ Tecnologias

- ASP.NET Core 8.0
- Entity Framework Core
- SQLite (desenvolvimento local)
- PostgreSQL (produção Heroku)
- Swagger/OpenAPI

## 📦 Executar Localmente

### Pré-requisitos
- .NET 8.0 SDK

### Passos

1. Restaurar pacotes:
```bash
dotnet restore
```

2. Criar migration inicial:
```bash
dotnet ef migrations add InitialCreate
```

3. Executar aplicação:
```bash
dotnet run
```

4. Acessar Swagger:
```
https://localhost:7XXX/swagger
```

## 🌐 Deploy no Heroku

### 1. Instalar Heroku CLI
```bash
# Baixar em: https://devcenter.heroku.com/articles/heroku-cli
```

### 2. Login no Heroku
```bash
heroku login
```

### 3. Criar aplicação
```bash
heroku create nome-do-seu-app
```

### 4. Adicionar PostgreSQL
```bash
heroku addons:create heroku-postgresql:essential-0
```

### 5. Adicionar Buildpack .NET
```bash
heroku buildpacks:set https://github.com/jincod/dotnetcore-buildpack
```

### 6. Deploy
```bash
git init
git add .
git commit -m "Initial commit"
git push heroku main
```

### 7. Executar Migrations no Heroku
As migrations são executadas automaticamente no startup da aplicação!

## 📡 Endpoints da API

### Listar todos os usuários
```http
GET /api/usuarios
```

### Buscar usuário por ID
```http
GET /api/usuarios/{id}
```

### Cadastrar novo usuário
```http
POST /api/usuarios
Content-Type: application/json

{
  "nome": "João Silva",
  "email": "joao@email.com",
  "senha": "senha123"
}
```

### Atualizar usuário
```http
PUT /api/usuarios/{id}
Content-Type: application/json

{
  "id": 1,
  "nome": "João Silva Atualizado",
  "email": "joao@email.com",
  "senha": "novasenha123"
}
```

### Deletar usuário
```http
DELETE /api/usuarios/{id}
```

## 📝 Modelo de Dados

```csharp
public class Usuario
{
    public int Id { get; set; }
    public string Nome { get; set; }
    public string Email { get; set; }
    public string Senha { get; set; }
    public DateTime DataCadastro { get; set; }
}
```

## ⚙️ Configuração de Banco de Dados

O sistema detecta automaticamente o ambiente:

- **Local**: Usa SQLite (arquivo `cadastro.db`)
- **Heroku**: Usa PostgreSQL (variável `DATABASE_URL`)

## 🔒 Segurança

⚠️ **IMPORTANTE**: Este é um exemplo básico. Para produção, adicione:

1. Hash de senha (BCrypt.NET)
2. Autenticação JWT
3. HTTPS obrigatório
4. Rate limiting
5. Validações adicionais

## 📄 Licença

MIT
