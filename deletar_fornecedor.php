<?php
// deletar_fornecedor.php
session_start(); if(!isset($_SESSION['user_id'])){ header('Location:index.php'); exit; }
$user_id = $_SESSION['user_id']; $id = intval($_GET['id'] ?? 0);
if($id<=0){ header('Location: fornecedores.php?erro=ID inválido'); exit; }
try{ $pdo=new PDO('mysql:host=127.0.0.1;port=3306;dbname=ControleExterno;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]); $stmt=$pdo->prepare('DELETE FROM Fornecedores WHERE id=? AND user_id=?'); $stmt->execute([$id,$user_id]); if($stmt->rowCount()>0) header('Location: fornecedores.php?sucesso=Fornecedor deletado'); else header('Location: fornecedores.php?erro=Fornecedor não encontrado'); } catch(PDOException $e){ header('Location: fornecedores.php?erro='.urlencode('Erro: '.$e->getMessage())); }
exit;
?>