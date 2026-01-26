namespace CadastroUsuarios.Middleware;

public class ErrorHandlingMiddleware
{
    private readonly RequestDelegate _next;
    private readonly ILogger<ErrorHandlingMiddleware> _logger;

    public ErrorHandlingMiddleware(RequestDelegate next, ILogger<ErrorHandlingMiddleware> logger)
    {
        _next = next;
        _logger = logger;
    }

    public async Task InvokeAsync(HttpContext context)
    {
        try
        {
            _logger.LogInformation($"📥 Requisição: {context.Request.Method} {context.Request.Path}");
            await _next(context);
            _logger.LogInformation($"📤 Resposta: {context.Response.StatusCode}");
        }
        catch (Exception ex)
        {
            _logger.LogError($"❌ Erro não tratado: {ex.Message}");
            _logger.LogError($"📋 Stack trace:\n{ex.StackTrace}");
            
            context.Response.StatusCode = StatusCodes.Status500InternalServerError;
            context.Response.ContentType = "application/json";
            await context.Response.WriteAsJsonAsync(new
            {
                mensagem = "Erro interno do servidor",
                erro = ex.Message
            });
        }
    }
}
