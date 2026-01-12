<?php
// criar_tabela_produtos.php - Script para criar tabela de produtos
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
    echo '<h2 style="color: green; font-family: Arial;">✅ Tabela Produtos criada com sucesso!</h2>';
    echo '<p>Redirecionando em 3 segundos...</p>';
    echo '<script>setTimeout(function() { location.href = "produtos.php"; }, 3000);</script>';
} catch (PDOException $e) {
    echo '<h2 style="color: red; font-family: Arial;">❌ Erro ao criar tabela:</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
