using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using System.IO;

namespace CadastroUsuarios.Controllers;

[ApiController]
[Route("api/[controller]")]
public class ProdutosController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly ILogger<ProdutosController> _logger;
    private readonly IWebHostEnvironment _env;

    public ProdutosController(AppDbContext context, ILogger<ProdutosController> logger, IWebHostEnvironment env)
    {
        _context = context;
        _logger = logger;
        _env = env;
    }

    // GET: api/produtos
    [HttpGet]
    public async Task<ActionResult<IEnumerable<object>>> GetProdutos()
    {
        var produtos = await _context.Produtos
            .OrderByDescending(p => p.Id)
            .Select(p => new
            {
                p.Id,
                p.Descricao,
                p.Quantidade,
                p.Valor,
                p.Fornecedor,
                p.CaminhoImagem,
                p.DataCadastro
            })
            .ToListAsync();

        return Ok(produtos);
    }

    // GET: api/produtos/5
    [HttpGet("{id}")]
    public async Task<ActionResult<object>> GetProduto(int id)
    {
        var produto = await _context.Produtos
            .Where(p => p.Id == id)
            .Select(p => new
            {
                p.Id,
                p.Descricao,
                p.Quantidade,
                p.Valor,
                p.Fornecedor,
                p.CaminhoImagem,
                p.DataCadastro
            })
            .FirstOrDefaultAsync();

        if (produto == null)
        {
            return NotFound(new { mensagem = "Produto não encontrado" });
        }

        return Ok(produto);
    }

    // POST: api/produtos
    [HttpPost]
    public async Task<ActionResult<object>> PostProduto([FromForm] string descricao, [FromForm] int quantidade, [FromForm] decimal valor, [FromForm] string fornecedor, [FromForm] IFormFile? imagem)
    {
        var produto = new Produto
        {
            Descricao = descricao,
            Quantidade = quantidade,
            Valor = valor,
            Fornecedor = fornecedor,
            DataCadastro = DateTime.UtcNow
        };

        // Processar upload de imagem
        if (imagem != null && imagem.Length > 0)
        {
            try
            {
                var uploadsDir = Path.Combine(_env.WebRootPath, "uploads");
                
                // Criar pasta se não existir (funciona em Windows e Linux)
                if (!Directory.Exists(uploadsDir))
                {
                    Directory.CreateDirectory(uploadsDir);
                }

                // Gerar nome único para arquivo
                var nomeArquivo = $"produto_{DateTime.Now:yyyyMMddHHmmss}_{Guid.NewGuid().ToString().Substring(0, 8)}{Path.GetExtension(imagem.FileName)}";
                var caminhoCompleto = Path.Combine(uploadsDir, nomeArquivo);

                using (var stream = new FileStream(caminhoCompleto, FileMode.Create))
                {
                    await imagem.CopyToAsync(stream);
                }

                // Armazenar caminho relativo (uploads/produto_...)
                produto.CaminhoImagem = $"uploads/{nomeArquivo}";
                _logger.LogInformation($"Imagem salva em: {caminhoCompleto}");
            }
            catch (Exception ex)
            {
                _logger.LogError($"Erro ao salvar imagem: {ex.Message}");
                return BadRequest(new { mensagem = "Erro ao salvar imagem" });
            }
        }

        _context.Produtos.Add(produto);
        await _context.SaveChangesAsync();

        _logger.LogInformation($"Produto {produto.Id} salvo com imagem: {produto.CaminhoImagem}");

        return CreatedAtAction(nameof(GetProduto), new { id = produto.Id }, produto);
    }

    // PUT: api/produtos/5
    [HttpPut("{id}")]
    public async Task<IActionResult> PutProduto(int id, [FromForm] string descricao, [FromForm] int quantidade, [FromForm] decimal valor, [FromForm] string fornecedor, [FromForm] IFormFile? imagem)
    {
        var existente = await _context.Produtos.FindAsync(id);
        if (existente == null)
        {
            return NotFound(new { mensagem = "Produto não encontrado" });
        }

        existente.Descricao = descricao;
        existente.Quantidade = quantidade;
        existente.Valor = valor;
        existente.Fornecedor = fornecedor;

        // Processar nova imagem se fornecida
        if (imagem != null && imagem.Length > 0)
        {
            try
            {
                // Deletar imagem antiga se existir
                if (!string.IsNullOrEmpty(existente.CaminhoImagem))
                {
                    var caminhoAntigo = Path.Combine(_env.WebRootPath, existente.CaminhoImagem);
                    if (System.IO.File.Exists(caminhoAntigo))
                    {
                        System.IO.File.Delete(caminhoAntigo);
                    }
                }

                var uploadsDir = Path.Combine(_env.WebRootPath, "uploads");
                if (!Directory.Exists(uploadsDir))
                {
                    Directory.CreateDirectory(uploadsDir);
                }

                var nomeArquivo = $"produto_{DateTime.Now:yyyyMMddHHmmss}_{Guid.NewGuid().ToString().Substring(0, 8)}{Path.GetExtension(imagem.FileName)}";
                var caminhoCompleto = Path.Combine(uploadsDir, nomeArquivo);

                using (var stream = new FileStream(caminhoCompleto, FileMode.Create))
                {
                    await imagem.CopyToAsync(stream);
                }

                existente.CaminhoImagem = $"uploads/{nomeArquivo}";
                _logger.LogInformation($"Imagem atualizada para: {caminhoCompleto}");
            }
            catch (Exception ex)
            {
                _logger.LogError($"Erro ao salvar imagem: {ex.Message}");
                return BadRequest(new { mensagem = "Erro ao salvar imagem" });
            }
        }

        await _context.SaveChangesAsync();

        return Ok(new { mensagem = "Produto atualizado com sucesso!" });
    }

    // DELETE: api/produtos/5
    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteProduto(int id)
    {
        var produto = await _context.Produtos.FindAsync(id);
        if (produto == null)
        {
            return NotFound(new { mensagem = "Produto não encontrado" });
        }

        // Deletar arquivo de imagem
        if (!string.IsNullOrEmpty(produto.CaminhoImagem))
        {
            try
            {
                var caminhoCompleto = Path.Combine(_env.WebRootPath, produto.CaminhoImagem);
                if (System.IO.File.Exists(caminhoCompleto))
                {
                    System.IO.File.Delete(caminhoCompleto);
                    _logger.LogInformation($"Imagem deletada: {caminhoCompleto}");
                }
            }
            catch (Exception ex)
            {
                _logger.LogError($"Erro ao deletar imagem: {ex.Message}");
            }
        }

        _context.Produtos.Remove(produto);
        await _context.SaveChangesAsync();

        return Ok(new { mensagem = "Produto excluído com sucesso!" });
    }
}
