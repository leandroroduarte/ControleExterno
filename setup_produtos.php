<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=ControleExterno;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $sql = "CREATE TABLE IF NOT EXISTS Produtos (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10, 2) NOT NULL,
        quantidade INT DEFAULT 0,
        imagem VARCHAR(255),
        user_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
        INDEX idx_nome (nome),
        INDEX idx_user_id (user_id),
        UNIQUE KEY uk_nome_user (nome, user_id)
    )";
    
    $pdo->exec($sql);
    echo "✅ Tabela Produtos criada com sucesso!\n";
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
