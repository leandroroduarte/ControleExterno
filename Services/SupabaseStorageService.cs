using System.Net.Http;
using System.Net.Http.Headers;

namespace CadastroUsuarios.Services;

public class SupabaseStorageService
{
    private readonly string _supabaseUrl;
    private readonly string _supabaseKey;
    private readonly string _bucketName;
    private readonly HttpClient _httpClient;
    private readonly ILogger<SupabaseStorageService> _logger;

    public SupabaseStorageService(IConfiguration configuration, HttpClient httpClient, ILogger<SupabaseStorageService> logger)
    {
        _supabaseUrl = configuration["Supabase:Url"] ?? "https://xlhgjoxukrdebnetpwqp.supabase.co";
        // Prioriza chave de serviço; se não houver, aceita publishable para políticas públicas
        _supabaseKey = Environment.GetEnvironmentVariable("SUPABASE_SERVICE_KEY")
                        ?? Environment.GetEnvironmentVariable("SUPABASE_PUBLISHABLE_KEY")
                        ?? configuration["Supabase:Key"]
                        ?? configuration["Supabase:PublishableKey"]
                        ?? string.Empty;
        _bucketName = configuration["Supabase:Bucket"] ?? "produtos-imagens";
        _httpClient = httpClient;
        _logger = logger;
        
        _logger.LogInformation($"SupabaseStorageService inicializado - URL: {_supabaseUrl}, Bucket: {_bucketName}, Key presente: {!string.IsNullOrEmpty(_supabaseKey)}");
    }

    public async Task<string?> UploadImageAsync(IFormFile file)
    {
        try
        {
            if (file == null || file.Length == 0)
            {
                _logger.LogWarning("Arquivo vazio ou nulo");
                return null;
            }

            // Se temos variável de ambiente STORAGE_MODE=supabase, usa Supabase
            var storageMode = Environment.GetEnvironmentVariable("STORAGE_MODE") ?? "local";
            
            if (storageMode == "supabase")
            {
                return await UploadToSupabaseAsync(file);
            }
            else
            {
                return await UploadLocalAsync(file);
            }
        }
        catch (Exception ex)
        {
            _logger.LogError($"Erro ao fazer upload de imagem: {ex.Message}");
            return null;
        }
    }

    private async Task<string?> UploadLocalAsync(IFormFile file)
    {
        try
        {
            var uploadsDir = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", "uploads");
            if (!Directory.Exists(uploadsDir))
            {
                Directory.CreateDirectory(uploadsDir);
            }

            var fileName = $"{DateTime.UtcNow:yyyyMMddHHmmss}_{Guid.NewGuid().ToString()[..8]}{Path.GetExtension(file.FileName)}";
            var filePath = Path.Combine(uploadsDir, fileName);

            using (var stream = new FileStream(filePath, FileMode.Create))
            {
                await file.CopyToAsync(stream);
            }

            var relativePath = $"uploads/{fileName}";
            _logger.LogInformation($"Imagem salva localmente: {relativePath}");
            return relativePath;
        }
        catch (Exception ex)
        {
            _logger.LogError($"Erro ao salvar imagem localmente: {ex.Message}");
            return null;
        }
    }

