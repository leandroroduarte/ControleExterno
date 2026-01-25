using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using System.IO;
using Microsoft.AspNetCore.Http;

var builder = WebApplication.CreateBuilder(args);

// 1. Configurar JSON em camelCase
builder.Services.AddControllers()
    .AddJsonOptions(options =>
    {
        options.JsonSerializerOptions.PropertyNamingPolicy = System.Text.Json.JsonNamingPolicy.CamelCase;
    });

// 2. Configurar Cache e Session
builder.Services.AddDistributedMemoryCache();
builder.Services.AddSession(options =>
{
    options.IdleTimeout = TimeSpan.FromHours(8);
    options.Cookie.HttpOnly = true;
    options.Cookie.IsEssential = true;
    options.Cookie.SameSite = SameSiteMode.Lax;
    options.Cookie.SecurePolicy = CookieSecurePolicy.SameAsRequest;
});

// 3. Configurar CORS
builder.Services.AddCors(options =>
{
    options.AddDefaultPolicy(policy =>
    {
        policy.AllowAnyOrigin() // Simplificado para evitar bloqueios em deploy
              .AllowAnyMethod()
              .AllowAnyHeader();
    });
});

// 4. Configurar Database Context (Supabase ou SQLite)
builder.Services.AddDbContext<AppDbContext>(options =>
{
    var connectionString = Environment.GetEnvironmentVariable("SUPABASE_URL");
    
    if (!string.IsNullOrEmpty(connectionString))
    {
        // Usa a string de conexão direta do Heroku/Supabase
        options.UseNpgsql(connectionString);
        Console.WriteLine("✅ Conectando ao PostgreSQL (Supabase)");
    }
    else
    {
        // Se local, usa SQLite
        var dbPath = Path.Combine(Directory.GetCurrentDirectory(), "cadastro.db");
        options.UseSqlite($"Data Source={dbPath}");
        Console.WriteLine("✅ Usando SQLite (Local)");
    }
});

builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

var app = builder.Build();

// 5. Inicialização do Banco e Usuário Demo
try
{
    using (var scope = app.Services.CreateScope())
    {
        var db = scope.ServiceProvider.GetRequiredService<AppDbContext>();
        db.Database.EnsureCreated();
        
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
            Console.WriteLine("✅ Usuário de demo criado: demo@email.com");
        }
    }
}
catch (Exception ex)
{
    Console.WriteLine($"⚠️ Erro na inicialização: {ex.Message}");
}

// 6. Pipeline HTTP
app.UseSwagger();
app.UseSwaggerUI();
app.UseStaticFiles();
app.UseDefaultFiles();
app.UseCors();
app.UseSession();
app.UseAuthorization();
app.MapControllers();

app.Run();