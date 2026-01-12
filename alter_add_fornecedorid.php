<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=ControleExterno;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    // Verifica se a coluna já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'Produtos' AND COLUMN_NAME = 'fornecedor_id'");
    $stmt->execute(['ControleExterno']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (intval($row['cnt']) === 0) {
        $pdo->exec("ALTER TABLE Produtos ADD COLUMN fornecedor_id INT NULL");
        echo "Coluna fornecedor_id adicionada.\n";
    } else {
        echo "Coluna fornecedor_id já existe.\n";
    }
    // Cria índice se não existir (verifica em INFORMATION_SCHEMA.STATISTICS)
    $stmt2 = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'Produtos' AND INDEX_NAME = 'idx_fornecedor_id'");
    $stmt2->execute(['ControleExterno']);
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    if (intval($row2['cnt']) === 0) {
        $pdo->exec("CREATE INDEX idx_fornecedor_id ON Produtos (fornecedor_id)");
        echo "Índice idx_fornecedor_id criado.\n";
    } else {
        echo "Índice idx_fornecedor_id já existe.\n";
    }
    echo "OK\n";
} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage() . "\n";
}
?>