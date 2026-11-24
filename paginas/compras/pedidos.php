<?php
session_start();
require_once __DIR__ . '/../../includes/catalogo.php';
require_once __DIR__ . '/../../includes/pedidos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../conta/entrar.php');
    exit;
}

$pedidos = pedidosDoUsuario((int)$_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meus pedidos | Mãos de Fada</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php include __DIR__.'/../../includes/header.php'; ?>
<main class="orders-page">
    <section class="page-hero compact-hero"><span class="eyebrow">MINHA CONTA</span><h1>Meus pedidos ♡</h1><p>Acompanhe os pedidos realizados na Mãos de Fada.</p></section>
    <?php if (!$pedidos): ?>
        <p>Você ainda não possui pedidos.</p>
        <a class="hero-btn" href="../produtos/produtos.php">COMEÇAR A COMPRAR</a>
    <?php else: ?>
        <div class="orders-list">
        <?php foreach ($pedidos as $pedido): ?>
            <article class="order-card">
                <div class="order-card-head"><h2>Pedido #<?= (int)$pedido['id'] ?></h2><span class="order-status"><?= e(ucfirst($pedido['status'])) ?></span></div>
                <div class="order-meta"><span>Total: <strong><?= money($pedido['total']) ?></strong></span><span>Data: <?= e(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></span></div>
                <div class="order-items">
                    <?php foreach (itensDoPedido((int)$pedido['id']) as $item): ?>
                        <div class="order-item"><span><?= e($item['nome']) ?> × <?= (int)$item['quantidade'] ?></span><strong><?= money($item['preco_unitario'] * $item['quantidade']) ?></strong></div>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php include __DIR__.'/../../includes/footer.php'; ?>
</body>
</html>
