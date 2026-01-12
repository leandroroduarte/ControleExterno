<?php
// verificar_tabela.php - Verificar se a tabela Clientes existe

try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Verificar se a tabela existe
    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'Clientes'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$db]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h1>Verificação de Tabela</h1>";
    echo "<hr>";
    
    if ($resultado) {
        echo "<h2 style='color: green;'>✅ A tabela <strong>Clientes</strong> EXISTS no banco <strong>ControleExterno</strong></h2>";
        
        // Mostrar estrutura da tabela
        echo "<h3>Estrutura da Tabela:</h3>";
        $sql = "DESCRIBE Clientes";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
        echo "</tr>";
        
        foreach ($colunas as $coluna) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($coluna['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($coluna['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($coluna['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($coluna['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($coluna['Default'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($coluna['Extra'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<h2 style='color: red;'>❌ A tabela <strong>Clientes</strong> NÃO EXISTE no banco <strong>ControleExterno</strong></h2>";
        echo "<p>Por favor, execute o comando SQL abaixo:</p>";
        echo "<pre style='background-color: #f0f0f0; padding: 10px; border-radius: 5px;'>";
        echo "CREATE TABLE IF NOT EXISTS Clientes (
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
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL
);";
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Erro de Conexão:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1, h2, h3 {
            color: #333;
        }
        table {
            background-color: white;
            margin-top: 20px;
        }
        pre {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
</html>
