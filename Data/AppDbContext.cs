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

        // Configurar nomes de colunas e tabelas para PostgreSQL (case-insensitive)
        modelBuilder.Entity<Usuario>(entity =>
        {
            entity.ToTable("usuarios");
            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.Nome).HasColumnName("nome");
            entity.Property(e => e.Email).HasColumnName("email");
            entity.Property(e => e.Senha).HasColumnName("senha");
            entity.Property(e => e.DataCadastro).HasColumnName("datacadastro");
            entity.HasIndex(u => u.Email).IsUnique();
        });

        modelBuilder.Entity<Produto>(entity =>
        {
            entity.ToTable("produtos");
        });

        modelBuilder.Entity<Cliente>(entity =>
        {
            entity.ToTable("clientes");
            entity.HasIndex(c => new { c.UsuarioId, c.Nome });
        });

        modelBuilder.Entity<Fornecedor>(entity =>
        {
            entity.ToTable("fornecedores");
            entity.HasIndex(f => new { f.UsuarioId, f.NomeFantasia });
        });
    }
}
