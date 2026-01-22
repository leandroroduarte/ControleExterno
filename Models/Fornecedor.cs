using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace CadastroUsuarios.Models;

public class Fornecedor
{
    public int Id { get; set; }

    [Required]
    [MaxLength(200)]
    public string NomeFantasia { get; set; } = string.Empty;

    [Required]
    [MaxLength(20)]
    public string CNPJ { get; set; } = string.Empty;

    [Required]
    [MaxLength(200)]
    [EmailAddress]
    public string EmailVendas { get; set; } = string.Empty;

    [Required]
    [MaxLength(30)]
    public string Telefone { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    public string Categoria { get; set; } = string.Empty;

    [Required]
    public int UsuarioId { get; set; }

    [ForeignKey(nameof(UsuarioId))]
    public Usuario? Usuario { get; set; }

    public DateTime DataCadastro { get; set; } = DateTime.UtcNow;
}
