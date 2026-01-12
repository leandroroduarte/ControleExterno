<?php
// executar_sql.php - Executar SQL para criar tabela de clientes

try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $sql = "CREATE TABLE IF NOT EXISTS Clientes (
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
    )";
    
    $pdo->exec($sql);
    
    echo "✅ Tabela Clientes criada com sucesso!";
    
} catch (PDOException $e) {
    echo "❌ Erro ao criar tabela: " . $e->getMessage();
}
?>
