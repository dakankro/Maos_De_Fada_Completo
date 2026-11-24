<?php
session_start();
require_once __DIR__ . '/../../includes/catalogo.php';
require_once __DIR__ . '/../../includes/pedidos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../conta/entrar.php?redirect=pagamento');
    exit;
}

$itens = pedidoItensDoCarrinho($_SESSION['carrinho'] ?? []);
$total = array_sum(array_column($itens, 'subtotal'));
$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $itens) {
    try {
        $pedidoId = criarPedido((int)$_SESSION['usuario_id'], $itens);
        $_SESSION['carrinho'] = [];
        $sucesso = 'Pedido #' . $pedidoId . ' criado com sucesso!';
        $itens = [];
        $total = 0;
    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Finalizar compra | Mãos de Fada</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<main class="checkout-page">
    <section class="page-hero compact-hero"><span class="eyebrow">ÚLTIMA ETAPA</span><h1>Finalizar compra ♡</h1><p>Confira seu pedido antes de confirmar.</p></section><div class="checkout-card">
    <?php if ($sucesso): ?>
        <p><?= e($sucesso) ?></p>
        <p>O pedido foi salvo no banco de dados e o estoque foi atualizado.</p>
        <a class="hero-btn" href="pedidos.php">VER MEUS PEDIDOS</a>
    <?php elseif (!$itens): ?>
        <p>Seu carrinho está vazio.</p>
        <a class="hero-btn" href="../produtos/produtos.php">VER PRODUTOS</a>
    <?php else: ?>
        <?php if ($erro): ?><p style="color:#b04a5a;font-weight:600;"> <?= e($erro) ?> </p><?php endif; ?>
        <h2>Resumo do pedido</h2>
        <div class="checkout-items">
        <?php foreach ($itens as $item): ?>
            <div class="checkout-item"><span><?= e($item['nome']) ?> × <?= (int)$item['quantidade'] ?></span><strong><?= money($item['subtotal']) ?></strong></div>
        <?php endforeach; ?>
        </div><div class="checkout-total"><span>Total</span><strong><?= money($total) ?></strong></div>
        <form method="POST">
            <p class="checkout-note">Ao confirmar, o pedido será registrado no banco de dados.</p>
            <button class="hero-btn" type="submit">CONFIRMAR PEDIDO</button>
        </form>
    <?php endif; ?>
</div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
