using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using CadastroUsuarios.Data;
using CadastroUsuarios.Models;
using Microsoft.AspNetCore.Http;
using BCrypt.Net;

namespace CadastroUsuarios.Controllers;

[ApiController]
[Route("api/[controller]")]
public class UsuariosController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly ILogger<UsuariosController> _logger;

    public class LoginRequest
    {
        public string Email { get; set; } = string.Empty;
        public string Senha { get; set; } = string.Empty;
    }

    public UsuariosController(AppDbContext context, ILogger<UsuariosController> logger)
    {
        _context = context;
        _logger = logger;
    }

    // POST: api/usuarios/login
    [HttpPost("login")]
    public async Task<ActionResult<object>> Login([FromBody] LoginRequest request)
    {
        if (string.IsNullOrWhiteSpace(request.Email) || string.IsNullOrWhiteSpace(request.Senha))
        {
            return BadRequest(new { mensagem = "Email e senha são obrigatórios" });
        }

        var usuario = await _context.Usuarios
            .FirstOrDefaultAsync(u => u.Email == request.Email);

        if (usuario == null || !BCrypt.Net.BCrypt.Verify(request.Senha, usuario.Senha))
        {
            return Unauthorized(new { mensagem = "Email ou senha incorretos" });
        }

        // Grava o ID do usuário na sessão para ser usado pelos endpoints protegidos
        HttpContext.Session.SetInt32("UsuarioId", usuario.Id);
        _logger.LogInformation($"Sessão criada para usuário ID {usuario.Id}");

        return Ok(new
        {
            usuario.Id,
            usuario.Nome,
            usuario.Email,
            mensagem = "Login realizado com sucesso"
        });
    }

    // GET: api/usuarios
    [HttpGet]
    public async Task<ActionResult<IEnumerable<object>>> GetUsuarios()
    {
        var usuarios = await _context.Usuarios
            .Select(u => new
            {
                u.Id,
                u.Nome,
                u.Email,
                u.DataCadastro
                // Não retornar a senha
            })
            .ToListAsync();

        return Ok(usuarios);
    }

    // GET: api/usuarios/5
    [HttpGet("{id}")]
    public async Task<ActionResult<object>> GetUsuario(int id)
    {
        var usuario = await _context.Usuarios
            .Where(u => u.Id == id)
            .Select(u => new
            {
                u.Id,
                u.Nome,
                u.Email,
                u.Senha, // Retornar senha para edição
                u.DataCadastro
            })
            .FirstOrDefaultAsync();

        if (usuario == null)
        {
            return NotFound(new { mensagem = "Usuário não encontrado" });
        }

        return Ok(usuario);
    }

    // POST: api/usuarios
    [HttpPost]
    public async Task<ActionResult<object>> PostUsuario(Usuario usuario)
    {
        if (!ModelState.IsValid)
        {
            return BadRequest(ModelState);
        }

        // Validar senha mínima
        if (string.IsNullOrWhiteSpace(usuario.Senha) || usuario.Senha.Length < 6)
        {
            return BadRequest(new { mensagem = "A senha deve ter no mínimo 6 caracteres" });
        }

        // Verificar se email já existe
        var emailExiste = await _context.Usuarios
            .AnyAsync(u => u.Email == usuario.Email);

        if (emailExiste)
        {
            return BadRequest(new { mensagem = "Email já cadastrado" });
        }

        // Aplicar criptografia BCrypt na senha
        usuario.Senha = BCrypt.Net.BCrypt.HashPassword(usuario.Senha);
        
        usuario.DataCadastro = DateTime.UtcNow;
        usuario.NormalizarDataCadastro();
        _context.Usuarios.Add(usuario);
        await _context.SaveChangesAsync();

        var usuarioRetorno = new
        {
            usuario.Id,
            usuario.Nome,
            usuario.Email,
            usuario.DataCadastro,
            mensagem = "Usuário cadastrado com sucesso!"
        };

        return CreatedAtAction(nameof(GetUsuario), new { id = usuario.Id }, usuarioRetorno);
    }

    // PUT: api/usuarios/5
    [HttpPut("{id}")]
    public async Task<IActionResult> PutUsuario(int id, Usuario usuario)
    {
        if (id != usuario.Id)
        {
            return BadRequest(new { mensagem = "ID do usuário não corresponde" });
        }

        if (!ModelState.IsValid)
        {
            return BadRequest(ModelState);
        }

        // Buscar usuário existente para comparar
        var usuarioExistente = await _context.Usuarios.FindAsync(id);
        if (usuarioExistente == null)
        {
            return NotFound(new { mensagem = "Usuário não encontrado" });
        }

        // Verificar se email já existe em outro usuário
        if (usuario.Email != usuarioExistente.Email)
        {
            var emailExiste = await _context.Usuarios
                .AnyAsync(u => u.Email == usuario.Email && u.Id != id);

            if (emailExiste)
            {
                return BadRequest(new { mensagem = "Email já cadastrado por outro usuário" });
            }
        }

        // Se a senha foi alterada, aplicar BCrypt
        if (!string.IsNullOrWhiteSpace(usuario.Senha) && usuario.Senha != usuarioExistente.Senha)
        {
            if (usuario.Senha.Length < 6)
            {
                return BadRequest(new { mensagem = "A senha deve ter no mínimo 6 caracteres" });
            }
            usuario.Senha = BCrypt.Net.BCrypt.HashPassword(usuario.Senha);
        }
        else
        {
            // Manter senha existente se não foi alterada
            usuario.Senha = usuarioExistente.Senha;
        }

        usuarioExistente.Nome = usuario.Nome;
        usuarioExistente.Email = usuario.Email;
        usuarioExistente.Senha = usuario.Senha;
        usuarioExistente.NormalizarDataCadastro();

        _context.Entry(usuarioExistente).State = EntityState.Modified;

        try
        {
            await _context.SaveChangesAsync();
        }
        catch (DbUpdateConcurrencyException)
        {
            return NotFound(new { mensagem = "Erro ao atualizar usuário" });
        }

        return Ok(new { mensagem = "Usuário atualizado com sucesso!" });
    }

    // DELETE: api/usuarios/5
    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteUsuario(int id)
    {
        var usuario = await _context.Usuarios.FindAsync(id);
        if (usuario == null)
        {
            return NotFound(new { mensagem = "Usuário não encontrado" });
        }

        _context.Usuarios.Remove(usuario);
        await _context.SaveChangesAsync();

        return Ok(new { mensagem = "Usuário excluído com sucesso!" });
    }

    private async Task<bool> UsuarioExists(int id)
    {
        return await _context.Usuarios.AnyAsync(e => e.Id == id);
    }

    // DEBUG: Listar todos os usuários com senha (remover em produção)
    [HttpGet("debug/todos")]
    public async Task<ActionResult<object>> DebugTodos()
    {
        var usuarios = await _context.Usuarios
            .Select(u => new
            {
                u.Id,
                u.Nome,
                u.Email,
                u.Senha,
                u.DataCadastro
            })
            .ToListAsync();

        return Ok(new
        {
            total = usuarios.Count,
            usuarios = usuarios
        });
    }
}
