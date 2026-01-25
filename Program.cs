using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using System.IO;
using Microsoft.AspNetCore.Http;

var builder = WebApplication.CreateBuilder(args);

// Adicionar Controllers com JSON em camelCase
builder.Services.AddControllers()
    .AddJsonOptions(options =>
    {
        options.JsonSerializerOptions.PropertyNamingPolicy = System.Text.Json.JsonNamingPolicy.CamelCase;
    });

// Cache e Session para autenticação simples
builder.Services.AddDistributedMemoryCache();
builder.Services.AddSession(options =>
{
    options.IdleTimeout = TimeSpan.FromHours(8);
    options.Cookie.HttpOnly = true;
    options.Cookie.IsEssential = true;
    options.Cookie.SameSite = SameSiteMode.Lax;
    options.Cookie.SecurePolicy = CookieSecurePolicy.SameAsRequest;
});

// Adicionar CORS para aceitar requisições do frontend
builder.Services.AddCors(options =>
{
    options.AddDefaultPolicy(builder =>
    {
        builder
            .WithOrigins("http://localhost:5132")
            .AllowAnyMethod()
            .AllowAnyHeader()
            .AllowCredentials();
    });
});

// Configurar Database Context - PostgreSQL (Heroku) ou SQLite (Local)
builder.Services.AddDbContext<AppDbContext>(options =>
{
    var databaseUrl = Environment.GetEnvironmentVariable("SUPABASE_URL") ?? Environment.GetEnvironmentVariable("DATABASE_URL");
    
    if (!string.IsNullOrEmpty(databaseUrl))
    {
        // Usar PostgreSQL no Heroku
        // DATABASE_URL vem no formato: postgresql://user:password@host:port/dbname
        var uri = new Uri(databaseUrl);
        var userInfo = uri.UserInfo.Split(':');
        var username = userInfo[0];
        var password = userInfo[1];
        var host = uri.Host;
        var port = uri.Port == -1 ? 5432 : uri.Port;
        var database = uri.LocalPath.TrimStart('/');
        
        var connectionString = $"Host={host};Port={port};Username={username};Password={password};Database={database};SSL Mode=Require;";
        
        options.UseNpgsql(connectionString);
        Console.WriteLine("✅ Usando PostgreSQL (Heroku)");
    }
    else
    {
        // Usar SQLite localmente
        var dbPath = Path.Combine(Directory.GetCurrentDirectory(), "cadastro.db");
        options.UseSqlite($"Data Source={dbPath}");
        Console.WriteLine("✅ Usando SQLite (Local)");
    }
});

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
app.UseCors(); // CORS deve vir ANTES de Session
app.UseSession(); // Habilitar Session antes da autorização
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
}// Fim do arquivo
