<?php
// deletar_cliente.php - Deletar cliente
session_start();

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: clientes.php?erro=ID inválido');
    exit;
}

try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ControleExterno';
    $user = 'root';
    $pass = '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Verificar se o cliente pertence ao usuário logado e deletar
    $stmt = $pdo->prepare('DELETE FROM Clientes WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        header('Location: clientes.php?sucesso=Cliente deletado com sucesso');
    } else {
        header('Location: clientes.php?erro=Cliente não encontrado');
    }
} catch (PDOException $e) {
    header('Location: clientes.php?erro=Erro ao deletar cliente: ' . urlencode($e->getMessage()));
}
exit;
?>
