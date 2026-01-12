<?php
// criar_tabela_clientes.php - Criar tabela Clientes

try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Primeiro, deletar a tabela se existir (para limpar qualquer estado anterior)
    echo "<h2>Limpando tabela anterior...</h2>";
    $pdo->exec("DROP TABLE IF EXISTS Clientes");
    echo "<p style='color: green;'>✅ Tabela anterior removida (se existisse)</p>";
    
    echo "<hr>";
    echo "<h2>Criando tabela Clientes...</h2>";
    
    $sql = "CREATE TABLE Clientes (
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
        FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL,
        INDEX idx_nome (nome),
        INDEX idx_email (email),
        INDEX idx_cpf_cnpj (cpf_cnpj),
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    // Verificar se foi criada
    $sql_check = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'Clientes'";
    $stmt = $pdo->prepare($sql_check);
    $stmt->execute([$db]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        echo "<h1 style='color: green;'>✅ Tabela Clientes criada com SUCESSO!</h1>";
        
        // Mostrar estrutura
        echo "<h3>Estrutura da Tabela Clientes:</h3>";
        $sql_desc = "DESCRIBE Clientes";
        $stmt = $pdo->prepare($sql_desc);
        $stmt->execute();
        $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background-color: #4CAF50; color: white;'>";
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
        
        echo "<p style='margin-top: 20px; color: green;'><strong>✅ A tabela está pronta para uso! Você pode começar a cadastrar clientes.</strong></p>";
    } else {
        echo "<h1 style='color: red;'>❌ Erro ao criar a tabela</h1>";
    }
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>❌ Erro:</h1>";
    echo "<p style='color: red; font-weight: bold;'>" . htmlspecialchars($e->getMessage()) . "</p>";
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
        h1, h3 {
            color: #333;
        }
        table {
            background-color: white;
        }
    </style>
</head>
</html>
