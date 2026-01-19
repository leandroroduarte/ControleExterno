using System.ComponentModel.DataAnnotations;

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

    public DateTime DataCadastro { get; set; } = DateTime.UtcNow;
}
