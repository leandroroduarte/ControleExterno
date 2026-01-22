using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using Microsoft.AspNetCore.Http;
using System.Security.Claims;

namespace CadastroUsuarios.Controllers;

[ApiController]
[Route("api/[controller]")]
public class ClientesController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly ILogger<ClientesController> _logger;

    public ClientesController(AppDbContext context, ILogger<ClientesController> logger)
    {
        _context = context;
        _logger = logger;
    }

    [HttpGet]
    public async Task<ActionResult<IEnumerable<object>>> GetClientes()
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        var clientes = await _context.Clientes
            .Where(c => c.UsuarioId == usuarioId)
            .OrderByDescending(c => c.Id)
            .Select(c => new {
                c.Id,
                c.Nome,
                c.CPF_CNPJ,
                c.Email,
                c.Telefone,
                c.Endereco,
                c.DataCadastro
            }).ToListAsync();

        return Ok(clientes);
    }

    [HttpGet("{id}")]
    public async Task<ActionResult<object>> GetCliente(int id)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        var cliente = await _context.Clientes
            .Where(c => c.Id == id && c.UsuarioId == usuarioId)
            .Select(c => new {
                c.Id,
                c.Nome,
                c.CPF_CNPJ,
                c.Email,
                c.Telefone,
                c.Endereco,
                c.DataCadastro
            })
            .FirstOrDefaultAsync();

        if (cliente == null) return NotFound(new { mensagem = "Cliente não encontrado" });
        return Ok(cliente);
    }

    [HttpPost]
    public async Task<ActionResult<object>> PostCliente([FromBody] Cliente cliente)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        if (!ModelState.IsValid) return BadRequest(ModelState);
        cliente.UsuarioId = usuarioId.Value;
        cliente.DataCadastro = DateTime.UtcNow;

        _context.Clientes.Add(cliente);
        await _context.SaveChangesAsync();

        return CreatedAtAction(nameof(GetCliente), new { id = cliente.Id }, new {
            cliente.Id,
            cliente.Nome,
            cliente.CPF_CNPJ,
            cliente.Email,
            cliente.Telefone,
            cliente.Endereco,
            cliente.DataCadastro
        });
    }

    [HttpPut("{id}")]
    public async Task<IActionResult> PutCliente(int id, [FromBody] Cliente cliente)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });
        if (id != cliente.Id) return BadRequest(new { mensagem = "ID do cliente não corresponde" });
        if (!ModelState.IsValid) return BadRequest(ModelState);

        var existente = await _context.Clientes.FirstOrDefaultAsync(c => c.Id == id && c.UsuarioId == usuarioId);
        if (existente == null) return NotFound(new { mensagem = "Cliente não encontrado" });

        existente.Nome = cliente.Nome;
        existente.CPF_CNPJ = cliente.CPF_CNPJ;
        existente.Email = cliente.Email;
        existente.Telefone = cliente.Telefone;
        existente.Endereco = cliente.Endereco;

        await _context.SaveChangesAsync();
        return Ok(new { mensagem = "Cliente atualizado com sucesso!" });
    }

    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteCliente(int id)
    {
        var usuarioId = ObterUsuarioId();
        if (usuarioId == null) return Unauthorized(new { mensagem = "Usuário não autenticado" });

        var cliente = await _context.Clientes.FirstOrDefaultAsync(c => c.Id == id && c.UsuarioId == usuarioId);
        if (cliente == null) return NotFound(new { mensagem = "Cliente não encontrado" });

        _context.Clientes.Remove(cliente);
        await _context.SaveChangesAsync();
        return Ok(new { mensagem = "Cliente excluído com sucesso!" });
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