    private async Task<string?> UploadToSupabaseAsync(IFormFile file)
    {
        try
        {
            var fileName = $"{DateTime.UtcNow:yyyyMMddHHmmss}_{Guid.NewGuid().ToString()[..8]}{Path.GetExtension(file.FileName)}";
            var filePath = $"produtos/{fileName}";

            using var stream = file.OpenReadStream();
            using var content = new StreamContent(stream);
            content.Headers.ContentType = new MediaTypeHeaderValue(file.ContentType ?? "application/octet-stream");

            var uploadUrl = $"{_supabaseUrl}/storage/v1/object/{_bucketName}/{filePath}";

            var request = new HttpRequestMessage(HttpMethod.Post, uploadUrl);
            request.Headers.Add("Authorization", $"Bearer {_supabaseKey}");
            request.Headers.Add("apikey", _supabaseKey);
            request.Content = content;

            _logger.LogInformation($"Iniciando upload para Supabase: {uploadUrl}");

            var response = await _httpClient.SendAsync(request);
            if (!response.IsSuccessStatusCode)
            {
                var error = await response.Content.ReadAsStringAsync();
                _logger.LogError($"Erro ao fazer upload no Supabase: {response.StatusCode} - {error}");
                return null;
            }

            var publicUrl = $"{_supabaseUrl}/storage/v1/object/public/{_bucketName}/{filePath}";
            _logger.LogInformation($"Imagem enviada para Supabase: {publicUrl}");
            return publicUrl;
        }
        catch (Exception ex)
        {
            _logger.LogError($"Erro ao fazer upload no Supabase: {ex.Message}");
            return null;
        }
    }

    public async Task<bool> DeleteImageAsync(string imageUrl)
    {
        var storageMode = Environment.GetEnvironmentVariable("STORAGE_MODE") ?? "local";
        
        if (storageMode == "supabase")
        {
            return await DeleteFromSupabaseAsync(imageUrl);
        }
        else
        {
            return DeleteLocalAsync(imageUrl);
        }
    }

    private bool DeleteLocalAsync(string imageUrl)
    {
        try
        {
            var fileName = Path.GetFileName(imageUrl);
            var filePath = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", "uploads", fileName);

            if (File.Exists(filePath))
            {
                File.Delete(filePath);
                _logger.LogInformation($"Imagem local deletada: {fileName}");
                return true;
            }

            return false;
        }
        catch (Exception ex)
        {
            _logger.LogError($"Erro ao deletar imagem local: {ex.Message}");
            return false;
        }
    }

    private async Task<bool> DeleteFromSupabaseAsync(string imageUrl)
    {
        try
        {
            _logger.LogInformation($"[DELETE] Tentando deletar imagem: {imageUrl}");
            
            // Extrai apenas "produtos/arquivo.jpg" da URL completa
            // URL: https://...supabase.co/storage/v1/object/public/produtos-imagens/produtos/arquivo.jpg
            // Queremos: produtos/arquivo.jpg
            var filePath = imageUrl.Split(new[] { $"{_bucketName}/" }, StringSplitOptions.None).LastOrDefault() ?? string.Empty;
            
            _logger.LogInformation($"[DELETE] FilePath extraído: {filePath}");

            var deleteUrl = $"{_supabaseUrl}/storage/v1/object/{_bucketName}/{filePath}";
            _logger.LogInformation($"[DELETE] URL completa: {deleteUrl}");
            _logger.LogInformation($"[DELETE] Usando chave: {_supabaseKey.Substring(0, 20)}...");

            var request = new HttpRequestMessage(HttpMethod.Delete, deleteUrl);
            request.Headers.Add("Authorization", $"Bearer {_supabaseKey}");
            request.Headers.Add("apikey", _supabaseKey);

            var response = await _httpClient.SendAsync(request);
            
            _logger.LogInformation($"[DELETE] Status HTTP: {response.StatusCode}");
            var responseBody = await response.Content.ReadAsStringAsync();
            _logger.LogInformation($"[DELETE] Resposta: {responseBody}");
            
            if (response.IsSuccessStatusCode)
            {
                _logger.LogInformation($"✅ Imagem Supabase deletada: {filePath}");
                return true;
            }
            else
            {
                _logger.LogError($"❌ Falha ao deletar. Status: {response.StatusCode}, Body: {responseBody}");
                return false;
            }
        }
        catch (Exception ex)
        {
            _logger.LogError($"❌ EXCEPTION ao deletar imagem do Supabase: {ex.Message}");
            _logger.LogError($"Stack: {ex.StackTrace}");
            return false;
        }
    }
}
