using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using CadastroUsuarios.Services;
using CadastroUsuarios.Middleware;
using System.IO;
using Microsoft.AspNetCore.Http;
using BCrypt.Net;

// Carregar variáveis de .env.local se existir
var envFile = Path.Combine(Directory.GetCurrentDirectory(), ".env.local");
if (File.Exists(envFile))
{
    foreach (var line in File.ReadAllLines(envFile))
    {
        if (!string.IsNullOrWhiteSpace(line) && !line.StartsWith("#"))
        {
            var parts = line.Split('=', 2);
            if (parts.Length == 2)
            {
                Environment.SetEnvironmentVariable(parts[0].Trim(), parts[1].Trim());
            }
        }
    }
    Console.WriteLine("✅ Variáveis .env.local carregadas");
}

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
    options.IdleTimeout = TimeSpan.FromMinutes(15); // Timeout de 15 minutos
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

// 4. Configurar Database Context (SEMPRE Supabase PostgreSQL)
builder.Services.AddDbContext<AppDbContext>(options =>
{
    var supabaseUrl = Environment.GetEnvironmentVariable("SUPABASE_URL") ?? "Host=aws-1-us-east-1.pooler.supabase.com;Port=5432;Database=postgres;Username=postgres.xlhgjoxukrdebnetpwqp;Password=WTGZ55jvIEwRrgYj;SSL Mode=Require;Trust Server Certificate=true";
    
    options.UseNpgsql(supabaseUrl);
    Console.WriteLine("✅ Conectando ao PostgreSQL (Supabase)");
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
            var senhaHash = BCrypt.Net.BCrypt.HashPassword("demo123456");
            db.Usuarios.Add(new Usuario
            {
                Nome = "Usuário Demo",
                Email = "demo@email.com",
                Senha = senhaHash,
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
    Console.WriteLine($"ERRO NÃO TRATADO: {e.ExceptionObject}");
    Environment.Exit(1);
};

TaskScheduler.UnobservedTaskException += (sender, e) =>
{
    Console.WriteLine($"ERRO TASK: {e.Exception}");
    e.SetObserved();
};

try
{
    Console.WriteLine("INICIANDO APP.RUN...");
    Console.WriteLine("SERVIDOR RODANDO - Pressione CTRL+C para parar");
    await app.RunAsync();
    Console.WriteLine("APP.RUN retornou (isto nao deveria acontecer)");
}
catch (OperationCanceledException)
{
    Console.WriteLine("Operacao foi cancelada");
}
catch (Exception ex)
{
    Console.WriteLine($"EXCEPTION em app.Run: {ex}");
}