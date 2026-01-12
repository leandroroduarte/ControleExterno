<?php
// deletar_produto.php - Deletar produto com imagem
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) { header('Location: produtos.php'); exit; }

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=ControleExterno;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    
    // Buscar produto para pegar imagem
    $stmt = $pdo->prepare('SELECT imagem FROM Produtos WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($produto) {
        // Deletar imagem se existir
        if ($produto['imagem'] && file_exists('uploads/' . $produto['imagem'])) {
            unlink('uploads/' . $produto['imagem']);
        }
        
        // Deletar registro
        $stmt = $pdo->prepare('DELETE FROM Produtos WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user_id]);
    }
} catch (PDOException $e) {
    // Erro ao deletar, mas redireciona mesmo assim
}

header('Location: produtos.php');
exit;
?>
