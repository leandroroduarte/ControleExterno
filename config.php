<?php
// config.php - Configuração de banco de dados para ambientes local e Heroku

function getDatabaseConfig() {
    // Verifica se está no Heroku (detecta variável de ambiente DATABASE_URL)
    $database_url = getenv('DATABASE_URL') ?: getenv('CLEARDB_DATABASE_URL') ?: getenv('JAWSDB_URL');
    
    if ($database_url) {
        // Parse da URL do banco de dados do Heroku
        // Formato: mysql://username:password@host:port/dbname
        $url = parse_url($database_url);
        
        return [
            'host' => $url['host'] ?? '127.0.0.1',
            'port' => $url['port'] ?? 3306,
            'database' => ltrim($url['path'] ?? '', '/'),
            'username' => $url['user'] ?? 'root',
            'password' => $url['pass'] ?? ''
        ];
    }
    
    // Configuração local (Laragon)
    return [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'ControleExterno',
        'username' => 'root',
        'password' => ''
    ];
}

function getDbConnection() {
    $config = getDatabaseConfig();
    
    try {
        $dsn = sprintf(
            "mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4",
            $config['host'],
            $config['port'],
            $config['database']
        );
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro de conexão com banco de dados: " . $e->getMessage());
        throw $e;
    }
}

// Configurações de email para Heroku
function getEmailConfig() {
    return [
        'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.zoho.com',
        'smtp_port' => getenv('SMTP_PORT') ?: 587,
        'smtp_username' => getenv('SMTP_USERNAME') ?: 'leandroroduarte@guilhermemduarte.systems',
        'smtp_password' => getenv('SMTP_PASSWORD') ?: '@Gmd320808',
        'from_email' => getenv('FROM_EMAIL') ?: 'leandroroduarte@guilhermemduarte.systems',
        'from_name' => getenv('FROM_NAME') ?: 'Controle Externo'
    ];
}

// URL base da aplicação
function getBaseUrl() {
    $heroku_app = getenv('HEROKU_APP_NAME');
    if ($heroku_app) {
        return "https://{$heroku_app}.herokuapp.com";
    }
    return 'http://localhost:8080/ControleExterno';
}
