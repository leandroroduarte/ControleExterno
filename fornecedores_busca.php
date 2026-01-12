<?php
// fornecedores_busca.php - retorna JSON com fornecedores do usuário logado filtrados por query
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode([]); exit; }
$user_id = $_SESSION['user_id'];
$q = trim($_GET['q'] ?? '');
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=ControleExterno;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    if ($q === '') {
        $stmt = $pdo->prepare('SELECT id,nome FROM Fornecedores WHERE user_id = ? ORDER BY nome LIMIT 20');
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare('SELECT id,nome FROM Fornecedores WHERE user_id = ? AND nome LIKE ? ORDER BY nome LIMIT 20');
        $stmt->execute([$user_id, "%".$q."%"]);
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([]);
}
?>