using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using System.IO;

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

// Configurar Database Context - Forçar uso de SQLite (ignorar DATABASE_URL)
builder.Services.AddDbContext<AppDbContext>(options =>
{
    var dbPath = Path.Combine(Directory.GetCurrentDirectory(), "cadastro.db");
    options.UseSqlite($"Data Source={dbPath}");
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
