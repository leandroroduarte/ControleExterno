using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using CadastroUsuarios.Services;
using System.IO;
using System.Security.Claims;
using Microsoft.AspNetCore.Http;

namespace CadastroUsuarios.Controllers;

[ApiController]
[Route("api/[controller]")]
public class ProdutosController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly ILogger<ProdutosController> _logger;
    private readonly SupabaseStorageService _storageService;

    public ProdutosController(AppDbContext context, ILogger<ProdutosController> logger, SupabaseStorageService storageService)
    {
        _context = context;
        _logger = logger;
        _storageService = storageService;
    }

    // GET: api/produtos
    [HttpGet]
    public async Task<ActionResult<IEnumerable<object>>> GetProdutos()
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null)
        {
            return Unauthorized(new { mensagem = "Usuário não autenticado" });
        }

        var produtos = await _context.Produtos
            .Where(p => p.UsuarioId == usuarioId)
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
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null)
        {
            return Unauthorized(new { mensagem = "Usuário não autenticado" });
        }

        var produto = await _context.Produtos
            .Where(p => p.Id == id && p.UsuarioId == usuarioId)
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
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null)
        {
            return Unauthorized(new { mensagem = "Usuário não autenticado" });
        }

        var produto = new Produto
        {
            Descricao = descricao,
            Quantidade = quantidade,
            Valor = valor,
            Fornecedor = fornecedor,
            DataCadastro = DateTime.UtcNow,
            UsuarioId = usuarioId.Value
        };

        // Processar upload de imagem para Supabase Storage
        if (imagem != null && imagem.Length > 0)
        {
            var imageUrl = await _storageService.UploadImageAsync(imagem);
            if (imageUrl != null)
            {
                produto.CaminhoImagem = imageUrl;
                _logger.LogInformation($"Imagem enviada para Supabase: {imageUrl}");
            }
            else
            {
                _logger.LogWarning("Falha ao fazer upload da imagem no Supabase");
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
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null)
        {
            return Unauthorized(new { mensagem = "Usuário não autenticado" });
        }

        var existente = await _context.Produtos.FirstOrDefaultAsync(p => p.Id == id && p.UsuarioId == usuarioId);
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
            // Deletar imagem antiga do Supabase se existir
            if (!string.IsNullOrEmpty(existente.CaminhoImagem) && existente.CaminhoImagem.Contains("supabase"))
            {
                await _storageService.DeleteImageAsync(existente.CaminhoImagem);
            }

            // Upload nova imagem
            var imageUrl = await _storageService.UploadImageAsync(imagem);
            if (imageUrl != null)
            {
                existente.CaminhoImagem = imageUrl;
                _logger.LogInformation($"Imagem atualizada no Supabase: {imageUrl}");
            }
        }

        await _context.SaveChangesAsync();

        return Ok(new { mensagem = "Produto atualizado com sucesso!" });
    }

    // DELETE: api/produtos/5
    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteProduto(int id)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null)
        {
            return Unauthorized(new { mensagem = "Usuário não autenticado" });
        }

        var produto = await _context.Produtos.FirstOrDefaultAsync(p => p.Id == id && p.UsuarioId == usuarioId);
        if (produto == null)
        {
            return NotFound(new { mensagem = "Produto não encontrado" });
        }

        // Deletar imagem do Supabase se existir
        if (!string.IsNullOrEmpty(produto.CaminhoImagem) && produto.CaminhoImagem.Contains("supabase"))
        {
            await _storageService.DeleteImageAsync(produto.CaminhoImagem);
            _logger.LogInformation($"Imagem deletada do Supabase: {produto.CaminhoImagem}");
        }

        _context.Produtos.Remove(produto);
        await _context.SaveChangesAsync();

        return Ok(new { mensagem = "Produto excluído com sucesso!" });
    }

    // Tenta resolver o ID do usuário a partir da Session ou Claims
    private int? ObterUsuarioId()
    {
        // 1) Session (ex.: HttpContext.Session.SetInt32("UsuarioId", id))
        var sessionId = HttpContext?.Session?.GetInt32("UsuarioId");
        if (sessionId.HasValue)
        {
            return sessionId.Value;
        }

        // 2) Claims (ex.: cookie de autenticação com ClaimTypes.NameIdentifier ou "UsuarioId")
        var claimId = User?.Claims?.FirstOrDefault(c =>
            c.Type == ClaimTypes.NameIdentifier || c.Type == "UsuarioId");
        if (claimId != null && int.TryParse(claimId.Value, out var idFromClaim))
        {
            return idFromClaim;
        }

        return null;
    }
}
