using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Models;

namespace CadastroUsuarios.Data;

public class AppDbContext : DbContext
{
    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options)
    {
    }

    public DbSet<Usuario> Usuarios { get; set; }
    public DbSet<Produto> Produtos { get; set; }
    public DbSet<Cliente> Clientes { get; set; }
    public DbSet<Fornecedor> Fornecedores { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        // Configurar nome da tabela com aspas para evitar case-sensitive no Postgres
        modelBuilder.Entity<Usuario>()
            .ToTable("Usuarios");

        // Configurar índice único para email
        modelBuilder.Entity<Usuario>()
            .HasIndex(u => u.Email)
            .IsUnique();

        // Índices úteis
        modelBuilder.Entity<Cliente>()
            .HasIndex(c => new { c.UsuarioId, c.Nome });

        modelBuilder.Entity<Fornecedor>()
            .HasIndex(f => new { f.UsuarioId, f.NomeFantasia });
    }
}
