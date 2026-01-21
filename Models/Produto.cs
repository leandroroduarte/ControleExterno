using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace CadastroUsuarios.Models;

public class Produto
{
    public int Id { get; set; }

    [Required]
    [MaxLength(200)]
    public string Descricao { get; set; } = string.Empty;

    [Range(0, int.MaxValue)]
    public int Quantidade { get; set; }

    [Range(0, double.MaxValue)]
    public decimal Valor { get; set; }

    [MaxLength(200)]
    public string Fornecedor { get; set; } = string.Empty;

    // Caminho relativo para a imagem (ex: uploads/produto_1.jpg)
    public string? CaminhoImagem { get; set; }

    // FK do usuário proprietário do produto
    [Required]
    public int UsuarioId { get; set; }

    // Navegação para o usuário
    [ForeignKey(nameof(UsuarioId))]
    public Usuario? Usuario { get; set; }

    public DateTime DataCadastro { get; set; } = DateTime.UtcNow;
}
