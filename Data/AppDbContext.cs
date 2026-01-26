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
            entity.Property(e => e.DataCadastro)
                .HasColumnName("datacadastro")
                .HasColumnType("timestamp with time zone");
            entity.HasIndex(u => u.Email).IsUnique();
        });

        modelBuilder.Entity<Produto>(entity =>
        {
            entity.ToTable("produtos");
            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.Descricao).HasColumnName("descricao");
            entity.Property(e => e.Quantidade).HasColumnName("quantidade");
            entity.Property(e => e.Valor).HasColumnName("valor");
            entity.Property(e => e.Fornecedor).HasColumnName("fornecedor");
            entity.Property(e => e.CaminhoImagem).HasColumnName("caminhoimagem");
            entity.Property(e => e.UsuarioId).HasColumnName("usuarioid");
            entity.Property(e => e.DataCadastro)
                .HasColumnName("datacadastro")
                .HasColumnType("timestamp with time zone");
        });

        modelBuilder.Entity<Cliente>(entity =>
        {
            entity.ToTable("clientes");
            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.Nome).HasColumnName("nome");
            entity.Property(e => e.CPF_CNPJ).HasColumnName("cpf_cnpj");
            entity.Property(e => e.Email).HasColumnName("email");
            entity.Property(e => e.Telefone).HasColumnName("telefone");
            entity.Property(e => e.Endereco).HasColumnName("endereco");
            entity.Property(e => e.UsuarioId).HasColumnName("usuarioid");
            entity.Property(e => e.DataCadastro)
                .HasColumnName("datacadastro")
                .HasColumnType("timestamp with time zone");
            entity.HasIndex(c => new { c.UsuarioId, c.Nome });
        });

        modelBuilder.Entity<Fornecedor>(entity =>
        {
            entity.ToTable("fornecedores");
            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.NomeFantasia).HasColumnName("nomefantasia");
            entity.Property(e => e.CNPJ).HasColumnName("cnpj");
            entity.Property(e => e.EmailVendas).HasColumnName("emailvendas");
            entity.Property(e => e.Telefone).HasColumnName("telefone");
            entity.Property(e => e.Endereco).HasColumnName("endereco");
            entity.Property(e => e.UsuarioId).HasColumnName("usuarioid");
            entity.Property(e => e.DataCadastro)
                .HasColumnName("datacadastro")
                .HasColumnType("timestamp with time zone");
            entity.HasIndex(f => new { f.UsuarioId, f.NomeFantasia });
        });
    }
}
