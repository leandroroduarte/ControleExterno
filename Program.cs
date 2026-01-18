using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;

var builder = WebApplication.CreateBuilder(args);

// Adicionar Controllers com JSON em camelCase
builder.Services.AddControllers()
    .AddJsonOptions(options =>
    {
        options.JsonSerializerOptions.PropertyNamingPolicy = System.Text.Json.JsonNamingPolicy.CamelCase;
    });

// Adicionar CORS para aceitar requisições do frontend
builder.Services.AddCors(options =>
{
    options.AddDefaultPolicy(builder =>
    {
        builder.AllowAnyOrigin()
               .AllowAnyMethod()
               .AllowAnyHeader();
    });
});

// Configurar Database Context - Detecta automaticamente o ambiente
var connectionString = Environment.GetEnvironmentVariable("DATABASE_URL");

if (!string.IsNullOrEmpty(connectionString))
{
    // PRODUÇÃO (Heroku) - PostgreSQL
    // Converter formato Heroku: postgres://user:pass@host:port/db
    // Para formato .NET: Host=host;Database=db;Username=user;Password=pass
    var databaseUri = new Uri(connectionString);
    var userInfo = databaseUri.UserInfo.Split(':');
    
    var npgsqlConnection = $"Host={databaseUri.Host};" +
                          $"Port={databaseUri.Port};" +
                          $"Database={databaseUri.LocalPath.TrimStart('/')};" +
                          $"Username={userInfo[0]};" +
                          $"Password={userInfo[1]};" +
                          $"SSL Mode=Require;" +
                          $"Trust Server Certificate=true";

    builder.Services.AddDbContext<AppDbContext>(options =>
        options.UseNpgsql(npgsqlConnection));
}
else
{
    // DESENVOLVIMENTO LOCAL - SQLite
    builder.Services.AddDbContext<AppDbContext>(options =>
        options.UseSqlite("Data Source=cadastro.db"));
}

// Configurar Swagger/OpenAPI
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

var app = builder.Build();

try
{
    // Criar/Atualizar database automaticamente
    using (var scope = app.Services.CreateScope())
    {
        var db = scope.ServiceProvider.GetRequiredService<AppDbContext>();
        db.Database.EnsureCreated();
        
        // Adicionar usuário de demo se não existir
        if (!db.Usuarios.Any(u => u.Email == "demo@email.com"))
        {
            db.Usuarios.Add(new Usuario
            {
                Nome = "Usuário Demo",
                Email = "demo@email.com",
                Senha = "demo123456",
                DataCadastro = DateTime.UtcNow
            });
            db.SaveChanges();
            Console.WriteLine("✅ Usuário de demo criado: demo@email.com / demo123456");
        }
    }
}
catch (Exception ex)
{
    Console.WriteLine($"Erro ao criar banco de dados: {ex.Message}");
    Console.WriteLine($"Stack trace: {ex.StackTrace}");
}

// Configurar pipeline HTTP
app.UseSwagger();
app.UseSwaggerUI();

app.UseDefaultFiles(); // Servir index.html como padrão
app.UseStaticFiles(); // Servir arquivos estáticos (wwwroot)
app.UseCors(); // Aplicar CORS
// app.UseHttpsRedirection(); // Desabilitado para desenvolvimento
app.UseAuthorization();
app.MapControllers();

try
{
    app.Run();
}
catch (Exception ex)
{
    Console.WriteLine($"Erro ao iniciar aplicação: {ex.Message}");
    Console.WriteLine($"Stack trace: {ex.StackTrace}");
}
