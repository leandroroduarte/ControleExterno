<?php
// verificar_e_criar_tabela.php - Verificar estrutura e criar com compatibilidade

try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<h2>Verificando estrutura de Users...</h2>";
    
    // Verificar estrutura de Users
    $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, COLLATION_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'Users' AND COLUMN_NAME = 'id'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$db]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        echo "<p><strong>Coluna 'id' em Users:</strong></p>";
        echo "<ul>";
        echo "<li>Tipo: " . htmlspecialchars($resultado['COLUMN_TYPE']) . "</li>";
        echo "<li>Collation: " . htmlspecialchars($resultado['COLLATION_NAME']) . "</li>";
        echo "</ul>";
    }
    
    // Deletar tabela Clientes se existir
    echo "<hr>";
    echo "<h2>Limpando tabela anterior...</h2>";
    $pdo->exec("DROP TABLE IF EXISTS Clientes");
    echo "<p style='color: green;'>✅ Tabela anterior removida</p>";
    
    // Agora criar sem foreign key primeiro
    echo "<hr>";
    echo "<h2>Criando tabela Clientes SEM constraint (primeira tentativa)...</h2>";
    
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
        user_id INT UNSIGNED,
        INDEX idx_nome (nome),
        INDEX idx_email (email),
        INDEX idx_cpf_cnpj (cpf_cnpj),
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Tabela Clientes criada sem constraint</p>";
    
    // Agora adicionar a foreign key
    echo "<h2>Adicionando constraint de chave estrangeira...</h2>";
    
    try {
        $sql_fk = "ALTER TABLE Clientes ADD CONSTRAINT fk_clientes_user FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL";
        $pdo->exec($sql_fk);
        echo "<p style='color: green;'>✅ Constraint adicionada com sucesso</p>";
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠️ Não foi possível adicionar constraint: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>A tabela foi criada mesmo assim e funcionará sem a constraint de integridade referencial.</p>";
    }
    
    // Mostrar estrutura final
    echo "<hr>";
    echo "<h3>Estrutura Final da Tabela Clientes:</h3>";
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
    
    echo "<p style='margin-top: 20px; color: green;'><strong>✅ Tabela Clientes pronta para uso!</strong></p>";
    echo "<p><a href='clientes.php' style='color: blue; text-decoration: underline;'>Ir para página de clientes</a></p>";
    
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
        h1, h2, h3 {
            color: #333;
        }
        table {
            background-color: white;
        }
        pre {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }
        ul {
            margin: 10px 0;
        }
    </style>
</head>
</html>
