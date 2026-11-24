<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (empty($_SESSION['funcionario_id'])) {
    header('Location: ../paginas/conta/login_funcionario.php');
    exit;
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$pedidos=$pdo->query("SELECT p.id,p.total,p.status,p.criado_em,u.nome,u.email FROM pedidos p INNER JOIN usuarios u ON u.id=p.usuario_id ORDER BY p.id DESC")->fetchAll();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Pedidos - Mãos de Fada</title><link rel="stylesheet" href="admin.css"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"></head><body><div class="admin-wrap"><header class="admin-top"><div class="brand">Mãos de Fada<small>Painel do funcionário</small></div><div class="admin-user"><span>Olá, <strong><?= h($_SESSION['funcionario_nome'] ?? 'Funcionário') ?></strong></span><a class="btn btn-light btn-small" href="logout.php">Sair</a></div></header><div class="layout"><aside class="sidebar"><a class="" href="index.php">📊 Dashboard</a><a class="" href="produtos.php">📦 Produtos</a><a class="" href="adicionar_produto.php">➕ Adicionar produto</a><a class="" href="estoque.php">📋 Estoque</a><a class="active" href="pedidos.php">🛒 Pedidos</a></aside><main class="content"><h1 class="title">Pedidos</h1><p class="subtitle">Acompanhe os pedidos registrados pelos clientes.</p><div class="section"><div class="table-wrap"><table><tr><th>Pedido</th><th>Cliente</th><th>E-mail</th><th>Total</th><th>Status</th><th>Data</th></tr><?php foreach($pedidos as $p): ?><tr><td>#<?= h($p['id']) ?></td><td><?= h($p['nome']) ?></td><td><?= h($p['email']) ?></td><td>R$ <?= number_format($p['total'],2,',','.') ?></td><td><?= h($p['status']) ?></td><td><?= date('d/m/Y H:i',strtotime($p['criado_em'])) ?></td></tr><?php endforeach; ?></table><?php if(!$pedidos): ?><div class="empty">Nenhum pedido registrado ainda.</div><?php endif; ?></div></div></main></div></div></body></html>