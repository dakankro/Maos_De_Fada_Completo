<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (empty($_SESSION['funcionario_id'])) {
    header('Location: ../paginas/conta/login_funcionario.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $st = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
    $st->execute([$id]);
}
header('Location: produtos.php?msg=Produto+excluido+com+sucesso');
exit;
