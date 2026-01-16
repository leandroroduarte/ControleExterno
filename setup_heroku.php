<?php
// setup_heroku.php - Script para configurar o banco de dados no Heroku
require_once 'config.php';

echo "<h2>Setup do Banco de Dados no Heroku</h2>";

try {
    $pdo = getDbConnection();
    echo "<p>✅ Conexão com o banco estabelecida com sucesso!</p>";
    
    // Lê o arquivo SQL
    $sql_file = __DIR__ . '/setup_heroku_db.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("Arquivo setup_heroku_db.sql não encontrado!");
    }
    
    $sql = file_get_contents($sql_file);
    
    // Separa por comando (dividido por ;)
    $commands = explode(';', $sql);
    
    echo "<h3>Executando comandos SQL...</h3>";
    foreach ($commands as $command) {
        $command = trim($command);
        if (!empty($command)) {
            try {
                $pdo->exec($command);
                echo "<p>✅ " . substr($command, 0, 60) . "...</p>";
            } catch (PDOException $e) {
                echo "<p>⚠️ Erro ao executar comando: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Verificando tabelas criadas:</h3>";
    
    // Para PostgreSQL
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
    ");
    
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>📋 " . $table['table_name'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ Nenhuma tabela encontrada.</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 Setup concluído!</h3>";
    echo "<p><a href='index.php'>Ir para a página inicial</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
