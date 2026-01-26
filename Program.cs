using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using CadastroUsuarios.Services;
using CadastroUsuarios.Middleware;
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

// 5. Registrar HttpClient e Supabase Storage Service
Console.WriteLine("🔧 Registrando SupabaseStorageService...");
builder.Services.AddHttpClient<SupabaseStorageService>();
builder.Services.AddScoped<SupabaseStorageService>();
Console.WriteLine("✅ SupabaseStorageService registrado");

builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

Console.WriteLine("🔧 Construindo aplicação...");
var app = builder.Build();
Console.WriteLine("✅ Aplicação construída com sucesso");

// 5. Inicialização do Banco e Usuário Demo
Console.WriteLine("🔧 Inicializando banco de dados e usuário demo...");
try
{
    using (var scope = app.Services.CreateScope())
    {
        var db = scope.ServiceProvider.GetRequiredService<AppDbContext>();
        Console.WriteLine("📦 Garantindo criação do banco...");
        db.Database.EnsureCreated();
        Console.WriteLine("✅ Banco criado/verificado");
        
        if (!db.Usuarios.Any(u => u.Email == "demo@email.com"))
        {
            Console.WriteLine("👤 Criando usuário demo...");
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
        else
        {
            Console.WriteLine("✅ Usuário demo já existe");
        }
    }
}
catch (Exception ex)
{
    Console.WriteLine($"❌ ERRO na inicialização: {ex.Message}");
    Console.WriteLine($"📋 Stack trace: {ex.StackTrace}");
    throw;
}

// 6. Pipeline HTTP
Console.WriteLine("🔧 Configurando pipeline HTTP...");
app.UseSwagger();
app.UseSwaggerUI();
app.UseStaticFiles();
app.UseDefaultFiles();
app.UseCors();
app.UseMiddleware<ErrorHandlingMiddleware>();
app.UseSession();
app.UseAuthorization();
app.MapControllers();

Console.WriteLine("✅ Pipeline configurado");
Console.WriteLine("🚀 Iniciando aplicação...");

AppDomain.CurrentDomain.UnhandledException += (sender, e) =>
{
    Console.WriteLine($"❌ Exceção não tratada: {e.ExceptionObject}");
    Environment.Exit(1);
};

TaskScheduler.UnobservedTaskException += (sender, e) =>
{
    Console.WriteLine($"❌ Task exceção não observada: {e.Exception}");
    e.SetObserved();
};

try
{
    Console.WriteLine("📥 Iniciando app.Run()...");
    app.Run();
    Console.WriteLine("✅ app.Run() completou normalmente");
}
catch (OperationCanceledException)
{
    Console.WriteLine("⚠️ Operação cancelada");
}
catch (Exception ex)
{
    Console.WriteLine($"❌ Erro fatal no app.Run(): {ex.GetType().Name}");
    Console.WriteLine($"   Mensagem: {ex.Message}");
    Console.WriteLine($"   Stack trace:\n{ex.StackTrace}");
    throw;
}