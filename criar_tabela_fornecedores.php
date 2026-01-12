<?php
// criar_tabela_fornecedores.php - Criar tabela Fornecedores com verificação

try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';

    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<h2>Criando tabela Fornecedores (se necessário)...</h2>";

    // Remover tabela antiga
    $pdo->exec("DROP TABLE IF EXISTS Fornecedores");
    echo "<p>✅ Tabela antiga removida (se existia).</p>";

    // Criar tabela
    $sql = "CREATE TABLE Fornecedores (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        telefone VARCHAR(20),
        cpf_cnpj VARCHAR(20) UNIQUE,
        cep VARCHAR(10),
        endereco VARCHAR(255),
        numero VARCHAR(10),
        complemento VARCHAR(255),
        bairro VARCHAR(100),
        cidade VARCHAR(100),
        estado VARCHAR(2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        user_id INT,
        INDEX idx_nome (nome),
        INDEX idx_email (email),
        INDEX idx_cpf_cnpj (cpf_cnpj),
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "<p>✅ Tabela <strong>Fornecedores</strong> criada (sem foreign key inicialmente).</p>";

    // Tentar adicionar foreign key (capturar erro de incompatibilidade)
    try {
        $pdo->exec("ALTER TABLE Fornecedores ADD CONSTRAINT fk_fornecedores_user FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL");
        echo "<p>✅ Constraint de chave estrangeira adicionada com sucesso.</p>";
    } catch (PDOException $e) {
        echo "<p style='color:orange;'>⚠️ Não foi possível adicionar constraint de foreign key: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>A tabela foi criada sem a constraint, e funcionará normalmente.</p>";
    }

    // Mostrar estrutura
    echo "<hr><h3>Estrutura da tabela Fornecedores:</h3>";
    $stmt = $pdo->query('DESCRIBE Fornecedores');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='8' style='border-collapse:collapse; background:white;'>";
    echo "<tr style='background:#4CAF50;color:white;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($cols as $c) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($c['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($c['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($c['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($c['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($c['Default'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($c['Extra'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<p style='color:green; margin-top:12px;'><strong>✅ Pronto — a tabela está disponível para uso.</strong></p>";

} catch (PDOException $e) {
    echo "<h1 style='color:red;'>Erro ao criar tabela</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Criar tabela Fornecedores</title></head><body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;"></body></html>
