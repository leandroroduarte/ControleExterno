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

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        // Configurar índice único para email
        modelBuilder.Entity<Usuario>()
            .HasIndex(u => u.Email)
            .IsUnique();
    }
}
