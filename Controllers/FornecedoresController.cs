using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using Microsoft.AspNetCore.Http;
using System.Security.Claims;

namespace CadastroUsuarios.Controllers;

[ApiController]
[Route("api/[controller]")]
public class FornecedoresController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly ILogger<FornecedoresController> _logger;

    public FornecedoresController(AppDbContext context, ILogger<FornecedoresController> logger)
    {
        _context = context;
        _logger = logger;
    }

    [HttpGet]
    public async Task<ActionResult<IEnumerable<object>>> GetFornecedores()
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        var fornecedores = await _context.Fornecedores
            .Where(f => f.UsuarioId == usuarioId)
            .OrderByDescending(f => f.Id)
            .Select(f => new {
                f.Id,
                f.NomeFantasia,
                f.CNPJ,
                f.EmailVendas,
                f.Telefone,
                f.Endereco,
                f.DataCadastro
            }).ToListAsync();

        return Ok(fornecedores);
    }

    [HttpGet("{id}")]
    public async Task<ActionResult<object>> GetFornecedor(int id)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        var fornecedor = await _context.Fornecedores
            .Where(f => f.Id == id && f.UsuarioId == usuarioId)
            .Select(f => new {
                f.Id,
                f.NomeFantasia,
                f.CNPJ,
                f.EmailVendas,
                f.Telefone,
                f.Endereco,
                f.DataCadastro
            })
            .FirstOrDefaultAsync();

        if (fornecedor == null) return NotFound(new { mensagem = "Fornecedor não encontrado" });
        return Ok(fornecedor);
    }

    [HttpPost]
    public async Task<ActionResult<object>> PostFornecedor([FromBody] Fornecedor fornecedor)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        if (!ModelState.IsValid) return BadRequest(ModelState);
        fornecedor.UsuarioId = usuarioId.Value;
        fornecedor.DataCadastro = DateTime.UtcNow;

        _context.Fornecedores.Add(fornecedor);
        await _context.SaveChangesAsync();

        return CreatedAtAction(nameof(GetFornecedor), new { id = fornecedor.Id }, new {
            fornecedor.Id,
            fornecedor.NomeFantasia,
            fornecedor.CNPJ,
            fornecedor.EmailVendas,
            fornecedor.Telefone,
            fornecedor.Endereco,
            fornecedor.DataCadastro
        });
    }

    [HttpPut("{id}")]
    public async Task<IActionResult> PutFornecedor(int id, [FromBody] Fornecedor fornecedor)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });
        if (id != fornecedor.Id) return BadRequest(new { mensagem = "ID do fornecedor não corresponde" });
        if (!ModelState.IsValid) return BadRequest(ModelState);

        var existente = await _context.Fornecedores.FirstOrDefaultAsync(f => f.Id == id && f.UsuarioId == usuarioId);
        if (existente == null) return NotFound(new { mensagem = "Fornecedor não encontrado" });

        existente.NomeFantasia = fornecedor.NomeFantasia;
        existente.CNPJ = fornecedor.CNPJ;
        existente.EmailVendas = fornecedor.EmailVendas;
        existente.Telefone = fornecedor.Telefone;
        existente.Endereco = fornecedor.Endereco;

        await _context.SaveChangesAsync();
        return Ok(new { mensagem = "Fornecedor atualizado com sucesso!" });
    }

    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteFornecedor(int id)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        var fornecedor = await _context.Fornecedores.FirstOrDefaultAsync(f => f.Id == id && f.UsuarioId == usuarioId);
        if (fornecedor == null) return NotFound(new { mensagem = "Fornecedor não encontrado" });

        _context.Fornecedores.Remove(fornecedor);
        await _context.SaveChangesAsync();
        return Ok(new { mensagem = "Fornecedor excluído com sucesso!" });
    }

    private int? ObterUsuarioId()
    {
        var sessionId = HttpContext?.Session?.GetInt32("UsuarioId");
        if (sessionId.HasValue) return sessionId.Value;

        var claimId = User?.Claims?.FirstOrDefault(c => c.Type == ClaimTypes.NameIdentifier || c.Type == "UsuarioId");
        if (claimId != null && int.TryParse(claimId.Value, out var idFromClaim)) return idFromClaim;

        return null;
    }
}
